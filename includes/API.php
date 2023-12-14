<?php
namespace AFS\Form_Submission;

use AFS\Form_Submission\API\Reports;

class API {
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
	}

	public function rest_api_init() {
		$reports = new Reports();
		$reports->register_routes();
	}
}
