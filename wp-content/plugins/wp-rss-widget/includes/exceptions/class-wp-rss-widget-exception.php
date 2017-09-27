<?php

/**
 * Base exception class for exceptions relevent to the addon.
 *
 * @since 1.0.0
 * @version 1.0.0
 */
class Wp_Rss_Widget_Exception extends Exception {
	
	/**
	 * Constructor.
	 */
	public function __construct( $msg, $code = 1 ) {
		parent::__construct( $msg, $code );
	}

}
