<?php

/**
 * PostCleanup
 * 
 * This command will search for old posts to delete
 * 
 * Usage:
 * wp content_cleanup <command> <agency_slug> --post_types=<csv_post_type_slugs> --after=<YYYY-MM-DD> --to=<YYYY-MM-DD> --dry-run=<true|false>
 * 
 * Example:
 * wp content_cleanup report cica --post_types=post,news --from=2016-01-01 --before=2019-12-31 --dry-run=true
 */

// use function rest_parse_date;

if (defined('WP_CLI') && WP_CLI) {
    class PostCleanup
    {
        const ALLOWED_COMMANDS = ['report'];

        const ALLOWED_POST_TYPES = ['event', 'news', 'post'];

        public string $agency;

        public null|DateTime|WP_Error $after = null;

        public null|DateTime|WP_Error $before = null;

        /** @var string[] */
        public array $post_types = ['event', 'news', 'post'];

        /** @var WP_Post_Type[] */
        public array $post_type_objects = [];

        public $dry_run = true;

        /**
         * Invoke
         *
         * @param mixed $args - the required arguments (single string e.g. command and agency slug)
         * @param mixed $assoc_args - the associative arguments (in the format --x=y)
         * @return void
         */
        public function __invoke($args, $assoc_args)
        {
            $command = $args[0] ?? '';

            if (
                !is_string($command) || !in_array($command, self::ALLOWED_COMMANDS, true) || !is_callable([$this, $command])
            ) {
                WP_CLI::error('Please provide a valid command.');
                return;
            }

            $this->agency = $args[1] ?? '';

            if (empty($this->agency)) {
                WP_CLI::error('Please provide an agency slug.');
                return;
            }

            try {
                $post_types = explode(',', $assoc_args['post_types']);
                $trimmed_post_types = array_map('trim', $post_types);
                $filtered_post_types = array_filter($trimmed_post_types, fn($s) => !empty($s));
                $has_disallowed_post_types = !!array_find($filtered_post_types, fn($s) => !in_array($s, self::ALLOWED_POST_TYPES));
                if ($has_disallowed_post_types) {
                    throw new Exception('Disallowed post types');
                }
                $this->post_types = $filtered_post_types;
            } catch (Exception $e) {
                WP_CLI::error($e);
                return;
            }

            $this->after = $this->parseDate($assoc_args['after'] ?? '');

            if (is_wp_error($this->after)) {
                WP_CLI::error($this->after->get_error_message());
            }

            $this->before = $this->parseDate($assoc_args['before'] ?? '');

            if (is_wp_error($this->before)) {
                WP_CLI::error($this->before->get_error_message());
            }

            if (($assoc_args['dry-run'] ?? '') === 'false') {
                $this->dry_run = false;
            }

            foreach ($this->post_types as $post_type) {
                $this->post_type_objects[$post_type] = get_post_type_object($post_type);
            }

            WP_CLI::line(sprintf('Running %s', $command));
            WP_CLI::line(sprintf('  Agency %s', $this->agency));
            WP_CLI::line(sprintf('  Dates between %s and %s inclusive', $this->after?->format('Y-m-d') ?? 'dawn of time', $this->before?->format('Y-m-d') ?? 'now'));
            WP_CLI::line(sprintf(
                '  Post types: %s',
                implode(
                    ', ',
                    array_map(
                        fn(WP_Post_Type $post_type) => sprintf('%s (%s)', $post_type->labels->name, $post_type->name),
                        $this->post_type_objects
                    )
                )
            ));
            WP_CLI::line(sprintf('  Mode %s', $this->dry_run ? 'dry-run' : 'live'));

            // Call the command, e.g. report
            call_user_func_array([$this, $command], []);
        }

        /**
         * Parse the date from the CLI argument.
         *
         * @param mixed $maybe_date - optional, unvalidated user supplied date string
         * @return null|DateTime|WP_Error
         */
        public function parseDate($maybe_date)
        {
            if (!is_string($maybe_date) || empty($maybe_date)) {
                return null;
            }

            // 1. Force the strict 'Y-m-d' pattern
            $date_object = DateTime::createFromFormat('!Y-m-d', $maybe_date);

            // 2. Validate that the string matches the exact format perfectly
            if ($date_object?->format('Y-m-d') === $maybe_date) {
                return $date_object;
            }

            // 3. Return a native WP_Error object
            return new WP_Error(
                'invalid_date_format', // Unique error slug/code
                'The provided date string is invalid. Please use the YYYY-MM-DD format.',
            );
        }

        public function report()
        {
            // TODO - work out who published.

            foreach ($this->post_type_objects as $post_type => $post_type_obj) {
                $posts = get_posts([
                    'post_type'      => $post_type,
                    'post_status'    => 'publish',
                    'posts_per_page' => -1,
                    'fields'         => 'ids',
                    'tax_query'      => [
                        [
                            'taxonomy' => 'agency',
                            'field'    => 'slug',
                            'terms'    => $this->agency,
                        ],
                    ],
                    'date_query' => [
                        [
                            'after'     => $this->after?->format('Y-m-d'),
                            'before'    => $this->before?->format('Y-m-d'),
                            'inclusive' => true,
                        ],
                    ],
                ]);

                $posts_with_exclusivity = array_combine(
                    $posts,
                    array_map(
                        fn(int $post_id) => [
                            // Is the agency the only one tagged for this post?
                            'exclusive' => count(
                                wp_get_post_terms($post_id, 'agency', ['fields' => 'ids'])
                            ) === 1,
                        ],
                        $posts
                    )
                );

                $all_count = count($posts_with_exclusivity);

                $exclusive_count =  count(
                        array_filter($posts_with_exclusivity, fn($p) => $p['exclusive'])
                    );

                $non_exclusive_count = $all_count - $exclusive_count;

                WP_CLI::line('---');
                $label = $post_type_obj->labels->name;

                WP_CLI::line(sprintf('%s (%s)', $label, $post_type));
                WP_CLI::line(sprintf('  Tagged with "%s": %d', $this->agency, $all_count));
                WP_CLI::line(sprintf('    Tagged with "%s" and other agencies: %d', $this->agency, $non_exclusive_count));
                WP_CLI::line(sprintf('    Tagged only with "%s": %d', $this->agency, $exclusive_count));
            }
        }
    }


    WP_CLI::add_command('content_cleanup', 'PostCleanup');
}
