<?php

$widget = $viewbag['widget'];
$instance = $viewbag['instance'];
$args = $viewbag['args'];

$before_widget = $viewbag['args']['before_widget'];
$after_widget = $viewbag['args']['after_widget'];
$before_title = $viewbag['args']['before_title'];
$after_title = $viewbag['args']['after_title'];

$tab_titles 	= stripslashes( $instance['tab_titles'] );
$count 			= intval( $instance['count'] );
$show_date 		= intval( $instance['show_date'] ) == 1;
$show_desc 		= intval( $instance['show_desc'] ) == 1;
$show_author 	= intval( $instance['show_author'] ) == 1;
$show_thumb 	= intval( $instance['show_thumb'] ) == 1;
$open_newtab 	= intval( $instance['open_newtab'] ) == 1;
$trim_desc  	= intval( $instance['trim_desc'] );
$trim_title 	= intval( $instance['trim_title'] );
$read_more 		= htmlspecialchars( $instance['read_more'] );
$rich_desc 		= intval( $instance['rich_desc'] );
$color_style 	= stripslashes( $instance['color_style'] );
$enable_ticker 	= intval( $instance['enable_ticker'] ) == 1;
$visible_items 	= intval( $instance['visible_items'] );
$ticker_speed 	= intval( $instance['ticker_speed'] ) * 1000;

$ent_enabled = defined( 'WPRSS_ET_VERSION' );

$query_args = array(
	'feed_limit'	=>	$count,
	'pagination'	=>	'off',
	'no-paged'		=>	TRUE
);
$feed_items = wprss_get_feed_items_query( $query_args );

echo $before_widget;

if ( ! empty( $instance['title'] ) ) {
	echo $before_title . apply_filters( 'widget_title', $instance['title'], $instance, $widget->id_base ) . $after_title;
}

?>
<div class="wp-rss-aggregator-widget">
	<div class="wprss-w-wrap <?php echo ($enable_ticker? 'wprss-w-vticker' : ''); ?> wprss-w-style-<?php echo $color_style; ?>"
		data-visible="<?php echo $visible_items; ?>" data-speed="<?php echo $ticker_speed; ?>" <?php echo $randAttr; ?> >
		<div>
			<?php
			if ( $feed_items->have_posts() ) :
				$j = 1;
		    	while ( $feed_items->have_posts() ) :
					$feed_items->the_post();
					$permalink = esc_attr( get_post_meta( get_the_ID(), 'wprss_item_permalink', TRUE ) );
					// Display the feed items ?>
					<div class="wprss-w-item <?php echo ( ($j % 2) == 0? 'even' : 'odd' ); ?>">

						<?php
							if ( $ent_enabled && $show_thumb ) :
							$thumbnail_img = get_post_meta( get_the_ID(), 'wprss_item_thumbnail', true );
							if ( ! empty( $thumbnail_img ) ) : ?>
								<div class="wprss-w-thumb">
									<a href="<?php echo $permalink; ?>" <?php if ( $open_newtab ) echo 'target="_blank"' ?>>
										<img src="<?php echo $thumbnail_img; ?>" />
									</a>
								</div>
							<?php endif; ?>
						<?php endif; ?>

						<div class="wprss-w-title">
							<a href="<?php echo $permalink; ?>" <?php if ( $open_newtab ) echo 'target="_blank"' ?>>
								<?php
									$title = get_the_title();
									if ( $trim_title !== '' && $trim_title !== 0 ) {
										$title = (strlen( $title ) > $trim_title)? substr( $title, 0 , $trim_title ) . '&hellip;' : $title;
									}
									echo $title;
								?>
							</a>
						</div>

						<?php if ( $ent_enabled && $show_desc ) : ?>
							<div class="wprss-w-excerpt">
								<?php
									$excerpt = get_the_content();
									$allowed_tags = apply_filters(
										'wprss_w_excerpt_allowed_tags',
										array( 'a', 'i', 'em', 'b', 'strong', 'u', 'span' )
									);
									if ( $trim_desc > 0 ) {
										$excerpt = wprss_trim_words(
											$excerpt,
											$trim_desc, 
											$allowed_tags
										);
										$hellip = '&hellip;';
										if ( strrpos( $excerpt, $hellip ) === ( strlen( $excerpt ) - strlen( $hellip ) ) )
											$excerpt .= sprintf( ' <a href="%s" %s>%s</a>', $permalink, $open_newtab? 'target="_blank"': '', $read_more );
									} else {
										$allowed_tags_string = sprintf('<%1$s>', implode( '><', $allowed_tags ) );
										$excerpt = strip_tags( $excerpt, $allowed_tags_string );
									}
									echo $excerpt;
								?>
							</div>
						<?php endif; ?>

						<?php if ( $show_date || $show_author ): ?>
							<div class="wprss-w-meta">
								<?php
									if ( $show_date ) {
										$item_date = get_the_time( 'U', get_the_ID() );
										$item_date = ( $item_date === '' )? date('U') : $item_date;
										$fulldate = date( 'jS M y', $item_date );
										echo '<span class="wprss-w-date">' . $fulldate . '</span>';
									}
									$author = get_post_meta( get_the_ID(), 'wprss_item_author', TRUE );
									if ( $show_author && ! empty( $author ) ) {
										echo '<span class="wprss-w-author">By ' . $author . '</span>';
									}
								?>
							</div>
						<?php endif; ?>
					</div>
					<?php $j++; ?>
				<?php endwhile; ?>
			<?php else: ?>
				<div class="wprss-w-item">
					<div class="wprss-w-title">
						<a href="">No feed items to show</a>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<?php echo $after_widget;