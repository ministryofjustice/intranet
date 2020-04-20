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
            <label for="ff_workplace_filter">Agency:</label>
            <select name="ff_workplace_filter" id="ff_workplace_filter">
                <option value=""><?php echo esc_attr(__('All')); ?></option>
                <?php foreach ($workplaces as $workplace){ ?>
                    <option value="<?php echo $workplace->term_id; ?>"><?php echo $workplace->name; ?></option>
                <?php } ?>

            </select>
        </div>
        <input type="submit" value="Filter" id="ff_button_submit"/>
    </form>
</section>
<!-- c-content-filter ends here -->
