<?php
/**
 * LifterLMS Lesson Mark Complete Module HTML
 * @since    1.3.0
 * @version  1.3.0
 */

// Restrict direct access
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>

<div class="llms-lab-lesson-mark-complete">
<?php if ( FLBuilderModel::is_builder_active() ) : ?>
	<div class="clear"></div>
	<div class="llms-lesson-button-wrapper">
		<?php llms_form_field( array(
			'columns' => 12,
			'classes' => 'llms-button-primary auto button',
			'id' => 'llms_mark_complete',
			'value' => apply_filters( 'lifterlms_mark_lesson_complete_button_text', __( 'Mark Complete', 'lifterlms-labs' ), llms_get_post( get_the_ID() ) ),
			'last_column' => true,
			'name' => 'mark_complete',
			'required' => false,
			'type'  => 'submit',
		) ); ?>
	</div>
<?php else : ?>
	[lifterlms_lesson_mark_complete]
<?php endif; ?>
</div>
