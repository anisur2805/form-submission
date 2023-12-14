<?php

namespace AFS\Form_Submission\API;

use WP_Error;
use WP_REST_Controller;
use WP_REST_Server;

class Reports extends WP_REST_Controller {
	public function __construct() {
		$this->namespace = 'afs-forms/v1';
		$this->rest_base = 'reports';
	}

	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'method'              => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_reports' ),
					'permission_callback' => array( $this, 'get_reports_permission_check' ),
					'args'                => $this->get_collection_params(),
				),
			)
		);
	}

	/**
	 * Retrieves the query params for collections.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		$params = parent::get_collection_params();

		unset( $params['search'] );

		return $params;
	}


	/**
	 * Retrieves a list of address items
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Request|WP_Error
	 */
	public function get_reports( $request ) {
		$args   = array();
		$params = $this->get_collection_params();
		foreach ( $params as $key => $value ) {
			if ( isset( $request[ $key ] ) ) {
				$args[ $key ] = $request[ $key ];
			}
		}

		$args['number'] = $args['per_page'];
		$args['offset'] = $args['number'] * ( $args['page'] - 1 );

		unset( $args['per_page'] );
		unset( $args['page'] );

		$reports = afs_get_reports( $args );
		$data    = array();

		foreach ( $reports as $report ) {
			$response = $this->prepare_item_for_response( $report, $request );
			$data[]   = $this->prepare_response_for_collection( $response );
		}

		$total     = afs_report_count();
		$max_pages = ceil( $total / (int) $args['number'] );

		$response = rest_ensure_response( $data );

		$response->header( 'X-WP-Total', (int) $total );
		$response->header( 'X-WP-TotalPages', (int) $max_pages );

		return $response;
	}

	public function prepare_item_for_response( $item, $request ) {
		$data   = array();
		$fields = $this->get_fields_for_response( $request );

		if ( in_array( 'id', $fields, true ) ) {
			$data['id'] = (int) $item['id'];
		}

		if ( in_array( 'buyer', $fields, true ) ) {
			$data['buyer'] = $item->name;
		}

		$response = rest_ensure_response( $item );
		// $response->add_links( $this->prepare_links( $item ) );

		return $response;
	}

	protected function prepare_links( $item ) {
		$base = sprintf( '%s/%s', $this->namespace, $this->rest_base );

		$links = array(
			'self'       => array(
				'href' => rest_url( trailingslashit( $base ) . $item['id'] ),
			),
			'collection' => array(
				'href' => rest_url( $base ),
			),
		);

		return $links;
	}

	/**
	 * Checks if a given request has access to read contacts
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return boolean
	 */
	public function get_reports_permission_check( $request ) {
		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}

		return false;
	}
}
