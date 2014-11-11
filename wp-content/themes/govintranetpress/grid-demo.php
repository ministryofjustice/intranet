<?php
/* Template name: Home page */

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
        <?php rows([12], [12], [12]); ?>
      </div>
      <div class="grid red">
        <?php rows([6, 6], [6, 6], [12, 12]); ?>
      </div>
      <div class="grid red">
        <?php rows([4, 4, 4], [6, 6, 6], [12, 12, 12]); ?>
      </div>
      <div class="grid red">
        <?php rows([3, 3, 3, 3], [6, 6, 6, 6], [12, 12, 12, 12]); ?>
      </div>
      <div class="grid red">
        <?php rows([2, 2, 2, 2, 2, 2], [4, 4, 4, 4, 4, 4], [6, 6, 6, 6, 6, 6]); ?>
      </div>
      <div class="grid red">
        <?php rows([1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1], [2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2], [4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4]); ?>
      </div>
      <div class="grid red">
        <?php rows([6], [6], [12]); ?>
        <div class="col-lg-6 col-md-6 col-sm-6">
          <div class="green">
            <div class="grid-nest red">
              <?php rows([1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1], [2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2], [4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4]); ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
