<?php
/**
 * Distractionless Lesson Template
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<main id="main" class="distractionless-main" role="main">

	<?php while ( have_posts() ) : the_post(); ?>

		<?php the_title(); ?>

		<?php lifterlms_template_single_lesson_video(); ?>

		<?php get_the_content(); ?>

	<?php endwhile; ?>

</main><!-- #main -->

<?php get_sidebar(); ?>

<?php wp_footer(); ?>
</body>
</html>
