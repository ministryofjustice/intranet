<?php

    // Get config and sort into named variables
    $type   = $config['type']; // required
    $prefix = $config['prefix']; // required
    $label  = $config['label'];

if ($prefix === '') :
      $name = $prefix . $config['name'];
else :
        $name = $prefix . '_' . $config['name'];
endif;
    
    $id          = $config['id'] ? $config['id'] : $name;
    $value       = $config['value'];
    // placeholder now is hint text
    $hint = $config['placeholder'] ? '<div id="'. $id .'-hint" class="govuk-hint">' . $config['placeholder'] . '</div>' : null;
    $class       = $config['class'] ? 'class="' . $config['class'] . '"' : null;
    $required    = $config['required'] ? 'required="required"' : null;
    $validation  = $config['validation'] ? 'pattern="' . $config['validation'] . '"' : null;
    $options     = $config['options'];
?>

  <div class="c-input-container c-input-container--<?php echo $type; ?>">
      <?php if ($label != false) : ?>
      <label class="govuk-label govuk-label--l" for="<?php echo $name; ?>"><?php echo $label; ?>
      
      <?php
        if ($required !== 'required="required"') {
          ?>
            <span class="c-input-container--optional">(optional)</span>
          <?php
        }
        if ($type !== 'checkbox' && $type !== 'radio') {
            // stop label here if it's a normal input type
            ?>
          </label>
          <?php
          if ($hint) echo $hint;
        }
    endif;
      // Outputs different elements depending on input type
    if ($type !== 'select') {
        $tag = ( $type === 'textarea' ) ? 'textarea' : 'input';
        ?><<?php echo $tag; ?> type="<?php echo $type; ?>" name="<?php echo $name; ?>" id="<?php echo $id; ?>" 
                    <?php
        if ($type !== 'textarea') {
            echo 'value="' . $value . '"';
        }
        if ($hint) {
            echo "aria-describedby='$id-hint'";
        }

        echo $class . ' ' . $required . ' ' . $validation;

        if ($type === 'textarea') {
            echo '>'. $value .'</textarea>';
        } else {
            echo ' />';
        }
    } else {
        // Input is a select box, act accordingly
        ?>
        <select name="<?php echo $name; ?>" id="<?php echo $id; ?>" <?php echo $class . ' ' . $required; ?>>
        <?php
          // loop through options array and build a select list
        foreach ($options as $key => $value) {
            $default = $value[2] ? 'selected' : null;
            ?>
              <option value="<?php echo $value[1]; ?>" <?php echo $default; ?>/><?php echo $value[0]; ?></option>
              <?php
        }
        ?>
        </select>
        <?php
    }
    if ($type === 'checkbox' && $type === 'radio') {
        // stop label here if it's a checkbox or radio input type
        ?>
        </label>
        <?php
    }
    ?>
  </div>
