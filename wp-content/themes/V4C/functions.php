<?php
/*
Author: Matters OF The Earth
URL: http://www.mattersoftheearth.com
*/

// Gender Stereotypes - Functions.php

// -------------------------------------------------------------------------------------------------
// FoundationPress setup
// -------------------------------------------------------------------------------------------------

// Various clean up functions
require_once('library/cleanup.php');

// Required for Foundation to work properly
require_once('library/foundation.php');

// Register all navigation menus
require_once('library/navigation.php');

// Add menu walker
require_once('library/menu-walker.php');

// Create widget areas in sidebar and footer
require_once('library/widget-areas.php');

// Return entry meta information for posts
require_once('library/entry-meta.php');

// Enqueue scripts
require_once('library/enqueue-scripts.php');

// Add theme support
require_once('library/theme-support.php');

// -------------------------------------------------------------------------------------------------
// Other Setup
// -------------------------------------------------------------------------------------------------

// Enable Featured Image (For BadgeOS)
// -------------------------------------------------------------------------------------------------

add_theme_support( 'post-thumbnails' );

// Hide content for all Users
// -------------------------------------------------------------------------------------------------

remove_action("admin_color_scheme_picker", "admin_color_scheme_picker");

// Hide content for Subscrbers
// -------------------------------------------------------------------------------------------------

function bp_core_admin_bar__for_theme() {
  echo '';
}

if ( is_user_logged_in() && current_user_is_subscriber() ) {
  remove_action('wp_footer', 'bp_core_admin_bar', 6);
  add_action( 'wp_footer', 'bp_core_admin_bar__for_theme', 8 ); // Buddypress menu bar
}


// -------------------------------------------------------------------------------------------------
// UI Helpers
// -------------------------------------------------------------------------------------------------

// Profile details
// -------------------------------------------------------------------------------------------------

function profile_display_name() {
    $current_user = wp_get_current_user();
    return $current_user->display_name;
}

// -------------------------------------------------------------------------------------------------
// UI Rendering
// -------------------------------------------------------------------------------------------------

// Render sidebar content
// -------------------------------------------------------------------------------------------------

function render_course_sidebar_content() {
  if ( is_user_logged_in() ) {
    echo do_shortcode("[course_content course_id='8']");
    echo do_shortcode("[learndash_course_progress]");
  }
}

// Render the menus used in the top-bar
// -------------------------------------------------------------------------------------------------

//function render_site_menu() {
//  $html = '<ul class="left SiteMenu">';
//  $html .= '<li><a class="course-link" href="' . home_url('/courses/gender-stereotypes/') . '">Course</a></li>';
//  $html .= '<li><a class="glossary-link" href="' . home_url('/glossary') . '">Glossary</a></li>';
//  $html .= '</ul>';
 // return $html;
//}

function render_user_menu() {
  $html = '<ul class="right MembersMenu">';
  if ( is_user_logged_in() ) {
    if ( current_user_is_admin() ){
      $html .= '<li ><a class="admin-link" href="' . admin_url() . '"> Admin </a></li>';
    }
    if ( is_user_logged_in() ) {
      $html .= '<li><a class="members-link" href="' . get_page_link(get_page_by_title( 'Members' )->ID) . '">Course Members</a></li>';
    }
    global $current_user;
    get_currentuserinfo();
//  html .= '<li ><a href="' . bp_loggedin_user_domain() . '">Your Profile: ' . profile_display_name() .'</a></li>';
    $html .= '<li ><a href="' . bp_loggedin_user_domain() . '">Your Profile: ' . $current_user->user_login .'</a></li>';    $html .= '<li class=""><a href="' . wp_logout_url('$index.php') . '">Logout</a></li>';
  } else {
    $html .= '<li class=""><a href="'. wp_login_url().'">Login</a></li>';
  }
  $html .= '</ul>';
  return $html;
}

// Render footer logo-links
// -------------------------------------------------------------------------------------------------

function render_logo_links() {
  $html =  '<a href="http://www.v4c-nigeria.com/">';
  $html .=  '<img src="' . get_bloginfo('template_directory') . '/assets/img/images_voices-of-change-logo.png" alt="Voices Of Change Logo">';
  $html .= '</a>';
  $html .= '<a href="https://www.gov.uk/government/organisations/department-for-international-development">';
  $html .=  '<img src="'  . get_bloginfo('template_directory') .  '/assets/img/images_uk-aid-logo.png" alt="UK Aid Logo">';
  $html .=  '</a>';
  return $html;
}

// -------------------------------------------------------------------------------------------------
// Authorization
// -------------------------------------------------------------------------------------------------

// Check User Role
// Checking whether a user can edit posts is enough.
// -------------------------------------------------------------------------------------------------

function current_user_is_subscriber() {
  return !current_user_can( 'edit_posts' );
}

function current_user_is_admin() {
  return current_user_can( 'edit_posts' );
}

function member_profile_belongs_to_current_user() {
  return bp_is_my_profile();
}

function current_user_can_view_learndash_profile() {
  return (member_profile_belongs_to_current_user() || current_user_is_admin());
}

// Redirect back to homepage and not allow access to WP admin for Subscribers.
// However, there might be admin access via AJAX even for non-admins, so make an exception if it's
// an Ajax request.
// -------------------------------------------------------------------------------------------------

function themeblvd_redirect_admin(){

  if ( !current_user_is_admin() && ( !defined( 'DOING_AJAX' ) || !DOING_AJAX ) ){
    wp_redirect( site_url() );
    exit;
  }
}

add_action( 'admin_init', 'themeblvd_redirect_admin' );

// Login Redirection
// -------------------------------------------------------------------------------------------------

function buddypress_login_redirection_for_non_admins($redirect_to_calculated,$redirect_url_specified,$user)
{

  if ( !is_wp_error($user) ){

    if(empty($redirect_to_calculated))
    {
      $redirect_to_calculated=admin_url();
    }

    if(!user_can($user, 'edit_posts')){
      return bp_core_get_user_domain($user->ID );
    }
  }
  return $redirect_to_calculated; /*if site admin or not logged in,do not do anything much*/
}

add_filter("login_redirect","buddypress_login_redirection_for_non_admins",10,3);


// -------------------------------------------------------------------------------------------------
// User Setup
// -------------------------------------------------------------------------------------------------

// Register User To Course Automatically On Registration
// -------------------------------------------------------------------------------------------------

function register_user_to_course($user_id) {
  $course_id = 8;
  ld_update_course_access($user_id, $course_id, $remove = false);
}

add_action( 'user_register', 'register_user_to_course', 10, 1 );

//
// This filter traps the completion of the last questionaire and passes it to the download pdf's page
//
add_filter ("learndash_completion_redirect", function ($link, $post_id ) {
//Foo\n");
         if ( $post_id == 10799) {
         	$link = site_url()."/pdf-downloads";
        }

         return $link;  
       }, 5, 2);   

// 
// this action adds the functionality of the earned badges to the users profile 
// it displays the LOGGED IN USERS PROFILE so needs a mod!
// iHm - umFundi ltd ian.molesworth@umfundi.com
//

add_action ('bp_after_profile_content','show_badgeos_badges',10,0);

function show_badgeos_badges(){
 //    error_log("Caught after profile content - \n",1,"imolesworth@gmail.com","Subject: Foo\n");


		//user must be logged in to view earned badges and points
		if ( is_user_logged_in() ) {

			echo '<br><br>';
			echo '<div class="GenderHubBranding"> <strong>Your current course credits</strong></div>';
			echo '<br>';
			//display user's points if widget option is enabled
			if ( $instance['point_total'] == 'on' )
				echo '<p class="badgeos-total-points">' . sprintf( __( 'My Total Points: %s', 'badgeos' ), '<strong>' . number_format( badgeos_get_users_points() ) . '</strong>' ) . '</p>';

			$achievements = badgeos_get_user_achievements();

			if ( is_array( $achievements ) && ! empty( $achievements ) ) {

				$number_to_show = absint( $instance['number'] );
				$thecount = 0;

				wp_enqueue_script( 'badgeos-achievements' );
				wp_enqueue_style( 'badgeos-widget' );

				//load widget setting for achievement types to display
				$set_achievements = ( isset( $instance['set_achievements'] ) ) ? $instance['set_achievements'] : '';

				//show most recently earned achievement first
				$achievements = array_reverse( $achievements );

				echo '<ul class="widget-achievements-listing">';
				foreach ( $achievements as $achievement ) {

					//verify achievement type is set to display in the widget settings
					//if $set_achievements is not an array it means nothing is set so show all achievements
					if ( ! is_array( $set_achievements ) || in_array( $achievement->post_type, $set_achievements ) ) {

						//exclude step CPT entries from displaying in the widget
						if ( get_post_type( $achievement->ID ) != 'step' ) {

							$permalink  = get_permalink( $achievement->ID );
							$title      = get_the_title( $achievement->ID );
							$img        = badgeos_get_achievement_post_thumbnail( $achievement->ID, array( 50, 50 ), 'wp-post-image' );
							$thumb      = $img ? '<a style="margin-top: -25px;" class="badgeos-item-thumb" href="'. esc_url( $permalink ) .'">' . $img .'</a>' : '';
							$class      = 'widget-badgeos-item-title';
							$item_class = $thumb ? ' has-thumb' : '';

							// Setup credly data if giveable
							$giveable   = credly_is_achievement_giveable( $achievement->ID, $user_ID );
							$item_class .= $giveable ? ' share-credly addCredly' : '';
							$credly_ID  = $giveable ? 'data-credlyid="'. absint( $achievement->ID ) .'"' : '';

							echo '<li id="widget-achievements-listing-item-'. absint( $achievement->ID ) .'" '. $credly_ID .' class="widget-achievements-listing-item'. esc_attr( $item_class ) .'">';
							echo $thumb;
							echo '<a class="widget-badgeos-item-title '. esc_attr( $class ) .'" href="'. esc_url( $permalink ) .'">'. esc_html( $title ) .'</a>';
							echo '</li>';

							$thecount++;

							if ( $thecount == $number_to_show && $number_to_show != 0 )
								break;

						}

					}
				}

				echo '</ul><!-- widget-achievements-listing -->';

			}

		} else {

			//user is not logged in so display a message
			_e( 'You must be logged in to view earned achievements', 'badgeos' );

		}

   }



?>