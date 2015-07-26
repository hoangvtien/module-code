<?php

/**
 * @Project NUKEVIET 4.x
 * @Author Hồ Ngọc Triển (hongoctrien@2mit.org)
 * @Copyright (C) 2015 Hồ Ngọc Triển. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Tue, 14 Jul 2015 04:03:52 GMT
 */

if ( ! defined( 'NV_IS_MOD_CODE' ) ) die( 'Stop!!!' );

$page_title = $module_info['custom_title'];
$key_words = $module_info['keywords'];
$contents = '';
$_id = $nv_Request->get_int( 'id', 'get, post', $id );

$_sql = 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . ' WHERE status=1 AND id=' . $_id;
$array_data = $db->query( $_sql )->fetch();

if( empty( $array_data ) )
{
	Header( 'Location: ' . nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name, true ) );
	die();
}

$time_set = $nv_Request->get_int( $module_data . '_' . $op . '_' . $_id, 'session' );
if( empty( $time_set ) )
{
	$nv_Request->set_Session( $module_data . '_' . $op . '_' . $_id, NV_CURRENTTIME );
	$query = 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . ' SET viewcount=viewcount+1 WHERE id=' . $_id;
	$db->query( $query );
}

$array_data['code_html'] = nv_unhtmlspecialchars( $array_data['code_html'] );
$array_data['code_css'] = nv_unhtmlspecialchars( $array_data['code_css'] );
$array_data['code_js'] = nv_unhtmlspecialchars( $array_data['code_js'] );
$array_data['code_php'] = nv_unhtmlspecialchars( $array_data['code_php'] );
$array_data['code_php_template'] = nv_unhtmlspecialchars( $array_data['code_php_template'] );

// Nguoi dang
$sql = 'SELECT username, last_name, first_name FROM ' . NV_USERS_GLOBALTABLE . ' WHERE userid=' . $array_data['adduser'];
list( $username, $last_name, $first_name ) = $db->query( $sql )->fetch( 3 );
$array_data['adduser'] = nv_show_name_user( $first_name, $last_name, $username );

if( ! defined( 'FACEBOOK_JSSDK' ) )
{
	$lang = ( NV_LANG_DATA == 'vi' ) ? 'vi_VN' : 'en_US';
	$facebookappid = '835372636499958';

	$contents .= "<div id=\"fb-root\"></div>
	<script type=\"text/javascript\" data-show=\"after\">
	 (function(d, s, id) {
	 var js, fjs = d.getElementsByTagName(s)[0];
	 if (d.getElementById(id)) return;
	 js = d.createElement(s); js.id = id;
	 js.src = \"//connect.facebook.net/" . $lang . "/all.js#xfbml=1&appId=" . $facebookappid . "\";
	 fjs.parentNode.insertBefore(js, fjs);
	 }(document, 'script', 'facebook-jssdk'));
	</script>";
	define( 'FACEBOOK_JSSDK', true );
}

if( ! defined( 'GOOGLE_PLUS' ) )
{
	$contents .= "<script type=\"text/javascript\" data-show=\"after\">
	window.___gcfg = {lang: nv_sitelang};
	(function() {
	var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
	po.src = 'https://apis.google.com/js/plusone.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
	})();
	</script>";
	define( 'GOOGLE_PLUS', true );
}

$contents .= '<h1>' . $array_data['title'] . '</h1>';
$contents .= '<span><strong>' . $lang_module['cat'] . '</strong>: <a href="#" title="' . $array_cat[$array_data['catid']]['title'] . '">' . $array_cat[$array_data['catid']]['title'] . '</a>&nbsp;&nbsp;&nbsp;<span><strong>' . $lang_module['poster'] . '</strong>: ' . $array_data['adduser'] . '</span>';
$contents .= '<div style="margin-top: 10px"><div style="float: left; margin-right: 30px" class="fb-like" data-href="' . $client_info['selfurl'] . '" data-layout="button_count" data-action="like" data-show-faces="true" data-share="true"></div><div class="g-plusone" data-size="medium"></div></div>';
$contents .= '<hr />';
if( $array_data['viewdemo'] )
{
	$contents .= nv_build_demo( $array_data );
}
$contents .= nv_theme_code_detail( $array_data );

if( !empty( $array_data['image'] ) )
{
	$meta_property['og:image'] = NV_MY_DOMAIN . NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $array_data['image'];
}

$page_title = $array_data['title'];
$description = $array_data['description'];

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';