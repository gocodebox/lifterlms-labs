<?php
/**
 * Lab: Action Manager
 * Remover LifterLMS Action Hooks with Checkboxes
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class LLMS_Lab_Distractionless extends LLMS_Lab {

	public function add_actions() {

		if ( is_lesson() ) {

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
			add_filter( 'template_include', array( $this, 'template_loader' ) );

		}

	}

	/**
	 * This function should define lab vars
	 * It's run during contruction before anything else happens
	 * @return   void
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	protected function configure() {

		$this->id = 'distractionless'; // leave this so we don't have to rewrite db options
		$this->title = __( 'Distractionless', 'lifterlms-labs' );
		$this->description = sprintf(
			__( 'wut Click %1$shere%2$s for more information.', 'lifterlms-labs' ),
			'<a href="#utm_source=settings&utm_medium=product&utm_campaign=lifterlmslabsplugin&utm_content=actionmanager">', '</a>'
		);

	}

	public function enqueue() {

		wp_enqueue_script( 'vimeo', 'https://player.vimeo.com/api/player.js', array( 'jquery' ), false, true );
		wp_enqueue_script( 'fitvids', 'https://raw.githubusercontent.com/davatron5000/FitVids.js/master/jquery.fitvids.js', array( 'vimeo' ), false, true );
		wp_add_inline_script( 'vimeo', $this->get_scripts(), 'after' );
		wp_add_inline_style( 'lifterlms-styles', $this->get_styles() );

	}

	public function get_scripts() {
		ob_start();
		?>
		;( function( $ ) {

			$( 'body' ).fitVids();

			var $iframe = $( '.llms-video-wrapper iframe[src*="vimeo"]' )[0],
				$player = new Vimeo.Player( $iframe ),
				$submit = $( '#llms_mark_complete' );

			$submit.hide();

			$player.on( 'ended', function() {
				$submit.trigger( 'click' );
			} );

		} )( jQuery );
		<?php
		return ob_get_clean();
	}

	public function get_styles() {
		ob_start();
		?>

		body {
			background: #111;
			box-shadow: inset 0 0px 350px #000;
		}

		.distractionless-main {
			padding:
		}

		<?php
		return ob_get_clean();
	}

	/**
	 * This function should do whatever the lab does
	 * It's run after during construction after configuration and default functions are run
	 * @return   void
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	protected function init() {

		add_action( 'wp', array( $this, 'add_actions' ) );

	}

	/**
	 * This function should return array of settings fields
	 * @return   array
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	protected function settings() {
		return array();
	}

	public function template_loader( $template ) {

		if ( strpos( $template, 'single-lesson.php' ) ) {

			$template = plugin_dir_path( LLMS_LABS_PLUGIN_FILE ) . 'templates/' . $this->id . '/single-lesson.php';

		}

		return $template;

	}

}

return new LLMS_Lab_Distractionless();
