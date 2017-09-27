<?php
/**
 * User functions
 *
 * @since 2.1.0
 *
 * @package LearnDash\Users
 */



/**
 *
 * Outputs HTML for courses which the user is enrolled into
 *
 * @since 2.1.0
 *
 * @param  object $user User object
 */
function learndash_show_enrolled_courses( $user ) {
	$courses = get_pages( 'post_type=sfwd-courses' );
	$enrolled = array();
	$notenrolled = array();
	?>
		<table class='form-table'>
			<tr>
				<th> <h3><?php printf( _x( 'Enrolled %s', 'Enrolled Courses Label', 'learndash' ), LearnDash_Custom_Label::get_label( 'courses' ) ); ?></h3></th>
				<td>
					<ol>
					<?php
						foreach ( $courses as $course ) {
							if ( sfwd_lms_has_access( $course->ID,  $user->ID ) ) {
								$since = ld_course_access_from( $course->ID,  $user->ID );
								$since = empty( $since ) ? '' : 'Since: '.date( 'm/d/Y H:i:s', $since );

								if ( empty( $since ) ) {
									$since = learndash_user_group_enrolled_to_course_from( $user->ID, $course->ID );
									$since = empty( $since ) ? '' : 'Since: '.date( 'm/d/Y H:i:s', $since ).' (Group Access)';
								}

								echo "<li><a href='".get_permalink( $course->ID )."'>".$course->post_title."</a> ".$since."</li>";
								$enrolled[] = $course;
							} else {
								$notenrolled[] = $course;
							}
						}
					?>
					</ol>
				</td>
			</tr>

			<?php if ( current_user_can( 'enroll_users' ) ) : ?>
					<tr>
						<th> <h3><?php printf( _x( 'Enroll a %s', 'Enroll a Course Label', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ) ); ?></h3></th>
						<td>
							<select name='enroll_course'>
								<option value=''><?php printf( _x('-- Select a %s --', 'Select a Course Label', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ) ); ?></option>
									<?php foreach ( $notenrolled as $course ) : ?>
										<option value="<?php echo $course->ID; ?>"><?php echo $course->post_title; ?></option>
									<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<th> <h3><?php printf( _x( 'Unenroll a %s', 'Unenroll a Course Label', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ) ); ?></h3></th>
						<td>
								<select name='unenroll_course'>
									<option value=''><?php printf( _x( '-- Select a %s --', 'Select a Course Label', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ) ); ?></option>
									<?php foreach ( $enrolled as $course ) : ?>
										<option value="<?php echo $course->ID; ?>"><?php echo $course->post_title; ?></option>
									<?php endforeach; ?>
								</select>
						</td>
					</tr>
			<?php endif; ?>
		</table>
	<?php
}



/**
 *
 * Saves enrolled courses for a particular user given it's user id.  Returns false on inability to enroll users.
 *
 * @since 2.1.0
 *
 * @param  int $user_id User ID
 * @return false
 */
function learndash_save_enrolled_courses( $user_id ) {
	if ( ! current_user_can( 'enroll_users' ) ) {
		return FALSE;
	}

	$enroll_course = $_POST['enroll_course'];
	$unenroll_course = $_POST['unenroll_course'];

	if ( ! empty( $enroll_course ) ) {
		$meta = ld_update_course_access( $user_id, $enroll_course );
	}

	if ( ! empty( $unenroll_course ) ) {
		$meta = ld_update_course_access( $user_id, $unenroll_course, $remove = true );
	}
}

if ((defined('LEARNDASH_GROUPS_LEGACY_v220') && (LEARNDASH_GROUPS_LEGACY_v220 === true))) {

	add_action( 'show_user_profile', 'learndash_show_enrolled_courses' );
	add_action( 'edit_user_profile', 'learndash_show_enrolled_courses' );

	add_action( 'personal_options_update', 'learndash_save_enrolled_courses' );
	add_action( 'edit_user_profile_update', 'learndash_save_enrolled_courses' );
}


/**
 * Output link to delete course data for user
 *
 * @since 2.1.0
 * 
 * @param  object $user WP_User object
 */
function learndash_delete_user_data_link( $user ) {
	if ( ! current_user_can( 'manage_options' ) ) {
		return '';
	}

	?>
		<div id="learndash_delete_user_data">
			<h2><?php printf( _x( 'Permanently Delete %s Data', 'Permanently Delete Course Data Label', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ) ); ?></h2>
			<p><input type="checkbox" id="learndash_delete_user_data" name="learndash_delete_user_data" value="<?php echo $user->ID; ?>"> <label for="learndash_delete_user_data"><?php _e( 'Check and click update profile to permanently delete user\'s LearnDash course data. <strong>This cannot be undone.</strong>', 'learndash' ); ?></label></p>
		</div>
	<?php
}

add_action( 'show_user_profile', 'learndash_delete_user_data_link', 1000, 1 );
add_action( 'edit_user_profile', 'learndash_delete_user_data_link', 1000, 1 );
add_action( 'nss_license_footer','learndash_delete_user_data_link' );



/**
 * Delete user data
 * 
 * @since 2.1.0
 * 
 * @param  int $user_id
 */
function learndash_delete_user_data( $user_id ) {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$user = get_user_by( 'id', $user_id );

	if ( ! empty( $user->ID ) && ! empty( $_POST['learndash_delete_user_data'] ) && $user->ID == $_POST['learndash_delete_user_data'] ) {
		global $wpdb;
		$ref_ids = $wpdb->get_col( $wpdb->prepare( 'SELECT statistic_ref_id FROM '.$wpdb->prefix."wp_pro_quiz_statistic_ref WHERE  user_id = '%d' ", $user->ID ) );

		if ( ! empty( $ref_ids[0] ) ) {
			$wpdb->delete( $wpdb->prefix.'wp_pro_quiz_statistic_ref', array( 'user_id' => $user->ID ) );
			$wpdb->query( 'DELETE FROM '.$wpdb->prefix.'wp_pro_quiz_statistic WHERE statistic_ref_id IN ('.implode( ',', $ref_ids ).')' );
		}

		$wpdb->delete( $wpdb->usermeta, array( 'meta_key' => '_sfwd-quizzes', 'user_id' => $user->ID ) );
		$wpdb->delete( $wpdb->usermeta, array( 'meta_key' => '_sfwd-course_progress', 'user_id' => $user->ID ) );
		$wpdb->query( 'DELETE FROM '.$wpdb->usermeta." WHERE meta_key LIKE 'completed_%' AND user_id = '".$user->ID."'" );
		$wpdb->query( 'DELETE FROM '.$wpdb->usermeta." WHERE meta_key LIKE 'course_%_access_from' AND user_id = '".$user->ID."'" );
		$wpdb->query( 'DELETE FROM '.$wpdb->usermeta." WHERE meta_key LIKE 'course_completed_%' AND user_id = '".$user->ID."'" );
		$wpdb->query( 'DELETE FROM '.$wpdb->usermeta." WHERE meta_key LIKE 'learndash_course_expired_%' AND user_id = '".$user->ID."'" );
		
		$wpdb->delete( $wpdb->prefix.'wp_pro_quiz_lock', array( 'user_id' => $user->ID ) );
		$wpdb->delete( $wpdb->prefix.'wp_pro_quiz_toplist', array( 'user_id' => $user->ID ) );

		// Move user uploaded Assignements to Trash.
		$user_assignements_query_args = array(
			'post_type'		=>	'sfwd-assignment',
			'post_status'	=>	'publish',
			'nopaging'		=>	true,
			'author' 		=> 	$user->ID
		);
		
		$user_assignements_query = new WP_Query( $user_assignements_query_args );
		//error_log('user_assignements_query<pre>'. print_r($user_assignements_query, true) .'</pre>');
		if ( $user_assignements_query->have_posts() ) {
			
			foreach( $user_assignements_query->posts as $assignment_post ) {
				wp_trash_post( $assignment_post->ID );
			}
		}
		wp_reset_postdata();


		// Move user uploaded Essay to Trash.
		$user_essays_query_args = array(
			'post_type'		=>	'sfwd-essays',
			'post_status'	=>	'any',
			'nopaging'		=>	true,
			'author' 		=> 	$user->ID
		);
		
		$user_essays_query = new WP_Query( $user_essays_query_args );
		//error_log('user_essays_query<pre>'. print_r($user_essays_query, true) .'</pre>');
		if ( $user_essays_query->have_posts() ) {
			
			foreach( $user_essays_query->posts as $essay_post ) {
				wp_trash_post( $essay_post->ID );
			}
		}
		wp_reset_postdata();
	}	
}

add_action( 'personal_options_update', 'learndash_delete_user_data' );
add_action( 'edit_user_profile_update', 'learndash_delete_user_data' );


/**
 * Get all Courses enrolled by User
 * 
 * @since 2.2.1
 * 
 * @param  int $user_id
 */
function learndash_user_get_enrolled_courses( $user_id = 0, $bypass_transient = false ) {
	global $wpdb;
	
	$enrolled_courses_ids = array();

	$transient_key = "learndash_user_courses_" . $user_id;

	if (!$bypass_transient) {
		$enrolled_courses_ids_transient = get_transient( $transient_key );
		//error_log('from transient: ['. $transient_key .']');
	} else {
		$enrolled_courses_ids_transient = false;
	}

	if ( $enrolled_courses_ids_transient === false ) {
	
		// Atypical meta_value set looks like:
		// a:10:{s:29:"sfwd-courses_course_materials";s:15:"Course Material";s:30:"sfwd-courses_course_price_type";s:4:"open";s:30:"sfwd-courses_custom_button_url";s:0:"";s:25:"sfwd-courses_course_price";s:0:"";s:31:"sfwd-courses_course_access_list";s:50:"1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20";s:34:"sfwd-courses_course_lesson_orderby";s:0:"";s:32:"sfwd-courses_course_lesson_order";s:0:"";s:32:"sfwd-courses_course_prerequisite";s:1:"0";s:31:"sfwd-courses_expire_access_days";s:0:"";s:24:"sfwd-courses_certificate";s:1:"6";}
		
		// First we try some magic. We attempt to query those course that have:
		// price type == 'open' or 'paynow'
		$course_price_type = "'s:30:\"sfwd-courses_course_price_type\";s:4:\"open\"'|'s:30:\"sfwd-courses_course_price_type\";s:6:\"paynow\"'";
		
		// OR the access list is not empty
		$not_like = "'s:31:\"sfwd-courses_course_access_list\";s:0:\"\";'";
		
		// OR the user ID is found in the access list. Note this pattern is four options
		// 1. The user ID is the only entry. 
		// 2. The user ID is at the front of the list as in "sfwd-courses_course_access_list";*:"X,*";
		// 3. The user ID is in middle "sfwd-courses_course_access_list";*:"*,X,*";
		// 4. The user ID is at the end "sfwd-courses_course_access_list";*:"*,X";
		$is_like = "
			's:31:\"sfwd-courses_course_access_list\";i:". $user_id .";'|
			's:31:\"sfwd-courses_course_access_list\";s:(.*):\"". $user_id .",(.*)\";'|
			's:31:\"sfwd-courses_course_access_list\";s:(.*):\"(.*),". $user_id .",(.*)\";'|
			's:31:\"sfwd-courses_course_access_list\";s:(.*):\"(.*),". $user_id ."\";'";


		$sql_str = "SELECT post_id FROM ". $wpdb->prefix ."postmeta WHERE meta_key='_sfwd-courses' AND (meta_value REGEXP ". $course_price_type ."	OR (meta_value NOT REGEXP ". $not_like ." AND meta_value REGEXP ". $is_like ."))";
		//error_log('sql_str['. $sql_str .']');
		
		$user_course_ids = $wpdb->get_col( $sql_str );
		//error_log('user_course_ids<pre>'. print_r($user_course_ids, true) .'</pre>');
		
		// Next we grap all the groups the user is a member of
		$users_groups = learndash_get_users_group_ids( $user_id );
		//error_log('users_groups<pre>'. print_r($users_groups, true) .'</pre>');
		
		$potential_course_ids = array_merge($user_course_ids, $users_groups);
		//error_log('potential_course_ids<pre>'. print_r($potential_course_ids, true) .'</pre>');
		
		// Instead of just getting ALL course IDs we settup a loop to grab batches (2000 per page) of Courses. 
		// This means if the site has 30k Courses we are not attempting to load all these in memory at once. 
		
		if ( !empty( $potential_course_ids ) ) {
		
			$course_query_args = array(
				'post_type'			=>	'sfwd-courses',
				'paged'				=>	1,
				'posts_per_page'	=>	2000,
				'fields'			=>	'ids',
				'post__in'			=>	$potential_course_ids
			);
		
			while( true ) {
				//error_log('course_query_args<pre>'. print_r($course_query_args, true) .'</pre>');
				$course_query = new WP_Query( $course_query_args );
				//error_log('course_query<pre>'. print_r($course_query->posts, true) .'</pre>');
	
				if ( ( isset( $course_query->posts ) ) && ( !empty( $course_query->posts ) ) ) {

					foreach ( $course_query->posts as $course_id ) {
						if ( sfwd_lms_has_access( $course_id,  $user_id ) ) {
							$enrolled_courses_ids[] = $course_id;
						}
					}
				
					$course_query_args['paged'] = intval($course_query_args['paged']) + 1;
				} else {
					break;
				}
			}	
		}
		set_transient( $transient_key, $enrolled_courses_ids, MINUTE_IN_SECONDS );
		//error_log('enrolled_courses_ids count['. count($enrolled_courses_ids) .']');
	} else {
		$enrolled_courses_ids = $enrolled_courses_ids_transient;
	}
	//error_log('enrolled_courses_ids<pre>'. print_r($enrolled_courses_ids, true) .'</pre> gettype['. gettype($enrolled_courses_ids) .']');
	
	return $enrolled_courses_ids;
}

function learndash_user_set_enrolled_courses( $user_id = 0, $user_courses_new = array() ) {

	if (!empty( $user_id )) {

		$user_courses_old = learndash_user_get_enrolled_courses( $user_id, true );
		if ((empty($user_courses_old)) && (!is_array($user_courses_old))) {
			$user_courses_old = array();
		}
		$user_courses_intersect = array_intersect( $user_courses_new, $user_courses_old );

		$user_courses_add = array_diff( $user_courses_new, $user_courses_intersect );
		if ( !empty( $user_courses_add ) ) {
			foreach ( $user_courses_add as $course_id ) {
				//update_post_meta( $course_id, 'learndash_group_enrolled_' . $group_id, $group_id );
				ld_update_course_access( $user_id, $course_id);
			}
		}
		$user_courses_remove = array_diff( $user_courses_old, $user_courses_intersect );
		if ( !empty( $user_courses_remove ) ) {
			foreach ( $user_courses_remove as $course_id ) {
				//update_post_meta( $course_id, 'learndash_group_enrolled_' . $group_id, $group_id );
				ld_update_course_access( $user_id, $course_id, true);
			}
		}
		
		// Finally clear our cache for other services 
		$transient_key = "learndash_user_courses_" . $user_id;
		delete_transient( $transient_key );
	}
}
