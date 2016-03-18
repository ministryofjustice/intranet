<?php if (!defined('ABSPATH')) die();

class Bad_words_model extends MVC_model {
  public function __construct() {
    parent::__construct();
  }

  public function has_bad_words($source) {
    $blocked = false;
    $source = preg_replace("/[^a-z]/"," ", strtolower($source));
    $source = preg_replace("/\s+/"," ", $source);
    $source_words = explode(" ", trim($source));

    foreach($source_words as $word) {
      $unique_words[$word] = 1;
    }

    $unique_words = array_keys($unique_words);

    $bad_words = $this->get_bad_words();

    foreach($unique_words as $word) {
      if(in_array($word, $bad_words)) {
        $blocked[] = $word;
      }
    }

    return $blocked;
  }

  private function add_word_variations() {

  }

  private function get_bad_words() {
    $bad_words = array(
      'anal',
      'anus',
      'arse',
      'ass',
      'ballsack',
      'balls',
      'bastard',
      'bitch',
      'biatch',
      'bloody',
      'blowjob',
      'blow job',
      'bollock',
      'bollok',
      'boner',
      'boob',
      'bugger',
      'bum',
      'butt',
      'buttplug',
      'clitoris',
      'cock',
      'coon',
      'crap',
      'cunt',
      'damn',
      'dick',
      'dildo',
      'dyke',
      'fag',
      'feck',
      'fellate',
      'fellatio',
      'felching',
      'fuck',
      'f u c k',
      'fudgepacker',
      'fudge packer',
      'flange',
      'goddamn',
      'god damn',
      'hell',
      'homo',
      'jerk',
      'jizz',
      'knobend',
      'knob end',
      'labia',
      'lmao',
      'lmfao',
      'muff',
      'nigger',
      'nigga',
      'omg',
      'penis',
      'piss',
      'poop',
      'prick',
      'pube',
      'pussy',
      'queer',
      'scrotum',
      'sex',
      'shit',
      's hit',
      'sh1t',
      'slut',
      'smegma',
      'spunk',
      'tit',
      'tosser',
      'turd',
      'twat',
      'vagina',
      'wank',
      'whore',
      'wtf'
    );

    return $bad_words;
  }
}
