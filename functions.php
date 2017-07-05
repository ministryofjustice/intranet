<?php
use MOJ\Intranet\Agency;

define('MAX_FEATURED_NEWS', 2);
define('MAX_HOMEPAGE_NEWS', 8);
if (get_template_directory() === get_stylesheet_directory()) {
    //Include Filters and actions Filters
    require_once('inc/hooks/author.php');
    require_once('inc/post-types/news.php');
}

require_once('inc/theme-setup.php');


/** Autoloader for inc */
spl_autoload_register('moj_autoload');

function moj_autoload($cls)
{
    $cls = ltrim($cls, '\\');


    if(strpos($cls, 'MOJ\Intranet') !== 0)
        return;

    $cls = str_replace('MOJ\Intranet', '', $cls);
    $cls = strtolower($cls);

    $path = get_stylesheet_directory() . '/inc' .
        str_replace('\\', DIRECTORY_SEPARATOR, $cls)  . '.php';

    require_once($path);
}

/** Not necessary because the styles are hardcoded on parent theme... */
//add_action( 'wp_enqueue_scripts', 'enqueue_parent_styles' );

function enqueue_parent_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri().'/assets/css/core.css' );
}


/**
 * Pick up ACF fields from parent theme
 */

add_filter('acf/settings/load_json', 'my_acf_json_load_point');

function my_acf_json_load_point( $paths ) {

    // append path
    $paths[] = get_template_directory() . '/acf-json';


    // return
    return $paths;

}

/**
 * Return the assets folder in the child theme
 */

function get_assets_folder() {
  return get_stylesheet_directory_uri().'/assets';
}

/***
 * Gets the intranet code, if present
 *
 */

function get_intranet_code()
{
    $oAgency = new Agency();
    $activeAgency = $oAgency->getCurrentAgency();
    return $activeAgency['shortcode'];
}


/***
 *
 * Set the intranet cookie if GET variables are passed
 *
 ***/
function set_intranet_cookie()
{

    $agency_value =  isset($_GET['dw_agency']) ? trim($_GET['dw_agency']) : 'hq';


    if (!isset($_COOKIE['dw_agency']))
    {

        setcookie( 'dw_agency', $agency_value, time()+ (3650 * DAY_IN_SECONDS), COOKIEPATH, COOKIE_DOMAIN );
        $_COOKIE['dw_agency'] = $agency_value;

    } else {
        if (isset($_GET['dw_agency']))
        {
            setcookie( 'dw_agency', $agency_value, time()+ (3650 * DAY_IN_SECONDS), COOKIEPATH, COOKIE_DOMAIN );
            $_COOKIE['dw_agency'] = $agency_value;
        }
    }

}
add_action( 'init', 'set_intranet_cookie' );


/**
 * Return the view for the component
 * @component [string] the folder name of the component
 * @data [array] the data to be passed to the component
 * @config [string, array] some components are reused, the config variable lets you pass info to them (see c-article-item for example)
 * @return true on success false on failure
 */

function get_component($component, $data = null, $config = null) {

  $agency = get_intranet_code();

  $override = get_stylesheet_directory().'/views/components/'.$component.'/view-'.$agency.'.php';
  $global = get_stylesheet_directory().'/views/components/'.$component.'/view.php';

  if (file_exists($override))
  {
      include ($override);
      return true;
  } else if (file_exists($global)) {
      include ($global);
      return true;
  } else return false;
}

/**
 * Returns any text you supply in lower-case and hyphenated
 * @string the string to convert
 * @return slugified string
 */

function slugify($string) {
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

function form_builder($type, $prefix, $label, $name, $id = '', $value = '', $placeholder = '', $class = '', $required = false, $validation = '', $options = '') {
  $config = array (
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
      'options' => $options);

  return get_component('c-input-container', null, $config);
}
