<?php
use MOJ\Intranet\Agency;

$oAgency = new Agency();
$activeAgency = $oAgency->getCurrentAgency();

$featuredLinkList = $activeAgency['links'];

if (!empty($featuredLinkList )) {
?>
    <ul class="c-basic-link-list">
    <?php foreach ($featuredLinkList as $link) {
    ?>
        <li class="highlight <?php if (isset($link['classes'])) echo $link['classes'];?>">
            <a href="<?php echo $link['url'];?>" <?php if ($link['is_external']) echo "rel='external' title='This link will take you away from the MoJ intranet'";?>><?php echo $link['label']; if ($link['is_external']) echo "<span class='u-icon u-icon--link'></span>"; ?></a>
        </li>
    <?php
    }
    ?>
    </ul>
<?php
}
?>


