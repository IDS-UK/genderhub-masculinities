<?php

/**
 * Tooltips controller class.
 *
 * @since 1.0.0
 * @version 1.0.0
 */
class Wp_Rss_Widget_Tooltips_Controller {

	/**
	 * Prefix for tooltip
	 */
	const TOOLTIP_PREFIX = 'widget_';

	/**
	 * Text domain for i18n
	 */
	const TEXT_DOMAIN = 'wprss_widget';

	/**
	 * Constructs a new instance.
	 */
	public function __construct() {
		$loader = wprss_widget_addon()->getLoader();
		$loader->add_action( 'plugins_loaded', $this, 'register_tooltips', 11 );
	}

	/**
	 * Registers the tooltips to the WPRSS Help class.
	 */
	public function register_tooltips() {
		if ( class_exists( 'WPRSS_Help' ) ) {
			$help = WPRSS_Help::get_instance()->add_tooltips( self::get_tooltips(), self::TOOLTIP_PREFIX );
		}
	}

	/**
	 * Returns the tooltip HTML.
	 */
	public static function do_tooltip( $id ) {
		return WPRSS_Help::get_instance()->do_tooltip( self::TOOLTIP_PREFIX . $id );
	}

	/**
	 * Returns the tooltips.
	 * 
	 * @return array
	 */
	public static function get_tooltips() {
		return apply_filters( 'wprss_widget_tooltips', self::_get_tooltips() );
	}

	/**
	 * Internal static array-return for the tooltips.
	 * 
	 * @return array
	 */
	protected static function _get_tooltips() {
		return array(
			'open_newtab'		=>	__( 'Tick this box to make links in the feed items open in a separate browser tab.', self::TEXT_DOMAIN ),
			'count'				=>	__( 'The number of feed items to show in the widget.', self::TEXT_DOMAIN ),
			'show_date'			=>	__( 'Tick this box to show the feed items\' published dates.', self::TEXT_DOMAIN ),
			'show_author'		=>	__( 'Tick this box to show the author for feed items, if the author is given by the feed.', self::TEXT_DOMAIN ),
			'trim_title'		=>	__( 'Set the maxium number of characters. Titles that are longer will be trimmed down and will have "..." appended. Leave empty or use zero to not trim feed item titles.', self::TEXT_DOMAIN ),
			'show_desc'			=>	__( 'Tick this box to show the excerpt for feed items, if they are given by the feed. Requires the Excerpts and Thumbnails add-on.', self::TEXT_DOMAIN ),
			'trim_desc'			=>	__( 'Set the maximum number of words. Excerpts that are longer will be trimmed to match this number of words and will have "..." appended. Leave empty or use zero to not trim excerpts.', self::TEXT_DOMAIN ),
			'show_thumb'		=>	__( 'Tick this box to show feed item thumbnails, if the feed items have them. Requires the Excerpts and Thumbnails add-on.', self::TEXT_DOMAIN ),
			'read_more'			=>	__( 'Enter the text to show after the excerpt when it is trimmed. This option can be left empty.', self::TEXT_DOMAIN ),
			'color_style'		=>	__( 'Choose the color style for the widget.', self::TEXT_DOMAIN ),
			'enable_ticker'		=>	__( 'Enables the ticker animation, which shows less feed items on the widget, but occasionally slides up to show more items.', self::TEXT_DOMAIN ),
			'visible_items'		=>	__( 'Set the number of items that are shown by the ticker. This number is recommened to be larger than the "Feed item count" option.', self::TEXT_DOMAIN ),
			'ticker_speed'		=>	__( 'Set the number of seconds between the ticker animation transitions. More seconds means he ticker will transition less often.', self::TEXT_DOMAIN ),
		);
	}

}
