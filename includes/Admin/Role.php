<?php

namespace AFS\Form_Submission\Admin;

class Role {
	public function __construct() {
		add_action( 'init', array( $this, 'create_capability' ) );
		// add_action( 'template_redirect', array( $this, 'check_report_capability' ) );
	}

	public function create_capability() {
		$editor_role = get_role( 'editor' );
		$editor_role->add_cap( 'view_reports' );
	}

	public function report_page_template_function() {
	}


	public function check_report_capability() {
		if ( is_page( 'afs-submission-report' ) && ! current_user_can( 'view_reports' ) ) {
			wp_safe_redirect( home_url() );
			exit;
		}
		add_action( 'template_include', array( $this, 'report_page_template_function' ) );
	}
}
