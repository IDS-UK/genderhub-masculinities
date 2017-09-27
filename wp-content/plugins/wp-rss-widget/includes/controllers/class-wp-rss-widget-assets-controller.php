<?php

/**
 * Assets controller, for enqueueing the static assset files.
 * 
 * @since 1.0.0
 * @version 1.0.0
 */
class Wp_Rss_Widget_Assets_Controller {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->defineHooks();
	}

	/**
	 * Registers the hooks to the loader.
	 *
	 * @since 1.0.0
	 */
	protected function defineHooks() {
		$loader = wprss_widget_addon()->getLoader();
		$loader->add_action( 'wp_enqueue_scripts', $this, 'enqueuePublicStyles' );
		$loader->add_action( 'wp_enqueue_scripts', $this, 'enqueuePublicScripts' );
		$loader->add_action( 'admin_enqueue_scripts', $this, 'enqueueAdminStyles' );
		$loader->add_action( 'admin_enqueue_scripts', $this, 'enqueueAdminScripts' );
	}

	/**
	 * Enqueues public styles.
	 *
	 * @since 1.0.0
	 */
	public function enqueuePublicStyles() {
		wp_enqueue_style( 'wprss-public-widget-styles', WPRSS_WIDGET_CSS_URI . 'frontend-styles.css' );
	}

	/**
	 * Enqueues public scripts.
	 * 
	 * @since 1.0.0
	 */
	public function enqueuePublicScripts() {
		wp_enqueue_script( 'wprss-widget-frontend-js', WPRSS_WIDGET_JS_URI . 'frontend.js', array( 'jquery' ) );
	}

	/**
	 * Enqueues admin styles.
	 * 
	 * @since 1.0.0
	 */
	public function enqueueAdminStyles() {
		// If on the widgets page, load the stylesheet
		if ( in_array( $GLOBALS['pagenow'], array( 'widgets.php', 'customize.php' ) ) ) {       	 
			wp_enqueue_style( 'wprss-admin-widget-styles', WPRSS_WIDGET_CSS_URI . 'backend-styles.css' );
			wp_enqueue_style( 'wprss-admin-styles', WPRSS_CSS . 'admin-styles.css' );
			wp_enqueue_style( 'wprss-fa', WPRSS_CSS . 'font-awesome.min.css' );
			wp_enqueue_style( 'jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css' );
		}
	}

	/**
	 * Enqueues admin scripts.
	 * 
	 * @since 1.0.0
	 */
	public function enqueueAdminScripts() {
		// If on the widgets page, load the stylesheet
		if ( in_array( $GLOBALS['pagenow'], array( 'widgets.php', 'customize.php' ) ) ) {       	 
			wp_enqueue_script( 'wprss-admin-widget-scripts', WPRSS_WIDGET_JS_URI . 'backend.js', array( 'jquery', 'jquery-ui' ) );

		}
	}
	
}
