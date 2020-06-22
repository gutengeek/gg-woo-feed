<?php
if ( ! isset( $args['fields'] ) || empty( $args['fields'] ) ) {
	return;
}

$group_numbering = isset( $args['options']['group_numbering'] ) ? (int) $args['options']['group_numbering'] : 0;
$close_tabs      = isset( $args['options']['close_tabs'] ) ? (int) $args['options']['close_tabs'] : 0;
$wrapper_class   = isset( $args['wrapper_class'] ) ? $args['wrapper_class'] : '';
?>
<div class="gg_woo_feed-group-field-wrap gg_woo_feed-repeatable-field-section <?php echo esc_attr( $wrapper_class ); ?>" data-field="<?php echo "{$args['id']}"; ?>" id="<?php echo "{$args['id']}_field"; ?>"
     data-group-numbering="<?php echo esc_attr( $group_numbering ); ?>" data-close-tabs="<?php echo esc_attr( $close_tabs ); ?>">
	<?php if ( ! empty( $args['name'] ) ) : ?>
        <p class="gg_woo_feed-repeater-field-name"><?php echo trim( $args['name'] ); ?></p>
	<?php endif; ?>

	<?php if ( ! empty( $args['description'] ) ) : ?>
        <p class="gg_woo_feed-repeater-field-description"><?php echo esc_html( $args['description'] ); ?></p>
	<?php endif; ?>

    <table class="gg_woo_feed-repeatable-fields-section-wrapper" cellspacing="0">
		<?php
		$repeater_field_values = $this->get_field_value( $args );

		$header_title = isset( $args['options']['header_title'] )
			? $args['options']['header_title']
			: esc_attr__( 'Group', 'gg-woo-feed' );

		$add_default_group_field = false;

		if ( is_array( $repeater_field_values ) && ( $args_count = count( $repeater_field_values ) ) ) {
			$repeater_field_values = array_values( $repeater_field_values );
		} else {
			$args_count              = 1;
			$add_default_group_field = true;
		}
		$add_default_group_field = false;
		?>
        <tbody class="container2"<?php echo " data-rf-row-count=\"{$args_count}\""; ?>>
        <!--Repeater field group template-->
        <tr class="gg_woo_feed-template">
            <td class="gg_woo_feed-repeater-field-wrap gg_woo_feed-column" colspan="2">
                <div class="opal-row-head gg_woo_feed-move">

                    <h4 class="repeat-title">
                        <span data-header-title="<?php echo esc_attr( $header_title ); ?>">
                        	<span class="repeat-counter">1</span>.
                        	<?php echo esc_html( $header_title ); ?></span>
                    </h4>

                    <div class="button-action-group">
                    	<span class="gg_woo_feed-remove button-action" title="<?php esc_attr_e( 'Remove Group', 'gg-woo-feed' ); ?>">
                    		<i class="fa fa-trash"></i>
						</span>
                        <button type="button" class="handlediv btn btn-link button-action">
                            <span class="fa fa-chevron-down"></span>
                        </button>

                    </div>

                </div>
                <div class="opal-row-body">
                    <div class="opal-row">
						<?php foreach ( $args['fields'] as $field ) : ?>

							<?php
							$field['repeat']  = true;
							$field['id']      = $this->get_repeater_field_id( $field, $args );
							$field['fied_id'] = str_replace(
								[ '[', ']' ],
								[ '_', '', ],
								$field['id']
							);
							$col              = isset( $field['col'] ) ? $field['col'] : 12;

							?>
                            <div class="col-lg-<?php echo $col; ?>">
								<?php
								$this->render_field( $field );
								?>
                            </div>
						<?php endforeach; ?>
                    </div>
                </div>
            </td>
        </tr>

		<?php if ( ! empty( $repeater_field_values ) ) : ?>
            <!--Stored repeater field group-->
			<?php foreach ( $repeater_field_values as $index => $field_group ) : ?>
                <tr class="opal-row-repeater">
                    <td class="gg_woo_feed-repeater-field-wrap gg_woo_feed-column" colspan="2">
                        <div class="opal-row-head gg_woo_feed-move">

                            <h4 class="repeat-title">
                                <span data-header-title="<?php echo esc_attr( $header_title ); ?>">
                           			<span class="repeat-counter"><?php echo( $index + 1 ); ?></span>.
                           			<?php echo esc_attr( $header_title ); ?>
                           		</span>
                            </h4>

                            <div class="button-action-group">
                            	<span class="gg_woo_feed-remove button-action" title="<?php esc_attr_e( 'Remove Group', 'gg-woo-feed' ); ?>">
	                        		<i class="fa fa-trash"></i>
								</span>
                                <button type="button" class="handlediv btn btn-link button-action">
                                    <span class="fa fa-chevron-down"></span>
                                </button>

                            </div>

                        </div>
                        <div class="opal-row-body">
							<?php foreach ( $args['fields'] as $field ) :
								?>
								<?php
								$value                        = $this->get_repeater_field_value( $field, $field_group, $args );
								$field['attributes']['value'] = $value;
								$field['repeat']              = true;
								$field['value']               = $value;
								$field['id_value']            = $this->get_repeater_field_id_value( $field, $field_group, $args );
								$field['hidden_id']           = $this->get_repeater_field_hidden_id( $field, $args, $index );
								$field['id']                  = $this->get_repeater_field_id( $field, $args, $index );
								?>
								<?php $this->render_field( $field ); ?>
							<?php endforeach; ?>
                        </div>
                    </td>
                </tr>
			<?php endforeach;; ?>

		<?php elseif ( $add_default_group_field ) : ?>

		<?php endif; ?>
        </tbody>
        <tfoot>
        <tr>
			<?php
			$add_row_btn_title = isset( $args['options']['add_button'] )
				? $add_row_btn_title = $args['options']['add_button']
				: esc_html__( 'Add Row', 'gg-woo-feed' );
			?>
            <td colspan="2" class="gg_woo_feed-add-repeater-field-section-row-wrap">
                <span class="button button-primary add-repeat-group-btn"><?php echo esc_html( $add_row_btn_title ); ?></span>
            </td>
        </tr>
        </tfoot>
    </table>
</div>
