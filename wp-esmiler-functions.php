<?php
$wp_esmiler_path = get_option('siteurl') . '/wp-content/plugins/wp-esmiler/emoticons/';
$opt 	= get_option('wp_esmiler_settings');

function is_smiley_file( &$file ) {
	if( in_array(substr($file, strrpos($file, '.')+1), array('jpg', 'gif', 'png', 'bmp', 'jpeg')) )
		return true;
	return false;
}

function auto_parse_smileys( &$smileys_set, $dir )
{
	global $opt, $wp_esmiler_path;
	$current_set_path = ABSPATH . '/wp-content/plugins/wp-esmiler/emoticons/' . $dir . '/';
	//echo $current_set_path;
	if( is_dir($current_set_path) && $handle = opendir($current_set_path) )
	{
		//$files = array();
		while( false !== ($file = readdir($handle)) )
	    {
	    	if( $file=="." || $file==".." || is_dir($current_set_path . $file) || !is_smiley_file($file) )
	    		continue;
	    	$name = substr($file, 0, strpos($file, '.'));
	    	//echo $name . '<br>';
	    	$smileys_set['{' . $name . '}'] = '<img src="'. $wp_esmiler_path . $dir . '/' . $file . '" class="wp-esmiler-icon" alt="{' . $name . '}" title="{' . $name . '}" />';
	    }
	    //print_r($smileys_set);
	    
	    /*foreach( $files as $value )
	    {
	    	if( in_array($value.'.gif', $files) )
	    		$matchs++;
	    }*/
	    closedir($handle);
	    //return $files;
	}
	//return array();
}

function list_smiley_sets_as_checkbox(&$sets_array, $new_set = false, $return = false) {
	$ret = '';
	foreach( $sets_array as $set ) {
		$name = 'sets_in_use[' . $set . ']';
		$checked_flag = '';
		if( $new_set ) {
			$name = 'sets_in_new[' . $set . ']';
		//	$checked_flag = ' checked="checked"';
		}
		$ret .= '<label for="' . $set . '"><input type="checkbox" id="' . $set . '" name="' . $name . '"' . $checked_flag . ' /> ' . $set . '</label> ';
	}
	if( $return )
		return $ret;
	echo $ret;
}

function get_new_smiley_sets()
{
	global $opt;
	$wp_esmiler_dir = ABSPATH . '/wp-content/plugins/wp-esmiler/emoticons/';
	$new_sets = '';
	if( is_dir($wp_esmiler_dir) && $handle = opendir($wp_esmiler_dir) )
	{
		$new_sets = array();
		while( false !== ($file = readdir($handle)) )
	    {
	    	if( $file=="." || $file==".." || in_array($file, $opt['smiley_sets']) )
	    		continue;
	    	//echo $file . '<br />';
	    	$new_sets[] .= $file;
	    }
	    closedir($handle);
	    $new_sets = list_smiley_sets_as_checkbox($new_sets, true, true);
	}
	return $new_sets;
}

$smileys_set = array();
if( isset($opt['smiley_sets']) && count($opt['smiley_sets']) ) {
	foreach( $opt['smiley_sets'] as $set ) {
		auto_parse_smileys( $smileys_set, $set );
	}
}
//auto_parse_smileys( $smileys_set, '36' );
//auto_parse_smileys( $smileys_set, '' );
//print_r($smileys_set);

$smileys_set2 = $smileys_set;
if( isset($opt['icons_stat']) ){
	foreach($opt['icons_stat'] as $k=>$v){
		$smileys_set[$k]="";
	}
}
?>