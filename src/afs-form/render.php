<div class="afs-form-wrapper" id="afs-form-wrapper">
<?php
	printf( '<h3>%s</h3>', esc_html( $attributes['title'] ) );
?>
	<form method="POST" id="submissionForm" class="submissionForm">
		<div class="form-row">
			<div class="form-col">
				<label for="amount"><?php _e( 'Enter Amount (only number)', 'afs-form' ); ?></label>
				<input type="number" name="amount" id="amount" class="amount">
				<p class="afs-error amount-error"></p>
			</div>

			<div class="form-col">
				<label for="receipt_id"><?php _e( 'Receipt ID', 'afs-form' ); ?></label>
				<input type="text" name="receipt_id" id="receipt_id" class="receipt_id">
				<p class="afs-error receipt_id-error"></p>
			</div>

			<div class="form-col last">
				<label for="buyer"><?php _e( 'Enter Buyer (max 20 chars)', 'afs-form' ); ?></label>
				<input type="text" name="buyer" id="buyer" class="buyer">
				<p class="afs-error buyer-error"></p>
			</div>
		</div>
		<div class="form-row">
			<div class="form-col">	
				<label for="buyer_email"><?php _e( 'Buyer Email', 'afs-form' ); ?></label>
				<input type="email" name="buyer_email" id="buyer_email" class="buyer_email">
				<p class="afs-error buyer_email-error"></p>
			</div>
			<div class="form-col last">				
				<label for="entry_by"><?php _e( 'Entry By (only number)', 'afs-form' ); ?></label>
				<input type="number" name="entry_by" id="entry_by" class="entry_by">
				<p class="afs-error  entry_by-error"></p>
			</div>
		</div>
		<div class="form-row">
			<div class="form-col">	
				<label for="city"><?php _e( 'City', 'afs-form' ); ?></label>
				<input type="text" name="city" id="city" class="city">
				<p class="afs-error city-error"></p>
			</div>
			<div class="form-col last">
				<label for="phone"><?php _e( 'Phone', 'afs-form' ); ?></label>
				<input type="tel" name="phone" id="phone" class="phone">
				<p class="afs-error phone-error"></p>
			</div>
		</div>

		<div class="form-row">
			<div class="form-col last">
				<label for="items"><?php _e( 'Items (you may add multiple)', 'afs-form' ); ?></label>
				<div id="itemsContainer" class="itemsContainer">
					<input type="text" id="items" class="items" name="items">
					<button type="button" id="addItem" class="addItem"><?php _e( 'Add Item', 'afs-form' ); ?></button>
					<div class="itemsContainer_inner">
					</div>
					<p class="afs-error items-error"></p>
				</div>
			</div>
		</div>

		<div class="form-row">
			<div class="form-col">	
				<label for="note"><?php _e( 'Note (max 30 words)', 'afs-form' ); ?></label>
				<textarea name="note" id="note" class="note" rows="4"></textarea>
				<p class="afs-error note-error"></p>
			</div>
		</div>
		<div>
			<input type="hidden" name="security" value="<?php echo esc_attr( wp_create_nonce( 'afs_form_nonce' ) ); ?>"/>
			<input type="submit" value="Submit">
		</div>
	</form>
	<div id="resultContainer" class="resultContainer"></div>
</div>