<?php

namespace AFS\Form_Submission\Admin\Widgets;

/**
 * Adds widget: Form.
 */
class Form_Widget extends \WP_Widget {

	public function __construct() {
		parent::__construct(
			'afs_widget',
			esc_html__( 'AFS Form', 'afs-form' ),
			array( 'description' => esc_html__( 'Form Description', 'afs-form' ) ) // Args
		);
	}

	private $widget_fields = array(
		array(
			'label' => 'Amount:',
			'id'    => 'amount',
			'type'  => 'number',
		),
		array(
			'label' => 'Buyer:',
			'id'    => 'buyer',
			'type'  => 'text',
		),
		array(
			'label' => 'Receipt ID:',
			'id'    => 'receipt_id',
			'type'  => 'text',
		),
		array(
			'label' => 'Items:',
			'id'    => 'items',
			'type'  => 'text',
		),
		array(
			'label' => 'Buyer Email:',
			'id'    => 'buyer_email',
			'type'  => 'email',
		),
		array(
			'label' => 'Note:',
			'id'    => 'note',
			'type'  => 'textarea',
		),
		array(
			'label' => 'City:',
			'id'    => 'city',
			'type'  => 'text',
		),
		array(
			'label' => 'Phone:',
			'id'    => 'phone',
			'type'  => 'tel',
		),
		array(
			'label' => 'Entry By:',
			'id'    => 'entry_by',
			'type'  => 'number',
		),
	);

	public function widget( $args, $instance ) {
		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		include AFS_INCLUDES_FILE . '/Frontend/views/form.php';

		echo $args['after_widget'];
	}

	public function field_generator( $instance ) {
		$output = '';
		foreach ( $this->widget_fields as $k => $widget_field ) {
			$default = '';
			if ( isset( $widget_field['default'] ) ) {
				$default = $widget_field['default'];
			}

			$widget_value = ! empty( $instance[ $widget_field['id'] ] ) ? $instance[ $widget_field['id'] ] : esc_html__( $default, 'afs-form' );

			switch ( $widget_field['type'] ) {
				case 'textarea':
					$output .= '<p>';
					$output .= '<label for="' . esc_attr( $this->get_field_id( $widget_field['id'] ) ) . '">' . esc_attr( $widget_field['label'], 'afs-form' ) . ':</label> ';
					$output .= '<textarea class="widefat" id="' . esc_attr( $this->get_field_id( $widget_field['id'] ) ) . '" name="' . esc_attr( $this->get_field_name( $widget_field['id'] ) ) . '" rows="6" cols="6" value="' . esc_attr( $widget_value ) . '">' . $widget_value . '</textarea>';
					$output .= '</p>';
					break;
				default:
					$output .= '<p> ';
					$output .= '<label for="' . esc_attr( $this->get_field_id( $widget_field['id'] ) ) . '">' . esc_attr( $widget_field['label'], 'afs-form' ) . ':</label> ';
					$output .= '<input class="widefat" id="' . esc_attr( $this->get_field_id( $widget_field['id'] ) ) . '" name="' . esc_attr( $this->get_field_name( $widget_field['id'] ) ) . '" type="' . $widget_field['type'] . '" value="' . esc_attr( $widget_value ) . '">';
					$output .= '</p>';
			}
		}
		echo $output;
	}

	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Form', 'afs-form' );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'afs-form' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php
		// $this->field_generator( $instance );
	}

	public function update( $new_instance, $old_instance ) {
		$instance          = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		foreach ( $this->widget_fields as $widget_field ) {
			switch ( $widget_field['type'] ) {
				default:
					$instance[ $widget_field['id'] ] = ( ! empty( $new_instance[ $widget_field['id'] ] ) ) ? strip_tags( $new_instance[ $widget_field['id'] ] ) : '';
			}
		}
		return $instance;
	}
}
