<?php
/**
 * LifterLMS Course Author Module HTML
 * @since    1.3.0
 * @version  1.3.0
 */

// Restrict direct access
if ( ! defined( 'ABSPATH' ) ) { exit; }

$course_id = ! empty( $settings->llms_course_id ) ? $settings->llms_course_id : get_the_ID();
?>

<div class="llms-lab-course-author">
	[lifterlms_course_author avatar_size="<?php echo $settings->llms_avatar_size; ?>" bio="<?php echo $settings->llms_show_bio; ?>" course_id="<?php echo $course_id; ?>"]
</div>
