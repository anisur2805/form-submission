<?php

namespace AFS\Form_Submission\Frontend;

class Shortcode {

	/**
	 * Initialize the class
	 */
	public function __construct() {
		add_shortcode( 'afs_form', array( $this, 'render_form' ) );
		add_shortcode( 'afs_report_table', array( $this, 'render_report_table' ) );
	}


	/**
	 * Shortcode Form handler
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public function render_form( $atts, $content = null ) {
		ob_start();
		$form_template = __DIR__ . '/views/form.php';
		if ( file_exists( $form_template ) ) {
			include $form_template;
		}
		return ob_get_clean();
	}

	/**
	 * Shortcode Report Table handler
	 *
	 * @param array $atts
	 * @param string $content
	 *
	 * @return string
	 */
	public function render_report_table() {
		if ( current_user_can( 'editor' ) || current_user_can( 'administrator' ) && is_user_logged_in() ) {
			ob_start();
			include __DIR__ . '/views/report-table.php';
			return ob_get_clean();
		}
	}
}
