<?php
use MOJ\Intranet\Newscategory;

$enews = 748; // category ID

$oNews = new NewsCategory();
echo $oNews->get_newscategory_list($enews);
