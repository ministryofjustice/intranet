<?php
/**
 * 27th April 2023: The file has been refactored to recognise that a variable can
 * be inferred within a single scope when using include/require.
 */

$c_input_container_config = $config ?? [];

if ($c_input_container_config['prefix'] ?? false) {
    $c_input_container_config['name'] = $c_input_container_config['prefix'] . '_' . $c_input_container_config['name'];
}

// Sort into named variables
$c_input_container_id = $c_input_container_config['id'] ?: $c_input_container_config['name'];
$c_input_container_value = $c_input_container_config['value'] ?: null;

// placeholder now is hint text
$c_input_container_hint = $c_input_container_config['placeholder'] ? '<div id="' . $c_input_container_id . '-hint" class="govuk-hint">' . $c_input_container_config['placeholder'] . '</div>' : null;
$c_input_container_class = $c_input_container_config['class'] ? 'class="' . $c_input_container_config['class'] . '"' : null;
$c_input_container_required = $c_input_container_config['required'] ? 'required="required"' : null;
$c_input_container_validation = $c_input_container_config['validation'] ? 'pattern="' . $c_input_container_config['validation'] . '"' : null;
$c_input_container_options = $c_input_container_config['options'] ?: null;

// start output
$c_input_container_output = '<div class="c-input-container c-input-container--' . $c_input_container_config['type'] . '">';

if ($c_input_container_config['label'] != false) {
    $c_input_container_output .= '<label class="govuk-label govuk-label--l" for="' . $c_input_container_config['name'] . '">' . $c_input_container_config['label'];

    if ($c_input_container_required !== 'required="required"') {
        $c_input_container_output .= ' <span class="c-input-container--optional">(optional)</span>';
    }

    if ($c_input_container_config['type'] !== 'checkbox' && $c_input_container_config['type'] !== 'radio') {
        // stop label here if it's a normal input type
        $c_input_container_output .= '</label>';
        $c_input_container_output .= $c_input_container_hint ?? '';
    }
}

// Outputs different elements depending on input type
if (!in_array($c_input_container_config['type'], ['select', 'radio-group'])) {
    $tag = ($c_input_container_config['type'] === 'textarea') ? 'textarea' : 'input';
    $c_input_container_output .= '<' . $tag . ' type="' . $c_input_container_config['type'] . '" name="' . $c_input_container_config['name'] . '" id="' . $c_input_container_id . '"';

    if ($c_input_container_config['type'] !== 'textarea') {
        $c_input_container_output .= 'value="' . $c_input_container_value . '"';
    }

    if ($c_input_container_hint) {
        $c_input_container_output .= 'aria-describedby="' . $c_input_container_id . '-hint"';
    }

    $c_input_container_output .= $c_input_container_class . ' ' . $c_input_container_required . ' ' . $c_input_container_validation;

    if ($c_input_container_config['type'] === 'textarea') {
        $c_input_container_output .= '>' . $c_input_container_value . '</textarea>';
    } else {
        $c_input_container_output .= ' />';
    }
} else if ($c_input_container_config['type'] === 'radio-group') {

    $c_input_container_output .= '<div class="'.$c_input_container_config['name'].'">';

    foreach($c_input_container_options as $key => $value) {
        $default = $value[2] ? 'checked' : null;
        $default = $default ?: ($key === 0 ? 'checked' : null);

        $c_input_container_output .= '
        <div class="c-input-container--radio-group-item">
            <input
                type="radio"
                id="'.$c_input_container_config['name'].'_'.$value[1].'"
                name="'.$c_input_container_config['name'].'"
                '.$c_input_container_class.'
                value="' . $value[1] . '" ' . $default . '/>
            <label class="radios__label" for="'.$c_input_container_config['name'].'_'.$value[1].'">
                ' . $value[0] . '
            </label>
        </div>';
    }

    $c_input_container_output .= '</div>';
} else {
    // Input is a select box, act accordingly
    $c_input_container_output .= '<select name="' . $c_input_container_config['name'] . '" id="' . $c_input_container_id . '" ' . $c_input_container_class . ' ' . $c_input_container_required . '>';

    // loop through options array and build a select list
    foreach ($c_input_container_options as $key => $value) {
        $default = $value[2] ? 'selected' : null;
        $c_input_container_output .= '<option value="' . $value[1] . '" ' . $default . '/>' . $value[0] . '</option>';
    }

    $c_input_container_output .= '</select>';
}

if ($c_input_container_config['type'] === 'checkbox' || $c_input_container_config['type'] === 'radio') {
    // stop label here if it's a checkbox or radio input type
    $c_input_container_output .= '</label>';
}

echo $c_input_container_output .= '</div>';
