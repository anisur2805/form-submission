<?php

namespace AFS\Form_Submission\Admin;

/**
 * Menu class
 */
class Menu {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	public function admin_menu() {
		$capability            = 'manage_options';
		$parent_slug           = 'afs-form-submission';
		$min_editor_capability = 'edit_others_posts';

		$page_title = __( 'Form Submission', 'afs-fs' );
		$hook       = add_menu_page( __( 'Submission Report', 'afs-submission' ), __( 'Reports', 'afs-submission' ), $min_editor_capability, 'afs-submission-report', array( $this, 'report_page' ), 'dashicons-media-document' );

		add_action( 'admin_head-' . $hook, array( $this, 'enqueue_assets' ) );
	}

	public function form_submission_page() {
	}
	public function report_page() {
		?>

		<div class="wrap">
			<?php
				$action = isset( $_GET['action'] ) ? $_GET['action'] : 'table';
				$id     = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;

			switch ( $action ) {
				case 'view':
					$template = __DIR__ . '/views/table-view.php';
					break;

				case 'edit':
					$template = __DIR__ . '/views/edit-view.php';
					break;

				default:
					$template = __DIR__ . '/views/table-view.php';
					break;
			}

			if ( file_exists( $template ) ) {
				include $template;
			}

			?>
		</div>
		<?php
	}


	public function enqueue_assets() {
		wp_enqueue_style( 'admin-style' );
		wp_enqueue_script( 'admin-script' );
	}
}
