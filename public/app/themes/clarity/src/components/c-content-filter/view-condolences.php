<?php

$workplaces = get_terms([
    'taxonomy' => 'workplace',
]);

?>

<!-- c-content-filter starts here -->
<section class="c-content-filter">
    <p>Filter condolences</p>
    <form action="" id="filter_condolences" action="post" data-page="0">
        <div class="c-input-container c-input-container--select">
            <label for="ff_workplace_filter">Agency</label>
            <select name="ff_workplace_filter" id="ff_workplace_filter">
                <option value=""><?= esc_attr(__('All')) ?></option>
                <?php foreach ($workplaces as $workplace) { ?>
                    <option value="<?= $workplace->term_id ?>"><?= $workplace->name ?></option>
                <?php } ?>

            </select>
        </div>
        <input type="submit" value="Filter" id="ff_button_submit"/>
    </form>
</section>
<!-- c-content-filter ends here -->
