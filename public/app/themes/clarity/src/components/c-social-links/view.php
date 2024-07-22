<?php
use MOJ\Intranet\Agency;

$socialLinks = Agency::getSocialLinks(get_intranet_code());

if (! empty($socialLinks)) {
    ?>
    <section class="c-social-links">
        <h1 class="o-title o-title--section">Follow us</h1>
        <ul>
            <?php foreach ($socialLinks as $link) { ?>
            <li>
                <a href="<?= $link['url'] ?>">
                    <span class="u-icon u-icon--<?= $link['name'] ?>"></span><?= $link['label'] ?>
                </a>
            </li>
            <?php } ?>
        </ul>
    </section>
    <?php
}
?>
