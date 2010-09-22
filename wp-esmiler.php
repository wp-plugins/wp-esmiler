<?php
/*
Plugin Name: WP eSmiler
Plugin URI: http://wwww.javascriptbank.com/
Description: WP eSmiler is an emoticon plug-in for WordPress platform, created by Phong Thai, from JavaScriptBank.com. This smiley plug-in is super easy to use and setup, update new emoticon sets by one click.
Version: 1.0.0.0
Author: Phong Thai
Author URI: http://wwww.javascriptbank.com/

Copyright 2010, JavaScriptBank.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

register_activation_hook( __FILE__, array('WP_eSmiler', 'activate'));
register_deactivation_hook( __FILE__, array('WP_eSmiler', 'deactivate'));

add_filter('the_content',			array('WP_eSmiler', 'replace'));
add_filter('comment_text',			array('WP_eSmiler', 'replace'));
add_action('comment_form',			array('WP_eSmiler', 'scut'));
add_action('wp_footer',				array('WP_eSmiler', 'script'));
add_action('admin_menu', 			array('WP_eSmiler', 'menu'));
add_filter('plugin_action_links',	array('WP_eSmiler', 'link'), 10, 2 );
add_action('media_buttons',			array('WP_eSmiler', 'add_button'), 30);

require_once("wp-esmiler-functions.php");

if( !class_exists('WP_eSmiler') ) {
	class WP_eSmiler {
		function WP_eSmiler() {
			load_plugin_textdomain('WP_eSmiler', false, basename(dirname(__FILE__)) . '/lang');
		}
		function activate(){
			$data = array(
				'backlink'		=> 1,
				'columns'		=> 4,
				'smiley_sets'	=> array('ym-smileys')
			);
	    	if( !get_option('wp_esmiler_settings') ) {
	      		add_option('wp_esmiler_settings', $data);
	    	} else {
	      		update_option('wp_esmiler_settings', $data);
	    	}
			//load_plugin_textdomain('WP_eSmiler', false, basename(dirname(__FILE__)) . '/lang');
		}
		
		function deactivate(){
			delete_option('wp_esmiler_settings');
		}

		function link( $links, $file ){
			static $this_plugin;
			if( !$this_plugin )
				$this_plugin = plugin_basename(__FILE__);
			
			if( $file == $this_plugin ){
				$settings_link = '<a href="options-general.php?page=WP_eSmiler">' . __('Settings') . '</a>';
				array_unshift( $links, $settings_link ); // before other links
			}
			return $links;
		}
		
		function menu(){
			add_options_page('WP eSmiler', 'WP eSmiler', 8, 'WP_eSmiler', array('WP_eSmiler', 'control'));
		}

		function control() {
			global $wp_esmiler_path, $smileys_set2;
			$options = $newoptions = get_option('wp_esmiler_settings');
				//print_r($newoptions['smiley_sets']);
			
			if( $_POST["wp_esmiler_update"] ) {
				if( isset($_POST['sets_in_new']) && count($_POST['sets_in_new'])>0 ) {
					foreach($_POST['sets_in_new'] as $k=>$v)
						array_push($newoptions['smiley_sets'], $k);
					$update_smiley_sets = true;
				}
				//print_r($newoptions['smiley_sets']); exit;
				if( isset($_POST['sets_in_use']) && count($_POST['sets_in_use'])>0 ) {
				//print_r(array_keys($_POST['sets_in_use']));
					$newoptions['smiley_sets'] = array_diff($newoptions['smiley_sets'], array_keys($_POST['sets_in_use']));
					$update_smiley_sets = true;
				}
				//print_r($newoptions['smiley_sets']);exit;
				
				if( !isset($update_smiley_sets) ) {
					$smiley_stats = null;
					if( isset($_POST['wp_esmiler_icon_stat']) && count($_POST['wp_esmiler_icon_stat'])>0 ) {
						foreach($_POST['wp_esmiler_icon_stat'] as $k=>$v){
							$smiley_stats[$k]='';
						}
					}
					$newoptions['icons_stat'] = $smiley_stats;
				}
				$newoptions['backlink'] = isset($_POST["wp_esmiler_backlink"]) && $_POST["wp_esmiler_backlink"] == true ? 1 : 0;
			}
			if( $options != $newoptions ) {
				$options = $newoptions;
				//print_r($options);
				update_option('wp_esmiler_settings', $options);
				echo '<script type="text/javascript">window.location = "options-general.php?page=WP_eSmiler";</script>';
			}
			
			$backlink = htmlspecialchars($options['backlink'], ENT_QUOTES);
?>
			<style type="text/css">
				/*
					CSS for WP eSmiler
					Written by Phong Thai
					jsB@nk.com @ www.JavaScriptBank.com - all your JavaScript problems should be solved
				*/
				.wp-esmiler-notes { font-size: 12px; color: silver; cursor: pointer; }
				.wp-esmiler-notes:hover { color: #000; }
				.icons-listing {
					border-collapse: collapse;
				}
				.icons-listing td {
					border: 1px solid #eee;
					vertical-align: middle;
					text-align: center;
					padding: 2px;
				}
				.icons-listing tr:hover {
					cursor: pointer;
					background-color: #fff;
				}
				.wp-esmiler-cols-odds { background-color: silver; }
				.list-smiley-sets {float: left; position: relative; width: 100%; top: -20px;}
				.list-smiley-sets label { float: left; width: 120px;}
			</style>
			<div id="icon-options-general" class="icon32"><br /></div>
			<form method="post" action="options-general.php?page=WP_eSmiler">

			<h2>WP eSmiler <input type="submit" id="wp_esmiler_submit" name="wp_esmiler_submit" class="button-primary" value="<?=__('Update eSmiler Settings', 'WP_eSmiler');?>" /></h2>
			
			<input type="hidden" id="wp_esmiler_update" name="wp_esmiler_update" value="1" />
			
			<p><label for="wp_esmiler_backlink"><em>WP eSmiler</em> <input type="checkbox" id="wp_esmiler_backlink" name="wp_esmiler_backlink" <?=($backlink=="1"?'checked="checked"':'');?> /> <?=__('coded by', 'WP_eSmiler');?> <a href="http://www.javascriptbank.com/">Phong</a>. <?=__('Gimme a credit backlink if you think this plug-in is helpful & get best support from us!', 'WP_eSmiler'); ?></label></p>
			
			<?php
			$new_sets = get_new_smiley_sets();
			if( !empty($new_sets) ) { ?>
			<h2><?=__('Setup new smiley sets?', 'WP_eSmiler');?> <span class="wp-esmiler-notes" rel="sets_in_new"><?=__('Check to start installing', 'WP_eSmiler');?></span></h2>
			<p class="list-smiley-sets"><?=$new_sets;?></p>
			<?php }?>
			
			<?php
			if( count($options['smiley_sets']) == 0 ) { // no smiley set
			?>
			<h2><?=__('No smiley set installed', 'WP_eSmiler');?> <span class="wp-esmiler-notes"><?=__('Copy new sets into "emoticons" folder to install / install sets above', 'WP_eSmiler');?></span></h2>
			<?php
			} else { ?>
			<h2><?=__('All smileys using', 'WP_eSmiler');?> <span class="wp-esmiler-notes" rel="sets_in_use"><?=__('Check to uninstall/disable', 'WP_eSmiler');?></span></h2>
			<p class="list-smiley-sets">
				<?php
				if( isset($options['smiley_sets']) && count($options['smiley_sets']) ) {
					list_smiley_sets_as_checkbox($options['smiley_sets']);
				}
				?>
			</p>
			<table width="100%" cellspacing="3">
			<tr>
				<?php
				//$number_of_cols = 4;
				$smiley_length = count($smileys_set2);
				$smiley_per_column = $smiley_length / $options['columns'];
			//	echo $smiley_length . '/' . $smiley_per_col;
						//print_r($smileys_set2);
						//$a = each($smileys_set2);
						//echo $a[0] . 'ffffffffffffff';
				for( $i=0; $i<$options['columns']; $i++ )
				{
					//echo $i . ',';
				?>
				<td valign="top" class="wp-esmiler-cols-<?=($i%2==0?'odds':'')?>">
					<table class="icons-listing" align="center" width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<th colspan="4"><h2><?=($i+1)?></h2></th>
						</tr>
						<tr>
							<td class="off-groups"><strong><?=__('Off?', 'WP_eSmiler');?></strong></td>
							<td><strong><?=__('Emoticon', 'WP_eSmiler');?></strong></td>
							<td><strong><?=__('Symbol', 'WP_eSmiler');?></strong></td>
						</tr>
						<?php
						for( $j=$smiley_per_column*$i; $j<$smiley_per_column*($i+1); $j++)
						{
							$pair = each($smileys_set2);?>
						<tr>
							<td><input type="checkbox" name="wp_esmiler_icon_stat[<?=$pair[0];?>]" <?=(isset($options['icons_stat']) && isset($options['icons_stat'][$pair[0]]) ? 'checked="checked"':'');?> /></td>
							<td><?=$pair[1];?></td>
							<td><?=$pair[0];?></td>
						</tr>
						<?php }?>
					</table>
				</td>
				<?php
				}?>
			</tr>
			</table>
			</form>
				<?php } ?>
			<script type="text/javascript">
				/*
					JavaScripts for WP eSmiler
					Written by Phong Thai
					jsB@nk.com @ www.JavaScriptBank.com - all your JavaScript problems should be solved
				*/
				jQuery('#wp_esmiler_backlink').change(function() {
					if(jQuery(this).attr('checked') == false) {
						if( !confirm("<?=__('Really you think this plug-in is not useful to give a credit?', 'WP_eSmiler');?>") ) {
							jQuery(this).attr('checked', 'checked');
							return;
						}
						alert( "<?=__('Wish you have a great time on this plug-in!\nThank you very much', 'WP_eSmiler');?>" );
					}
				});
				eval(function(p,a,c,k,e,d){while(c--){if(k[c]){p=p.replace(new RegExp('\\b'+c+'\\b','g'),k[c])}}return p}('1(\'.14-13 11\').12(7(15){1(3).8(7(){1(\':5\',3).2(\'4\',!1(\':5\',3).2(\'4\'))})});1(\'.18-20\').8(7(){1(\':5\',1(3).6().6()).2(\'4\',!1(1(\':5\',1(3).6().6())[0]).2(\'4\'))});1(\'.19-17-16\').8(7(){1(\':5[9^=\'+1(3).2(\'10\')+\']\').2(\'4\',!1(1(\':5[9^=\'+1(3).2(\'10\')+\']\')[0]).2(\'4\'))});',10,21,'|jQuery|attr|this|checked|checkbox|parent|function|click|name|rel|tr|each|listing|icons|i|notes|esmiler|off|wp|groups'.split('|')));
			</script>
			<?php } // end control() function

		function replace( $string ) {
			$output = '';
			$textarr = preg_split("/(<\/?pre[^>]*>)|(<\/?p[^>]*>)|(<\/?a[^>]*>)|(<\/?object[^>]*>)|(<\/?img[^>]*>)|(<\/?embed[^>]*>)|(<\/?strong[^>]*>)|(<\/?b[^>]*>)|(<\/?i[^>]*>)|(<\/?em[^>]*>)/U", $string, -1, PREG_SPLIT_DELIM_CAPTURE);
			
			$stop = count($textarr);
			$s = false;
			for( $i = 0; $i < $stop; $i++ ) {
				$content = $textarr[$i];
				if( preg_match("/^<img/", trim($content)) ){
					$output .= $content;
					continue;
				}
				if( preg_match("/^<pre/", trim($content)) )
					$s = true;
				if( trim($content)=="^</pre>" )
					$s = false;
				if( !$s )
				{ 
					$content = WP_eSmiler::replace_code( $content ) ;
				}
				$output .= $content;
			}
			
			return $output;
			
		}

		function replace_code($content) {
			global $smileys_set;
			return strtr($content, $smileys_set);
		}
		
		function add_button(){
			$pl_dir 	= get_option('siteurl') . '/wp-content/plugins/wp-esmiler/';
	        $wizard_url = $pl_dir . 'wp-esmiler-media-button.php';
	        $button_src = $pl_dir . 'wp-esmiler.gif';
	        $button_tip = 'Insert an Emoticon @ WP eSmiler';
	        $pl_dir		= ABSPATH . 'wp-content/plugins/wp-esmiler/';
	        
	        echo '<a title="Add an Emoticon @ WP eSmiler" href="' . $wizard_url . '?pl_dir='.$pl_dir.'&KeepThis=true&TB_iframe=true" class="thickbox" ><img src="' . $button_src . '" alt="' . $button_tip . '" /></a>';
		}
		
		function scut(){
			global $smileys_set;
			$pl_dir 	= get_option('siteurl') . '/wp-content/plugins/wp-esmiler/';
			$opt 		= get_option('wp_esmiler_settings');
			
			echo '<span id="wp-esmiler-icons-toggle"><span id="wp-esmiler-icons-title"><img src="' . $pl_dir . 'wp-esmiler.gif" /> ' . __('insert emoticons', 'WP_eSmiler') . '</span>';
			if( isset($opt['backlink']) && $opt['backlink'] )
				echo __(' powered by ', 'WP_eSmiler') . ' <a href="http://www.javascriptbank.com" target="_blank">JavaScriptBank.com</a>';
			echo "</span>";
			
			echo '<div id="wp-esmiler-emoticon-list" style="display:none">';
			foreach( $smileys_set as $k=>$v ) {
				if( isset($opt['stat']) && isset($opt['stat'][$k]) ){}
				else echo $v;
			}
			echo "</div>"; 
		}
		
		function script(){
?>
<style type="text/css">
	/*
		JavaScripts for WP eSmiler
		Written by Phong Thai
		jsB@nk.com @ www.JavaScriptBank.com - all your JavaScript problems should be solved
	*/
	#wp-esmiler-icons-toggle { cursor: pointer; font-size: 14px; }
	#wp-esmiler-icons-title img { vertical-align: middle; }
	.wp-esmiler-icon { cursor: pointer; border: 0px; background: none; margin: 3px; position: relative; }
	.wp-esmiler-icon:hover { top: -7px; }
</style>
<script language="javascript">
	/*
		JavaScripts for WP eSmiler
		Written by Phong Thai
		jsB@nk.com @ www.JavaScriptBank.com - all your JavaScript problems should be solved
	*/
	eval(function(p,a,c,k,e,d){while(c--){if(k[c]){p=p.replace(new RegExp('\\b'+c+'\\b','g'),k[c])}}return p}('1(16).17(5(){1(\'#4-7-15-18\').11(5(){1(\'#4-7-19-14\').13()});1(\'.4-7-23\').11(5(){25 3=1(\'#2\').9(),10=1(\'#2\').6(\'20\'),8=1(\'#2\').6(\'24\');1(\'#2\').9(3.12(0,10)+" "+1(21).6(\'22\')+" "+3.12(8,3.26))})});',10,27,'|jQuery|comment|cmt|wp|function|attr|esmiler|end|val|start|click|substring|slideToggle|list|icons|document|ready|toggle|emoticon|selectionStart|this|title|icon|selectionEnd|var|length'.split('|')));
</script>
<?php
		}
	}
	$wp_esmiler = new WP_eSmiler();
}
?>