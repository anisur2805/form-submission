<?php

namespace AFS\Form_Submission;

class Assets {
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	public function get_scripts() {
		return array(
			'afs-script'   => array(
				'src'     => AFS_ASSETS . '/js/frontend.js',
				'version' => filemtime( AFS_PATH . '/assets/js/frontend.js' ),
				'deps'    => array( 'jquery' ),
			),

			'admin-script' => array(
				'src'     => AFS_ASSETS . '/js/admin.js',
				'version' => filemtime( AFS_PATH . '/assets/js/admin.js' ),
				'deps'    => array( 'jquery', 'wp-util' ),
			),
		);
	}

	public function get_styles() {
		return array(
			'afs-style'   => array(
				'src'     => AFS_ASSETS . '/css/frontend.css',
				'version' => filemtime( AFS_PATH . '/assets/css/frontend.css' ),
			),
			'admin-style' => array(
				'src'     => AFS_ASSETS . '/css/admin.css',
				'version' => filemtime( AFS_PATH . '/assets/css/admin.css' ),
			),
		);
	}

	public function enqueue_assets() {
		$scripts = $this->get_scripts();

		foreach ( $scripts as $handle => $script ) {
			$deps = isset( $script['deps'] ) ? $script['deps'] : false;
			wp_register_script( $handle, $script['src'], $deps, $script['version'], true );
		}

		$styles = $this->get_styles();

		foreach ( $styles as $handle => $style ) {
			$deps = isset( $style['deps'] ) ? $style['deps'] : false;
			wp_register_style( $handle, $style['src'], $deps, $style['version'] );
		}

		wp_enqueue_script( 'afs-script' );
		wp_enqueue_style( 'afs-style' );

		wp_localize_script(
			'afs-script',
			'afsFormObj',
			array(
				'ajaxUrl'          => admin_url( 'admin-ajax.php' ),
				'error'            => __( 'Something went wrong', 'afs-form' ),
				'resubmit_message' => __( 'Oops! It seems you\'ve already submitted the form within the last 24 hours. Kindly check back later to submit another response.', 'afs-form' ),
				'success'          => __( 'Successfully submitted the form!', 'afs-form' ),
				'amount'           => __( 'Amount is required', 'afs-form' ),
				'buyer'            => __( 'Buyer is required', 'afs-form' ),
				'buyer_email'      => __( 'Buyer Email is required', 'afs-form' ),
				'receipt_id'       => __( 'Receipt ID is required', 'afs-form' ),
				'entry_by'         => __( 'Entry by is required', 'afs-form' ),
				'city'             => __( 'City is required', 'afs-form' ),
				'phone'            => __( 'Phone number is required', 'afs-form' ),
				'items'            => __( 'Please add minimum one item', 'afs-form' ),
				'length_exceed'    => __( 'Maximum length reached.', 'afs-form' ),
				'receipt_msg'      => __( 'Only text is allowed.', 'afs-form' ),
				'city_msg'         => __( 'Only text and spaces are allowed.', 'afs-form' ),
				'invalid_email'    => __( 'Invalid email address', 'afs-form' ),
				'number_msg'       => __( 'Only positive number', 'afs-form' ),
				'nonce'            => wp_create_nonce( 'wp_rest' ),
			)
		);

		wp_localize_script(
			'admin-script',
			'afsSubObj',
			array(
				'nonce'    => wp_create_nonce( 'afs-admin-nonce' ),
				'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
				'confirm'  => __( 'Are you sure?', 'afs-form' ),
				'error'    => __( 'Something went wrong', 'afs-form' ),
				'security' => wp_create_nonce( 'afs_form_nonce' ),
			)
		);
	}
}
