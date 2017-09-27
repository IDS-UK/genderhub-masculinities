<?php

/**
 * Main class for the WP RSS Widget Add-on
 * 
 * @since 1.0.0
 * @version 1.0.0
 */
class Wp_Rss_Widget_Addon {

	/**
	 * The minimum core version required.
	 */
	const CORE_MIN_VERSION = '4.8';

	/**
	 * Singleton instance.
	 * @var Wp_Rss_Widget_Addon
	 */
	protected static $instance = NULL;

	/**
	 * The hook loader.
	 * @var Wp_Rss_Widget_Loader
	 */
	protected $loader = NULL;

	/**
	 * The widget controller.
	 * @var Wp_Rss_Widget_Controller
	 */
	protected $widgetController = NULL;

	/**
	 * The assets controller.
	 * @var Wp_Rss_Widget_Assets_Controller
	 */
	protected $assetsController = NULL;

	/**
	 * The assets controller.
	 * @var Wp_Rss_Widget_Tooltips_Controller
	 */
	protected $tooltipsController = NULL;

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( self::$instance !== NULL ) {
			throw new Wp_Rss_Widget_Singleton_Exception( __CLASS__ );
		} else self::$instance = $this;
		$this->loadDependancies();
		$this->loader = new Wp_Rss_Widget_Loader();
		$this->loader->add_action( 'admin_init', $this, 'checkPluginDependancy' );
		$this->widgetController = new Wp_Rss_Widget_Controller();
		$this->assetsController = new Wp_Rss_Widget_Assets_Controller();
		// $this->tooltipsController = new Wp_Rss_Widget_Tooltips_Controller();
	}

	/**
	 * Gets the singleton instance.
	 * 
	 * @return Wp_Rss_Widget_Addon
	 * @since 1.0.0
	 */
	public static function getInstance() {
		return ( self::$instance === NULL )? new self() : self::$instance;
	}

	/**
	 * Gets the loader.
	 * 
	 * @return Wp_Rss_Widget_Loader
	 * @since 1.0.0
	 */
	public function getLoader() {
		return $this->loader;
	}

	/**
	 * Gets the widget controller.
	 * 
	 * @return Wp_Rss_Widget_Controller
	 * @since 1.0.0
	 */
	public function getWidgetController() {
		return $this->widgetController;
	}

	/**
	 * Gets the widget controller.
	 * 
	 * @return Wp_Rss_Widget_Controller
	 * @since 1.0.0
	 */
	public function getAssetsController() {
		return $this->assetsController;
	}

	/**
	 * Runs the loader, which attaches all registered hooks to WordPress.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * Loads required files.
	 *
	 * @since 1.0.0
	 */
	protected function loadDependancies() {
		require WPRSS_WIDGET_EXCEPTIONS_DIR . 'class-wp-rss-widget-exception.php';
		require WPRSS_WIDGET_EXCEPTIONS_DIR . 'class-wp-rss-widget-singleton-exception.php';
		require WPRSS_WIDGET_INCLUDES_DIR . 'class-wp-rss-widget-loader.php';
		require WPRSS_WIDGET_CONTROLLERS_DIR . 'class-wp-rss-widget-controller.php';
		require WPRSS_WIDGET_CONTROLLERS_DIR . 'class-wp-rss-widget-assets-controller.php';
		require WPRSS_WIDGET_CONTROLLERS_DIR . 'class-wp-rss-widget-tooltips-controller.php';
		require WPRSS_WIDGET_INCLUDES_DIR . 'enum-wp-rss-widget-color-styles.php';
	}

	/**
	 * Checks if the plugins required are active and at the appropriate version.
	 *
	 * @since 1.0.0
	 */
	public function checkPluginDependancy() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		$exception = NULL;
		if ( ! is_plugin_active( 'wp-rss-aggregator/wp-rss-aggregator.php' ) || version_compare( WPRSS_VERSION, self::CORE_MIN_VERSION, '<' ) ) {
			add_action( 'admin_notices', 'wp_rss_widget_plugin_dependancy_notice' );
			deactivate_plugins( WPRSS_WIDGET_BASE );
		}
	}

	/**
	 * Renders a view and returns the render as a string. Does not echo the view.
	 * 
	 * @param  string $view    The view name.
	 * @param  array  $viewbag Date to be included for the view.
	 * 
	 * @return string          The rendered view contents.
	 * @since  1.0.0
	 */
	public function renderView( $view, $viewbag = array() ) {
		$viewfile = WPRSS_WIDGET_VIEWS_DIR . 'view-' . $view . '.php';
		if ( ! is_file( $viewfile ) ) return '';
		ob_start();
		require $viewfile;
		return ob_get_clean();
	}

}

/**
 * The admin notice that informs the user that the plugin dependancies are not present or do not meet the minimum version requirement.
 *
 * @since 1.0.0
 */
function wp_rss_widget_plugin_dependancy_notice() {
	echo '<div id="message" class="error notice is-dismissible">';
	echo '<p>The <strong>WP RSS Aggregator - Widget</strong> addon has been deactivated since it requires the <strong>WP RSS Aggregator</strong> plugin to be installed at version <code>' . Wp_Rss_Widget_Addon::CORE_MIN_VERSION . '</code> or later.</p>';
	echo '<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>';
	echo '</div>';
}
