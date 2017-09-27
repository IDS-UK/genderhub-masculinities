<?php
if (!class_exists('Learndash_Admin_Settings_Support_Panel')) {
	class Learndash_Admin_Settings_Support_Panel {
		
		function __construct() {
			add_action( 'admin_menu', 			array( $this, 'admin_menu' ) );
		}
		
		/**
		 * Register settings page
		 */
		public function admin_menu() {
			$page_hook = add_submenu_page(
				'learndash-lms-non-existant',
				_x( 'Support', 'Support Tab Label', 'learndash' ),
				_x( 'Support', 'Support Tab Label', 'learndash' ),
				'manage_options',
				'learndash_support',
				array( $this, 'admin_page' )
			);
			//add_action( 'load-'. $page_hook, array( $this, 'on_load_panel' ) );
		}
		
		function on_load_panel() {

			//$current_screen = get_current_screen();
			//error_log('current_screen<pre>'. print_r($current_screen, true) .'</pre>');
			
			// Load JS/CSS as needed for page
		}

		/**
		 * Output settings page
		 */
		public function admin_page() {
			?>
			<div id="learndash-settings learndash-settings-support" class="wrap">
				<h1><?php _e( 'Support', 'learndash' ); ?></h1>
				<p><a class="button button-primary" target="_blank" href="http://support.learndash.com/"><?php _e('LearnDash Support', 'learndash') ?></a></p>
				<hr />

				<?php
				global $wpdb, $wp_version;
				?>
				
				<h2><?php _e('Server', 'learndash' ); ?></h2>
				<ul>
					<li><strong><?php _e('PHP Version', 'learndash') ?></strong>: <?php echo phpversion(); ?></li>
					<li><strong><?php _e('MySQL version', 'learndash') ?></strong>: <?php echo $wpdb->db_version(); ?></li>
				</ul>
				
				<h2><?php _e('WordPress', 'learndash' ); ?></h2>
				<ul>
					<li><strong><?php _e('Version', 'learndash') ?></strong>: <?php echo $wp_version; ?></li>
					<li><strong><?php _e('Multisite', 'learndash') ?></strong>: <?php if (is_multisite()) { echo "Yes"; } else { echo "No"; } ?></li>
					<li><strong><?php _e('Site Language', 'learndash') ?></strong>: <?php echo get_locale(); ?></li>
					<li><strong><?php _e('DISABLE_WP_CRON', 'learndash') ?></strong>: <?php if ( defined('DISABLE_WP_CRON')) { DISABLE_WP_CRON; } else { _e('not defined', 'learndash'); } ?></li>
					<li><strong><?php _e('WP_DEBUG', 'learndash') ?></strong>: <?php if ( defined('WP_DEBUG')) { echo WP_DEBUG; } else { echo _e('not defined', 'learndash'); } ?></li>
					<li><strong><?php _e('WP_DEBUG_DISPLAY', 'learndash') ?></strong>: <?php if ( defined('WP_DEBUG_DISPLAY')) { echo WP_DEBUG_DISPLAY; } else { echo _e('not defined', 'learndash'); } ?></li>
					<li><strong><?php _e('WP_DEBUG_LOG', 'learndash') ?></strong>: <?php if ( defined('WP_DEBUG_LOG')) { echo WP_DEBUG_LOG; } else { echo _e('not defined', 'learndash'); } ?></li>
					<li><strong><?php _e('WP_AUTO_UPDATE_CORE', 'learndash') ?></strong>: <?php if ( defined('WP_AUTO_UPDATE_CORE')) { echo WP_AUTO_UPDATE_CORE; } else { echo _e('not defined', 'learndash'); } ?></li>
					<li><strong><?php _e('WP_MAX_MEMORY_LIMIT', 'learndash') ?></strong>: <?php if ( defined('WP_MAX_MEMORY_LIMIT')) { echo WP_MAX_MEMORY_LIMIT; } else { echo _e('not defined', 'learndash'); } ?></li>
					<li><strong><?php _e('WP_MEMORY_LIMIT', 'learndash') ?></strong>: <?php if ( defined('WP_MEMORY_LIMIT')) { echo WP_MEMORY_LIMIT; } else { echo _e('not defined', 'learndash'); } ?></li>
					<li><strong><?php _e('DB_CHARSET', 'learndash') ?></strong>: <?php if ( defined('DB_CHARSET')) { echo DB_CHARSET; } else { echo _e('not defined', 'learndash'); } ?></li>
					<li><strong><?php _e('DB_COLLATE', 'learndash') ?></strong>: <?php if ( defined('DB_COLLATE')) { echo DB_COLLATE; } else { echo _e('not defined', 'learndash'); } ?></li>
				</ul>
				
				<h2><?php _e('Templates', 'learndash' ); ?></h2>
				<?php $template_array = array('course_content_shortcode', 'course_info_shortcode', 'course_list_template', 'course_navigation_admin', 'course_navigation_widget', 'course_progress_widget', 'course', 'lesson', 'profile', 'quiz', 'topic', 'user_groups_shortcode'); ?>
				<ul>
					<?php foreach($template_array as $template) { ?>
						<li><strong><?php echo $template ?></strong>: <?php echo str_replace(ABSPATH, '', SFWD_LMS::get_template( $template, null, null, true )); ?></li>
					<?php } ?>
				</ul>
				
				<?php /* ?>
				<h2><?php _e('Active Theme', 'learndash' ); ?></h2>
				<ul>
				<?php 
					$current_theme =  wp_get_theme(); 
					//echo "current_theme<pre>". print_r($current_theme, true) ."</pre>";
					if ( $current_theme->exists() ) {
						?><li><strong><?php echo $current_theme->get( 'Name' ) ?></strong>: <?php echo $current_theme->get( 'Version' ) ?> ( <?php echo $current_theme->get( 'ThemeURI' ) ?> )</li><?php
					}
				?>
				</ul>
				<?php */ ?>
				<?php /* ?>
				<h2><?php _e('Active Plugins', 'learndash' ); ?></h2>
				<?php 
					$all_plugins = get_plugins(); 
					//echo "all_plugins<pre>". print_r($all_plugins, true) ."</pre>";
					if (!empty( $all_plugins ) ) {
						?><ul><?php
						foreach( $all_plugins as $plugin_key => $plugin_data ) { 
							if (is_plugin_active($plugin_key)) {
								?><li><strong><?php echo $plugin_data['Name'] ?></strong>: <?php echo $plugin_data['Version'] ?> ( <?php echo $plugin_data['PluginURI'] ?> )</li><?php
							}
						}
						?></ul><?php
					}
				?>
				<?php */ ?>
			</div>
			<?php
		}
	}
}
