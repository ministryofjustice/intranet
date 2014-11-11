<?php

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
<html>
  <head>
    <link type="text/css" rel="stylesheet" href="css/style.css" />
    <style>
      div {
        text-align: center;
      }

      body {
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
      .blue { background-color: #00f; }
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
        <div class="col-lg-6 col-md-6 col-sm-6">
          <div class="green">
            <div class="grid-nest red">
              <?php rows(array(1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1), array(2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2), array(4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4)); ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
