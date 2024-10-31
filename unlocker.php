<?php
/*
Plugin Name: Leadbolt Wordpress Unlocker
Plugin URI: http://www.leadbolt.com
Description: Plugin for displaying Leadbolt unlocker for wordpress sites
Author: Leadbolt Pty Ltd.
Version: 1.1
Author URI: http://www.leadbolt.com
*/

global $wpdb;
	
define("DB_TABLE",$wpdb->prefix."leadbolt_options");
register_activation_hook( __FILE__, 'unlocker_install' );

function load_unlocker()
{
	// get all the unlockers and check if this page requires an unlocker
	$res=mysql_query('SELECT * FROM '.DB_TABLE.' WHERE Status=1');
	if($res !== false && mysql_num_rows($res))
	{
		$unlocker_displayed=false;
		while($row = mysql_fetch_assoc($res))
		{
			if($unlocker_displayed)
			{
				break;
			}
			$pages=explode(',',$row['Pages']);
			foreach($pages as $page)
			{
				list($type, $id) = explode('_',$page);
				$id=intval($id);
				if($page == 'page_0')
				{
					if(is_home())
					{
						echo stripslashes($row['Code']);
						$unlocker_displayed=true;
						break;
					}
				}
				elseif(($type == 'post' && is_single($id)) || ($type == 'page' && is_page($id)))
				{
					echo stripslashes($row['Code']);
					$unlocker_displayed=true;
					break;
				}
			}
		}
	}
}

function config_options_populate()
{
	include('unlocker_options.php');
}

function config_options()
{
	add_options_page('Leadbolt-Unlocker', 'Leadbolt-Unlocker', 1, 'Leadbolt-Unlocker', 'config_options_populate');
}

function unlocker_install()
{
	// on activation of the plugin drop table and create table again - cleans data from previous activation
	mysql_query('DROP TABLE '.DB_TABLE);
	mysql_query("CREATE TABLE IF NOT EXISTS ".DB_TABLE." (`Id` int(11) NOT NULL auto_increment, `Pages` text NOT NULL, `Code` text NOT NULL, `Notes` text NOT NULL, `Status` int(11) NOT NULL default '1', PRIMARY KEY  (`Id`) ) ENGINE=MyISAM") or die(mysql_error());
}

add_action('wp_print_scripts', 'load_unlocker');
add_action('admin_menu', 'config_options');
?>