<?php
/*
Plugin Name: Memory Load db Usage
Plugin URI: http://slangji.wordpress.com/wp-memory-db-usage/
Description: Indicate Memory Load Consumption and db size Usage on WordPress Backend. Show Memory Load and db size on Footer, and Max Memory Load on Admin Bar or ToolBar. Work under GPLv2 License. | <a href="http://slangji.wordpress.com/donate/" title="Free Donation">Donate</a> | <a href="http://slangji.wordpress.com/contact/" title="Send Me Bug and Suggestions">Contact</a> | <a href="http://wordpress.org/extend/plugins/wp-overview-lite/" title="Show Dashboard Overview and Footer Memory Load Usage">WP Overview?</a> | <a href="http://wordpress.org/extend/plugins/wp-missed-schedule/" title="Fix Missed Scheduled Future Posts Cron Job">WP Missed Schedule?</a> | <a href="http://wordpress.org/extend/plugins/wp-admin-bar-removal/" title="Remove Admin Bar Frontend Backend User Profile and Code">Admin Bar Removal?</a> | <a href="http://wordpress.org/extend/plugins/wp-admin-bar-node-removal/" title="Remove Admin Bar Frontend and Backend Node">Admin Bar Node Removal?</a> | <a href="http://wordpress.org/extend/plugins/wp-toolbar-removal/" title="Remove ToolBar Frontend Backend User Profile and Code">ToolBar Removal?</a> | <a href="http://wordpress.org/extend/plugins/wp-toolbar-node-removal/" title="Remove ToolBar Frontend and Backend Node">ToolBar Node Removal?</a> | <a href="http://wordpress.org/extend/plugins/wp-login-deindexing/" title="Total DeIndexing WordPress LogIn from all Search Engines">LogIn DeIndexing?</a> | <a href="http://wordpress.org/extend/plugins/wp-total-deindexing/" title="Total DeIndexing WordPress from all Search Engines">WP DeIndexing?</a> | <a href="http://wordpress.org/extend/plugins/wp-ie-enhancer-and-modernizer/" title="Enhancer and Modernizer IE Surfing Expirience">Enhancer IE Surfing?</a>
Version: 2012.1125.0000
Author: sLa
Author URI: http://slangji.wordpress.com/
Requires at least: 3.1
Tested up to: 3.5
License: GPLv2 or later
 *
 * Development Release: Version 2013 Build 0000-BUGFIX Revision 0000-DEVELOPMENT - DEV
 *
 * [Memory Load Consuption and db size Usage](http://wordpress.org/extend/plugins/wp-memory-db-indicator/) WordPress PlugIn
 *
 *  This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License
 *  as published by the Free Software Foundation; either version 2
 *  of the License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 *  The license for this software can be found @ http://www.gnu.org/licenses/gpl-2.0.html
 *
 * This uses code derived from
 * wp-overview-lite.php by sLa <slangji[at]gmail[dot]com>
 * according to the terms of the GNU General Public License version 2 (or later)
 *
 * Copyright © 2009-2012 [sLa](http://wordpress.org/extend/plugins/profile/slangji) (slangji[at]gmail[dot]com)
 */
/**
 * @package Memory Load db Usage
 * @subpackage WordPress PlugIn
 * @since 3.1.0
 * @version 2012.1125.0000
 * @author sLa
 * @license GPLv2 or later
 *
 * Display the PHP Memory Load Consuption and db Usage on Dashboard Footer, and Backend Admin Bar or ToolBar.
 */
if(!function_exists('add_action')){header('HTTP/1.0 403 Forbidden');header('HTTP/1.1 403 Forbidden');exit();}function wpmldbu_footer_log(){echo"\n<!--Plugin Memory Load db Usage 2012.1125.0000 Active-->";}add_action('wp_head','wpmldbu_footer_log');add_action('wp_footer','wpmldbu_footer_log');if(is_blog_admin()||is_network_admin()){class wp_memory_load_db_usage{var$memory=false;function wpo(){return$this->__construct();}function __construct(){add_action('init',array(&$this,'wpmldbu_limit'));add_action('wp_dashboard_setup',array(&$this,'wpmldbu_dashboard'));add_action('wp_network_dashboard_setup',array(&$this,'wpmldbu_network_dashboard'));add_filter('admin_footer_text',array(&$this,'wpmldbu_footer'));$this->memory=array();}function wpmldbu_limit(){$this->memory['wpmldbu-limit']=(int)ini_get('memory_limit');}function wpmldbu_load(){$this->memory['wpmldbu-load']=function_exists('memory_get_usage')?round(memory_get_usage()/1024/1024,2):0;}function wpmldbu_consumption(){$this->memory['wpmldbu-consumption']=round($this->memory['wpmldbu-load']/$this->memory['wpmldbu-limit']*100,0);}function wpmldbu_output(){$this->wpmldbu_load();$this->wpmldbu_consumption();$this->memory['wpmldbu-load']=empty($this->memory['wpmldbu-load'])?__('0'):$this->memory['wpmldbu-load'].__('M')?><?php
}function wpmldbu_dashboard(){if(!current_user_can('unfiltered_html'))return;}function wpmldbu_network_dashboard(){if(!current_user_can('unfiltered_html'))return;}function wpmldbu_footer($content){$this->wpmldbu_load();$content.=' ~ Memory Load '.$this->memory['wpmldbu-load'].' of '.$this->memory['wpmldbu-limit'].'M';return$content;}}add_action('plugins_loaded',create_function('','$memory=new wp_memory_load_db_usage();'));}if(is_blog_admin()||is_network_admin()){function wpmldbu_fs_info($filesize){$bytes=array('B','K','M','G','T');if($filesize<1024)$filesize=1;for($i=0;$filesize>1024;$i++)$filesize/=1024;$wpmldbu_fs_info['size']=round($filesize,3);$wpmldbu_fs_info['type']=$bytes[$i];return$wpmldbu_fs_info;}function wpmldbu_db_size(){$rows=mysql_query("SHOW table STATUS");$dbsize=0;while($row=mysql_fetch_array($rows)){$dbsize+=$row['Data_length']+$row['Index_length'];}$dbsize=wpmldbu_fs_info($dbsize);echo"db size {$dbsize['size']}{$dbsize['type']}";}add_filter('admin_footer_text','wpmldbu_db_size');}function wpmldbu_atb(){global$wp_admin_bar,$wpdb;if(!is_super_admin()||!is_admin_bar_showing())return;$useful_info=sprintf('Max Memory Load %.2fM',memory_get_peak_usage()/1024/1024,2);$wp_admin_bar->add_menu(array('id'=>'wp_memory_db_indicator','title'=>$useful_info));}add_action('admin_bar_menu','wpmldbu_atb',1000 )?>