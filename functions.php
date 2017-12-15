<?php
use MOJ\Intranet\Agency;

define('MAX_FEATURED_NEWS', 2);
define('MAX_HOMEPAGE_NEWS', 8);

if (get_template_directory() === get_stylesheet_directory()) {
    //Include Filters and actions Filters
    require_once('inc/hooks/author.php');
    require_once('inc/post-types/news.php');
}

/**
* Initialise WP admin Toolbar
* https://codex.wordpress.org/Toolbar
* This is initialised here rather than on the parent theme because,
* on the parent theme this is initialised within the MVC plugin.
* //LEGACY This function is not intended for plugin or theme use, so once the
* old theme and MVC is deprecated we can look at the necessity of this function.
*/
add_action('init', '_wp_admin_bar_init');

/** Autoloader for inc */
spl_autoload_register('moj_autoload');

function moj_autoload($cls)
{
    $cls = ltrim($cls, '\\');

    if (strpos($cls, 'MOJ\Intranet') !== 0) {
        return;
    }

    $cls = str_replace('MOJ\Intranet', '', $cls);
    $cls = strtolower($cls);

    $path = get_stylesheet_directory() . '/inc' .
        str_replace('\\', DIRECTORY_SEPARATOR, $cls)  . '.php';

    require_once($path);
}

/** Not necessary because the styles are hardcoded on parent theme... */
//add_action( 'wp_enqueue_scripts', 'enqueue_parent_styles' );

// function enqueue_parent_styles()
// {
//     wp_enqueue_style('parent-style', get_template_directory_uri().'/assets/css/core.css');
// }

function enqueue_core_script(){

    wp_enqueue_script( 'core-js', get_stylesheet_directory_uri().'/assets/js/core.min.js', array(), 'v1.7.5' );
    wp_enqueue_style( 'core-style', get_stylesheet_directory_uri().'/assets/css/core.min.css', array(), 'v.1.1');
    wp_enqueue_style( 'print-style', get_stylesheet_directory_uri().'/assets/css/print.min.css', array(), 'v1.0', 'print' );
    

}
add_action( 'wp_enqueue_scripts','enqueue_core_script'  );



/**
 * Pick up ACF fields from parent theme
 */
add_filter('acf/settings/load_json', 'my_acf_json_load_point');

function my_acf_json_load_point($paths)
{
    // append path
    $paths[] = get_template_directory() . '/acf-json';

    // return
    return $paths;
}

/**
 * Return the assets folder in the child theme
 */

function get_assets_folder()
{
    return get_stylesheet_directory_uri().'/assets';
}

/***
*
 * Finds the current agency that has been set by an agency cookie.
 * Some agency landing pages are currently faux dummy pages that
 * have the agency title but use all of MoJ HQ content. The if statement
 * below assigns the dummy agencies to use the HQ content.
 * As the dummy agencies get onboarded, they can be removed from here.
 *
 */
function get_intranet_code()
{
    $oAgency = new Agency();
    $activeAgency = $oAgency->getCurrentAgency();
    $agency = $activeAgency['shortcode'];

    if ($agency === 'noms' || $agency === 'ospt' || $agency === 'judicial-appointments-commission') {
      $agency = 'hq';
      return $agency;
    } else {
      return $agency;
    }
}


/***
 *
 * Set the intranet cookie if GET variables are passed
 *
 ***/
add_action('init', 'set_intranet_cookie');

function set_intranet_cookie()
{
    $default_agency = 'hq';

    if (isset($_GET['agency'])) {
        $agency_value =  isset($_GET['agency']) ? trim($_GET['agency']) : $default_agency;
        setcookie('dw_agency', $agency_value, time()+ (3650 * DAY_IN_SECONDS), COOKIEPATH, COOKIE_DOMAIN);
        $_COOKIE['dw_agency'] = $agency_value;
    } elseif (!isset($_COOKIE['dw_agency'])) {
        setcookie('dw_agency', $default_agency, time()+ (3650 * DAY_IN_SECONDS), COOKIEPATH, COOKIE_DOMAIN);
        $_COOKIE['dw_agency'] = $default_agency;
    }
    
}

/**
 * Return the view for the component
 * @component [string] the folder name of the component
 * @data [array] the data to be passed to the component
 * @config [string, array] some components are reused, the config variable lets you pass info to them (see c-article-item for example)
 * @return true on success false on failure
 */

function get_component($component, $data = null, $config = null)
{
    $agency = get_intranet_code();

    $override = get_stylesheet_directory().'/views/components/'.$component.'/view-'.$agency.'.php';
    $global = get_stylesheet_directory().'/views/components/'.$component.'/view.php';

    if (file_exists($override)) {
        include($override);
        return true;
    } elseif (file_exists($global)) {
        include($global);
        return true;
    } else {
        return false;
    }
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

/*
 * Use get_component method to build a form input (I realise this is probably awfully done so feel free to refactor/replace/burn. PHP isn't my area - AF)
 * @component [string] the name of the component to build
 * @type [string] the type of input to place (eg, checkbox, text, radio, textarea etc...)
 * @prefix [string] the prefix of the form it belongs to (e.g. feedback form would be 'fbf')
 * @label [string] what should be in the label?
 * @name [string] the name of the input. It will automatically prefix the name with the @prefix value
 * @name [string] (optional) the id of the input. If none is specified it will take the @name value
 * @value [string] (optional) if there is a default value, place it here
 * @placeholder [string] (optional) if there should be some placeholder text, place it here
 * @class [string] (optional) add a custom class here
 * @required [boolean] is this required, true or false
 * @validation [string] (optional) add a regex based validation string here
 * @options [array] a list of options to use if using a select input type
 */

function form_builder($type, $prefix, $label, $name, $id = '', $value = '', $placeholder = '', $class = '', $required = false, $validation = '', $options = '')
{
    $config = [
      'type' => $type,
      'prefix' => $prefix,
      'label' => $label,
      'name' => $name,
      'id' => $id,
      'value' => $value,
      'placeholder' => $placeholder,
      'class' => $class,
      'required' => $required,
      'validation' => $validation,
      'options' => $options
    ];

    return get_component('c-input-container', null, $config);
}

/*
 * Register new Clarity main menu.
 * We are using this menu as a replacement main menu on all new templates.
 * Assigning menus are found by loging into wp-admin and setting the menu to display.
 * //LEGACY: Other menus are registered in the old template in menu-locations.php.
 */

add_action('init', 'register_my_menu');

function register_my_menu()
{
    register_nav_menu('header-menu', __('Header Menu'));

    register_nav_menu('hq-menu', __('HQ Menu'));
    register_nav_menu('cica-menu', __('CICA Menu'));
    register_nav_menu('hmcts-menu', __('HMCTS Menu'));
    register_nav_menu('judicial-appointments-commission-menu', __('JAC Menu'));
    register_nav_menu('judicial-office-menu', __('JO Menu'));
    register_nav_menu('law-commission-menu', __('LawCom Menu'));
    register_nav_menu('laa-menu', __('LAA Menu'));
    register_nav_menu('noms-menu', __('NOMS Menu'));
    register_nav_menu('nps-menu', __('NPS Menu'));
    register_nav_menu('opg-menu', __('OPG Menu'));
    register_nav_menu('ospt-menu', __('OSPT Menu'));
    register_nav_menu('pb-menu', __('PB Menu'));
    register_nav_menu('ppo-menu', __('PPO Menu'));
}

/***
*
* New option page for header banner - ACF options
* https://www.advancedcustomfields.com/resources/acf_add_options_page/
*
***/

if (function_exists('acf_add_options_page')) {
    acf_add_options_page('Phase Banner');
}

/***
 *
 * Feedback Form
 * Two action occurs here.
 * This is the feedback form used everywhere. footer.php
 * - Mail to intranet@justice.gsi.gov.uk which captures the name,email,message,agency & client info
 * - Confirmation mail to the user
 *
 ***/
add_action('wp_head', 'feedback_form');

function feedback_form()
{
    if (isset($_POST['submit'])) {
        $form = [
            'name'      => $_POST['fbf_name'],
            'email'     => $_POST['fbf_email'],
            'message'   => $_POST['fbf_message'],
            'agency'    => $_POST['agency'],
        ];
        $to = 'intranet@justice.gsi.gov.uk';
        $subject = 'Feedback Form';
        $message  = "Name: " . $form['name'] ."\n";
        $message .= "Email: " . $form['email'] ."\n";
        $message .= "Message: " . $form['message'] ."\n";
        $message .= "Client info:\n";
        $message .= "Page URL: " . get_permalink() . "\n";
        $message .= "Agency: " . $form['agency'] . "\n";
        $message .= "Referrer: " . $_SERVER['HTTP_REFERER'] ."\n";
        $message .= "User agent: " . $_SERVER['HTTP_USER_AGENT'] . "\n";
        $headers = "Content-Type: text/html; charset=UTF-8\n";
        $headers .= "From: Feedback form Intranet\n";

        wp_mail($to, $subject, $message, $headers);
        $feedback_email = $form['email'];
        $feedback_subject = 'Page feedback - MoJ Intranet [T'. get_the_date('Ymdgi').']';
        $feedback = "Thank you for contacting us. \n";
        $feedback .= "Your feedback matters – it helps us find out what we need to improve so that we can offer you a better intranet experience. \n";
        $feedback .= "Your query has been logged and we will deal with it as soon as possible.";
        wp_mail($feedback_email, $feedback_subject, $feedback);
    }
}
add_action( 'wp_head', 'feedback_form' );

function posts_link_attributes_prev() {
    return 'class="c-pagination__link c-pagination__link--prev"';
}
function posts_link_attributes_next() {
    return 'class="c-pagination__link c-pagination__link--next"';
}
add_filter('next_posts_link_attributes', 'posts_link_attributes_prev');
add_filter('previous_posts_link_attributes', 'posts_link_attributes_next');


function custom_monthly_archive($link_html, $url, $text, $format){
    if( 'custom' === $format )
        $strip_url = str_replace('http://intranet.docker/blog/', '', $url);
        $link_html = '<option value='.$strip_url.'>'.$text.'</option>';
    return $link_html;
}
add_filter( 'get_archives_link', 'custom_monthly_archive', 10, 6 );


function ajax_search_enqueues() {
    	wp_enqueue_script( 'ajax-search', get_stylesheet_directory_uri() . '/tests/js-test/blog-content_filter.js', array( ), '1.0.2', true );
        wp_localize_script( 'ajax-search', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
}

add_action( 'wp_enqueue_scripts', 'ajax_search_enqueues' );


function load_search_results() {
    $query = $_POST['query'];
    
    $args = array(
        'paged' => $paged,
        'posts_per_page' => 5,
        'post_type' => 'post',
        'post_status' => 'publish',
        
        's' => $query
    );
    $search = new WP_Query( $args );

    $prev_page_number = $paged-1;
    $next_page_number = $paged+1;

    $total_page_number = $query->max_num_pages;
    
    ob_start();
    
    if ( $search->have_posts() ) : 
    
    ?>

		<?php
			while ( $search->have_posts() ) : $search->the_post();
				get_component('c-article-item', '', 'show_excerpt');
            endwhile;
            ?>
            <nav class="c-pagination" role="navigation" aria-label="Pagination Navigation">
                <?php 
                    echo previous_posts_link( '<span class="c-pagination__main">Previous page</span><span class="c-pagination__count">'.$prev_page_number.' of '.$total_page_number.'</span>' );
                    echo next_posts_link( '<span class="c-pagination__main">Next page</span><span class="c-pagination__count">'.$next_page_number.' of '.$total_page_number.'</span>', $total_page_number );          
                ?>
            </nav> 
            <?php
	else :
		echo 'nothing';
	endif;
	
	$content = ob_get_clean();
	
	echo $content;
	die();
			
}
add_action( 'wp_ajax_load_search_results', 'load_search_results' );
add_action( 'wp_ajax_nopriv_load_search_results', 'load_search_results' );

