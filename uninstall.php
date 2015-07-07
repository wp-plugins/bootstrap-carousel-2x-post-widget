<?php
/**
   *
   * @package   bdwebteam-single2x-post-widget
   * @author    Md. Mahabub Masan Manik <m.manik01@gmail.com>
   * @license   MIT License
   * @link      http://plugin.bdwebteam.com/bdwebteam-single2x-post-widget
   * @copyright 2015 Mahabub Hasan
   * 
 */

// If uninstall, not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete plugin settings
delete_option( 'Responsive_Posts_Widget' );
delete_site_option( 'Responsive_Posts_Widget' );