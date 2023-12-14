<?php

printf( '<div class="notice notice-success hidden afs-success-message is-dismissible"><p>' . __( 'Report update successfully!', 'afs-submission' ) . '</p></div>' );
printf( '<div class="notice notice-error hidden afs-error-message is-dismissible"><p>' . __( 'Something went wrong!', 'afs-submission' ) . '</p></div>' );

$results = get_afs_form_by_id( $id );

if ( is_array( $results ) && count( $results ) > 0 ) {
	$items         = explode( ', ', $results[0]['items'] );
	$items_wrapper = array();
	foreach ( $items as $item ) {
		$items_wrapper[] = sprintf( '<span class="item"><span class="text">%s</span><span class="item-remove">X</span></span>', wp_kses_post( $item ) );
	}
}

$items_html = implode( '', $items_wrapper );
?>

<div class="afs-form-wrapper afs-form-wrapper-admin-area" id="afs-form-wrapper">
	<h1 class="wp-heading-inline"><?php _e( 'Edit Report', 'afs-form' ); ?></h1>
	<div class="afs-form-wrapper" id="afs-form-wrapper">
		<form method="POST" id="submissionForm" class="submissionForm">

			<div class="form-row">
				<div class="form-col">
				<label for="amount"><?php _e( 'Enter Amount (only number)', 'afs-form' ); ?></label>
				<input type="number" name="amount" id="amount" class="amount" value="<?php echo esc_attr( $results[0]['amount'] ); ?>" >
				<p class="afs-error amount-error"></p>
				</div>

				<div class="form-col">
					<label for="receipt_id"><?php _e( 'Receipt ID', 'afs-form' ); ?></label>
					<input type="text" name="receipt_id" id="receipt_id" class="receipt_id" value="<?php echo esc_attr( $results[0]['receipt_id'] ); ?>">
					<p class="afs-error receipt_id-error"></p>
				</div>

				<div class="form-col last">
				<label for="buyer"><?php _e( 'Enter Buyer (max 20 chars)', 'afs-form' ); ?></label>
				<input type="text" name="buyer" id="buyer" class="buyer" value="<?php echo esc_attr( $results[0]['buyer'] ); ?>">
				<p class="afs-error buyer-error"></p>
				</div>
			</div>

			<div class="form-row">
				<div class="form-col">	
				<label for="buyer_email"><?php _e( 'Buyer Email', 'afs-form' ); ?></label>
					<input type="email" name="buyer_email" id="buyer_email" class="buyer_email" value="<?php echo esc_attr( $results[0]['buyer_email'] ); ?>">
					<p class="afs-error buyer_email-error"></p>
				</div>
				<div class="form-col last">				
					<label for="entry_by"><?php _e( 'Entry By (only number)', 'afs-form' ); ?></label>
					<input type="number" name="entry_by" id="entry_by" class="entry_by" value="<?php echo esc_attr( $results[0]['entry_by'] ); ?>">
					<p class="afs-error  entry_by-error"></p>
				</div>
			</div>
			<div class="form-row">
				<div class="form-col">	
				<label for="city"><?php _e( 'City', 'afs-form' ); ?></label>
					<input type="text" name="city" id="city" class="city" value="<?php echo esc_attr( $results[0]['city'] ); ?>">
					<p class="afs-error city-error"></p>
				</div>
				<div class="form-col last">
				<label for="phone"><?php _e( 'Phone', 'afs-form' ); ?></label>
					<input type="tel" name="phone" id="phone" class="phone" value="<?php echo esc_attr( $results[0]['phone'] ); ?>">
					<p class="afs-error phone-error"></p>
				</div>
			</div>

			<div class="form-row">
				<div class="form-col last">
				<label for="items"><?php _e( 'Items (you may add multiple)', 'afs-form' ); ?></label>
					<div id="itemsContainer" class="itemsContainer">
						<input type="text" id="items" class="items" name="items">
						<button type="button" id="addItem" class="addItem"><?php _e( 'Add Item', 'afs-form' ); ?></button>
						<div class="itemsContainer_inner"><?php echo wp_kses_post( $items_html ); ?></div>
						<p class="afs-error items-error"></p>
					</div>
				</div>
			</div>

			<div class="form-row">
				<div class="form-col">	
				<label for="note"><?php _e( 'Note (max 30 words)', 'afs-form' ); ?></label>
					<textarea name="note" id="note" class="note" rows="4"><?php echo wp_kses_post( $results[0]['note'] ); ?></textarea>
					<p class="afs-error note-error"></p>
				</div>
			</div>

			<div>
				<input type="hidden" name="id"  value="<?php echo esc_attr( $results[0]['id'] ); ?>" />
				<input type="hidden" name="security" value="<?php echo esc_attr( wp_create_nonce( 'afs_form_nonce' ) ); ?>"/>
				<input type="submit" value="Update Report">
			</div>
		</form>
	</div>
</div>