<div id="cil-root-stream-<?=$coveritlive_id?>" class="cil-root">
  <span class="cil-config-data" title='{"altcastCode":"<?=$coveritlive_id?>",
    "server":"www.coveritlive.com",
    "geometry":{"width":"fit",
    "height":"600"},"configuration":{"pinsGrowSize":"on",
    "newEntryLocation":"top",
    "commentLocation":"top",
    "replayContentOrder":"chronological",
    "titlePage":"on",
    "skinOverride":"",
    "embedType":"stream",
    "titleImage":"\/templates\/coveritlive\/images\/buildPage\/BusinessImage.jpg"}}'>
    &nbsp;
  </span>
</div>
<script type="text/javascript">
  window.cilAsyncInit = function() {
    cilEmbedManager.init()
  };
  (function() {
    if (window.cilVwRand === undefined) {
      window.cilVwRand = Math.floor(Math.random()*10000000);
    }
    var e = document.createElement('script');
    e.async = true;
    var domain = (document.location.protocol == 'http:' || document.location.protocol == 'file:') ? 'http://cdnsl.coveritlive.com' : 'https://cdnslssl.coveritlive.com';
    e.src = domain + '/vw.js?v=' + window.cilVwRand;e.id = 'cilScript-<?=$coveritlive_id?>';
    document.getElementById('cil-root-stream-<?=$coveritlive_id?>').appendChild(e);
  }());
</script>
