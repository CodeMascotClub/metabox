<?php # -*- coding: utf-8 -*-

namespace TheDramatist\MetaBox;

/**
 * Class MetaBox
 *
 * @package TheDramatist\MetaBox\MetaBox
 *
 * @author K. M. Rashedun-Naby <naby88@gmail.com>
 * @link http://thedramatist.me
 * @since 1.0.0
 * @version 1.0.0
 * @license MIT
 */
class MetaBox {
	private $metabox_config;
	private $post_type;
	private $label;
	private $id;
	private $context;
	private $priority;
	private $error_message;

	/**
	 * MetaBox constructor.
	 *
	 * @param        $id
	 * @param string $title
	 * @param        $post_type
	 * @param        $metabox_config
	 * @param string $context
	 * @param string $priority
	 */
	public function __construct(
		$id,
		$title = 'Attributes',
		$post_type,
		$metabox_config,
		$context = 'advanced',
		$priority = 'high'
	) {

		$this->post_type      = $post_type;
		$this->id             = $id;
		$this->metabox_config = $metabox_config;
		$this->title          = $title;
		$this->context        = $context;
		$this->priority       = $priority;

		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
		add_action( 'save_post', [ $this, 'save_metabox_data' ] );
		add_filter( 'post_updated_messages', [ $this, 'messages' ] );
	}

	/**
	 * Initiating metaboxes.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_meta_boxes() {
		add_meta_box(
			$this->id,
			$this->title,
			[
				$this,
				'render_meta_boxes',
			],
			$this->post_type,
			$this->context,
			$this->priority
		);
	}

	/**
	 * Table headline method.
	 *
	 * @since 1.0.0
	 *
	 * @param $label
	 * @param $text
	 *
	 * @return void
	 */
	protected function table_headline( $label, $text ) {
		echo '<th><label for="'
			 . esc_attr( $label )
			 . '">'
			 . esc_html( $text )
			 . '</label></th>';
	}

	/**
	 * Render metaboxes
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function render_meta_boxes() {

		global $post;

		ob_start();
		$data                              = [];
		$data[ $this->id . '_meta_nonce' ] = wp_create_nonce(
			wp_create_nonce( $this->id . '-meta' )
		);

		$data = [];
		// Get the existing values from database

		echo '<table class="form-table">';
		foreach ( $this->metabox_config as $item ) {
			$value = get_post_meta(
				$post->ID,
				$item[0],
				true
			);

			echo '<tr>';

			$req = '';
			if (
				strpos( $item[3], 'required' ) !== false
			) {
				$req .= '*';
			}

			switch ( $item[2] ) {
				// Section Titles
				case 'title':
					if ( ! empty( $item[1] ) ) {
						echo '<tr><th style="padding: 0"><h2 class="'
							 . esc_attr( $item[0] )
							 . '">'
							 . esc_html( $item[1] )
							 . '</h2></th></tr>';
					}
					break;

				case 'input':
					$this->table_headline( $item[1], $item[1] . $req );
					echo '<td><input class="widefat" name="'
						 . esc_attr( $item[0] )
						 . '" id="'
						 . esc_attr( $item[0] )
						 . '" type="text" value="'
						 . esc_attr( $value )
						 . '" /></td>';
					break;

				case 'select':
					$this->table_headline( $item[1], $item[1] . $req );
					echo '<td><select class="widefat" name="'
						 . esc_attr( $item[0] )
						 . '" id="'
						 . esc_attr( $item[0] )
						 . '">';

					foreach ( $item[4] as $option_key => $option_value ) {
						$select = '';
						if( $option_key == $value ) {
							$select = 'selected="selected"';
						}
						echo '<option '
							 . $select
							 . ' value="'
							 . esc_attr( $option_key )
							 . '">'
							 . esc_html( $option_value )
							 . '</opton>';
					}
					echo '</td>';
					break;

				case 'check':
					$this->table_headline( $item[1], $item[1] . $req );
					echo '<td>';
					$value_array = explode( ',', $value );
					foreach ( $item[4] as $option_key => $option_value ) {
						$checked = '';
						if (
							in_array(
								$option_key,
								$value_array,
								true
							)
						) {
							$checked = 'checked="checked"';
						}

						echo '<input class="selectit widefat" type="checkbox" '
							 . $checked
							 . ' name="'
							 . esc_attr( $item[0] )
							 . '[]" value="'
							 . esc_attr( $option_key )
							 . '">'
							 . esc_html( $option_value )
							 . '</br>';
					}
					echo '</td>';
					break;

				case 'radio':
					$this->table_headline( $item[1], $item[1] . $req );
					echo '<td>';

					$i = 0;
					foreach ( $item[4] as $option_key => $option_value ) {
						$checked = '';
						if ( $value == $option_key ) {
							$checked = 'checked="checked"';
						}
						echo '<input '
							 . $checked
							 . ' type="radio" class="widefat" name="'
							 . esc_attr( $item[0] ) .
							 '" value="'
							 . esc_attr( $option_key )
							 . '">'
							 . esc_html( $option_value )
							 . '</br>';
						$i ++;
					}

					echo '</td>';
					break;

				case 'texteditor':
					echo '</tr>';
					echo '</table>';
					echo '<td id="myeditor">';
					echo '<label style="font-weight: bold" for="'
						 . esc_attr( $item[1] )
						 . '">'
						 . esc_html( $item[1] )
						 . '</label>';
					echo '<input type="hidden" name="'
						 . esc_attr( $item[0] )
						 . '" id="shortdescmeta_noncename" value="'
						 . esc_html( $value )
						 . '" />';

					$settings = [
						'media_buttons' => false,
						'textarea_rows' => 5,
						'tinymce'       => [
							'menubar'  => false,
							'toolbar1' => 'bold,italic,underline,blockquote,strikethrough,bullist,numlist,alignleft,aligncenter,alignright,undo,redo,link,unlink,fullscreen',
							'toolbar2' => '',
							'toolbar3' => '',
							'toolbar4' => '',
						],
					];
					wp_editor( $value, $item[0], $settings );

					echo '<table>';
					echo '<tr>';

					break;
			}
			echo '</tr>';
		}
		echo '</table>';
		ob_end_flush();
	}
	
	/**
	 * Message generating method.
	 *
	 * @since 1.0.0
	 *
	 * @param $messages
	 *
	 * @return mixed
	 */
	public function messages( $messages ) {
		$this->error_message = get_transient(
			'product_error_message_$post->ID'
		);

		$message_no = isset( $_GET['message'] )
			? sanitize_text_field( $_GET['message'] )
			: '0';

		delete_transient( 'product_error_message_{$post->ID}' );

		if ( ! empty( $this->error_message ) ) {
			$messages[ $this->post_type ] = [
				'{$message_no}' => $this->error_message,
			];
		}

		return $messages;
	}

	/**
	 * Saving metabox data.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function save_metabox_data() {
		global $post;
		if (
			isset( $_POST['post_type'] ) &&
			$_POST['post_type'] === $this->post_type &&
			current_user_can( 'edit_post', $post->ID )
		) {
			$this->error_message = '';
			$data                = [];
			foreach ( $this->metabox_config as $item ) {

				/**
				 * Use `the_dramatist_metabox_api_data_filter`
				 * to validate or sanitize data
				 * for custom data validation.
				 */
				$post_raw_data = apply_filters(
					'the_dramatist_metabox_api_data_filter',
					$_POST[ $item[0] ], // The data
					$item[0] // Data Field Token
				);

				if ( is_array( $post_raw_data ) ) {
					$post_data = implode(
						',',
						$post_raw_data
					);
				} else {
					$post_data = $post_raw_data;
				}

				if (
					( empty( $post_data ) || '-' === $post_data ) &&
					( false !== strpos( $item[3], 'required' ) )
				) {
					$this->error_message .= $item[1] . __( ' cannot be empty' ) . '</br>';
				}

				$data[ $item[0] ] = $post_data;
			}
		}

		if ( empty( $data ) ) {
			return;
		}

		foreach ( $data as $item_key => $item_value ) {
			update_post_meta( $post->ID, $item_key, $item_value );
		}
	}
}
