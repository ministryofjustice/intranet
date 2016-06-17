<?php
    if ($_SERVER['HTTP_HOST'] == 'moj-intranet.staging.dxw.net') {
        $gt_code = 'GTM-W5VKJK';
    }
    else {
        $gt_code = 'GTM-P545JM';
    }
?>
<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=<?=$gt_code?>"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        '//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','<?=$gt_code?>');</script>
<!-- End Google Tag Manager -->
