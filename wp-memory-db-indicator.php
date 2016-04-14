<?php 
/*
Plugin Name: Memory usage db size
Plugin URI: https://slangji.wordpress.com/wp-memory-db-indicator/
Description: Indicate WordPress Memory Consumption and db size Usage on Backend Control Panel Footer and Show Max Memory Load Peak on Admin Bar or ToolBar.
Author: sLaNGjI's Team
Author URI: https://slangji.wordpress.com/plugins/
Requires at least: 3.1
Tested up to: 4.3
Version: 2013.0124.2016.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Network: true
 *
 * KeyTag:           false
 * Donate link:      https://slangji.wordpress.com/donate/
 * Indentation:      GNU style coding standard
 * Indentation URI:  https://www.gnu.org/prep/standards/standards.html
 * Humans:           We are the humans behind
 * Humans URI:       https://humanstxt.org/Standard.html
 *
 * DEVELOPMENT NOTE
 *
 * COMPATIBILITY WITH WORDPRESS: 4.4+ OR LATER, GREATER, HIGHER IS NOT GUARANTITED ONLY FOR DB MODULE
 * COMPATIBILITY WITH PHP:  5.5+ 5.6+ OR LATER, GREATER, HIGHER IS NOT GUARANTITED ONLY FOR DB MODULE
 *
 * THIS PLUGIN RUN WITH PHP 4.3.2 OR GREATER BUT THE BEST COMPATIBILITY CHOICE IS PHP 5.2.1 TO 5.4.45
 *
 * DISCLAIMER
 *
 * NO OTHER FIX WAS RELEASE SOON FOR THIS ON FREE PLUGIN
 * THIS IS ONLY A RECCOMANDATION AND NOT A SPECIFICATION
 */

	/**
	 * @package      Memory Load and db size Usage
	 * @subpackage   WordPress PlugIn
	 * @description  Indicate Memory and db Usage on Footer Backend and Max Memory Load Peak on Admin Bar or Toolbar
	 * @author       sLaNGjI's Team
	 * @develop      Code in Becoming!
	 * @status       STABLE Release
	 * @since        3.1+
	 * @tested       4.3+
	 * @branche      2013
	 * @version      2013.0124.2016.2
	 * @release      2013-01-24
	 * @build        2016-01-30
	 * @revision     2016
	 * @update       2
	 * @license      GPLv2 or later
	 * @indentation  GNU style coding standard
	 */
?>
<?php 
	defined( 'ABSPATH' ) OR exit;
	defined( 'WPINC' ) OR exit;
	if ( ! function_exists( 'add_action' ) )
		{
			header( 'HTTP/0.9 403 Forbidden' );
			header( 'HTTP/1.0 403 Forbidden' );
			header( 'HTTP/1.1 403 Forbidden' );
			header( 'Status: 403 Forbidden' );
			header( 'Connection: Close' );
				exit();
		}
	global $wp_version;
	if ( $wp_version < 3.1 )
		{
			wp_die( __( 'This plugin requires WordPress 3.1+ or greater: Activation Stopped.' ) );
		}
	function wpmldbu_1st()
		{
			if ( ! current_user_can( 'activate_plugins' ) )
				return;

			$wp_path_to_this_file = preg_replace( '/(.*)plugins\/(.*)$/', WP_PLUGIN_DIR . "/$2", __FILE__ );
			$this_plugin          = plugin_basename( trim( $wp_path_to_this_file ) );
			$active_plugins       = get_option( 'active_plugins' );
			$this_plugin_key      = array_search( $this_plugin, $active_plugins );

			if ( $this_plugin_key )
				{
					array_splice( $active_plugins, $this_plugin_key, 1 );
					array_unshift( $active_plugins, $this_plugin );
					update_option( 'active_plugins', $active_plugins );
				}
		}
	add_action( 'activated_plugin', 'wpmldbu_1st', 0 );
	function wpmldbu_prml( $links, $file )
		{
			if ( $file == plugin_basename( __FILE__ ) )
				{
					$links[] = '<a title="Offer a Beer to sLa" href="https://slangji.wordpress.com/donate/">Donate</a>';
					$links[] = '<a title="Bugfix and Suggestions" href="https://slangji.wordpress.com/contact/">Contact</a>';
				}
			return $links;
		}
	add_filter( 'plugin_row_meta', 'wpmldbu_prml', 10, 2 );
	function wpmldbu_footer_log()
		{
			echo "\n\n<!--Plugin Memory Load and db size Usage Active-->\n\n";
		}
	add_action('wp_head', 'wpmldbu_footer_log');
	add_action('wp_footer', 'wpmldbu_footer_log');
	add_action('admin_head', 'wpmldbu_footer_log');
	add_action('admin_footer', 'wpmldbu_footer_log');
?>
<?php 
	if ( is_blog_admin() || is_network_admin() )
		{
			class wp_memory_load_db_usage // resource limits memory_limit "128M" PHP_INI_ALL "8M" before PHP 5.2.0 "16M" in PHP 5.2.0
				{
					var $memory = false;
					function wpo()
						{
							return $this->__construct();
						}
					function __construct()
						{
							add_action('init', array(
								&$this,
								'wpmldbu_limit'
							));
							add_action('wp_dashboard_setup', array(
								&$this,
								'wpmldbu_dashboard'
							));
							add_action('wp_network_dashboard_setup', array(
								&$this,
								'wpmldbu_network_dashboard'
							));
							add_filter('admin_footer_text', array(
								&$this,
								'wpmldbu_footer'
							));
							$this->memory = array();
						}
					function wpmldbu_limit()
						{
							$this->memory['wpmldbu-limit'] = (int) ini_get('memory_limit');
						}
					function wpmldbu_load()
						{
							$this->memory['wpmldbu-load'] = function_exists('memory_get_usage') ? round(memory_get_usage() / 1024 / 1024, 2) : 0; // PHP 4.3.2 or greater required best PHP 5.2.1
						}
					function wpmldbu_consumption()
						{
							$this->memory['wpmldbu-consumption'] = round($this->memory['wpmldbu-load'] / $this->memory['wpmldbu-limit'] * 100, 0);
						}
					function wpmldbu_output()
						{
							$this->wpmldbu_load();
							$this->wpmldbu_consumption();
							$this->memory['wpmldbu-load'] = empty($this->memory['wpmldbu-load']) ? __('0') : $this->memory['wpmldbu-load'] . __('M');
?>
<?php 
						}
					function wpmldbu_dashboard()
						{
							if (!current_user_can('unfiltered_html'))
								return;
						}
					function wpmldbu_network_dashboard()
						{
							if (!current_user_can('unfiltered_html'))
								return;
						}
					function wpmldbu_footer($content)
						{
							$this->wpmldbu_load();

							// Start limit patch
							$memorylimit = (int) ini_get('memory_limit');
							$this->memory['wpmldbu-peak'] = round ($this->memory['wpmldbu-load'] / $this->memory['wpmldbu-limit'] * 100, 0);
							$content    .= ' Mem ' . $this->memory['wpmldbu-load'] . ' of ' . $memorylimit . 'M ' . '(' . $this->memory['wpmldbu-peak'] . '%) ' . ' on PHP ' . PHP_VERSION;
							// End limit patch

							/**
							 * Old limit code
							 * $content .= ' Mem ' . $this->memory['wpmldbu-load'] . ' of ' . $this->memory['wpmldbu-limit'] . 'M';
							 */

							return $content;
						}
				}
			add_action('plugins_loaded', create_function('', '$memory=new wp_memory_load_db_usage();'));
		}
	if (is_blog_admin() || is_network_admin())
		{
			global $wp_version;
			if (version_compare(PHP_VERSION, '5.5.0', '<'))
				{
			function wpmldbu_fs_info($filesize)
				{
					$bytes = array(
						'B',
						'K',
						'M',
						'G',
						'T'
					);
					if ($filesize < 1024) $filesize = 1;
					for ($i = 0; $filesize > 1024; $i++) $filesize /= 1024;
					$wpmldbu_fs_info['size'] = round($filesize, 3);
					$wpmldbu_fs_info['type'] = $bytes[$i];
					return $wpmldbu_fs_info;
				}
			function wpmldbu_db_size()
				{
					$rows   = mysql_query("SHOW table STATUS");
					$dbsize = 0;
					while ($row = mysql_fetch_array($rows))
						{
							$dbsize += $row['Data_length'] + $row['Index_length'];
						}
					$dbsize = wpmldbu_fs_info($dbsize);
					echo "Usage: db size {$dbsize['size']}{$dbsize['type']} ~ ";
				}
			add_filter('admin_footer_text', 'wpmldbu_db_size');
				}
			else
				{
					function wpmldbsu_footer($wpmldbsuf)
						{
							$wpmldbsuf = ' Usage: ';
							return $wpmldbsuf;
						}
					add_filter('admin_footer_text', 'wpmldbsu_footer');
				}
		}
	function wpmldbu_atb()
		{
			global $wp_admin_bar, $wpdb;
			if (!is_super_admin() || !is_admin_bar_showing())
				return;
			$useful_info_usage = sprintf('Usage %.2fM', memory_get_usage()      / 1024 / 1024, 2); // PHP 4.3.2 or greater required best PHP 5.2.1
			$useful_info_peak  = sprintf('Peak  %.2fM', memory_get_peak_usage() / 1024 / 1024, 2); // PHP 5.2.0 or greater required best PHP 5.2.1
			$wp_admin_bar->add_menu( array(
				'id'     => 'memory_peak_indicator',
				'parent' => 'top-secondary',
				'title'  => $useful_info_peak
			));
			$wp_admin_bar->add_menu( array(
				'id'     => 'memory_usage_indicator',
				'parent' => 'top-secondary',
				'title'  => $useful_info_usage
			));
		}
	add_action('admin_bar_menu', 'wpmldbu_atb', 1000);
?>
<?php 
	function wpmldbu_opt()
		{
			$m1 = memory_get_usage();
			$m3 = memory_get_peak_usage();
			$m1 = number_format($m1);
			$m3 = number_format($m3);
			echo "\r\n\r\n<!--\r\n\r\n";
			echo "Memory Usage - Current: $m1 Peak: $m3\r\n\r\n";
			echo get_num_queries() . " queries ";
			timer_stop(1);
			echo " seconds";
			echo "\r\n\r\n-->\r\n";
		}
	add_action('wp_head', 'wpmldbu_opt');
	add_action('wp_footer', 'wpmldbu_opt');
	add_action('admin_head', 'wpmldbu_opt');
	add_action('admin_footer', 'wpmldbu_opt');
?>