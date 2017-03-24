<?php
/**
 * The main labs settings page
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class LLMS_Labs_Settings_Page {

	/**
	 * Constructor
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function __construct() {

		add_action( 'admin_init', array( $this, 'handle_form' ) );
		add_action( 'admin_menu', array( $this, 'register' ), 777 );

	}

	/**
	 * Get the current tab (if set)
	 * @return   string
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	private function get_tab() {
		return isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : '';
	}

	/**
	 * Handle form submissions
	 * @return   void
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function handle_form() {

		if ( empty( $_POST['llms_labs_manager_nonce'] ) || ! wp_verify_nonce( $_POST['llms_labs_manager_nonce'], 'llms_labs_manager' ) ) {
			return;
		}

		$action = false;

		if ( ! empty( $_POST['llms-lab-enable'] ) ) {

			$action = 'manage';
			$id = sanitize_text_field( $_POST['llms-lab-enable'] );
			$val = 'yes';

		} elseif ( ! empty( $_POST['llms-lab-disable'] ) ) {

			$action = 'manage';
			$id = sanitize_text_field( $_POST['llms-lab-disable'] );
			$val = 'no';

		} elseif ( isset( $_POST['llms-lab-settings-save'] ) ) {

			$action = 'settings';
			$id = sanitize_text_field( $_POST['llms-lab-id'] );

		} else {

			return;

		}

		$lab = LLMS_Labs_LabTech::get_lab( $id );

		if ( 'manage' === $action ) {

			if ( ! $lab ) {
				return;
			}

			$lab->set_option( 'enabled', $val );

			if ( 'yes' === $val ) {

				do_action( 'llms_lab_' . $lab->get_id() . '_enabled' );

				wp_safe_redirect( admin_url( 'admin.php?page=llms-labs&tab=' . $lab->get_id() ) );

			} else {

				do_action( 'llms_lab_' . $lab->get_id() . '_disabled' );

			}

		} elseif ( 'settings' === $action ) {

			foreach ( $lab->get_settings() as $field ) {

				if ( 'html' === $field['type'] ) {
					continue;
				}

				$name = ! empty( $field['name'] ) ? $field['name'] : $field['id'];

				if ( isset( $_POST[ $name ] ) ) {
					$lab->set_option( $name, sanitize_text_field( $_POST[ $name ] ) );
				} elseif ( 'checkbox' === $field['type'] ) {
					$lab->set_option( $name, sanitize_text_field( $field['default'] ) );
				}

			}

			do_action( 'llms_lab_' . $id . '_settings_saved' );

		}

	}

	/**
	 * Register the labs settings page
	 * @return   void
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function register() {
		add_submenu_page( 'lifterlms', __( 'LifterLMS Labs', 'lifterlms-labs' ), __( 'Labs', 'lifterlms-labs' ), apply_filters( 'llms_labs_settings_page_capability', 'manage_options' ), 'llms-labs', array( $this, 'render' ) );
	}

	/**
	 * Render the page
	 * @return   void
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	public function render() {
		echo '<div class="wrap lifterlms lifterlms-labs">';
		$this->render_title();
		echo '<form action="" method="POST">';

		if ( ! $this->get_tab() ) {
			$this->render_main();
		} else {
			$this->render_tab();
		}

		wp_nonce_field( 'llms_labs_manager', 'llms_labs_manager_nonce' );

		echo '</form>';
		echo '</div>';

	}

	/**
	 * Render the main Labs screen content
	 * @return   void
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	private function render_main() {
		?>

		<h4><?php _e( 'Each lab is an experimental, conceptual, or fun new feature which you can enable to enhance, improve, or alter the core functionality of LifterLMS.' ); ?></h4>
		<h4><?php _e( 'Some labs are being tested and may be moved into the LifterLMS core, others might remain here forever.' ); ?></h4>

		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th><?php _e( 'Name', 'lifterlms-lab' ); ?></th>
					<th><?php _e( 'Descrpition', 'lifterlms-lab' ); ?></th>
					<th><?php _e( 'Status', 'lifterlms-lab' ); ?></th>
					<th><?php _e( 'Action', 'lifterlms-lab' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach( LLMS_Labs_LabTech::get_labs() as $id => $lab ): $enabled = $lab->is_enabled(); ?>
					<tr>
						<td>
							<?php if ( $enabled ) :?>
								<a href="<?php echo admin_url( 'admin.php?page=llms-labs&tab=' . $lab->get_id() ); ?>"><?php echo $lab->get_title(); ?></a>
							<?php else : ?>
								<?php echo $lab->get_title(); ?>
							<?php endif; ?>
						</td>
						<td><?php echo $lab->get_description(); ?></td>
						<td>
							<?php if ( $enabled ) :?>
								<span class="screen-reader-text"><?php _e( 'Enabled', 'lifterlms-labs' ); ?></span><span class="dashicons dashicons-yes"></span>
							<?php else : ?>
								<span class="screen-reader-text"><?php _e( 'Disabled', 'lifterlms-labs' ); ?></span><span class="dashicons dashicons-no"></span>
							<?php endif; ?>
						</td>
						<td>
							<?php if ( $enabled ) :?>
								<a class="llms-button-primary small" href="<?php echo admin_url( 'admin.php?page=llms-labs&tab=' . $lab->get_id() ); ?>"><?php _e( 'Configure', 'lifterlms-labs' ); ?></a>
								<button class="llms-button-danger small" name="llms-lab-disable" type="submit" value="<?php echo $lab->get_id(); ?>"><?php _e( 'Disable', 'lifterlms-labs' ); ?></button>
							<?php else : ?>
								<button class="llms-button-primary small" name="llms-lab-enable" type="submit" value="<?php echo $lab->get_id(); ?>"><?php _e( 'Enable', 'lifterlms-labs' ); ?></button>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<?php
	}

	/**
	 * Render content for a specific lab
	 * @return   void
	 * @since    1.0.0
	 * @version  1.1.0
	 */
	private function render_tab() {

		$lab = LLMS_Labs_LabTech::get_lab( $this->get_tab() );
		if ( ! $lab ) {
			_e( 'Invalid lab.', 'lifterlms-labs' );
			return;
		}
		if ( ! $lab->is_enabled() ) {
			_e( 'This lab in not enabled, please enable the lab and try again.', 'lifterlms-labs' );
			return;
		}

		echo '<div class="llms-widget">';

			echo '<h4>' . $lab->get_description() . '</h4>';

			echo '<div class="llms-form-fields">';

			if ( $settings = $lab->get_settings() ) {

				foreach ( $settings as $field ) {
					llms_form_field( $field );
				}

				llms_form_field( array(
					'columns' => 2,
					'classes' => 'llms-button-primary',
					'id' => 'llms-lab-settings-save',
					'value' => __( 'Save', 'lifterlms-labs' ),
					'last_column' => true,
					'required' => false,
					'type'  => 'submit',
				) );

			} else {

				_e( 'This lab doesn\'t have any settings.', 'lifterlms-labs' );

			}

			echo '<input name="llms-lab-id" type="hidden" value="' . $lab->get_id() . '">';

			echo '</div>';

		echo '</div>';

	}

	/**
	 * Output HTML for the page title based on current tab
	 * @return   void
	 * @since    1.0.0
	 * @version  1.0.0
	 */
	private function render_title() {
		echo '<h1>';
			_e( 'LifterLMS Labs', 'lifterlms-labs' );
			if ( $id = $this->get_tab() ) {
				$lab = LLMS_Labs_LabTech::get_lab( $id );
				if ( $lab ) {
					printf( ' &ndash; %s', $lab->get_title() );
				}
			}
		echo '</h1>';
	}

}

return new LLMS_Labs_Settings_Page();