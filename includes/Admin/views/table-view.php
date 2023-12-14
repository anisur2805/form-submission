<?php
use AFS\Form_Submission\Admin\Report_List;

?>

<form id="art-search-form" method="GET">
	<?php
	printf( '<h1>%s</h1>', get_admin_page_title() );
	$itc_subscriber_table = new Report_List();
	$itc_subscriber_table->prepare_items();
	// Search form
	$itc_subscriber_table->search_box( 'search', 'search_id' );
	$itc_subscriber_table->display();
	echo '<input type="hidden" name="page" class="hello-world" value="' . esc_attr( $_REQUEST['page'] ) . '"/>';
	?>
</form>