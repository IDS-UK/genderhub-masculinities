<?php
/**
 * Adds ability to have "Essay / Open Answer" questions in Wp Pro Quiz
 *
 * @since 2.2.0
 *
 * @package LearnDash\Essay
 */



/**
 * Register Essay Post Type
 *
 * @since 2.2.0
 *
 * Holds the responses of user submitted essay questions
 */
function learndash_register_essay_post_type() {
	
	$labels = array(
		'name'               => _x( 'Submitted Essays', 'Post Type General Name', 'learndash' ),
		'singular_name'      => _x( 'Submitted Essay', 'Post Type Singular Name', 'learndash' ),
		'menu_name'          => __( 'Submitted Essays', 'learndash' ),
		'name_admin_bar'     => __( 'Submitted Essays', 'learndash' ),
		'parent_item_colon'  => __( 'Parent Submitted Essay:', 'learndash' ),
		'all_items'          => __( 'All Submitted Essays', 'learndash' ),
		'add_new_item'       => __( 'Add New Submitted Essay', 'learndash' ),
		'add_new'            => __( 'Add New', 'learndash' ),
		'new_item'           => __( 'New Submitted Essay', 'learndash' ),
		'edit_item'          => __( 'Edit Submitted Essay', 'learndash' ),
		'update_item'        => __( 'Update Submitted Essay', 'learndash' ),
		'view_item'          => __( 'View Submitted Essay', 'learndash' ),
		'search_items'       => __( 'Search Submitted Essays', 'learndash' ),
		'not_found'          => __( 'Not found', 'learndash' ),
		'not_found_in_trash' => __( 'Not found in Trash', 'learndash' ),
	);

	$capabilities = array(
		'edit_essay'          => 'edit_essay',
		'read_essay'          => 'read_essay',
		'delete_essay'        => 'delete_essay',
		'edit_essays'         => 'edit_essays',
		'edit_others_essays'  => 'edit_others_essays',
		'publish_essays'      => 'publish_essays',
		'read_private_essays' => 'read_private_essays',
	);

	$args = array(
		'label'               => __( 'sfwd-essays', 'learndash' ),
		'description'         => __( 'Submitted essays via a quiz question.', 'learndash' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'comments', 'author'),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => false,
		'query_var' 		  => true,
		'rewrite' 			  => array( 'slug' => 'essay' ), 
		'menu_position'       => 5,
		'show_in_admin_bar'   => false,
		'show_in_nav_menus'   => false,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => true,
		'publicly_queryable'  => true,
		'capability_type'     => 'essay',
		'capabilities'        => $capabilities,
		'map_meta_cap'        => true,
	);

	register_post_type( 'sfwd-essays', $args );
	
	/*
	$labels = array(
		'name' 					=> 	__( 'Submitted Essays', 'learndash' ), 
		'singular_name' 		=> 	__( 'Submitted Essay', 'learndash' ), 
		'edit_item' 			=> 	__( 'Edit Submitted Essay', 'learndash' ), 
		'view_item' 			=> 	__( 'View Submitted Essay', 'learndash' ), 
		'search_items' 			=> 	__( 'Search Submitted Essays', 'learndash' ), 
		'not_found' 			=> 	__( 'No Submitted Essay found', 'learndash' ), 
		'not_found_in_trash' 	=> 	__( 'No Submitted Essay found in Trash', 'learndash' ), 
		'parent_item_colon' 	=> 	__( 'Parent:', 'learndash' ), 
		'menu_name' 			=> 	__( 'Submitted Essays', 'learndash' ),
	);

	$args = array(
		'labels' 				=> 	$labels, 
		'hierarchical' 			=> 	false, 
		'supports' 				=> 	array( 'title', 'editor', 'comments', 'author' ), 
		'public' 				=> 	true, 
		'show_ui' 				=> 	true, 
		'show_in_menu' 			=> 	false, 
		'show_in_nav_menus' 	=> 	false, 
		'publicly_queryable' 	=> 	true, 
		'exclude_from_search' 	=> 	true, 
		'has_archive' 			=> 	true, 
		'query_var' 			=> 	true,
		'rewrite' 				=> 	array( 'slug' => 'essay' ), 
		'capability_type' 		=> 	'essay', 
		'capabilities' 			=> 	array( 
										'edit_post'          => 'edit_essay', 
										'read_post'          => 'read_essay', 
										'delete_post'        => 'delete_book', 
										'edit_posts'         => 'edit_essays', 
										'edit_others_posts'  => 'edit_others_essays', 
										'publish_posts'      => 'publish_essays',       
										'read_private_posts' => 'read_private_essays', 
										'create_posts'       => 'edit_essays', 
										
									), 
		'map_meta_cap' => true,
		
	);

	register_post_type( 'sfwd-essays', $args );	
	*/
}

add_action( 'init', 'learndash_register_essay_post_type' );



/**
 * Essay post type capabilities
 *
 * Add essay capabilities to administrators and group leaders
 *
 * @since 2.2.0
 */
function learndash_add_essay_caps() {
	$admin_role = get_role( 'administrator' );
	if ( ( $admin_role ) && ( $admin_role instanceof WP_Role ) ) {
	
		$cap  = $admin_role->has_cap( 'delete_others_essays' );
		if ( empty( $cap ) ) {
			$admin_role->add_cap( 'edit_essays' );
			$admin_role->add_cap( 'edit_others_essays' );
			$admin_role->add_cap( 'publish_essays' );
			$admin_role->add_cap( 'read_essays' );
			$admin_role->add_cap( 'read_private_essays' );
			$admin_role->add_cap( 'delete_essays' );
			$admin_role->add_cap( 'edit_published_essays' );
			$admin_role->add_cap( 'delete_others_essays' );
			$admin_role->add_cap( 'delete_published_essays' );
		}
	}
	
	$group_leader_role = get_role( 'group_leader' );
	if ( ( $group_leader_role ) && ( $group_leader_role instanceof WP_Role ) ) {
		$group_leader_role->add_cap( 'edit_essays' );
		$group_leader_role->add_cap( 'edit_others_essays' );
		$group_leader_role->add_cap( 'publish_essays' );
		$group_leader_role->add_cap( 'read_essays' );
		$group_leader_role->add_cap( 'read_private_essays' );
		$group_leader_role->add_cap( 'delete_essays' );
		$group_leader_role->add_cap( 'edit_published_essays' );
		$group_leader_role->add_cap( 'delete_others_essays' );
		$group_leader_role->add_cap( 'delete_published_essays' );
	}
}

add_action( 'admin_init', 'learndash_add_essay_caps' );



/**
 * Map meta capabilities
 *
 * @since 2.2.0
 *
 * @param $caps
 * @param $cap
 * @param $user_id
 * @param $args
 *
 * @return array
 */
function learndash_map_metacap_essays( $caps, $cap, $user_id, $args ) {

	/* If editing, deleting, or reading a essays, get the post and post type object. */
	if ( 'edit_essay' == $cap || 'delete_essay' == $cap || 'read_essay' == $cap ) {

		$post      = get_post( $args[0] );
		if ( ! is_a( $post, 'WP_Post' ) ) {
			return $caps;
		}

		$post_type = get_post_type_object( $post->post_type );

		/* Set an empty array for the caps. */
		$caps = array();
	}

	/* If editing a essay, assign the required capability. */
	if ( 'edit_essay' == $cap ) {
		if ( $user_id == $post->post_author ) {
			$caps[] = $post_type->cap->edit_posts;
		} else {
			$caps[] = $post_type->cap->edit_others_posts;
		}
	} /* If deleting a essay, assign the required capability. */
	elseif ( 'delete_essay' == $cap ) {
		if ( $user_id == $post->post_author ) {
			$caps[] = $post_type->cap->delete_posts;
		} else {
			$caps[] = $post_type->cap->delete_others_posts;
		}
	} /* If reading a private essay, assign the required capability. */
	elseif ( 'read_essay' == $cap ) {

		if ( 'private' != $post->post_status ) {
			$caps[] = 'read';
		} elseif ( $user_id == $post->post_author ) {
			$caps[] = 'read';
		} else {
			$caps[] = $post_type->cap->read_private_posts;
		}
	}

	/* Return the capabilities required by the user. */

	return $caps;
}

add_filter( 'map_meta_cap', 'learndash_map_metacap_essays', 10, 4 );



/**
 * Create 'Graded' and 'Not Graded' post status
 *
 * @since 2.2.0
 */
function learndash_register_essay_post_status() {
	register_post_status( 'graded', array(
		'label'                     => _x( 'Graded', 'Custom Essay post type status: Graded', 'learndash' ),
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Graded <span class="count">(%s)</span>', 'Graded <span class="count">(%s)</span>' ),
	) );

	register_post_status( 'not_graded', array(
		'label'                     => _x( 'Not Graded', 'Custom Essay post type status: Not Graded', 'learndash' ),
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Not Graded <span class="count">(%s)</span>', 'Not Graded <span class="count">(%s)</span>' ),
	) );
}

add_action( 'init', 'learndash_register_essay_post_status' );


/*
add_filter( 'the_content', 'the_content_essays', 1000 );

function the_content_essays( $content = '' ) {
	if (is_singular( 'sfwd-essays' ) ) {
		 $queried_object = get_queried_object();
		 error_log('queried_object<pre>'. print_r($queried_object, true) .'</pre>');
		 
		 $post_status_object = get_post_status_object($queried_object->post_status);
		 error_log('post_status_object<pre>'. print_r($post_status_object, true) .'</pre>');
	}
	
	return $content;	
}
*/

/**
 * Only allow admins, group leaders, and essay owners to see assignment
 * 
 * @since 2.1.0
 */
function learndash_essay_permissions() {
	global $post;

	if ( ! empty( $post->post_type ) && $post->post_type == 'sfwd-essays' && is_singular() ) {
		$user_id = get_current_user_id();

		if ( current_user_can( 'manage_options' ) ) {
			return;
		}
		
		if ( $post->post_author == $user_id ) {
			return;
		} else {
			wp_redirect( get_bloginfo( 'url' ) );
			exit;
		}
	}
}

add_action( 'wp', 'learndash_essay_permissions' ); //, 0, 3 );



/**
 * Populate Essay post type columns in the admin
 *
 * Columns are can be filtered by Quiz, Lesson, and Course
 *
 * @since 2.2.0
 *
 * @param $column
 * @param $post_id
 */
function learndash_populate_essay_cpt_columns( $column, $post_id ) {
	
	$essay = get_post( $post_id );
	switch ( $column ) {
		case 'quiz':
			$quiz_id = get_post_meta( $post_id, 'quiz_id', true );
			if ( !empty( $quiz_id ) ) {
				$quizMapper = new WpProQuiz_Model_QuizMapper();
				$quiz       = $quizMapper->fetch( $quiz_id );
				if ( ( !empty( $quiz ) ) && ( $quiz instanceof WpProQuiz_Model_Quiz ) ) {
					echo sprintf( '<a href="edit.php?post_type=sfwd-essays&quiz_id=%s">%s</a>', $quiz_id, $quiz->getName() );
				}
			}
			break;

		case 'lesson':
			$lesson_id = get_post_meta( $post_id, 'lesson_id', true );
			if ( !empty( $lesson_id ) ) {
				$lesson = get_post( $lesson_id );
				if ( ( !empty( $lesson ) ) && ( $lesson instanceof WP_Post ) ) {
					echo sprintf( '<a href="edit.php?post_type=sfwd-essays&lesson_id=%s">%s</a>', $lesson_id, $lesson->post_title );
				}
			}
			break;

		case 'course':
			$course_id = get_post_meta( $post_id, 'course_id', true );
			if ( !empty( $course_id ) ) {
				$course = get_post( $course_id );
				if ( ( !empty( $course ) ) && ( $course instanceof WP_Post ) ) {
					echo sprintf( '<a href="edit.php?post_type=sfwd-essays&course_id=%s">%s</a>', $course_id, $course->post_title );
				}
			}
			break;
	}
}

add_action( 'manage_sfwd-essays_posts_custom_column', 'learndash_populate_essay_cpt_columns', 10, 2 );



/**
 * Adjust Essay post type query in admin
 *
 * Essay query should only include essays with a 'graded' and 'not_graded' post status
 *
 * @since 2.2.0
 *
 * @param $essay_query
 */
function learndash_modify_admin_essay_listing_query( $essay_query ) {
	if ( is_admin() && $essay_query->is_main_query() && 'sfwd-essays' == $essay_query->query['post_type'] && ( ( ! isset( $_GET['post_status'] ) ) || ( isset( $_GET['post_status'] ) && 'all' == $_GET['post_status'] ) ) ) {
		$essay_query->set( 'post_status', array( 'graded', 'not_graded' ) );
	}
}

add_action( 'pre_get_posts', 'learndash_modify_admin_essay_listing_query' );



/**
 * Add a new essay response
 *
 * Called from LD_QuizPro::checkAnswers via AJAX
 *
 * @since 2.2.0
 *
 * @param string						$response
 * @param WpProQuiz_Model_Question		$this_question
 * @param WpProQuiz_Model_Quiz			$quiz
 *
 * @return bool|int|WP_Error
 */
function learndash_add_new_essay_response( $response, $this_question, $quiz ) {
	if ( ! is_a( $this_question, 'WpProQuiz_Model_Question' ) || ! is_a ( $quiz, 'WpProQuiz_Model_Quiz' ) ) {
		return false;
	}

	$user = wp_get_current_user();

	// essay args defaults
	$essay_args = array(
		'post_title'   => $this_question->getTitle(),
		'post_status'  => 'not_graded',
		'post_type'    => 'sfwd-essays',
		'post_author'  => $user->ID,
	);

	$essay_data = $this_question->getAnswerData();
	$essay_data = array_shift( $essay_data );

	// switch on grading progression in order to set post status
	switch ( $essay_data->getGradingProgression() ) {
		case 'not-graded-none':
			$essay_args['post_status'] = 'not_graded';
			break;
		case 'not-graded-full':
			$essay_args['post_status'] = 'not_graded';
			break;
		case 'graded-full' :
			$essay_args['post_status'] = 'graded';
			break;
	}

	// switch on graded type to handle the response
	// used a switch in case we add more types
	switch( $essay_data->getGradedType() ) {
		case 'text' :
			$essay_args['post_content'] = wp_kses( 
				$response, 
				apply_filters('learndash_essay_new_allowed_html', wp_kses_allowed_html( 'post' ) ) 
			);
			break;
		case 'upload' :
			$essay_args['post_content'] = __( 'See upload below.', 'learndash' );
	}

	/**
	 * Filter a new essays arguments
	 */
	apply_filters( 'learndash_new_essay_submission_args', $essay_args );
	$essay_id = wp_insert_post( $essay_args );

	if ( ! empty( $essay_id ) ) {
		$quiz_id = learndash_get_quiz_id_by_pro_quiz_id(  $this_question->getQuizId() );
		$course_id = learndash_get_course_id( $quiz_id );
		$lesson_id = learndash_get_lesson_id( $quiz_id );

		update_post_meta( $essay_id, 'question_id', $this_question->getId() );
		update_post_meta( $essay_id, 'quiz_id', $this_question->getQuizId() );
		update_post_meta( $essay_id, 'course_id', $course_id );
		update_post_meta( $essay_id, 'lesson_id', $lesson_id );

		if ( 'upload' == $essay_data->getGradedType() ){
			update_post_meta( $essay_id, 'upload', esc_url( $response ) );
		}
	}

	do_action( 'learndash_new_essay_submitted', $essay_id, $essay_args );

	return $essay_id;
}



/**
 * Remove the default submitdiv metabox from the Essay post type admin edit screen
 *
 * @since 2.2.0
 */
function learndash_essays_remove_subbmitdiv_metabox() {
	remove_meta_box( 'submitdiv', 'sfwd-essays', 'side' );
}

add_action( 'admin_menu', 'learndash_essays_remove_subbmitdiv_metabox' );



/**
 * Register Essay Upload metabox
 *
 * @since 2.2.0
 */
function learndash_register_essay_upload_metabox() {
	add_meta_box(
		'learndash_essay_upload_div',
		__( 'Essay Upload', 'learndash' ),
		'learndash_essay_upload_meta_box',
		'sfwd-essays',
		'normal',
		'high'
	);
	
	// This is added here because we wanted the inline comments ability on the single edit post type form. But since 
	// This post type uses custom post statuses the default logic in WP was failing. 
	add_meta_box( 'commentsdiv', __( 'Comments' ), 'post_comment_meta_box', null, 'normal', 'core' );
}

add_action( 'add_meta_boxes_sfwd-essays', 'learndash_register_essay_upload_metabox' );



/**
 * Display metabox for essay upload
 *
 * @since 2.2.0
 *
 * @param WP_Post   $essay
 */
function learndash_essay_upload_meta_box( $essay ) {
	$upload = get_post_meta( $essay->ID, 'upload', true );
	if ( ! empty( $upload ) ) {
		echo sprintf( '<a target="_blank" href="%1$s">%s</a>', esc_url( $upload ) );
	} else {
		_e( 'Upload was not provided for this question', 'learndash' );
	}
}



/**
 * Register Essay Grading Response metabox
 *
 * @since 2.2.0
 *
 * Used for when a grader wants to respond to a users submitted essay
 */
function learndash_register_essay_grading_response_metabox() {
	add_meta_box(
		'learndash_essay_grading_response_div',
		__( 'Your Response to Submitted Essay (optional)', 'learndash' ),
		'learndash_essay_grading_response_meta_box',
		'sfwd-essays',
		'normal',
		'high'
	);
}

//add_action( 'add_meta_boxes_sfwd-essays', 'learndash_register_essay_grading_response_metabox' );



/**
 * Display metabox for grading response
 *
 * @since 2.2.0
 *
 * @param WP_Post   $essay
 */
function learndash_essay_grading_response_meta_box( $essay ) {
	$grading_response = get_post_meta( $essay->ID, 'ld_essay_grading_response', true );
	$grading_response = ( ! empty( $grading_response ) ) ? wp_kses( 
		$grading_response,
		apply_filters('learndash_essay_grading_response_meta_box_allowed_html', wp_kses_allowed_html( 'post' ) ) ) : '';
	$grading_response = apply_filters( 'learndash_grading_response', $grading_response );
	?>
		<textarea name="grading-response" id="grading-response" rows="10"><?php echo $grading_response; ?></textarea>
	<?php
}



/**
 * Save essay grading response to post meta
 *
 * @since 2.2.0
 *
 * @param int		$essay_id
 * @param WP_Post	$essay
 * @param bool		$update
 */
function learndash_save_essay_grading_response( $essay_id, $essay, $update ) {
	if ( ! isset( $_POST['grading-response'] ) ) {
		return;
	}

	$grading_response = wp_kses( 
		$_POST['grading-response'], 
		apply_filters('learndash_essay_save_grading_response_allowed_html', wp_kses_allowed_html( 'post' ) ) 
	);

	/**
	 * Filter the grading response message
	 */
	$grading_response = apply_filters( 'learndash_grading_response', $grading_response );
	update_post_meta( $essay_id, 'ld_essay_grading_response', $grading_response );

	/**
	 * Perform an action after the grading response is updated
	 */
	do_action( 'learndash_essay_grading_response_updated', $grading_response );
}

add_action( 'save_post_sfwd-essays', 'learndash_save_essay_grading_response', 10, 3 );



/**
 * Register Essay grading metabox
 *
 * @since 2.2.0
 *
 * Replaces the submitdiv metabox that comes with every post type
 */
function learndash_register_essay_grading_metabox() {
	add_meta_box(
		'learndash_essay_status_div',
		__( 'Essay Grading Status', 'learndash' ),
		'learndash_essay_grading_meta_box',
		'sfwd-essays',
		'side',
		'core'
	);
}

add_action( 'add_meta_boxes_sfwd-essays', 'learndash_register_essay_grading_metabox' );



/**
 * Display Essay grading metabox
 *
 * Copied/modified version of submitdiv from core
 *
 * @since 2.2.0
 *
 * @param WP_Post   $essay
 */
function learndash_essay_grading_meta_box( $essay ) {

	$post_type            = $essay->post_type;
	$post_type_object     = get_post_type_object( $post_type );
	$can_publish          = current_user_can( $post_type_object->cap->publish_posts );
	$quiz_id              = get_post_meta( $essay->ID, 'quiz_id', true );
	$question_id          = get_post_meta( $essay->ID, 'question_id', true );

	if ( ! empty( $quiz_id ) ) {
		$questionMapper = new WpProQuiz_Model_QuestionMapper();
		$question       = $questionMapper->fetchById( intval( $question_id ) );
	}

	if ( $question && is_a( $question, 'WpProQuiz_Model_Question' ) )  {
		$submitted_essay_data = learndash_get_submitted_essay_data( $quiz_id, $question->getId(), $essay );
	}

	?>
	<div class="submitbox" id="submitpost">
		<div id="minor-publishing">
			<div id="misc-publishing-actions">
				<div class="misc-pub-section misc-pub-post-status">
					<?php if ( 'not_graded' == $essay->post_status || 'graded' == $essay->post_status || $can_publish ) : ?>

						<div id="post-status-select">
							<select name='post_status' id='post_status'>
								<option<?php selected( $essay->post_status, 'not_graded' ); ?>
									value='not_graded'><?php _e( 'Not Graded', 'learndash' ) ?></option>
								<option<?php selected( $essay->post_status, 'graded' ); ?>
									value='graded'><?php _e( 'Graded', 'learndash' ) ?></option>
							</select>
						</div>

					<?php endif; ?>
				</div>

				<div class="misc-pub-section">
					<?php if ( $question && is_a( $question, 'WpProQuiz_Model_Question' ) ) : ?>
						<p>
							<strong><?php _e( 'Essay Question', 'learndash' ); ?>:</strong> <?php echo $question->getQuestion(); ?>
							<span>(<a href="admin.php?page=ldAdvQuiz&module=question&action=delete&quiz_id=<?php echo $quiz_id; ?>&id=<?php echo $question->getId(); ?>"><?php _e( 'Edit', 'learndash' ); ?></a>)</span>
						</p>
						<p><strong><?php _e( 'Points available', 'learndash' ); ?>:</strong> <?php echo $question->getPoints(); ?></p>
						<p>
							<strong><?php _e( 'Points awarded', 'learndash' ); ?>:</strong>
							<input name="points_awarded" type="number" min="0" max="<?php echo $question->getPoints(); ?>" value="<?php echo $submitted_essay_data['points_awarded']; ?>">
							<input name="original_points_awarded" type="hidden" value="<?php echo $submitted_essay_data['points_awarded']; ?>">
						</p>
						<input name="quiz_id" type="hidden" value="<?php echo $quiz_id; ?>">
						<input name="question_id" type="hidden" value="<?php echo $question->getId(); ?>">
					<?php else : ?>
						<p><?php _e( 'We could not find the essay question for this response', 'learndash' ); ?></p>
					<?php endif; ?>
				</div>

				<?php
				/* translators: Publish box date format, see http://php.net/date */
				$datef = __( 'M j, Y @ H:i' );
				if ( 0 != $essay->ID ) :
					$stamp = __( 'Submitted on: <b>%1$s</b>' );
					$date  = date_i18n( $datef, strtotime( $essay->post_date ) );
				endif;

				if ( $can_publish ) : // Contributors don't get to choose the date of publish ?>
					<div class="misc-pub-section curtime misc-pub-curtime">
					<span id="timestamp"><?php printf( $stamp, $date ); ?></span>
					</div>
				<?php endif; ?>

				<?php
				/**
				 * Fires after the post time/date setting in the Publish meta box.
				 *
				 * @since 2.9.0
				 */
				do_action( 'post_submitbox_misc_actions' );
				?>
			</div>
			<div class="clear"></div>
		</div>

		<div id="major-publishing-actions">
			<?php
			/**
			 * Fires at the beginning of the publishing actions section of the Publish meta box.
			 *
			 * @since 2.7.0
			 */
			do_action( 'post_submitbox_start' );
			?>
			<div id="delete-action">
				<?php
				if ( current_user_can( "delete_post", $essay->ID ) ) :
					if ( ! EMPTY_TRASH_DAYS ) :
						$delete_text = __( 'Delete Permanently' );
					else :
						$delete_text = __( 'Move to Trash' );
					endif;
					?>
					<a class="submitdelete deletion" href="<?php echo get_delete_post_link( $essay->ID ); ?>"><?php echo $delete_text; ?></a><?php
				endif;
				?>
			</div>

			<div id="publishing-action">
				<span class="spinner"></span>
				<?php if ( $can_publish ) : ?>
					<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Update', 'learndash' ) ?>"/>
					<?php submit_button( __( 'Update', 'learndash' ), 'primary button-large', 'submit', false ); ?>
				<?php endif; ?>
			</div>
			<div class="clear"></div>
		</div>
	</div>

	<?php
}



/**
 * Get the essay data for this particular submission
 *
 * Loop through all the quizzes and return the quiz that matches as soon as it's found
 *
 * @since 2.2.0
 *
 * @param int       $quiz_id
 * @param int       $question_id
 * @param WP_Post   $essay
 *
 * @return mixed
 */
function learndash_get_submitted_essay_data( $quiz_id, $question_id, $essay  ) {
	$users_quiz_data = get_user_meta( $essay->post_author, '_sfwd-quizzes', true );

	foreach ( $users_quiz_data as $quiz_data ) {
		if ( $quiz_id != $quiz_data['pro_quizid'] || ! isset( $quiz_data['has_graded'] ) || false == $quiz_data['has_graded'] ) {
			continue;
		}

		foreach ( $quiz_data['graded'] as $key => $graded_question ) {
			if ( ( $key == $question_id ) && ( $essay->ID == $graded_question['post_id'] ) ) {
				return $quiz_data['graded'][ $key ];
			}
		}
	}
}



/**
 * Update a users essay and quiz data on save post
 *
 * @since 2.2.0
 *
 * @param int 		$essay_id
 * @param WP_Post 	$essay
 * @param bool 		$update
 */
function learndash_save_essay_status_metabox_data( $essay_id, $essay, $update ) {
	if ( ! isset( $_POST['question_id'] ) || empty( $_POST['question_id'] ) ) {
		return;
	}

	$quiz_id = intval( $_POST['quiz_id'] );
	$question_id = intval( $_POST['question_id'] );

	$submitted_essay = learndash_get_submitted_essay_data( $quiz_id, $question_id, $essay );
	$submitted_essay['status'] = esc_html( $_POST['post_status'] );
	$submitted_essay['points_awarded'] = intval( $_POST['points_awarded'] );

	/**
	 * Filter essay status data
	 */
	$submitted_essay = apply_filters( 'learndash_essay_status_data', $submitted_essay );
	learndash_update_submitted_essay_data( $quiz_id, $question_id, $essay, $submitted_essay );

	$original_points_awarded = isset( $_POST['original_points_awarded'] ) ? intval( $_POST['original_points_awarded'] ) : null;
	$points_awarded = isset( $_POST['points_awarded'] ) ?  intval( $_POST['points_awarded'] ) : null;

	if ( ! is_null( $original_points_awarded ) && ! is_null( $points_awarded ) ) {
		if ( $points_awarded > $original_points_awarded ) {
			$points_awarded_difference = intval( $points_awarded ) - intval( $original_points_awarded );
		} else {
			$points_awarded_difference = ( intval( $original_points_awarded ) - intval( $points_awarded ) ) * -1;
		}

		$updated_scoring = array(
			'updated_question_score' => $points_awarded,
			'points_awarded_difference' => $points_awarded_difference,
		);

		/**
		 * Filter updated scoring data
		 */
		$updated_scoring = apply_filters( 'learndash_updated_essay_scoring', $updated_scoring );
		learndash_update_quiz_data( $quiz_id, $question_id, $updated_scoring, $essay );

		/**
		 * Perform action after all the quiz data is updated
		 */
		do_action( 'learndash_essay_all_quiz_data_updated', $quiz_id, $question_id, $updated_scoring, $essay );
	}
}

add_action( 'save_post_sfwd-essays', 'learndash_save_essay_status_metabox_data', 10, 3 );



/**
 * Updates a users submitted essay data
 *
 * Finds the essay in this particular quiz attempt in the users meta and updates its data
 *
 * @since 2.2.0
 *
 * @param int		$quiz_id
 * @param int		$question_id
 * @param WP_Post	$essay
 * @param array		$submitted_essay
 */
function learndash_update_submitted_essay_data( $quiz_id, $question_id, $essay, $submitted_essay ) {
	$users_quiz_data = get_user_meta( $essay->post_author, '_sfwd-quizzes', true );

	foreach ( $users_quiz_data as $quiz_key => $quiz_data ) {
		if ( $quiz_id != $quiz_data['pro_quizid'] || ! isset( $quiz_data['has_graded'] ) || false == $quiz_data['has_graded'] ) {
			continue;
		}

		foreach ( $quiz_data['graded'] as $question_key => $graded_question ) {
			if ( ( $question_key == $question_id ) && ( $essay->ID == $graded_question['post_id'] ) ) {
				$users_quiz_data[ $quiz_key ]['graded'][ $question_key ] = $submitted_essay;
			}
		}
	}

	update_user_meta( $essay->post_author, '_sfwd-quizzes', $users_quiz_data );

	/**
	 * Perform action after essay response data is updated
	 */
	do_action( 'learndash_essay_response_data_updated', $quiz_id, $question_id, $essay, $submitted_essay );
}



/**
 * Updates a users quiz data
 *
 * Finds this particular quiz attempt in the users meta and updates its data
 *
 * @since 2.2.0
 *
 * @param int		$quiz_id
 * @param int		$question_id
 * @param array		$updated_scoring
 * @param WP_Post	$essay
 */
function learndash_update_quiz_data( $quiz_id, $question_id, $updated_scoring, $essay ) {
	$users_quiz_data = get_user_meta( $essay->post_author, '_sfwd-quizzes', true );

	foreach ( $users_quiz_data as $quiz_key => $quiz_data ) {
		if ( $quiz_id != $quiz_data['pro_quizid'] || ! isset( $quiz_data['has_graded'] ) || false == $quiz_data['has_graded'] ) {
			continue;
		}

		// update total points
		$users_quiz_data[ $quiz_key ]['points'] = $users_quiz_data[ $quiz_key ]['points'] + $updated_scoring['points_awarded_difference'];

		// update total score percentage
		$updated_percentage = ( $users_quiz_data[ $quiz_key ]['points'] / $users_quiz_data[ $quiz_key ]['total_points'] ) * 100;
		$users_quiz_data[ $quiz_key ]['percentage'] = round( $updated_percentage, 2 );

		// update passing score
		$quizmeta = get_post_meta( $quiz_data['quiz'], '_sfwd-quiz', true );
		$passingpercentage = intVal( $quizmeta['sfwd-quiz_passingpercentage'] );
		$users_quiz_data[ $quiz_key ]['pass'] = ( $users_quiz_data[ $quiz_key ]['percentage'] >= $passingpercentage ) ? 1 : 0;

		learndash_update_quiz_statistics( $quiz_id, $question_id, $updated_scoring, $essay );
	}

	update_user_meta( $essay->post_author, '_sfwd-quizzes', $users_quiz_data );

	/**
	 * Perform action after essay quiz data is updated
	 */
	do_action( 'learndash_essay_quiz_data_updated', $quiz_id, $question_id, $updated_scoring, $essay );
}



/**
 * Update the quiz statistics for this quiz attempt
 *
 * Updates the score when the essay grading is adjusted, I ran this through manual SQL queries
 * because WpProQuiz doesn't offer an elegant way to grab a particular question and update it.
 *
 * @since 2.2.0
 *
 * @param int		$quiz_id
 * @param int		$question_id
 * @param array		$updated_quiz_data
 * @param WP_Post	$essay
 */
function learndash_update_quiz_statistics( $quiz_id, $question_id, $updated_quiz_data, $essay ) {
	global $wpdb;

	$wpdb->wp_pro_quiz_statistic_ref = "{$wpdb->prefix}wp_pro_quiz_statistic_ref";
	$wpdb->wp_pro_quiz_statistic = "{$wpdb->prefix}wp_pro_quiz_statistic";

	$refId = $wpdb->get_var(
		$wpdb->prepare("
					SELECT statistic_ref_id
					FROM $wpdb->wp_pro_quiz_statistic_ref
					WHERE quiz_id = %d AND user_id = %d
				", $quiz_id, $essay->post_author)
	);

	$refId = intval( $refId );

	$row = $wpdb->get_results(
		$wpdb->prepare("
					SELECT *
					FROM $wpdb->wp_pro_quiz_statistic
					WHERE statistic_ref_id = %d AND question_id = %d
				", $refId, $question_id)
	);

	if ( empty( $row ) ) {
		return;
	}

	if ( $updated_quiz_data['updated_question_score'] > 0 ) {
		$correct_count = 1;
		$incorrect_count = 0;
	} else {
		$correct_count = 0;
		$incorrect_count = 1;
	}

	$update  = $wpdb->update(
		$wpdb->wp_pro_quiz_statistic,
		array(
			'correct_count' => $correct_count,
			'incorrect_count' => $incorrect_count,
			'points' => $updated_quiz_data['updated_question_score'],
		),
		array(
			'statistic_ref_id' => $refId,
			'question_id' => $question_id,
		),
		array( '%d', '%d', '%d'	),
		array( '%d', '%d' )
	);

	do_action( 'learndash_essay_question_stats_updated' );
}



/**
 * Restrict assignment listings view to group leader only
 *
 * @since 2.2.0
 *
 * @param  object 	$query 	WP_Query
 * @return object 	$query 	WP_Query
 */
function learndash_restrict_essay_listings_for_group_admins( $query ) {
	global $pagenow, $typenow;
	$q_vars = & $query->query_vars;

	if ( ( is_admin() ) && ( $query->is_main_query() ) && ($pagenow == 'edit.php') && ( !empty( $typenow ) ) && ( $typenow == 'sfwd-essays' ) ) {
		if ( !current_user_can( 'manage_options' ) ) {
			$user_id = get_current_user_id();

			if ( is_group_leader( $user_id ) ) {
				$group_ids = learndash_get_administrators_group_ids( $user_id );
				$user_ids = array();

				if ( ! empty( $group_ids ) && is_array( $group_ids ) ) {
					foreach( $group_ids as $group_id ) {

						$group_users = learndash_get_groups_user_ids( $group_id );

						if ( ! empty( $group_users ) && is_array( $group_users ) ) {
							foreach( $group_users as $group_user_id ) {
								$user_ids[ $group_user_id ] = $group_user_id;
							}
						}
					}
				}

				if ( ! empty( $user_ids ) && count( $user_ids ) ) {
					$q_vars['author__in'] = $user_ids;
				} else {
					$q_vars['author__in'] = - 2;
				}
			}
		}
	}
}

add_filter( 'parse_query', 'learndash_restrict_essay_listings_for_group_admins' );


/**
 * AJAX callback for Uploading a file for an essay quesiton
 *
 * @since 2.2.0
 *
 * Runs checks for needing information, or will die and send an error back to browser
 */
function learndash_upload_essay() {

	if ( ! isset( $_POST['nonce'] ) || ! isset( $_POST['question_id'] ) || ! isset( $_FILES['essayUpload'] ) ) {
		wp_send_json_error();
		die();
	}

	$nonce = $_POST['nonce'];
	$question_id = intval( $_POST['question_id'] );

	if ( ! wp_verify_nonce( $nonce, 'learndash-upload-essay' ) ) {
		wp_send_json_error();
		die( 'Security check' );
	} else {
		$file_desc = learndash_essay_fileupload_process( $_FILES['essayUpload'], $question_id );

		if ( ! empty( $file_desc ) ) {
			wp_send_json_success( $file_desc );
		} else {
			wp_send_json_error();
		}
		die();
	}
}

add_action( 'wp_ajax_learndash_upload_essay', 'learndash_upload_essay' );
add_action( 'wp_ajax_nopriv_learndash_upload_essay', 'learndash_upload_essay' );


/**
 * Upload files for essays
 *
 * @since 2.2.0
 *
 * @param array $uploadfiles
 * @param int $question_id
 *
 * @return array file description
 * @internal param int $post_id assignment id
 */
function learndash_essay_fileupload_process( $uploadfiles, $question_id ) {
	if ( is_array( $uploadfiles ) ) {

		// look only for uploded files
		if ( $uploadfiles['error'] == 0 ) {

			$filetmp = $uploadfiles['tmp_name'];

			//clean filename
			$filename = learndash_clean_filename( $uploadfiles['name'] );

			//extract extension
			if ( ! function_exists( 'wp_get_current_user' ) ) {
				include ABSPATH . 'wp-includes/pluggable.php';
			}

			//current user
			$user = get_current_user_id();

			// get file info
			// @fixme: wp checks the file extension....
			$filetype = wp_check_filetype( basename( $filename ), null );
			$filetitle = preg_replace( '/\.[^.]+$/', '', basename( $filename ) );
			$filename = sprintf( 'question_%d_%s.%s', $question_id, $filetitle, $filetype['ext'] );
			$filename = apply_filters( 'learndash_essay_upload_filename', $filename, $question_id, $filetitle, $filetype['ext'] );
			$upload_dir = wp_upload_dir();
			$upload_dir_base = $upload_dir['basedir'];
			$upload_url_base = $upload_dir['baseurl'];
			$upload_dir_path = $upload_dir_base . apply_filters( 'learndash_essay_upload_dirbase', '/essays', $filename, $upload_dir );
			$upload_url_path = $upload_url_base . apply_filters( 'learndash_essay_upload_urlbase', '/essays/', $filename, $upload_dir );

			if ( ! file_exists( $upload_dir_path ) ) {
				mkdir( $upload_dir_path );
			}

			/**
			 * Check if the filename already exist in the directory and rename the
			 * file if necessary
			 */
			$i = 0;

			while ( file_exists( $upload_dir_path . '/' . $filename ) ) {
				$i++;
				$filename = sprintf( 'question_%d_%s_%d.%s', $question_id, $filetitle, $i, $filetype['ext'] );
				$filename = apply_filters( 'learndash_essay_upload_filename_dup', $filename, $question_id, $filetitle, $i, $filetype['ext'] );
			}

			$filedest = $upload_dir_path . '/' . $filename;
			$destination = $upload_url_path . $filename;

			/**
			 * Check write permissions
			 */
			if ( ! is_writeable( $upload_dir_path ) ) {
				wp_send_json_error( __( 'Unable to write to directory. Is this directory writable by the server?', 'learndash' ) );
				die();
			}

			/**
			 * Save temporary file to uploads dir
			 */
			if ( ! @move_uploaded_file( $filetmp, $filedest ) ) {
				wp_send_json_error( "Error, the file $filetmp could not moved to : $filedest " );
				die();
			}

			$file_desc = array();
			$file_desc['filename'] = $filename;
			$file_desc['filelink'] = $destination;
			return $file_desc;
		}
	}
}