<?php

if (!class_exists('Learndash_Admin_User_Profile_Edit')) {
	class Learndash_Admin_User_Profile_Edit {
		
		function __construct() {
			// Hook into the on-load action for our post_type editor
			add_action( 'load-profile.php', 		array( $this, 'on_load_user_profile') );
			add_action( 'load-user-edit.php', 		array( $this, 'on_load_user_profile') );

			add_action( 'show_user_profile', 		array( $this, 'show_user_profile') );
			add_action( 'edit_user_profile', 		array( $this, 'show_user_profile') );

			// The action priority was added in v2.2.1.2 to resolve a conflict with other add-ons like
			// PMPro also effecting the user course selection/association
			add_action( 'personal_options_update',  array( $this, 'save_user_profile' ), 1 );
			add_action( 'edit_user_profile_update', array( $this, 'save_user_profile' ), 1 );

		}
		
		function on_load_user_profile() {

			wp_enqueue_script( 
				'learndash-admin-binary-selector-script', 
				LEARNDASH_LMS_PLUGIN_URL . 'assets/js/learndash-admin-binary-selector'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.js', 
				array( 'jquery' ),
				LEARNDASH_VERSION,
				true
			);

			wp_enqueue_style( 
				'learndash-admin-binary-selector-style', 
				LEARNDASH_LMS_PLUGIN_URL . 'assets/css/learndash-admin-binary-selector'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.css', 
				array( ),
				LEARNDASH_VERSION
			);
		}
		
		function show_user_profile( $user ) {
			$this->show_user_courses( $user );
			$this->show_user_groups( $user );
			$this->show_leader_groups( $user );
		}
		
		function save_user_profile( $user_id ) {

			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}
			
			if ( ( isset( $_POST['learndash_user_courses'] ) ) && ( isset( $_POST['learndash_user_courses'][$user_id] ) ) && ( !empty( $_POST['learndash_user_courses'][$user_id] ) ) ) {
				if ( ( isset( $_POST['learndash_user_courses-'. $user_id .'-nonce'] ) ) && ( !empty( $_POST['learndash_user_courses-'. $user_id .'-nonce'] ) ) ) {
					if (wp_verify_nonce( $_POST['learndash_user_courses-'. $user_id .'-nonce'], 'learndash_user_courses-'.$user_id )) {
						$user_courses = (array)json_decode( stripslashes( $_POST['learndash_user_courses'][$user_id] ) );
						learndash_user_set_enrolled_courses( $user_id, $user_courses );
					}
				}
			}

			if ( ( isset( $_POST['learndash_user_groups'] ) ) && ( isset( $_POST['learndash_user_groups'][$user_id] ) ) && ( !empty( $_POST['learndash_user_groups'][$user_id] ) ) ) {
				if ( ( isset( $_POST['learndash_user_groups-'. $user_id .'-nonce'] ) ) && ( !empty( $_POST['learndash_user_groups-'. $user_id .'-nonce'] ) ) ) {
					if (wp_verify_nonce( $_POST['learndash_user_groups-'. $user_id .'-nonce'], 'learndash_user_groups-'.$user_id )) {

						$user_groups = (array)json_decode( stripslashes( $_POST['learndash_user_groups'][$user_id] ) );
						learndash_set_users_group_ids( $user_id, $user_groups );
					}
				}
			}

			if ( ( isset( $_POST['learndash_leader_groups'] ) ) && ( isset( $_POST['learndash_leader_groups'][$user_id] ) ) && ( !empty( $_POST['learndash_leader_groups'][$user_id] ) ) ) {
				if ( ( isset( $_POST['learndash_leader_groups-'. $user_id .'-nonce'] ) ) && ( !empty( $_POST['learndash_leader_groups-'. $user_id .'-nonce'] ) ) ) {
					if (wp_verify_nonce( $_POST['learndash_leader_groups-'. $user_id .'-nonce'], 'learndash_leader_groups-'.$user_id )) {
						$user_groups = (array)json_decode( stripslashes( $_POST['learndash_leader_groups'][$user_id] ) );
						learndash_set_administrators_group_ids( $user_id, $user_groups );
					}
				}
			}
			
			if ( ( isset( $_POST['user_progress'] ) ) && ( isset( $_POST['user_progress'][$user_id] ) ) && ( !empty( $_POST['user_progress'][$user_id] ) ) ) {
				if ( ( isset( $_POST['user_progress-'. $user_id .'-nonce'] ) ) && ( !empty( $_POST['user_progress-'. $user_id .'-nonce'] ) ) ) {
					if (wp_verify_nonce( $_POST['user_progress-'. $user_id .'-nonce'], 'user_progress-'.$user_id )) {
						$user_progress = (array)json_decode( stripslashes( $_POST['user_progress'][$user_id] ) );
						$user_progress = json_decode(json_encode($user_progress), true);

						$processed_course_ids = array();

						if ( ( isset( $user_progress['course'] ) ) && ( !empty( $user_progress['course'] ) ) ) {
							
							$usermeta = get_user_meta( $user_id, '_sfwd-course_progress', true );
							$course_progress = empty( $usermeta ) ? array() : $usermeta;
							
							$_COURSE_CHANGED = false; // Simple flag to let us know we changed the quiz data so we can save it back to user meta.
							
							foreach($user_progress['course'] as $course_id => $course_data ) {
							
								$processed_course_ids[] = $course_id;
								
								$course_progress[$course_id] = $course_data;
								$_COURSE_CHANGED = true;	
							}

							if ( $_COURSE_CHANGED === true )
								update_user_meta( $user_id, '_sfwd-course_progress', $course_progress );
						}

						if ( ( isset( $user_progress['quiz'] ) ) && ( !empty( $user_progress['quiz'] ) ) ) {
							
							$usermeta = get_user_meta( $user_id, '_sfwd-quizzes', true );
							$quizz_progress = empty( $usermeta ) ? array() : $usermeta;
							$_QUIZ_CHANGED = false; // Simple flag to let us know we changed the quiz data so we can save it back to user meta.
							
							foreach( $user_progress['quiz'] as $quiz_id => $quiz_new_status ) {
								$quiz_meta = get_post_meta( $quiz_id, '_sfwd-quiz', true);
								//error_log('quiz_meta<pre>'. print_r($quiz_meta, true) .'</pre>');
								
								if (!empty($quiz_meta)) {
									$quiz_old_status = !learndash_is_quiz_notcomplete( $user_id, array( $quiz_id => 1 ) );
								
									if ($quiz_new_status == true) {
										if ($quiz_old_status != true) {
											
											// If the admin is marking the quiz complete AND the quiz is NOT already complete...
											// Then we add the minimal quiz data to the user profile
											$quizdata = array(
												'quiz' 					=> 	$quiz_id,
												'score' 				=> 	0,
												'count' 				=> 	0,
												'pass' 					=> 	true,
												'rank' 					=> 	'-',
												'time' 					=> 	time(),
												'pro_quizid' 			=> 	$quiz_meta['sfwd-quiz_quiz_pro'],
												'points' 				=> 	0,
												'total_points' 			=> 	0,
												'percentage' 			=> 	0,
												'timespent' 			=> 	0,
												'has_graded'   			=> 	false,
												'statistic_ref_id' 		=> 	0,
												'm_edit_by'				=>	get_current_user_id(),	// Manual Edit By ID
												'm_edit_time'			=>	time()			// Manual Edit timestamp
											);
											
											$quizz_progress[] = $quizdata;
											$_QUIZ_CHANGED = true;

										}
									} else if ($quiz_new_status != true) {
										if ($quiz_old_status == true) {

											if (!empty($quizz_progress)) {
												foreach($quizz_progress as $quiz_idx => $quiz_item) {
													if (($quiz_item['quiz'] == $quiz_id) && ($quiz_item['pass'] == true)) {
														$quizz_progress[$quiz_idx]['pass'] = false;
														$_QUIZ_CHANGED = true;
													}
												}
											}
										}
									}
									
									$course_id = learndash_get_course_id( $quiz_id );
									if ( !empty( $course_id ) ) {
										$processed_course_ids[] = $course_id;
										
									}
								}
							}
							
							if ($_QUIZ_CHANGED == true) {
								$ret = update_user_meta( $user_id, '_sfwd-quizzes', $quizz_progress );
							}
						}
						
						if (!empty( $processed_course_ids ) ) {
							foreach( $processed_course_ids as $course_id ) {
								learndash_process_mark_complete( $user_id, $course_id);								
							}
						}
					}
				}
			}
			
		}
		
		function show_user_courses( $user ) {
			if (current_user_can('manage_options')) {
				if ( !user_can( $user->ID, 'manage_options' ) ) {
					
					$ld_binary_selector_user_courses = new Learndash_Binary_Selector_User_Courses(
						array(
							'user_id'				=>	$user->ID,
							'selected_ids'			=>	learndash_user_get_enrolled_courses( $user->ID, true ),
							'search_posts_per_page' => 100
						)
					);
					$ld_binary_selector_user_courses->show();
				} else {
					?>
					<h3><?php echo sprintf( _x('User Enrolled %s', 'User Enrolled Courses', 'learndash'), LearnDash_Custom_Label::get_label( 'courses' ) )  ?></h3>
					<p><?php _e('Administrators are automatically enrolled in all Courses.', 'learndash') ?></p>
					<?php
				}
			}
		}

		function show_user_groups( $user ) {
			if (current_user_can('manage_options')) {
				$ld_binary_selector_user_groups = new Learndash_Binary_Selector_User_Groups(
					array(
						'user_id'				=>	$user->ID,
						'selected_ids'			=>	learndash_get_users_group_ids( $user->ID, true ),
						'search_posts_per_page' => 100
					)
				);
				$ld_binary_selector_user_groups->show();
			}
		}

		function show_leader_groups( $user ) {
			if (current_user_can('manage_options')) {
				if ( user_can( $user->ID, 'group_leader' ) ) {
					$ld_binary_selector_leader_groups = new Learndash_Binary_Selector_Leader_Groups(
						array(
							'user_id'				=>	$user->ID,
							'selected_ids'			=>	learndash_get_administrators_group_ids( $user->ID, true ),
							'search_posts_per_page' => 100
						)
					);
					$ld_binary_selector_leader_groups->show();
				}
			}
		}

		// End of functions
	}
}
