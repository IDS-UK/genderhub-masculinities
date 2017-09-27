<?php

/**
 * Enum-style abstract class for the availabile color styles.
 *
 * @since 1.0.0
 * @version 1.0.0
 */
abstract class Wp_Rss_Widget_Color_Styles {
	const NONE = 'No Style';
	const GREY = 'Grey';
	const DARK = 'Dark';
	const ORANGE = 'Orange';
	const SMODERN = 'Simple Modern';

	/**
	 * Gets the enum const names and values as an assoc array.
	 * 
	 * @return array An array with the const names as array keys and their respective values as array values.
	 * @since 1.0.0
	 */
	public static function getAssoc() {
		$refClass = new ReflectionClass( __CLASS__ );
		return $refClass->getConstants();
	}
}
