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

// check for array keys
$banner_label = $variant_values['label'] ?? '';
$banner_icon = $variant_values['icon'] ?? false;
?>

<!-- c-moj-banner starts here -->
<section class="c-moj-banner c-moj-banner--<?= $variant_with_default ?>" role="region" aria-label="<?= $banner_label ?>">
    <?php
    $banner_icon_file = 'icons/' . $banner_icon . '.php';
    if (file_exists($banner_icon_file)) {
        require $banner_icon_file;
    }
    ?>
    <div class="c-moj-banner__message">
        <h2 class="o-title"><?= $args['heading']; ?></h2>
    </div>
</section>
<!-- c-moj-banner ends here -->
