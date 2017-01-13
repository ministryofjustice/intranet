<?php
  header('Content-type: text/css; charset:UTF-8');
  $campaign_theme = "red";
?>

.template-campaign-content {
    .template-container {
     .editable {

        h2, h3 {
          margin-top: 25px;
          margin-bottom: 10px;
          color: red;
        }

        p {
          margin-top: 0;
        }

        hr {
          display: inline-block;
          width: 100%;
          margin: 10px 0 0 0;
          background: #ccc;
          border: 1px solid <?=$campaign_theme?>;
        }

        blockquote {
          margin-right: 0;
          padding: 25px 25px 5px 25px;
          background: #ccc;
          font-size: 27px;
          line-height: 30px;

          p {
            padding-left: 0;
            font-size: 27px;
            line-height: 30px;
          }

          p::before {
            content: '';
          }
        }
      }
    }
   .main-content .editable .example {
     border-left: 10px solid <?=$campaign_theme?>;
   }
  }
