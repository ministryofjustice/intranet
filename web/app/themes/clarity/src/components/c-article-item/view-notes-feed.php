<?php
/**
 *  Individual notes feed list item
 *  http://intranet.docker/notes-from-antonia/
 *
 * @package Clarity
 */

// This component sometimes requires `$set_cpt` depending on where this component gets called.
if (!isset($set_cpt)) {
    $set_cpt = '';
}
?>

<article id="note-<?= $post->ID ?>" class="c-article-item__note-from-antonia c-article-item"
         data-type="<?= $set_cpt ?>">
</article>
