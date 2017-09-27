<?php

/**
 * Controller class for the widget.
 *
 * @since 1.0.0
 * @version 1.0.0
 */
class Wp_Rss_Widget_Controller {

	/**
	 * The widget model class name.
	 */
	const WIDGET_CLASSNAME = 'Wp_Rss_Widget';

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->loadDependancies();
		$this->defineHooks();
	}

	protected function loadDependancies() {
		require WPRSS_WIDGET_MODELS_DIR . 'class-wp-rss-widget.php';
	}

	/**
	 * Registers hooks to the loader.
	 */
	protected function defineHooks() {
		$loader = wprss_widget_addon()->getLoader();
		$loader->add_action( 'widgets_init', $this, 'registerWidget' );
	}

	/**
	 * Registers the widget.
	 */
	public function registerWidget() {
		register_widget( self::WIDGET_CLASSNAME );
	}

}

