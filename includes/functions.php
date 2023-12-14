<?php

/**
 * Retrieve all submitted reports.
 */
function afs_get_reports( $args ) {
	global $wpdb;
	$afs_form_table = $wpdb->prefix . 'afs_form';

	$defaults = array(
		'number'  => 20,
		'offset'  => 0,
		'orderby' => 'id',
		'order'   => 'DESC',
		's'       => '',
	);

	$args = wp_parse_args( $args, $defaults );

	$where = '';

	if ( ! empty( $args['s'] ) ) {
		$keyword = '%' . $wpdb->esc_like( $args['s'] ) . '%';

		$where = $wpdb->prepare( 'WHERE amount LIKE %s OR buyer_email LIKE %s OR phone LIKE %s OR entry_by LIKE %s', $keyword, $keyword, $keyword, $keyword );
	}

	$items = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT * from $afs_form_table
            $where
            ORDER BY {$args['orderby']} {$args['order']}
            LIMIT %d, %d",
			$args['offset'],
			$args['number']
		),
		ARRAY_A
	);

	return $items;
}

/**
 * Count total submitted reports.
 */
function afs_report_count() {
	global $wpdb;

	return $wpdb->get_var( "SELECT count(id) FROM {$wpdb->prefix}afs_form" );
}

/**
 * Delete process for single report.
 */
function afs_delete_item( $item_id ) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'afs_form';
	$wpdb->delete(
		$table_name,
		array( 'id' => $item_id ),
		array( '%d' )
	);
}

/**
 * Get data from the specified AFS form table based on ID.
 *
 * @param int $id The ID of the form.
 *
 * @return array|null The results from the query as an associative array, or null if no results are found.
 */
function get_afs_form_by_id( $id ) {
	global $wpdb;
	return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}afs_form WHERE id = %d", $id ), ARRAY_A );
}

/**
 * Register Sidebar Widget.
 */
function register_afs_widget() {
	register_widget( 'AFS\Form_Submission\Admin\Widgets\Form_Widget' );
}
add_action( 'widgets_init', 'register_afs_widget' );

/**
 * Retrieve the data from report table
 */
function atf_display_report() {
	global $wpdb;

	$items_per_page = 10;
	$current_page   = max( 1, get_query_var( 'paged' ) );
	$offset         = ( $current_page - 1 ) * $items_per_page;
	$total_items    = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}afs_form" );
	$total_pages    = ceil( $total_items / $items_per_page );

	return array(
		'data'         => $wpdb->get_results(
			$wpdb->prepare(
				"SELECT id, amount, buyer, receipt_id, buyer_email, entry_at FROM {$wpdb->prefix}afs_form ORDER BY id DESC LIMIT %d OFFSET %d",
				$items_per_page,
				$offset
			),
			ARRAY_A
		),
		'current_page' => $current_page,
		'total_pages'  => $total_pages,
	);
}

/**
 * Insert submitted data to `afs_form` table.
 */
function afs_insert_report( $args = array() ) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'afs_form';
	$defaults   = array(
		'amount'      => '',
		'buyer'       => '',
		'receipt_id'  => '',
		'items'       => '',
		'buyer_email' => '',
		'note'        => '',
		'city'        => '',
		'phone'       => '',
		'entry_by'    => '',
		'buyer_ip'    => '',
		'hash_key'    => '',
		'entry_at'    => current_time( 'mysql' ),
	);

	$format = array( '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s' );
	$data   = wp_parse_args( $args, $defaults );

	if ( isset( $data['id'] ) ) {

		$id = $data['id'];
		unset( $data['id'] );

		$updated = $wpdb->update(
			$table_name,
			$data,
			array( 'id' => $id ),
			$format,
			array( '%d' )
		);

		return $updated;
	} else {
		$inserted = $wpdb->insert( $table_name, $data, $format );

		if ( ! $inserted ) {
			return new \WP_Error( 'failed-to-insert', __( 'Failed to insert', 'afs-form' ) );
		}

		return $wpdb->insert_id;
	}
}

/**
 * Fetch data from the API and output a table.
 *
 * @param array $attributes Block attributes.
 */
function fetch_and_output_data( $attributes, $paged ) {
	$api_url = esc_url( site_url( "/wp-json/afs-forms/v1/reports?per_page={$attributes['rowsPerPage']}&page={$paged}&offset={$paged}" ) ); // phpcs:ignore
	$response = wp_remote_get(
		$api_url,
		array(
			'headers' => array(
				'Authorization' => 'Basic ' . base64_encode( 'admin:admin' ), // TODO: Please replace your dev site username:password.
			),
		)
	);

	if ( is_wp_error( $response ) ) {
		output_error_message( $response->get_error_message() );
	} else {
		handle_api_response( $response );
	}
}

/**
 * Output an error message.
 *
 * @param string $error_message Error message to display.
 */
function output_error_message( $error_message ) {
	printf( '<tr><td colspan="6">%s</td></tr>', esc_html( $error_message ) );
}

/**
 * Handle the API response and output the table rows.
 *
 * @param WP_Error|array $response API response.
 */
function handle_api_response( $response ) {
	$response_code = wp_remote_retrieve_response_code( $response );

	if ( 200 === $response_code ) {
		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( is_array( $data ) ) {
			output_table_rows( $data );
		} else {
			output_error_message( 'Invalid data format' );
		}
	} else {
		output_error_message( 'Error: ' . esc_html( $response_code ) );
	}
}

/**
 * Output table rows based on the API data.
 *
 * @param array $data API data.
 */
function output_table_rows( $data ) {
	foreach ( $data as $item ) {
		printf(
			'<tr key="%s">
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
			</tr>',
			esc_attr( $item['id'] ),
			esc_html( $item['id'] ),
			esc_html( $item['amount'] ),
			esc_html( $item['buyer'] ),
			esc_html( $item['receipt_id'] ),
			esc_html( $item['buyer_email'] ),
			esc_html( $item['entry_at'] )
		);
	}
}

/**
 * Output pagination buttons.
 *
 * @param int $total_pages Total number of pages.
 */
function output_pagination_buttons( $total_pages ) {
	echo '<div class="afs-footer-pagination"><div class="pagination">';

	// Output page number buttons
	for ( $i = 1; $i <= $total_pages; $i++ ) {
		echo '<button class="page-numbers' . ( $i === 1 ? ' current' : '' ) . '">' . esc_html( $i ) . '</button>'; // phpcs:ignore
	}

	// Output the "Next" button
	echo '<button class="next-btn">Next &gt;&gt;</button>';

	echo '</div></div>';
}

/**
 * Re-useable pagination.
 */
function afs_pagination( array $data ) {
	return paginate_links(
		array(
			'base'      => add_query_arg( 'paged', '%#%' ),
			'format'    => '',
			'prev_text' => __( '<< Previous' ),
			'next_text' => __( 'Next >>' ),
			'total'     => $data['total_pages'],
			'current'   => $data['current_page'],
		)
	);
}
