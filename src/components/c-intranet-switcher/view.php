<?php
use MOJ\Intranet\Agency;
$oAgency = new Agency();
$activeAgencies = $oAgency->getList();
$current_intranet = get_intranet_code();

$referrer = (isset($_SERVER['HTTP_REFERER']) ? parse_url($_SERVER['HTTP_REFERER']) : parse_url(get_home_url()));


if (isset($referrer['query']))
{
    parse_str($referrer['query'], $output);
    if (isset($output['dw_agency'])) unset($output['dw_agency']);
    $previous_parameters = array();
    foreach ($output as $key=>$value )
    {
        $previous_parameters[] = $key.'='.$value;
    }
    $referrer['query'] = implode('&', $previous_parameters);
    if (trim($referrer['query']) != '') $referrer['query'] = '&'.$referrer['query'];
} else
{
    $referrer['query'] = '';
}


?>

<div class="c-intranet-switcher">
  <ul class="c-intranet-switcher">
<?php foreach ($activeAgencies as $agency_id => $agency) {

    if ($current_intranet == $agency_id) $extra_class = ' u-active';
    else  $extra_class = '';


    echo '<li class="c-intranet-switcher__switch c-intranet-switcher__switch--'.$agency_id.$extra_class.' "><a href="/?dw_agency='.$agency_id.$referrer['query'].'">'.$agency['label'].'</a></li>';
}
  ?>
  </ul>
</div>
