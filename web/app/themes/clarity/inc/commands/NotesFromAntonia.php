<?php

/**
 * Import and restructure content from the long-tail NFA page
 * Usage:
 *  dry-run: wp notes-from-antonia convert
 *  real-run: wp notes-from-antonia convert import
 */
class NotesFromAntonia
{
    public string $environment;

    public int $main_page_id = 295969;

    public int $pattern_matches = 0;

    public object|null $post;

    public bool|null $hide_progress = true;

    /**
     * Holds the last year found, to try and fix missing years
     * @var string
     */
    public string $last_year_discovered = '';

    /**
     * A regular expression to capture content from a long-tail, news
     * post; like Notes from Antonia
     *
     * Contains 4 pattern groups
     * @var string
     */
    public string $regex = '';

    /**
     * @var array
     */
    public array $posts;

    /**
     * Populated via this->import
     * @var array
     */
    public array $notes = [];

    /**
     * @var false
     */
    private bool $dry_run;

    public function __construct()
    {
        $this->dry_run = true;
        $this->post = get_post($this->main_page_id);
    }

    public function __invoke($args)
    {
        error_reporting(0);

        $do_convert = $args[0] ?? '';
        $do_import = $args[1] ?? '';

        if ($do_import === 'import') {
            WP_CLI::confirm("\nAre you sure you want to import Notes from Antonia?");
            $this->dry_run = false;
        }

        if ($do_convert === 'convert') {
            if (!$this->dry_run) {
                $this->hide_progress = false;
            }

            $this->convert();
        }
    }

    public function convert()
    {
        if (!$this->collect()) {
            WP_CLI::error("something happened and we cannot proceed");
            return false;
        }

        $this->prepare();

        // perform the dry-run / insert
        $this->perform();
    }

    private function perform()
    {
        if (!$this->dry_run) {
            // double check...
            $this->import_second_chance();

            WP_CLI::log("");
            $progress = $this->progress("---> importing 'Notes from Antonia': ", $this->pattern_matches);

            $count = 0;
            $agencies = [];
            foreach (wp_get_object_terms($this->main_page_id, 'agency') as $agency) {
                $agencies[] = $agency->slug;
            }

            foreach ($this->notes as $note) {
                $date = $note['date']['parsed'];
                $title = $note['title']['string'];
                $display = $note['title']['display'];
                $content = $note['content'];

                $id = wp_insert_post([
                    'post_type' => 'note-from-antonia',
                    'post_title' => $title,
                    'post_content' => $content,
                    'post_status' => 'publish',
                    'post_author' => 3771, // Antonia Romeo
                    'post_date' => $date
                ]);

                if (is_wp_error($id)) {
                    WP_CLI::log($id);
                    WP_CLI::log("");
                    WP_CLI::error("something happened when inserting a note, we cannot proceed.");
                } else {

                    $terms = wp_set_object_terms($id, $agencies, 'agency');

                    // now check the result of our term inputs...
                    if (is_wp_error($terms)) {
                        WP_CLI::log($id);
                        WP_CLI::log("");
                        WP_CLI::error("terms could not be added for a note with the ID of: " . $id);
                    }
                }

                update_field('display_title_notes_from_antonia', $display, $id);

                // progress bar admin
                $this->tick($progress);
                $count++;
            }

            $this->finish($progress);

            WP_CLI::log("");
            WP_CLI::success(sprintf('%d notes have been imported!', $this->pattern_matches));
        }
    }

    private function collect(): bool
    {
        // bail early if no post data
        if (!$this->can_use_post_object()) {
            return false;
        }
        WP_CLI::log("");

        // preg_match the content
        $this->pattern_matches = preg_match_all(
            '/\n(\*{2}(?P<date>.+)\s-\s(?P<title>.+)\*{2})?[\s]+(?P<object><img[^>]+>|<blockquote[\s\S]+?script>|\[caption[\s\S]+?\]+<img[^>]+>\[\/caption\]|\s)\s(?P<content>[\s\S]+?)---/m',
            $this->post->post_content,
            $matches
        );

        // bail if we couldn't find any matches
        if (!$this->pattern_matches) {

            WP_CLI::warning(($this->pattern_matches === 0
                ? 'no matches were found using the pattern - was the post ID correct?'
                : 'an error occurred whilst executing preg_match_all()'));
            return false;
        }

        $this->posts = $matches;

        WP_CLI::success(sprintf('We have %d notes', $this->pattern_matches));
        WP_CLI::log("");

        return true;
    }

    private function prepare()
    {
        $progress = $this->progress('---> preparing new blog format: ', ($this->pattern_matches * 4));
        $count = 0;

        // P A R S E
        // loop the match
        foreach ($this->posts as $name => $groups) {
            if (is_numeric($name)) {
                continue;
            }

            // loop the captures
            foreach ($groups as $key => $value) {
                /**
                 * Get notes into logical format
                 */
                $this->parse($name, $value, $key);

                $count++;
                $this->tick($progress);
            }
        }

        $this->finish($progress);

        WP_CLI::log("");
        WP_CLI::success('Preparation complete!');
        WP_CLI::log("");
        $progress = $this->progress('---> applying note entry fixes: ', ($this->pattern_matches * 4));
        $count = 0;

        // F I X
        // loop through notes
        foreach ($this->notes as $key => $note) {
            foreach ($note as $name => $value) {

                $this->fix($key, $name, $value);

                $count++;
                $this->tick($progress);
            }
        }

        $this->finish($progress);

        WP_CLI::log("");
        WP_CLI::success("Note fixes complete!\n");
        WP_CLI::log("---------------------------------");
    }

    private function fix($key, $name, $value)
    {
        $this->notes[$key][$name] = match ($name) {
            'date' => $this->fix_date($key, $value),
            'title' => $this->fix_title($key, $value),
            'object' => $value,
            'content' => $this->fix_content($key, $value)
        };
    }

    private function fix_date($key, $value)
    {
        if ($value['raw'] !== "") {
            return $value;
        }

        // does object contain a blockquote?
        if (str_contains($this->notes[$key]['object']['raw'], 'blockquote')) {
            // extract a date
            if (preg_match('/>([\w]{3,11} [\d]{1,2}, [\d]{4})</', $this->notes[$key]['object']['raw'], $match)) {
                $value['parsed'] = date('Y-m-d 01:00:00', strtotime($match[1]));
            }
        }

        return $value;
    }

    private function fix_title($key, $value)
    {
        if ($value['string'] !== "") {
            return $value;
        }

        // does object contain a blockquote? We can strip a title from this...
        if (str_contains($this->notes[$key]['object']['raw'], 'blockquote')) {
            // some <br>'s are used to break content up, yet we need
            // to understand where breaks occur; replace with
            // recognisable characters; ' . '
            $object = str_replace('<br>', ' . ', $this->notes[$key]['object']['raw']);

            // decode html entities
            $object = htmlspecialchars_decode($object);

            // extract a title
            if ($title = preg_replace('/[^A-Za-z0-9\-\s.,:&\'%]/', "", strip_tags($object))) {
                // let's try and resolve a useful string to
                // use as a title. Save them in this...
                $title_suggestions = [];
                foreach (['.', ',', '-', ':'] as $token) {
                    // check for lonesome single chars ones that shouldn't
                    // be in a title... take the chars that occur before it...
                    if (preg_match('/(.*?)\s[^a&-]\s/', $title, $match)) {
                        $title_suggestions[] = $match[1];
                    }

                    $title_suggestions[] = strtok($title, $token);
                }

                // only record unique suggestions
                $title_suggestions = array_unique($title_suggestions);

                /**
                 * Determine the winning title choice
                 * We need a short one, but not too short
                 */
                $mapping = array_combine($title_suggestions, array_map('strlen', $title_suggestions));
                $smallest = min($mapping);
                if ($smallest < 10) {
                    unset($mapping[array_search($smallest, $mapping)]);
                }

                /**
                 * ... and the winner is:
                 */
                $value['string'] = trim(array_keys($mapping, min($mapping))[0]);
            }
        }

        return $value;
    }

    private function fix_content($key, $value): string
    {
        $return = $this->notes[$key]['object']['raw'];
        if (!empty($value)) {
            $return .= "\n\n";
        }
        return $return . $value;
    }

    private function parse($name, $value, $key)
    {
        // clean up a little...
        $value = trim($value);

        /**
         * Uses match() to execute the right parse method
         * Add result to the notes array
         */
        $this->notes[$key][$name] = match ($name) {
            'date' => $this->parse_date($value),
            'title' => $this->parse_title($value),
            'object' => $this->parse_object($value),
            'content' => $value
        };
    }

    private function parse_date($value): array
    {
        // bail if empty
        if (empty($value)) {
            return $this->parsed_format($value);
        }

        // does the date have a year?
        if (!preg_match('/\s[\d]{4}/', $value, $match)) {
            // if not, append the last known year
            $value .= $this->last_year_discovered;
        } else {
            $this->last_year_discovered = $match[0];
        }

        $datetime = strtotime($value);
        return $this->parsed_format($value, date('Y-m-d 01:00:00', $datetime));
    }

    public function parse_title($value)
    {
        $meta = [
            'string' => $value,
            'display' => 1
        ];

        // produce a string if value results as empty
        if (empty($value)) {
            $meta['display'] = 0;
            return $meta;
        }

        return $meta;
    }

    /**
     * @param $value
     * @return array
     */
    private function parse_object($value): array
    {
        $parsed = '';
        if (strpos($value, 'img src') === 1) {
            if (preg_match('/src="(\S+)"/', $value, $match)) {
                $parsed = $match[1];
            }
        }

        return $this->parsed_format($value, $parsed);
    }

    private function colour($colour, $message, $lead = 'Import:')
    {
        return WP_CLI::colorize("%" . $colour . $lead . "%n " . $message);
    }

    /**
     * @param string $raw
     * @param string|array $parsed
     * @return array{ raw: string, parsed: string }
     */
    private function parsed_format(string $raw, string|array $parsed = ''): array
    {
        return [
            'raw' => $raw,
            'parsed' => $parsed
        ];
    }

    private function can_use_post_object(): bool
    {
        // bail early if we couldn't find a post
        if (!$this->post) {
            WP_CLI::warning("no post was found with ID " . $this->main_page_id);
            return false;
        }

        return true;
    }

    private function import_second_chance()
    {
        if (!$this->dry_run) {
            WP_CLI::confirm("\nLast chance :o)... are you sure you want to import Notes from Antonia?");
            WP_CLI::log("");
            WP_CLI::log("---------------------------------");
            WP_CLI::log("");
            WP_CLI::warning("We will perform a data import.");
            WP_CLI::warning("All changes will take effect on the front end once the current page has been updated to use the new template.\n");

            sleep(5);

            WP_CLI::log("");
            WP_CLI::log("---------------------------------");
            WP_CLI::log("");

            WP_CLI::log("Beginning import now...");

            sleep(2);
        }
    }

    private function progress($message, $count)
    {
        if (!$this->hide_progress) {
            return WP_CLI\Utils\make_progress_bar($message, $count);
        }

        return false;
    }

    private function tick($progress)
    {
        if (!$this->hide_progress) {
            $progress->tick();
        }
    }

    private function finish($progress)
    {
        if (!$this->hide_progress) {
            $progress->finish();
        }
    }
}

// 1. Register the instance for the callable parameter.

$instance = new NotesFromAntonia();
WP_CLI::add_command('notes-from-antonia', $instance);

// 2. Register object as a function for the callable parameter.
WP_CLI::add_command('notes-from-antonia', 'NotesFromAntonia');
