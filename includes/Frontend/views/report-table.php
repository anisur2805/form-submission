<div class="afs-report-table-fe">
	<h3><?php echo esc_html( 'Report List', 'afs-form' ); ?></h3>
	<p><?php echo esc_html( 'This list is only visible for logged-in and minimum of Editor roles.', 'afs-form' ); ?></p>
	<table class="afs-report-table">
		<thead>
			<tr>
				<th><?php echo esc_html( 'ID', 'afs-form' ); ?></th>
				<th><?php echo esc_html( 'Amount', 'afs-form' ); ?></th>
				<th><?php echo esc_html( 'Buyer', 'afs-form' ); ?></th>
				<th><?php echo esc_html( 'Receipt ID', 'afs-form' ); ?></th>
				<th><?php echo esc_html( 'Buyer Email', 'afs-form' ); ?></th>
				<th><?php echo esc_html( 'Entry Date', 'afs-form' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$report_rows = atf_display_report();
			foreach ( $report_rows['data'] as $row ) {
				echo '<tr>';
				echo '<td>' . esc_html( $row['id'] ) . '</td>';
				echo '<td>' . esc_html( $row['amount'] ) . '</td>';
				echo '<td>' . esc_html( $row['buyer'] ) . '</td>';
				echo '<td>' . esc_html( $row['receipt_id'] ) . '</td>';
				echo '<td>' . esc_html( $row['buyer_email'] ) . '</td>';
				echo '<td>' . esc_html( $row['entry_at'] ) . '</td>';
				echo '</tr>';
			}
			?>
		</tbody>
	</table>

	<div class="afs-footer-pagination">
		<?php
		$data = array(
			'total_pages'  => $report_rows['total_pages'],
			'current_page' => $report_rows['current_page'],
		);
		echo afs_pagination( $data );
		?>
	</div>
</div>
