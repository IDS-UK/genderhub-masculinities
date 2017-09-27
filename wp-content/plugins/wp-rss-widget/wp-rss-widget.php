<?php
    /**
     * Plugin Name: WP RSS Aggregator - Widget
     * Plugin URI: https://www.wprssaggregator.com/#utm_source=wpadmin&utm_medium=plugin&utm_campaign=wpraplugin
     * Description: An add-on for WP RSS Aggregator that displays your imported feed items in a widget.
     * Version: 1.1.1
     * Author: RebelCode
     * Author URI: https://www.wprssaggregator.com
     * Text Domain: wprss
     * Domain Path: /languages/
     * License: GPLv3
     */

    /**
     * Copyright (C) 2012-2016 RebelCode Ltd.
     *
     * This program is free software: you can redistribute it and/or modify
     * it under the terms of the GNU General Public License as published by
     * the Free Software Foundation, either version 3 of the License, or
     * (at your option) any later version.
     *
     * This program is distributed in the hope that it will be useful,
     * but WITHOUT ANY WARRANTY; without even the implied warranty of
     * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     * GNU General Public License for more details.
     *
     * You should have received a copy of the GNU General Public License
     * along with this program.  If not, see <http://www.gnu.org/licenses/>.
     */

// If the file is called directly, or has already been called, abort
if ( ! defined('WPINC') || defined('WPRSS_WIDGET_ADDON') ) die;

// Plugin presence constant
define( 'WPRSS_WIDGET_ADDON',			__FILE__ );
// Alias for WPRSS_WIDGET_ADDON
define( 'WPRSS_WIDGET_PATH',			WPRSS_WIDGET_ADDON );
// Plugin version constants
define( 'WPRSS_WIDGET_VERSION',			'1.1.1' );
// Plugin path constants
define( 'WPRSS_WIDGET_DIR',				plugin_dir_path( WPRSS_WIDGET_ADDON ) );
define( 'WPRSS_WIDGET_BASE',			plugin_basename( WPRSS_WIDGET_ADDON ) );
define( 'WPRSS_WIDGET_INCLUDES_DIR',	WPRSS_WIDGET_DIR . trailingslashit('includes') );
define( 'WPRSS_WIDGET_CONTROLLERS_DIR',	WPRSS_WIDGET_INCLUDES_DIR . trailingslashit('controllers') );
define( 'WPRSS_WIDGET_MODELS_DIR',		WPRSS_WIDGET_INCLUDES_DIR . trailingslashit('models') );
define( 'WPRSS_WIDGET_VIEWS_DIR',		WPRSS_WIDGET_INCLUDES_DIR . trailingslashit('views') );
define( 'WPRSS_WIDGET_EXCEPTIONS_DIR',	WPRSS_WIDGET_INCLUDES_DIR . trailingslashit('exceptions') );
// Plugin URI constants
define( 'WPRSS_WIDGET_BASE_URI',		plugin_dir_url( WPRSS_WIDGET_ADDON ) );
define( 'WPRSS_WIDGET_ASSETS_URI',		WPRSS_WIDGET_BASE_URI . trailingslashit('assets') );
define( 'WPRSS_WIDGET_CSS_URI',			WPRSS_WIDGET_ASSETS_URI . trailingslashit('css') );
define( 'WPRSS_WIDGET_JS_URI',			WPRSS_WIDGET_ASSETS_URI . trailingslashit('js') );
// Plugin store info
define( 'WPRSS_WIDGET_SL_STORE_URL',	'http://www.wprssaggregator.com/edd-sl-api/' );
define( 'WPRSS_WIDGET_SL_ITEM_NAME',	'Widget' );

// Adding autoload paths
add_action( 'plugins_loaded', function() {
    wprss_autoloader()->add('Aventura\\Wprss\\Widget', WPRSS_WIDGET_INCLUDES_DIR);
});

// Load licensing loader file
require_once ( WPRSS_WIDGET_INCLUDES_DIR . 'licensing.php' );

// Require main file
require WPRSS_WIDGET_INCLUDES_DIR . 'class-wp-rss-widget-addon.php';

/**
 * Returns the Widget Addon class singleton instance.
 *
 * @return Wp_Rss_Widget_Addon
 */
function wprss_widget_addon() {
	return Wp_Rss_Widget_Addon::getInstance();
}

// Begin "execution"
try {
	$instance = wprss_widget_addon();
	$instance->run();
} catch (Exception $e) {
	wp_die( $e->getMessage() );
}
