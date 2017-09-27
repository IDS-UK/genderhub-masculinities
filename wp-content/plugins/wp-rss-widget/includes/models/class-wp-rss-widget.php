<?php

/**
 * Model class for the widget.
 *
 * @since 1.0.0
 * @version 1.0.0
 */
class Wp_Rss_Widget extends WP_Widget {

	/**
	 * Thw width of the widget form.
	 */
	const WIDTH = 400;

	/**
	 * The height of the widget form.
	 */
	const HEIGHT = 500;

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			self::getId(),
			self::getName(),
			array(
				'classname'		=>	'wprss_widget',
				'description'	=>	self::getDescription()
			),
			self::getSize()
		);
	}

	protected function parseArgs( $args ) {
		$defaults = array(
			'title' 		=> '',
			'show_date' 	=> 0,
			'show_author' 	=> 0,
			'open_newtab' 	=> 1,
			'show_desc' 	=> 0,
			'show_thumb' 	=> 0,
			'count' 		=> 5,
			'trim_desc' 	=> 100,
			'read_more' 	=> '[Read More]',
			'trim_title' 	=> 0,
			'color_style' 	=> 'none',
			'enable_ticker' => 1,
			'visible_items' => 5,
			'ticker_speed' 	=> 5,
		);
		return wp_parse_args( (array) $args, $defaults );
	}

	/**
	 * Front-end rendering for the widget.
	 *
	 * @see WP_Widget::widget()
	 * 
	 * @param  array $args     Widget arguments.
	 * @param  array $instance Saved widget instance values from database.
	 */
	public function widget( $args, $instance ) {
		$viewbag = array(
			'widget'	=>	$this,
			'instance'	=>	$this->parseArgs( $instance ),
			'args'		=>	$args
		);
		echo wprss_widget_addon()->renderView( 'widget-display', $viewbag );
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 * 
	 * @param  array $instance Saved widget instance values.
	 */
	public function form( $instance ) {
		$viewbag = array(
			'widget'	=>	$this,
			'instance'	=>	$this->parseArgs( $instance )
		);
		echo wprss_widget_addon()->renderView( 'widget-form', $viewbag );
	}

	/**
	 * Sanitizes the widget form values as they are saved.
	 * 
	 * @see  WP_Widget::update()
	 * 
	 * @param  array $new_instance The new received values to be saved.
	 * @param  array $old_instance The saved values in the database.
	 * 
	 * @return array The updated and sanitized values to save in the database.
	 * @since 1.0.0
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] 			= stripslashes($new_instance['title'] );
		$instance['show_desc'] 		= intval( $new_instance['show_desc'] );
		$instance['show_date'] 		= intval( $new_instance['show_date'] );
		$instance['show_author'] 	= intval(  $new_instance['show_author'] );
		$instance['open_newtab'] 	= intval( $new_instance['open_newtab'] );
		$instance['show_thumb'] 	= stripslashes( $new_instance['show_thumb'] );
		$instance['count'] 			= intval( $new_instance['count'] );
		$instance['trim_desc'] 	    = intval( $new_instance['trim_desc'] );
		$instance['read_more'] 		= stripslashes( $new_instance['read_more'] );
		$instance['trim_title'] 	= intval( $new_instance['trim_title'] );
		$instance['color_style'] 	= stripslashes( $new_instance['color_style'] );
		$instance['enable_ticker'] 	= intval( $new_instance['enable_ticker'] );
		$instance['visible_items'] 	= intval( $new_instance['visible_items'] );
		$instance['ticker_speed'] 	= intval( $new_instance['ticker_speed'] );
		return $instance;
	}

	/**
	 * Gets the widget ID.
	 * 
	 * @return string
	 */
	public static function getId() {
		return 'wp_rss_widget';
	}

	/**
	 * Gets the widget name.
	 * 
	 * @return string
	 */
	public static function getName() {
		return __( 'WP RSS Aggregator Widget', 'wprss' );
	}

	/**
	 * Gets the widget description.
	 * 
	 * @return string
	 */
	public static function getDescription() {
		return __( 'Your imported feed items in a widget.', 'wprss' );
	}

	/**
	 * Returns the widget form size.
	 * 
	 * @return array Array containing the dimensions.
	 */
	public static function getSize() {
		return array(
			'width' => self::WIDTH,
			'height' => self::HEIGHT
		);
	}

}
