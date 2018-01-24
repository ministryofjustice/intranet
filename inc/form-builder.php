<?php

if (!defined('ABSPATH')) {
    die();
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
