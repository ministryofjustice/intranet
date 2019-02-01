<?php
use MOJ\Intranet\Newscategory;

$tibits = 749; // category ID

$oNews = new NewsCategory();
echo $oNews->get_newscategory_list( $tibits );
