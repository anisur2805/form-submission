<?php

namespace AFS\Form_Submission;

class Ajax {
	public function __construct() {
		// AJAX handler for form submission
		add_action( 'wp_ajax_handle_submission', array( $this, 'handle_submission' ) );
		add_action( 'wp_ajax_nopriv_handle_submission', array( $this, 'handle_submission' ) );

		// AJAX handler for report delete
		add_action( 'wp_ajax_afs-delete', array( $this, 'delete_report' ) );
	}

	public function handle_submission() {
		$permission = check_ajax_referer( 'afs_form_nonce', 'security', false );
		if ( false === $permission ) {
			wp_send_json(
				array(
					'error' => true,
					'msg'   => __( 'Nonce verification failed', 'afs-fs' ),
				)
			);
			wp_die();
		} else {
			$id          = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
			$amount      = isset( $_POST['amount'] ) ? absint( $_POST['amount'] ) : 0;
			$buyer       = isset( $_POST['buyer'] ) ? sanitize_text_field( wp_unslash( $_POST['buyer'] ) ) : '';
			$receipt_id  = isset( $_POST['receipt_id'] ) ? sanitize_text_field( wp_unslash( $_POST['receipt_id'] ) ) : '';
			$items       = isset( $_POST['items'] ) ? wp_unslash( $_POST['items'] ) : '';
			$buyer_email = isset( $_POST['buyer_email'] ) ? sanitize_email( wp_unslash( $_POST['buyer_email'] ) ) : '';
			$note        = isset( $_POST['note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['note'] ) ) : '';
			$city        = isset( $_POST['city'] ) ? sanitize_text_field( wp_unslash( $_POST['city'] ) ) : '';
			$phone       = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
			$entry_by    = isset( $_POST['entry_by'] ) ? absint( $_POST['entry_by'] ) : 0;

			$salt_key = 'AbZE7i{S)iVyd!6Z| x@2/bYNADwZB1##Mty$Us*(/| P3!9|#`E@9HJ%{}RLJRE';
			$hash_key = hash_hmac( 'sha512', $receipt_id, $salt_key );
			$buyer_ip = $_SERVER['REMOTE_ADDR'];

			$errors = array();

			if ( false === $buyer_ip ) {
				$errors['buyer_ip'] = __( 'Buyer IP not found', 'afs-fs' );
			}

			if ( $amount <= 0 ) {
				$errors['amount'] = __( 'Only positive number', 'afs-fs' );
			}

			if ( empty( $buyer ) || preg_match( '/[^a-zA-Z0-9\s]/', $buyer ) || strlen( $buyer ) > 20 ) {
				$errors['buyer'] = __( 'Max 20 characters.', 'afs-fs' );
			}

			if ( empty( $receipt_id ) ) {
				$errors['receipt_id'] = __( 'Receipt ID is required.', 'afs-fs' );
			}

			if ( empty( $items ) ) {
				$errors['items'] = __( 'Please add minimum one item', 'afs-fs' );
			}

			if ( ! is_email( $buyer_email ) ) {
				$errors['buyer_email'] = __( 'Invalid email address', 'afs-fs' );
			}

			$note_word_count = str_word_count( strip_tags( $note ) );
			if ( $note_word_count > 30 ) {
				$errors['note'] = __( 'Maximum length reached', 'afs-fs' );
			}

			if ( empty( $city ) || ! preg_match( '/^[a-zA-Z\s]+$/', $city ) ) {
				$errors['city'] = __( 'Only text and spaces are allowed.', 'afs-fs' );
			}

			if ( ! preg_match( '/[^0-9]|0(?=0*880)/', $phone ) ) {
				$errors['phone'] = __( 'Phone number is required.', 'afs-fs' );
			}

			if ( $entry_by <= 0 ) {
				$errors['entry_by'] = __( 'Only positive number.', 'afs-fs' );
			}

			// Check for validation errors.
			if ( ! empty( $errors ) ) {
				wp_send_json(
					array(
						'error'  => true,
						'errors' => $errors,
					)
				);
			}

			$args = array(
				'amount'      => $amount,
				'buyer'       => $buyer,
				'receipt_id'  => $receipt_id,
				'items'       => $items,
				'buyer_email' => $buyer_email,
				'note'        => $note,
				'city'        => $city,
				'phone'       => $phone,
				'entry_by'    => $entry_by,
				'buyer_ip'    => $buyer_ip,
				'hash_key'    => $hash_key,
				'entry_at'    => current_time( 'mysql', true ),
			);

			if ( $id ) {
				$args['id'] = $id;
			}

			$insert_id = afs_insert_report( $args );
			if ( is_wp_error( $insert_id ) ) {
				wp_die( $insert_id->get_error_message() );
			}

			$show_notice = false;
			if ( $id ) {
				$show_notice = true;
			}

			if ( $insert_id ) {
				wp_send_json(
					array(
						'success'     => true,
						'message'     => __( 'Successfully submitted the form!', 'afs-form' ),
						'show_notice' => $show_notice,
						'is_admin'    => is_user_logged_in(),
					)
				);
			} else {
				wp_send_json(
					array(
						'error'   => true,
						'message' => __( 'Something went wrong while inserting data.', 'afs-form' ),
						'errors'  => $errors,
						'od'      => $insert_id,
					)
				);
			}
		}
	}

	public function delete_report() {
		$permission = check_ajax_referer( 'afs-admin-nonce', 'security', false );
		if ( false === $permission ) {
			wp_send_json(
				array(
					'error' => true,
					'msg'   => __( 'Nonce verification failed', 'afs-fs' ),
				)
			);
			wp_die();
		} else {
			$item_id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
			if ( $item_id < 1 ) {
				return;
			}

			afs_delete_item( $item_id );

			wp_send_json(
				array(
					'success' => true,
					'message' => __( 'Report deleted successfully!', 'afs-form' ),
				)
			);
		}
	}
}
