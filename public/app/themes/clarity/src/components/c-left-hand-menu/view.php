<?php

namespace MOJ\Intranet;


require_once 'controller.php';

$list = (new CLeftHandMenu($post->ID))->getListWithCache($post->Id);

if (!$list) {
    return;
}

?>

<!-- c-left-hand-menu starts here -->
<nav class="c-left-hand-menu js-left-hand-menu">

    <div class="c-left-hand-menu__step_back">
        <?= get_the_title($post->ID) ?>
    </div>
    <ul><?= $list ?></ul>

</nav>
<!-- c-left-hand-menu ends here -->
