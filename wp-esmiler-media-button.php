<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  dir="ltr" lang="en-US">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<style type="text/css">
	/*
		JavaScripts for WP eSmiler
		Written by Phong Thai
		jsB@nk.com @ www.JavaScriptBank.com - all your JavaScript problems should be solved
	*/
	body {
	    text-align: center;
	    font-family: "Lucida Grande",Verdana,Arial,"Bitstream Vera";
	}
	#wp-esmiler-icons-toggle { cursor: pointer; font-size: 14px; }
	.wp-esmiler-icon { cursor: pointer; border: 0px; background: none; margin: 3px; position: relative; }
	.wp-esmiler-icon:hover { top: -7px; }
</style>
</head>
<body>
<?php
$httphost = @$_SERVER ['HTTP_HOST'];
$httpreferer = @$_SERVER ['HTTP_REFERER'];
$nonce = $_POST ['nonce'];
require_once '../../../wp-config.php';
require_once("wp-esmiler-functions.php");

foreach($smileys_set as $k=>$v){
	echo '<a title="' . $k. '" href="javascript:window.parent.send_to_editor(\' ' . $k . ' \');">' . $v . '</a>';
}
?>
</body>
</html>