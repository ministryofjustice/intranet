<?php

/** Grid demo - for dev purposes only.
 * This file is meant to be accessed directly, not via WP.
 */

function rows($lg, $md, $sm){
  ?>
  <?php for($a=0,$count=count($lg); $a<$count; $a++): ?>
    <div class="col-lg-<?=$lg[$a]?> col-md-<?=$md[$a]?> col-sm-<?=$sm[$a]?>">
      <div class="green"><?=$lg[$a]?>-<?=$md[$a]?>-<?=$sm[$a]?></div>
    </div>
  <?php endfor ?>
  <?php
}

?>
<!DOCTYPE html>

<!--[if lt IE 7 ]> <html class="ie6"> <![endif]-->
<!--[if IE 7 ]>    <html class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <html class="ie8"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html><!--<![endif]-->
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link type="text/css" rel="stylesheet" href="css/style.css" />
    <link type="text/css" rel="stylesheet" href="css/ie.css" />
    <style>
      div {
        text-align: center;
      }

      body {
        margin: 0;
        background-color: #fff;
      }

      .grid {
        padding-bottom: 10px;
      }

      .grid:first-of-type {
        padding-top: 10px;
      }

      .red { background-color: #f00; }
      .green { background-color: #0f0; }
      .blue,
      .grid .grid { background-color: #00f; }
      .grey { background-color: #aaa; }
    </style>
  </head>
  <body>
    <div class="grey">
      <div class="grid red">
        <?php rows(array(12), array(12), array(12)); ?>
      </div>
      <div class="grid red">
        <?php rows(array(6, 6), array(6, 6), array(12, 12)); ?>
      </div>
      <div class="grid red">
        <?php rows(array(4, 4, 4), array(6, 6, 6), array(12, 12, 12)); ?>
      </div>
      <div class="grid red">
        <?php rows(array(3, 3, 3, 3), array(6, 6, 6, 6), array(12, 12, 12, 12)); ?>
      </div>
      <div class="grid red">
        <?php rows(array(2, 2, 2, 2, 2, 2), array(4, 4, 4, 4, 4, 4), array(6, 6, 6, 6, 6, 6)); ?>
      </div>
      <div class="grid red">
        <?php rows(array(1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1), array(2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2), array(4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4)); ?>
      </div>
      <div class="grid red">
        <?php rows(array(6), array(6), array(12)); ?>
        <div class="col-lg-6 col-md-6 col-sm-12">
          <div class="grid">
            <?php rows(array(4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4), array(4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4), array(2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2)); ?>
          </div>
          <div class="grid">
            <?php rows(array(6, 6, 6, 6), array(6, 6, 6, 6), array(3, 3, 3, 3)); ?>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
