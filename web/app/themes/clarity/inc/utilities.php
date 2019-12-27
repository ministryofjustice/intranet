<?php

// -----------------
// Utility functions
// -----------------

/* checks to see if the a post has an ancestor by slug name */
function has_ancestor($s) {
    global $post;
    $a = get_post_ancestors( $post );
    foreach (array_reverse($a) as $v) {
      $p = get_post($v);
      if ($p->post_name==$s) {
        return true;
      }
    }
    return false;
  }
  
  /**
   * Determines the difference between two timestamps.
   *
   * The difference is returned in a human readable format such as "1 hour",
   * "5 mins", "2 days".
   *
   * @since 1.5.0
   *
   * @param int $from Unix timestamp from which the difference begins.
   * @param int $to Optional. Unix timestamp to end the time difference. Default becomes time() if not set.
   * @return string Human readable time difference.
   * Taken from formatting.php to include months and years - Luke Oatham
   */
  function human_time_diff_plus( $from, $to = '' ) {
    $tzone = get_option('timezone_string');
    date_default_timezone_set($tzone);
  
    $MONTH_IN_SECONDS = DAY_IN_SECONDS * 30;
       if ( empty( $to ) )
            $to = time();
       $diff = (int) abs( $to - $from );
       if ( $diff <= HOUR_IN_SECONDS ) {
            $mins = round( $diff / MINUTE_IN_SECONDS );
            if ( $mins <= 1 ) {
                 $mins = 0;
            }
            /* translators: min=minute */
            $since = sprintf( _n( '%s min', '%s mins', $mins ), $mins );
       } elseif ( ( $diff <= DAY_IN_SECONDS ) && ( $diff > HOUR_IN_SECONDS ) ) {
            $hours = round( $diff / HOUR_IN_SECONDS );
            if ( $hours <= 1 ) {
                 $hours = 1;
            }
            $since = sprintf( _n( '%s hour', '%s hours', $hours ), $hours );
       } elseif ( $diff >= YEAR_IN_SECONDS ) {
            $years = round( $diff / YEAR_IN_SECONDS );
            if ( $years <= 1 ) {
                 $years = 1;
            }
            $since = sprintf( _n( '%s year', '%s years', $years ), $years );
       } elseif ( ( $diff >= $MONTH_IN_SECONDS ) && ( $diff < YEAR_IN_SECONDS ) ) {
            $months = round( $diff / $MONTH_IN_SECONDS );
            if ( $months <= 1 ) {
                 $months = 1;
            }
            $since = sprintf( _n( '%s month', '%s months', $months ), $months );
       } elseif ( $diff >= DAY_IN_SECONDS ) {
            $days = round( $diff / DAY_IN_SECONDS );
            if ( $days <= 1 ) {
                 $days = 1;
            }
            $since = sprintf( _n( '%s day', '%s days', $days ), $days );
       }
       return $since;
  }
  
  // Returns the caption of the featured image associated with the current post
  function get_post_thumbnail_caption() {
    if ( $thumb = get_post_thumbnail_id() )
      return get_post( $thumb )->post_excerpt;
  }
  
  function get_field_by_id($id, $function) {
    global $post;
    $orig_post = $post;
    $post = get_post($id);
    $args = array_slice(func_get_args(), 2);
    $value = call_user_func_array($function, $args);
    $post = $orig_post;
  
    return $value;
  }
  
  function get_the_excerpt_by_id($id) {
    return get_field_by_id($id, 'get_the_excerpt');
  }
  
  function get_the_content_by_id($id) {
    $post = get_post($id);
    $content = $post->post_content;
    $content = apply_filters('the_content', $content);
    $content = str_replace(']]>', ']]&gt;', $content);
    return $content;
  }
  
  /**
   * Get an attachment ID given a URL.
   *
   * @param string $url
   *
   * @return int Attachment ID on success, 0 on failure
   */
  function get_attachment_id_from_url( $url ) {
    $attachment_id = 0;
    $dir = wp_upload_dir();
    if ( false !== strpos( $url, $dir['baseurl'] . '/' ) ) { // Is URL in uploads directory?
      $file = basename( $url );
      $query_args = array(
        'post_type'   => 'attachment',
        'post_status' => 'inherit',
        'fields'      => 'ids',
        'meta_query'  => array(
          array(
            'value'   => $file,
            'compare' => 'LIKE',
            'key'     => '_wp_attachment_metadata',
          ),
        )
      );
      $query = new WP_Query( $query_args );
      if ( $query->have_posts() ) {
        foreach ( $query->posts as $post_id ) {
          $meta = wp_get_attachment_metadata( $post_id );
          $original_file       = basename( $meta['file'] );
          $cropped_image_files = wp_list_pluck( $meta['sizes'], 'file' );
          if ( $original_file === $file || in_array( $file, $cropped_image_files ) ) {
            $attachment_id = $post_id;
            break;
          }
        }
      }
    }
    return $attachment_id;
  }
  
  function html_mail($email, $subject, $message) {
    add_filter('wp_mail_content_type', function($content_type) {
      return 'text/html';
    });
  
    wp_mail($email, $subject, $message);
  }
  
  /** comparator function for sorting items in an array by value of a specific key
   * @param {Mixed} $a First value
   * @param {Mixed} $b Second value
   * @param {String} $key Array key to use for comparison
   */
  function alpha_sort_by_key($a, $b, $key) {
    return strnatcmp($a[$key], $b[$key]);
  }
  
  /** gets a value of an element of the array
   * @param {Array} $array Input array
   * @param {Integer|String} $element Key name or index number of the desired element
   * @param {Mixed} $default The default value to be returned if element doesn't exist
   * @return {Mixed} Element value or default value
   */
  function get_array_value($array, $element, $default) {
    return isset($array) && isset($array[$element]) && $array[$element] ? $array[$element] : $default;
  }
  
  /** Check if the supplied tax_query array uses the specified taxonomy
   * @param {Array} $tax_query Tax query array
   * @param {Array} $taxonomy Taxonomy name
   * @return {Boolean} true if it uses regional taxonomy, otherwise false
   */
  function has_taxonomy($tax_query, $taxonomy) {
    foreach ($tax_query as $row) {
      if (is_array($row) && $row['taxonomy'] == $taxonomy) return true;
    }
  
    return false;
  }
  
  /** Gets a list of term slugs within a taxonomy
   * @param {String} $taxonomy Taxonomy name
   * @return {Array} List of all term slugs
   */
  function get_term_slugs($taxonomy) {
    $term_slugs = [];
  
    $terms = get_terms([
      'taxonomy' => $taxonomy,
      'hide_empty' => false
    ]);
  
    foreach ($terms as $term) {
      $term_slugs[] = $term->slug;
    }
  
    return $term_slugs;
  }

/**
 * Returns any text you supply in lower-case and hyphenated
 * @string the string to convert
 * @return slugified string
 */

function slugify($string)
{
    $newstring = str_replace(' ', '-', $string);
    $newstring = strtolower($newstring);
    return $newstring;
}
/***
 *
 * Next and previous function used on all list/archive pages, ie, blogs, news.
 *
 ***/
//add_action('wp_head', 'feedback_form');
//add_filter('next_posts_link_attributes', 'posts_link_attributes_prev');
//add_filter('previous_posts_link_attributes', 'posts_link_attributes_next');

function posts_link_attributes_prev()
{
    return 'class="c-pagination__link c-pagination__link--prev"';
}

function posts_link_attributes_next()
{
    return 'class="c-pagination__link c-pagination__link--next"';
}


add_filter('get_archives_link', 'custom_monthly_archive', 10, 6);

function custom_monthly_archive($link_html, $url, $text, $format)
{
    if ('custom' === $format) {
        $siteurl = get_home_url() . '/blog/';
        $strip_url = str_replace($siteurl, '', $url);

        $replace_with_slash = str_replace('/', '-', $strip_url);

        $valueSelected = $replace_with_slash . '01T00:00:00';

        /* $valueSelected returns 'http:--intranet.docker-2017-12-01T00:00:00' a sting that is not parsed in any usable format.
        * preg_match strips it to the values the variables below require.
        * TODO find out why is was orgnially parsed in this way and then why it stopped working.
        */

        $subject = $valueSelected;
        $pattern = '/[\d]{4}-[\d]{2}-[\d]{2}[\D]0{2}:0{2}:0{2}$/';
        /* Example string 'http:--intranet.docker-2017-12-01T00:00:00' . We want only the last part from the year onward.
        */

        preg_match($pattern, $subject, $match);

        $valueSelected = $match[0];

        $get_year = substr($valueSelected, 0, 4);
        $get_month = substr($valueSelected, 5, 2);

        if ($get_month == 12) {
            $year = $get_year + 1;
            $month = '01';
            $date_range = '&after='.$valueSelected.'&before='.$year.'-'.$month.'-01T00:00:00';
            $link_html = '<option value='.$date_range.'>'.$text.'</option>';
        } else {
            $year = $get_year;
            $month = $get_month + 1;

            $add_leading_zero = str_pad($month, 2, '0', STR_PAD_LEFT);

            $date_range = '&after='.$valueSelected.'&before='.$year.'-'.$add_leading_zero.'-01T00:00:00';
            $link_html = '<option value='.$date_range.'>'.$text.'</option>';
        }
    }

    return $link_html;
}
