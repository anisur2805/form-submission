<?php

namespace AFS\Form_Submission\Admin;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Report_List extends \WP_List_Table {
	public function __construct() {
		parent::__construct(
			array(
				'singular' => 'report',
				'plural'   => 'reports',
				'ajax'     => false,
			)
		);
	}

	public function get_columns() {
		return array(
			'cb'          => '<input type="checkbox" />',
			'amount'      => __( 'Amount', 'afs-form' ),
			'buyer_email' => __( 'Email', 'afs-form' ),
			'items'       => __( 'items', 'afs-form' ),
			'entry_by'    => __( 'Entry By', 'afs-form' ),
			'phone'       => __( 'Phone', 'afs-form' ),
			'entry_at'    => __( 'Date', 'afs-form' ),
			'receipt_id'  => __( 'Receipt ID', 'afs-form' ),
		);
	}

	/**
	 * Get sortable columns
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'amount'     => array( 'amount', true ),
			'entry_by'   => array( 'entry_by', true ),
			'items'      => array( 'items', true ),
			'entry_at'   => array( 'entry_at', true ),
			'receipt_id' => array( 'receipt_id', true ),
		);

		return $sortable_columns;
	}

	protected function column_default( $item, $column_name ) {

		switch ( $column_name ) {
			case 'value':
				# code...
				break;

			default:
				return isset( $item->$column_name ) ? $item->$column_name : '';
		}
	}

	public function column_amount( $item ) {
		$action           = array();
		$action['edit']   = sprintf(
			'<a href="' . admin_url( 'admin.php?page=afs-submission-report&action=edit&id=%d' ) . '" >%s</a>',
			esc_attr( $item['id'] ),
			__( 'Edit' )
		);
		$action['delete'] = sprintf(
			'<a class="submit_delete" data-delete-id="%1$s" href="#">%2$s</a>',
			esc_attr( $item['id'] ),
			__( 'Delete' )
		);
		return sprintf(
			'<strong>%s</strong>%s',
			esc_attr( $item['amount'] ),
			$this->row_actions( $action ),
		);
	}

	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="report_id[]" value="%d" />',
			$item['id']
		);
	}

	public function column_receipt_id( $item ) {
		return sprintf(
			'<strong>%s</strong>',
			$item['receipt_id'],
		);
	}

	public function column_entry_by( $item ) {
		return sprintf(
			'<strong>%s</strong>',
			$item['entry_by'],
			$item['entry_by'],
		);
	}

	public function column_items( $item ) {
		return sprintf(
			'<strong>%s</strong>',
			$item['entry_by'],
			$item['entry_by'],
		);
	}

	public function column_buyer_email( $item ) {
		return sprintf(
			'<a href="mailto:%s"><strong>%s</strong></a>',
			$item['buyer_email'],
			$item['buyer_email'],
		);
	}

	public function column_phone( $item ) {
		return sprintf(
			'<a href="mailto:%s"><strong>%s</strong></a>',
			$item['phone'],
			$item['phone'],
		);
	}

	public function column_entry_at( $item ) {
		return sprintf(
			'<a href="%s"><strong>%s</strong></a>',
			$item['entry_at'],
			$item['entry_at'],
		);
	}

	protected function get_bulk_actions() {
		return array(
			'bulk-delete' => 'Delete',
		);
	}

	protected function process_bulk_action() {
		$action = $this->current_action();

		if ( 'bulk-delete' === $action ) {
			$selected_items = $_REQUEST['bulk-delete'] ?? array();

			if ( is_array( $selected_items ) && ! empty( $selected_items ) ) {
				global $wpdb;
				$table_name = $wpdb->prefix . 'afs_form';

				foreach ( $selected_items as $item_id ) {
					$wpdb->delete(
						$table_name,
						array( 'id' => $item_id ),
						array( '%d' ),
					);
					echo $wpdb->last_query;
				}
			}
		}
	}

	protected function column_delete( $item ) {
		$actions = array(
			'delete' => sprintf(
				'<a href="?page=%s&action=%s&item=%s">Delete</a>',
				esc_attr( $_REQUEST['page'] ),
				'delete',
				absint( $item['id'] )
			),
		);

		return $this->row_actions( $actions );
	}


	protected function delete_item( $item_id ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'afs_form';

		// Perform the delete operation for the single item
		$deleted_item = $wpdb->delete(
			$table_name,
			array( 'id' => $item_id ),
			array( '%d' ),
		);
	}

	public function prepare_items() {
		$column   = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();
		$per_page = 20;

		$primary = 'amount';

		$this->_column_headers = array( $column, $hidden, $sortable, $primary );

		$per_page     = 20;
		$current_page = $this->get_pagenum();
		$offset       = ( $current_page - 1 ) * $per_page;

		$search = isset( $_REQUEST['s'] ) ? sanitize_text_field( $_REQUEST['s'] ) : '';

		$args = array(
			'number' => $per_page,
			'offset' => $offset,
			's'      => $search,
		);

		if ( isset( $_REQUEST['orderby'] ) && isset( $_REQUEST['order'] ) ) {
			$args['orderby'] = $_REQUEST['orderby'];
			$args['order']   = $_REQUEST['order'];
		}

		$this->items = afs_get_reports( $args );

		$this->set_pagination_args(
			array(
				'total_items' => afs_report_count(),
				'per_page'    => $per_page,
			)
		);

		$this->process_bulk_action();
	}
}
