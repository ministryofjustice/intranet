<?php
use MOJ\Intranet\Agency;

$agency = get_intranet_code();

$enable_notification  = get_field($agency . '_enable_notification', 'option');
$notification_type    = get_field($agency . '_notification_type', 'option');
$notification_title   = get_field($agency . '_notification_title', 'option');
$notification_date    = get_field($agency . '_notification_date', 'option');
$notification_message = get_field($agency . '_notification_message', 'option');
?>
<?php if ($enable_notification) : ?>
  <!-- c-emergency-banner starts here -->
  <section class="c-emergency-banner c-emergency-banner--<?= $notification_type ?>">
    <div class="c-emergency-banner__meta">
    <time datetime="<?= $notification_date; ?>"><?= $notification_date ?></time>
      <h1><?= $notification_title ?></h1>
    </div>
    <div class="c-emergency-banner__content ie_content">
      <?= $notification_message ?>
    </div>
  </section>
  <!-- c-emergency-banner ends here -->
<?php else : ?>
  <!-- No emergency/service banner selected, no banner to display. -->
<?php endif; ?>
