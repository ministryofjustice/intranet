<?php
use MOJ\Intranet\Agency;

$oAgency = new Agency();
$activeAgency = $oAgency->getCurrentAgency();

$featuredLinkList = $activeAgency['links'];

if (!empty($featuredLinkList )) {
?>
    <ul class="c-agency-link-list">
    <?php foreach ($featuredLinkList as $link) {
    ?>
        <li class="highlight <?php if (isset($link['classes'])) echo $link['classes'];?>">
            <a href="<?php echo $link['url'];?> <?php if ($link['is_external']) echo "rel='external'";?>("><?php echo $link['label'];?></a>
        </li>
    <?php
    }
    ?>
    </ul>
<?php
}
?>


