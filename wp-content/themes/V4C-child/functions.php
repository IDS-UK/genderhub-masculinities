<?php
function v4c_child_theme_enqueue_styles() {

    $parent_style = 'parent-style';

    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style )
    );
}

add_action( 'wp_enqueue_scripts', 'v4c_child_theme_enqueue_styles' );

/* replaces the fixed URL links that are embedded in the V4C theme */
function render_site_menu() {
  $html = '<ul class="left SiteMenu">';
  $html .= '<li><a class="course-link" href="' . home_url('/courses/masculinities') . '">Course</a></li>';
  $html .= '<li><a class="glossary-link" href="' . home_url('/glossary') . '">Glossary</a></li>';
  $html .= '</ul>';
 return $html;
}

/* Disable WordPress Admin Bar for all users but admins. */
  show_admin_bar(false);
  
  
  add_shortcode( 'visitor', 'visitor_check_shortcode' );
  add_shortcode( 'member', 'member_check_shortcode' );
  
 function visitor_check_shortcode( $atts, $content = null ) {
	if ( ( !is_user_logged_in() && !is_null( $content ) ) || is_feed() )
	return $content;
	return '';
} 

function member_check_shortcode( $atts, $content = null ) {
	if ( is_user_logged_in() && !is_null( $content ) && !is_feed() )
	return $content;
	return '';
}

?>