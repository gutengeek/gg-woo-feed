<?php
/**
 * Define
 * Note: only use for internal purpose.
 *
 * @package     GG_Woo_Feed
 * @since       1.0
 */
namespace GG_Woo_Feed\Libraries\Form\Field;

use GG_Woo_Feed\Libraries\Form\Form;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * HTML Form
 *
 * A helper class for outputting common HTML elements, such as product drop downs
 *
 * @package     GG_Woo_Feed
 * @subpackage  GG_Woo_Feed\Libraries
 */
class File {
	/**
	 * @var array
	 */
	public $args;

	/**
	 * @var \GG_Woo_Feed\Libraries\Form\Form
	 */
	public $form;

	/**
	 * @var
	 */
	public $type;

	/**
	 * Init Constructor of this
	 *
	 * @return string
	 *
	 */
	public function __construct( $args, Form $form, $type = 'file' ) {
		$classes = [
			'gg_woo_feed-upload-file',
			( 'file_list' === $type ) ? 'gg_woo_feed-upload-list' : '',
			'regular-text',
			'form-control',
		];

		$defaults = [
			'id'              => '',
			'hidden_id'       => '',
			'value'           => isset( $args['default'] ) ? $args['default'] : null,
			'name'            => '',
			'button_text'     => isset( $args['button_text'] ) ? esc_html( $args['button_text'] ) : esc_html__( 'Add or Upload File', 'gg-woo-feed' ),
			'description'     => null,
			'class'           => esc_attr( implode( ' ', array_map( 'sanitize_html_class', $classes ) ) ),
			'data'            => false,
			'required'        => false,
			'id_value'        => null,
			'size'            => 50,
			'js_dependencies' => 'media-editor',
			'preview_size'    => 'thumbnail',
			'attributes'      => [],
			'query_args'      => [],
		];

		$form->add_dependencies( 'media-editor' );

		$args = wp_parse_args( $args, $defaults );

		$valued = $form->get_field_value( $args );
		if ( null == $valued ) {
			$value = '';
		} else {
			$value = $valued;
		}

		$args['value']     = ! isset( $args['attributes']['value'] ) || ! $args['attributes']['value'] ? $value : $args['attributes']['value'];
		$args['hidden_id'] = $args['hidden_id'] ? $args['hidden_id'] : $args['id'] . '_id';
		$id_value          = $args['id_value'] ? $args['id_value'] : $form->get_field_value( [ 'id' => $args['hidden_id'] ] );
		$id_value          = $id_value ? absint( $id_value ) : '';
		$args['id_value']  = $id_value;

		$this->args = $args;
		$this->form = $form;
		$this->type = $type;

		$this->render();
	}

	/**
	 * Render.
	 */
	public function render() {
		if ( 'file_list' === $this->type ) {
			$this->render_file_list();
		} else {
			$this->render_file();
		}
	}

	/**
	 * Render file.
	 */
	public function render_file() {
	    $value = ! isset( $this->args['attributes']['value'] ) || ! $this->args['attributes']['value'] ? 'value="' . esc_attr( $this->args['value'] ) . '"' : '';
		$data = '';
		if ( ! empty( $this->args['data'] ) ) {
			foreach ( $this->args['data'] as $key => $_value ) {
				$data .= 'data-' . $key . '="' . $_value . '" ';
			}
		}

		if ( ! empty( $this->args['query_args'] ) ) {
			$data .= 'data-queryargs=\'' . json_encode( $this->args['query_args'] ) . '\' ';
        }

		if ( ! empty( $this->args['attributes'] ) ) {
			foreach ( $this->args['attributes'] as $key => $_value ) {
				$data .= $key . '="' . esc_attr( $_value ) . '" ';
				if ( $key == 'required' ) {
					$this->args['required'] = true;
				}
			}
		}

		if ( $this->args['required'] ) {
			$data .= ' required="required" ';
		}

		$output = '<div class="gg_woo_feed-field-wrap gg_woo_feed-file-field-wrap form-group" id="' . sanitize_key( $this->form->form_id . $this->args['id'] ) . '-wrap" >';

		$output .= '<label class="gg_woo_feed-label" for="' . sanitize_key( $this->form->form_id . $this->args['id'] ) . '">' . esc_html( $this->args['name'] ) . '</label>';
		$output .= '<div class="gg_woo_feed-field-main">';
		$output .= '<input type="hidden" name="' . esc_attr( $this->args['id'] ) . '" id="' . sanitize_key( $this->form->form_id . $this->args['id'] ) . '" ' . $value . ' class="' .
		           $this->args['class'] . '" ' . $data . ' size="' . esc_attr( $this->args['size'] ) . '" data-previewsize="[150,150]" data-sizename="' . esc_attr( $this->args['preview_size'] ) . '"/>';
		$output .= '<input class="gg_woo_feed-upload-button button-secondary" type="button" value="' . $this->args['button_text'] . '">';
		$output .= '<input type="hidden" class="gg_woo_feed-upload-file-id" name="' . esc_attr( $this->args['hidden_id'] ) . '" id="' . sanitize_key( $this->args['hidden_id'] ) . '" value="' . esc_attr(
				$this->args['id_value']
			) . '">';

		if ( ! empty( $this->args['description'] ) ) {
			$output .= '<p class="gg_woo_feed-description">' . $this->args['description'] . '</p>';
		}

		$output .= '<div id="' . esc_attr( $this->args['id'] ) . '-status" class="gg_woo_feed-media-status">';

		if ( ! empty( $this->args['value'] ) ) {
			$output .= $this->get_file_preview_output();
		}

		$output .= '</div>';

		$output .= '</div>';
		$output .= '</div>';

		echo $output;
	}

	/**
	 * Render file list.
	 */
	public function render_file_list() {
		$data = '';
		if ( ! empty( $this->args['data'] ) ) {
			foreach ( $this->args['data'] as $key => $_value ) {
				$data .= 'data-' . $key . '="' . $_value . '" ';
			}
		}

		if ( ! empty( $this->args['query_args'] ) ) {
			$data .= 'data-queryargs=\'' . json_encode( $this->args['query_args'] ) . '\' ';
		}

		if ( ! empty( $this->args['attributes'] ) ) {
			foreach ( $this->args['attributes'] as $key => $_value ) {
				$data .= $key . '="' . esc_attr( $_value ) . '" ';
				if ( $key == 'required' ) {
					$this->args['required'] = true;
				}
			}
		}

		if ( $this->args['required'] ) {
			$data .= ' required="required" ';
		}

		$output = '<div class="gg_woo_feed-field-wrap gg_woo_feed-file-list-field-wrap form-group" id="' . sanitize_key( $this->form->form_id . $this->args['id'] ) . '-wrap" >';

		$output .= '<label class="gg_woo_feed-label" for="' . sanitize_key( $this->form->form_id . $this->args['id'] ) . '">' . esc_html( $this->args['name'] ) . '</label>';

		$output .= '<input type="hidden" name="' . esc_attr( $this->args['id'] ) . '" id="' . sanitize_key( $this->form->form_id . $this->args['id'] ) . '" class="' .
		           $this->args['class'] . '" ' . $data . ' size="' . esc_attr( $this->args['preview_size'] ) . '" data-previewsize="[150,150]" data-sizename="' . esc_attr( $this->args['preview_size']
		           ) . '"/>';
		$output .= '<input class="gg_woo_feed-upload-button gg_woo_feed-upload-list button-secondary" type="button" value="' . $this->args['button_text'] . '">';
		$output .= '<input type="hidden" class="gg_woo_feed-upload-file-id" name="' . esc_attr( $this->args['hidden_id'] ) . '" id="' . sanitize_key( $this->args['hidden_id'] ) . '" value="' . esc_attr(
				$this->args['id_value']
			) . '">';

		if ( ! empty( $this->args['description'] ) ) {
			$output .= '<p class="gg_woo_feed-description">' . $this->args['description'] . '</p>';
		}

		$output .= '<ul id="' . esc_attr( $this->args['id'] ) . '-status" class="gg_woo_feed-media-status gg_woo_feed-attach-list">';

		$meta_value = $this->args['value'];

		if ( $meta_value && is_array( $meta_value ) ) {

			foreach ( $meta_value as $id => $fullurl ) {
				$id_input = sprintf( '<input type="hidden" id="filelist-%1$s" data-id="%1$s" name="%2$s" value="%3$s">',
					$id,
					esc_attr( $this->args['id'] ) . '[' . $id . ']',
					$fullurl
				);

				if ( $this->is_valid_img_ext( $fullurl ) ) {
					$output .= $this->img_status_output( [
						'image'    => wp_get_attachment_image( $id, esc_attr( $this->args['preview_size'] ) ),
						'tag'      => 'li',
						'id_input' => $id_input,
					] );
				} else {
					$output .= $this->file_status_output( [
						'value'    => $fullurl,
						'tag'      => 'li',
						'id_input' => $id_input,
					] );
				}
			}
		}

		$output .= '</ul>';

		$output .= '</div>';

		echo $output;
	}

	public function get_file_preview_output() {
		if ( ! $this->is_valid_img_ext( $this->args['value'] ) ) {
			return $this->file_status_output( [
				'value'     => $this->args['value'],
				'tag'       => 'div',
				'cached_id' => $this->args['id'],
			] );
		}

		if ( $this->args['id_value'] ) {
			$image = wp_get_attachment_image( $this->args['id_value'], $this->args['preview_size'], null, [
				'class' => 'gg_woo_feed-file-field-image',
			] );
		} else {
			$image = '<img style="max-width: 50px; width: 100%;" src="' . $this->args['value'] . '" class="gg_woo_feed-file-field-image" alt="" />';
		}

		return $this->img_status_output( [
			'image'     => $image,
			'tag'       => 'div',
			'cached_id' => $this->args['id'],
		] );
	}

	/**
	 * file/file_list image wrap
	 *
	 * @param array $args Array of arguments for output
	 * @return string       Image wrap output
	 */
	public function img_status_output( $args ) {
		return sprintf( '<%1$s class="img-status gg_woo_feed-media-item">%2$s<p class="gg_woo_feed-remove-wrapper"><a href="#" class="gg_woo_feed-remove-file-button"%3$s>%4$s</a></p>%5$s</%1$s>',
			$args['tag'],
			$args['image'],
			isset( $args['cached_id'] ) ? ' rel="' . $args['cached_id'] . '"' : '',
			esc_html__( 'Remove Image', 'gg-woo-feed' ),
			isset( $args['id_input'] ) ? $args['id_input'] : ''
		);
	}

	/**
	 * file/file_list file wrap
	 *
	 * @param array $args Array of arguments for output
	 * @return string       File wrap output
	 */
	public function file_status_output( $args ) {
		return sprintf( '<%1$s class="file-status gg_woo_feed-media-item"><span>%2$s <strong>%3$s</strong></span>&nbsp;&nbsp; (<a href="%4$s" target="_blank" rel="external">%5$s</a> / <a href="#" class="gg_woo_feed-remove-file-button"%6$s>%7$s</a>)%8$s</%1$s>',
			$args['tag'],
			esc_html__( 'File:', 'gg-woo-feed' ),
			$this->get_file_name_from_path( $args['value'] ),
			$args['value'],
			esc_html__( 'Download', 'gg-woo-feed' ),
			isset( $args['cached_id'] ) ? ' rel="' . $args['cached_id'] . '"' : '',
			esc_html__( 'Remove', 'gg-woo-feed' ),
			isset( $args['id_input'] ) ? $args['id_input'] : ''
		);
	}

	/**
	 * Determines if a file has a valid image extension
	 *
	 * @param string $file File url
	 * @return bool         Whether file has a valid image extension
	 */
	public function is_valid_img_ext( $file, $blah = false ) {
		$file_ext = $this->get_file_ext( $file );

		$valid_types    = [ 'jpg', 'jpeg', 'png', 'gif', 'ico', 'icon' ];
		$is_valid_types = apply_filters( 'gg_woo_feed_valid_img_types', $valid_types );
		$is_valid       = $file_ext && in_array( $file_ext, (array) $is_valid_types );

		return (bool) $is_valid;
	}

	/**
	 * Determine a file's extension
	 *
	 * @param string $file File url.
	 * @return string|false       File extension or false
	 */
	public function get_file_ext( $file ) {
		$parsed = parse_url( $file, PHP_URL_PATH );

		return $parsed ? strtolower( pathinfo( $parsed, PATHINFO_EXTENSION ) ) : false;
	}

	/**
	 * Get the file name from a url
	 *
	 * @param string $value File url or path.
	 * @return string        File name
	 */
	public function get_file_name_from_path( $value ) {
		$parts = explode( '/', $value );

		return is_array( $parts ) ? end( $parts ) : $value;
	}

	/**
	 * Outputs the file/file_list underscore Javascript templates in the footer.
	 *
	 * @return void
	 */
	public static function output_js_underscore_templates() {
		?>
        <script type="text/html" id="tmpl-gg_woo_feed-single-image">
            <div class="img-status gg_woo_feed-media-item">
                <img width="{{ data.sizeWidth }}" height="{{ data.sizeHeight }}" src="{{ data.sizeUrl }}" class="gg_woo_feed-file-field-image" alt="{{ data.filename }}" title="{{ data.filename }}"/>
                <p><a href="#" class="gg_woo_feed-remove-file-button" rel="{{ data.mediaField }}">{{ data.stringRemoveImage }}</a></p>
            </div>
        </script>
        <script type="text/html" id="tmpl-gg_woo_feed-single-file">
            <div class="file-status gg_woo_feed-media-item">
                <span>{{ data.stringFile }} <strong>{{ data.filename }}</strong></span>&nbsp;&nbsp; (<a href="{{ data.url }}" target="_blank" rel="external">{{ data.stringDownload }}</a> / <a
                        href="#" class="gg_woo_feed-remove-file-button" rel="{{ data.mediaField }}">{{ data.stringRemoveFile }}</a>)
            </div>
        </script>
        <script type="text/html" id="tmpl-gg_woo_feed-list-image">
            <li class="img-status gg_woo_feed-media-item">
                <img width="{{ data.sizeWidth }}" height="{{ data.sizeHeight }}" src="{{ data.sizeUrl }}" class="gg_woo_feed-file_list-field-image" alt="{{ data.filename }}">
                <p><a href="#" class="gg_woo_feed-remove-file-button" rel="{{ data.mediaField }}[{{ data.id }}]">{{ data.stringRemoveImage }}</a></p>
                <input type="hidden" id="filelist-{{ data.id }}" data-id="{{ data.id }}" name="{{ data.mediaFieldName }}[{{ data.id }}]" value="{{ data.url }}">
            </li>
        </script>
        <script type="text/html" id="tmpl-gg_woo_feed-list-file">
            <li class="file-status gg_woo_feed-media-item">
                <span>{{ data.stringFile }} <strong>{{ data.filename }}</strong></span>&nbsp;&nbsp; (<a href="{{ data.url }}" target="_blank" rel="external">{{ data.stringDownload }}</a> / <a
                        href="#" class="gg_woo_feed-remove-file-button" rel="{{ data.mediaField }}[{{ data.id }}]">{{ data.stringRemoveFile }}</a>)
                <input type="hidden" id="filelist-{{ data.id }}" data-id="{{ data.id }}" name="{{ data.mediaFieldName }}[{{ data.id }}]" value="{{ data.url }}">
            </li>
        </script>
		<?php
	}
}
