<?php

defined('ABSPATH') || exit;

if (empty($args['heading'])) {
    return;
}

$variants = [
    'default' => [
        'label' => 'Information'
    ],
    'information' => [
        'label' => 'Information',
        'icon' => 'information'
    ],
    'success' => [
        'label' => 'Success',
        'icon' => 'success'
    ],
    'warning' => [
        'label' => 'Warning',
        'icon' => 'warning'
    ]
];

$variant_with_default = isset($args['variant']) && in_array($args['variant'], array_keys($variants)) ? $args['variant'] : 'default';

$variant_values = $variants[$variant_with_default];

?>

<!-- c-moj-banner starts here -->
<section class="c-moj-banner c-moj-banner--<?= $variant_with_default ?>" role="region" aria-label="<?= $variant_values['label'] ?>">
    <?php
    if ($variant_values['icon']) {
        require 'icons/' . $variant_values['icon'] . '.php';
    }
    ?>
    <div class="c-moj-banner__message">
        <h2 class="o-title"><?= $args['heading']; ?></h2>
    </div>
</section>
<!-- c-moj-banner ends here -->
