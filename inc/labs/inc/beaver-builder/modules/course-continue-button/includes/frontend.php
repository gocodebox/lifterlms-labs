<?php
/**
 * LifterLMS Course Continue Button Module HTML
 * @since    1.3.0
 * @version  1.3.0
 */

// Restrict direct access
if ( ! defined( 'ABSPATH' ) ) { exit; }

$course_id = ! empty( $settings->llms_course_id ) ? $settings->llms_course_id : get_the_ID();
?>

<div class="llms-lab-course-continue-button">
	[lifterlms_course_continue_button course_id="<?php echo $course_id; ?>"]
</div>
