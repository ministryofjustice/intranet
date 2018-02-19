<?php
use MOJ\Intranet\Events;

$oEvents = new Events();

//Todo: Pass it as part of $data from the container
$options = array (
    'page' => 1,
    'per_page' => 2,
);

$eventsList = $oEvents->getEvents($options);

if (!empty($eventsList)) {
?>
    <div class="c-events-list">
        <?php foreach ($eventsList as $data) {
             get_component('c-events-item', $data);
        }
        ?>
    </div>
<?php
}
?>

