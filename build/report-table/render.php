<?php if ( current_user_can( 'editor' ) || current_user_can( 'administrator' ) && is_user_logged_in() ) : ?>
<div class="afs-report-table-fe">
	<h3><?php echo $attributes['title']; ?></h3>
	<p><?php echo $attributes['content']; ?></p>

	<table class="afs-report-table">
		<thead>
			<tr>
			<tr>
				<th><?php esc_html_e( 'ID', 'afs-fs' ); ?></th>
				<th><?php esc_html_e( 'Amount', 'afs-fs' ); ?></th>
				<th><?php esc_html_e( 'Buyer', 'afs-fs' ); ?></th>
				<th><?php esc_html_e( 'Receipt ID', 'afs-fs' ); ?></th>
				<th><?php esc_html_e( 'Buyer Email', 'afs-fs' ); ?></th>
				<th><?php esc_html_e( 'Entry Date', 'afs-fs' ); ?></th>
			</tr>
			<tr>
			<?php
			$paged = max( 1, get_query_var( 'paged' ) );
			fetch_and_output_data( $attributes, $paged );
			?>
			</tr>
			</tr>
		</thead>

		<tbody>
		</tbody>
	</table>

	<?php if ( $attributes['showPagination'] ) : // phpcs:ignore ?>
	<div class="afs-footer-pagination">	
		<?php
			$data = array(
				'total_pages'  => $attributes['rowsPerPage'],
				'current_page' => $paged,
			);

			echo afs_pagination( $data );
			?>
	</div>
	<?php endif; ?>
</div>
<?php endif; ?>
