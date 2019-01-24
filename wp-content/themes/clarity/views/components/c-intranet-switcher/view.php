<?php
use MOJ\Intranet\Agency;

$oAgency          = new Agency();
$activeAgencies   = $oAgency->getList();
$current_intranet = get_intranet_code();

$referrer = ( isset( $_SERVER['HTTP_REFERER'] ) ? parse_url( $_SERVER['HTTP_REFERER'] ) : parse_url( get_home_url() ) );
/**
 * Remove the query parameter so it isn't sent
 * twice/several times on repeated access to this page
 */
if ( isset( $referrer['query'] ) ) {
	parse_str( $referrer['query'], $output );
	if ( isset( $output['agency'] ) ) {
		unset( $output['agency'] );
	}
	$previous_parameters = [];
	foreach ( $output as $key => $value ) {
		$previous_parameters[] = $key . '=' . $value;
	}
	$referrer['query'] = implode( '&', $previous_parameters );
	if ( trim( $referrer['query'] ) != '' ) {
		$referrer['query'] = '&' . $referrer['query'];
	}
} else {
	$referrer['query'] = '';
}

?>
<?php
/**
 * Displaying agencies by rows and adding agency
 * specific classes to those agencies listed.
 */
	?>
<div class="c-intranet-switcher">
  <ul class="c-intranet-switcher">
<?php
foreach ( $activeAgencies as $agency_id => $agency ) {
	// print_r($agency_id);
	if ( $current_intranet == $agency_id ) {
		$extra_class = ' u-active';
	} else {
		$extra_class = '';
	}
	if ( $agency_id != 'noms' ) {
		echo '<li class="c-intranet-switcher__switch c-intranet-switcher__switch--' . $agency_id . $extra_class . ' "><a href="/?agency=' . $agency_id . $referrer['query'] . '">' . $agency['label'] . '</a></li>';
	} else {
		echo '<li class="c-intranet-switcher__switch c-intranet-switcher__switch--' . $agency_id . $extra_class . ' "><a href="https://intranet.noms.gsi.gov.uk/">' . $agency['label'] . '</a></li>';
	}
}
	?>
	<li class="c-intranet-switcher__switch c-intranet-switcher__switch--ospt">
	  <a href="http://officialsolicitorandpublictrustee.intranet.service.justice.gov.uk/">Official Solicitor and Public Trustee</a>
	</li>
	<li class="c-intranet-switcher__switch c-intranet-switcher__switch--yjbrh">
		<a href="https://yjresourcehub.uk/">Youth Justice Board Resource Hub</a>
	</li>
  </ul>
</div>
