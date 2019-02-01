<?php
use MOJ\Intranet\Newscategory;

$oneupdate = 1257; // category ID

$oNews = new NewsCategory();
echo $oNews->get_newscategory_list( $oneupdate );
