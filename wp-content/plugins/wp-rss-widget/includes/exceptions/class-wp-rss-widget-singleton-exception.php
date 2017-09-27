<?php

/**
 * Singleton reinstantiation exception.
 *
 * @since 1.0.0
 * @version 1.0.0
 */
class Wp_Rss_Widget_Singleton_Exception extends Wp_Rss_Widget_Exception {
	
	/**
	 * Constructor.
	 */
	public function __construct( $class ) {
		parent::__construct( "Cannot reinstansiate the {$class} class instance." );
	}

}
