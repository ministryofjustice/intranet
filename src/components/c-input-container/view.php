  <?php
    // Get config and sort into named variables
    $type = $config['type']; //required
    $prefix = $config['prefix']; //required
    $label = $config['label']; //required
    $name = $prefix . "_" . $config['name']; //required
    $id = $config['id'] ? $config['id']: $prefix . "_" . $name;
    $value = $config['value'];
    $placeholder = $config['placeholder'] ? 'placeholder = "' . $config['placeholder'] . '"' : null;
    $class = $config['class'] ? 'class = "'.$config['class'].'"' : null;
    $required = $config['required'] ? 'required = "required"' : null;
    $validation = $config['validation'] ? 'pattern = "'.$config['validation'].'"' : null;
    $options = $config['options'];
  ?>

  <div class="c-input-container c-input-container--<?php echo $type ?>">
    <label for="<?php echo $name; ?>"><?php echo $label ?>
    <?php
      if ($required) { ?>
        <span class="c-input-container--required">*</span>
      <?php
      }
      if ($type !== 'checkbox' && $type !== 'radio') {
        // stop label here if it's a normal input type
        ?>:</label>
      <?php
      }
      // Outputs different elements depending on input type
      if ($type !== 'select') {
        $tag = ($type === 'textarea') ? 'textarea' : 'input';
        ?>
        <<?php echo $tag; ?> type="<?php echo $type; ?>" name="<?php echo $name ?>" id="<?php echo $id; ?>" <?php if ($type !== 'textarea') {?> value="<?php echo $value; ?>" <?php } ?> <?php echo $class .' '. $required .' '. $validation .' '. $placeholder ?>><?php if ($type === 'textarea') { echo $value ?></textarea>
        <?php }
      } else {
        // Input is a select box, act accordingly
        ?>
        <select name="<?php echo $name ?>" id="<?php echo $id; ?>" <?php echo $class .' '. $required ?>>
          <?php
            // loop through options array and build a select list
            foreach ($options as $key => $value) {
              $default = $value[2] ? 'selected="selected"' : null;
            ?>
              <option value="<?php echo $value[1] ?>"><?php echo $value[0] ?></option>
              <?php
            }
          ?>
        </select>
    <?php }
    if ($type === 'checkbox' && $type === 'radio') {
        // stop label here if it's a checkbox or radio input type
        ?></label><?php
      } ?>
  </div>
