<?php

// Get viewbag data
$widget = $viewbag['widget'];
$instance = $viewbag['instance'];

// Get instance data
$title = htmlspecialchars( $instance['title'] );
$show_desc 		= intval( $instance['show_desc'] );
$show_date 		= intval( $instance['show_date'] );
$show_author 	= intval( $instance['show_author'] );
$open_newtab 	= intval( $instance['open_newtab'] );
$show_thumb 	= intval( $instance['show_thumb'] );
$count 			= intval( $instance['count'] );
$trim_desc 	    = intval( $instance['trim_desc'] );
$read_more 		= htmlspecialchars( $instance['read_more'] );
$trim_title 	= intval( $instance['trim_title'] );
$color_style 	= stripslashes( $instance['color_style'] );
$enable_ticker 	= intval( $instance['enable_ticker'] );
$visible_items 	= intval( $instance['visible_items'] );
$ticker_speed 	= intval( $instance['ticker_speed']) ;

$wprss_widget_color_styles = Wp_Rss_Widget_Color_Styles::getAssoc();

$ent_enabled = defined( 'WPRSS_ET_VERSION' );
$ent_option = ($ent_enabled)? '' : 'class="wprss-w-disabled"';

?>

<div class="wprss_w_settings">
	<table width="100%" height="42" border="0">
		<tr>
			<td width="13%" height="33"><label for="<?php echo $widget->get_field_id('title'); ?>">Title: </label></td>
			<td width="87%"><input id="<?php echo $widget->get_field_id('title');?>" name="<?php echo $widget->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" class="widefat"/></td>
		</tr>
	</table>
</div>

<div class="wprss_w_settings">
	<h4>Settings</h4>
	<table width="100%" border="0">
		<tr>
			<td height="29">
				<input id="<?php echo $widget->get_field_id('open_newtab'); ?>" type="checkbox"  name="<?php echo $widget->get_field_name('open_newtab'); ?>" value="1" <?php echo $open_newtab == "1" ? 'checked="checked"' : ""; ?> />
			</td>
			<td>
				<label for="<?php echo $widget->get_field_id('open_newtab'); ?>">Open links in new tab</label>
				<?php echo Wp_Rss_Widget_Tooltips_Controller::do_tooltip('open_newtab'); ?>
			</td>
			<td width="28%">
				<label for="<?php echo $widget->get_field_id('count');?>">Feed item Count</label>
				<?php echo Wp_Rss_Widget_Tooltips_Controller::do_tooltip('count'); ?>
			</td>
			<td width="20%">
				<input id="<?php echo $widget->get_field_id('count');?>" name="<?php echo $widget->get_field_name('count'); ?>" type="text" value="<?php echo $count; ?>" class="widefat" title="No of feed items to parse"/>
			</td>
		</tr>
		<tr>
			<td height="32">
				<input id="<?php echo $widget->get_field_id('show_date'); ?>" type="checkbox"  name="<?php echo $widget->get_field_name('show_date'); ?>" value="1" <?php echo $show_date == "1" ? 'checked="checked"' : ""; ?> />
			</td>
			<td>
				<label for="<?php echo $widget->get_field_id('show_date'); ?>">Show Date</label>
				<?php echo Wp_Rss_Widget_Tooltips_Controller::do_tooltip('show_date'); ?>
			</td>
			<td>
				<label for="<?php echo $widget->get_field_id('trim_title'); ?>">Trim Title</label>
				<?php echo Wp_Rss_Widget_Tooltips_Controller::do_tooltip('trim_title'); ?>
			</td>
			<td>
				<input id="<?php echo $widget->get_field_id('trim_title');?>" name="<?php echo $widget->get_field_name('trim_title'); ?>" type="text" value="<?php echo $trim_title; ?>" class="widefat" title="The number of charaters to be displayed. Use 0 to disable stripping"/>
			</td>
		</tr>
		<tr>
			<td height="29">
				<input id="<?php echo $widget->get_field_id('show_author'); ?>" type="checkbox"  name="<?php echo $widget->get_field_name('show_author'); ?>" value="1" <?php echo $show_author == "1" ? 'checked="checked"' : ""; ?> />
			</td>
			<td>
				<label for="<?php echo $widget->get_field_id('show_author'); ?>">Show Author</label>
				<?php echo Wp_Rss_Widget_Tooltips_Controller::do_tooltip('show_author'); ?>
			</td>
			<td colspan="2"></td>
		</tr>
	</table>
</div>

<div class="wprss_w_settings">
	<h4>Excerpts &amp; Thumbnails <small>(<em>Requires add-on</em>)</small></h4>
	<table width="100%" border="0">
		<tr>
			<td width="7%" height="28" <?php echo $ent_option; ?> >
				<input id="<?php echo $widget->get_field_id('show_desc'); ?>" type="checkbox"  name="<?php echo $widget->get_field_name('show_desc'); ?>" value="1" <?php echo $show_desc == "1" ? 'checked="checked"' : ""; ?> />
			</td>
			<td width="36%" <?php echo $ent_option; ?> >
				<label for="<?php echo $widget->get_field_id('show_desc'); ?>">Show Excerpt</label>
				<?php echo Wp_Rss_Widget_Tooltips_Controller::do_tooltip('show_desc'); ?>
			</td>
			<td width="32%" <?php echo $ent_option; ?> >
				<label for="<?php echo $widget->get_field_id('trim_desc');?>">Trim Excerpt</label>
				<?php echo Wp_Rss_Widget_Tooltips_Controller::do_tooltip('trim_desc'); ?>
			</td>
			<td width="25%" <?php echo $ent_option; ?> >
				<input id="<?php echo $widget->get_field_id('trim_desc');?>" name="<?php echo $widget->get_field_name('trim_desc'); ?>" type="text" value="<?php echo $trim_desc; ?>" class="widefat" title="The number of charaters to be displayed. Use 0 to disable stripping"/>
			</td>
		</tr>
		<tr>
			<td height="29" <?php echo $ent_option; ?> >
				<input id="<?php echo $widget->get_field_id('show_thumb'); ?>" type="checkbox"  name="<?php echo $widget->get_field_name('show_thumb'); ?>" value="1" <?php echo $show_thumb == "1" ? 'checked="checked"' : ""; ?> />
			</td>
			<td <?php echo $ent_option; ?> >
				<label for="<?php echo $widget->get_field_id('show_thumb'); ?>">Show Thumbnail</label>
				<?php echo Wp_Rss_Widget_Tooltips_Controller::do_tooltip('show_thumb'); ?>
			</td>
			<td <?php echo $ent_option; ?> >
				<label for="<?php echo $widget->get_field_id('read_more'); ?>">Read more text</label>
				<?php echo Wp_Rss_Widget_Tooltips_Controller::do_tooltip('read_more'); ?>
			</td>
			<td <?php echo $ent_option; ?> >
				<input id="<?php echo $widget->get_field_name('read_more'); ?>" name="<?php echo $widget->get_field_name('read_more'); ?>" type="text" value="<?php echo $read_more; ?>" class="widefat" title="Leave blank to hide read more text"/>
			</td>
			<td>&nbsp;</td>
		</tr>
	</table>
</div>

<div class="wprss_w_settings">
	<h4>Styles and Animations</h4>
	<table width="100%" border="0">
		<tr>
			<td height="32">
				<label>Color style:</label>
				<?php echo Wp_Rss_Widget_Tooltips_Controller::do_tooltip('color_style'); ?>
			</td>
			<td>
			<?php
			echo '<select name="' . $widget->get_field_name('color_style') . '" id="' . $widget->get_field_id('color_style') . '">';
			foreach( $wprss_widget_color_styles as $key => $val ) {
				$key = strtolower( $key );
				echo '<option value="' . $key . '" ' . ( $color_style == $key ? 'selected="selected"' : "" ) .  '>' . $val . '</option>';
			}
			echo '</select>';
			?>
			</td>
		</tr>
		<tr>
			<td height="33">
				<label for="<?php echo $widget->get_field_id('enable_ticker'); ?>">Ticker animation:</label>
				<?php echo Wp_Rss_Widget_Tooltips_Controller::do_tooltip('enable_ticker'); ?>
			</td>
			<td><input id="<?php echo $widget->get_field_id('enable_ticker'); ?>" type="checkbox"  name="<?php echo $widget->get_field_name('enable_ticker'); ?>" value="1" <?php echo $enable_ticker == "1" ? 'checked="checked"' : ""; ?> class="wprss-w-ticker-enable" /></td>
		</tr>
		<tr>
			<td height="36">
				<label for="<?php echo $widget->get_field_id('visible_items');?>">Visible items: </label>
				<?php echo Wp_Rss_Widget_Tooltips_Controller::do_tooltip('visible_items'); ?>
			</td>
			<td><input id="<?php echo $widget->get_field_id('visible_items');?>" name="<?php echo $widget->get_field_name('visible_items'); ?>" type="number" min="1" value="<?php echo $visible_items; ?>" title="The no of feed items to be visible."/>
			</td>
		</tr>
		<tr>
			<td height="36">
				<label for="<?php echo $widget->get_field_id('ticker_speed');?>">Ticker speed: </label>
				<?php echo Wp_Rss_Widget_Tooltips_Controller::do_tooltip('ticker_speed'); ?>
			</td>
			<td><input id="<?php echo $widget->get_field_id('ticker_speed');?>" name="<?php echo $widget->get_field_name('ticker_speed'); ?>" type="number" min="1" value="<?php echo $ticker_speed; ?>" title="Speed of the ticker in seconds"/> seconds
			</td>
		</tr>
	</table>
</div>

<br />

<script type="text/javascript">
	(function($) {
		var disabledClass = 'wprss-w-disabled';
		$('td.' + disabledClass).find('input, select').prop('disabled', true);
	})(jQuery);
</script>