<?php
/**
 * LifterLMS Course Meta Info Module HTML
 * @since    [version]
 * @version  [version]
 */

// Restrict direct access
if ( ! defined( 'ABSPATH' ) ) { exit; }

$course_id = ! empty( $settings->llms_course_id ) ? $settings->llms_course_id : get_the_ID();
?>

<div class="llms-lab-course-meta-info">
	[lifterlms_course_meta_info course_id="<?php echo $course_id; ?>"]
</div>
