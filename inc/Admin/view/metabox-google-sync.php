<?php

use GG_Woo_Feed\Common\Module\Google_Merchant_Content_API;

$google_merchant = new Google_Merchant_Content_API();

?>

<div id="googlesyncdiv" class="postbox">
    <h2 class="hndle"><?php esc_html_e( 'Google Merchant Sync', 'gg-woo-feed' ); ?></h2>
    <div class="inside">
        <div class="submitbox">

			<?php if ( ! ( $google_merchant->is_authenticate() ) ) : ?>
				<?php
				printf( '<p class="gg-woo-feed-google-token-status">%s <a href="%s">' . __( 'Authenticate', 'gg-woo-feed' ) . '</a> </p>',
					esc_html__( 'Access token has expired. Please authenticate it to send feed.', 'gg-woo-feed' ),
					admin_url( 'admin.php?page=gg-woo-feed-google-sync' ) );
				?>
			<?php else : ?>
                <div class="googlesync-actions">
                    <div class="gg-woo-feed-side-row gg-woo-feed-id-gg_woo_feed_google_target_country table-layout">
                        <div class="gg-woo-feed-side-label">
                            <label for="google_target_country"><?php esc_html_e( 'Target Country', 'gg-woo-feed' ); ?></label>
                        </div>
                        <div class="gg-woo-feed-side-input">
                            <input type="text" class="regular-text" name="google_target_country" id="google_target_country" value="<?php echo isset( $feed_queries['google_target_country'] ) ?
								$feed_queries['google_target_country'] : 'US'; ?>" required="required">
                        </div>
                    </div>

                    <div class="gg-woo-feed-side-row gg-woo-feed-id-gg_woo_feed_google_target_language table-layout">
                        <div class="gg-woo-feed-side-label">
                            <label for="google_target_language"><?php esc_html_e( 'Target Language', 'gg-woo-feed' ); ?></label>
                        </div>
                        <div class="gg-woo-feed-side-input">
                            <input type="text" class="regular-text" name="google_target_language" id="google_target_language" value="<?php echo isset( $feed_queries['google_target_language'] ) ?
								$feed_queries['google_target_language'] : 'en'; ?>" required="required">
                        </div>
                    </div>
					<?php
					$schedules = [
						'monthly' => esc_html__( 'monthly', 'gg-woo-feed' ),
						'weekly'  => esc_html__( 'weekly', 'gg-woo-feed' ),
						'hourly'  => esc_html__( 'hourly', 'gg-woo-feed' ),
					];
					?>

                    <div class="gg-woo-feed-side-row gg-woo-feed-id-gg_woo_feed_google_schedule table-layout" id="google_schedule_wrapper">
                        <div class="gg-woo-feed-side-label">
                            <label for="google_schedule"><?php esc_html_e( 'Schedule', 'gg-woo-feed' ); ?></label>
                        </div>
                        <div class="gg-woo-feed-side-input">
                            <select name="google_schedule" id="google_schedule">
								<?php foreach ( $schedules as $schedule_key => $schedule_name ) : ?>
                                    <option <?php selected( $schedule_key, isset( $feed_queries['google_schedule'] ) ? $feed_queries['google_schedule'] : 'hourly', true ); ?>
                                            value="<?php echo esc_attr( $schedule_key ); ?>"><?php echo esc_html( $schedule_name ); ?></option>
								<?php endforeach; ?>
                            </select>
                        </div>
                    </div>

					<?php
					$month_array = range( 1, 31 );
					array_unshift( $month_array, "" );
					unset( $month_array[0] );
					?>
                    <div class="gg-woo-feed-side-row gg-woo-feed-id-google_schedule_month table-layout" id="google_schedule_month_wrapper">
                        <div class="gg-woo-feed-side-label">
                            <label for="google_schedule_month"><?php esc_html_e( 'Select day of month', 'gg-woo-feed' ); ?></label>
                        </div>
                        <div class="gg-woo-feed-side-input">
                            <select name="google_schedule_month" id="google_schedule_month">
								<?php foreach ( $month_array as $month_val ) : ?>
                                    <option <?php selected( $month_val, isset( $feed_queries['google_schedule_month'] ) ? $feed_queries['google_schedule_month'] : '1', true ); ?> value="<?php echo
									$month_val; ?>"><?php echo esc_html( $month_val ); ?></option>
								<?php endforeach; ?>
                            </select>
                        </div>
                    </div>

					<?php
					$days = [
						'monday'    => esc_html__( 'Monday', 'gg-woo-feed' ),
						'tuesday'   => esc_html__( 'Tuesday', 'gg-woo-feed' ),
						'wednesday' => esc_html__( 'Wednesday', 'gg-woo-feed' ),
						'thursday'  => esc_html__( 'Thursday', 'gg-woo-feed' ),
						'friday'    => esc_html__( 'Friday', 'gg-woo-feed' ),
						'saturday'  => esc_html__( 'Saturday', 'gg-woo-feed' ),
						'sunday'    => esc_html__( 'Sunday', 'gg-woo-feed' ),
					];
					?>

                    <div class="gg-woo-feed-side-row gg-woo-feed-id-google_schedule_week_day table-layout" id="google_schedule_week_day_wrapper">
                        <div class="gg-woo-feed-side-label">
                            <label for="google_schedule_week_day"><?php esc_html_e( 'Select day of week', 'gg-woo-feed' ); ?></label>
                        </div>
                        <div class="gg-woo-feed-side-input">
                            <select name="google_schedule_week_day" id="google_schedule_week_day">
								<?php foreach ( $days as $day_val => $day_text ) : ?>
                                    <option <?php selected( $day_val, isset( $feed_queries['google_schedule_week_day'] ) ? $feed_queries['google_schedule_week_day'] : 'monday', true ); ?>
                                            value="<?php echo
											esc_attr( $day_val ); ?>">
										<?php echo esc_html( $day_text ); ?>
                                    </option>
								<?php endforeach; ?>
                            </select>
                        </div>
                    </div>
					<?php
					$times = range( 0, 23 );
					?>
                    <div class="gg-woo-feed-side-row gg-woo-feed-id-google_schedule_time table-layout" id="google_schedule_time-wrapper">
                        <div class="gg-woo-feed-side-label">
                            <label for="google_schedule_time"><?php esc_html_e( 'Select Hour', 'gg-woo-feed' ); ?></label>
                        </div>
                        <div class="gg-woo-feed-side-input">
                            <select name="google_schedule_time" id="google_schedule_time">
								<?php foreach ( $times as $time ) : ?>
                                    <option <?php selected( $time, isset( $feed_queries['google_schedule_time'] ) ? $feed_queries['google_schedule_time'] : '1', true ); ?> value="<?php echo
									esc_attr( $time ); ?>"><?php echo esc_html( $time ); ?></option>
								<?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="gg-woo-feed-side-actions">
                        <button class="gg_woo_feed-btn gg_woo_feed-btn-submit gg_woo_feed-btn--full" id="submit-google-merchant" type="button">
							<?php esc_html_e( 'Send to Google Merchant', 'gg-woo-feed' ); ?>
                            <i class="gg-sync-upload-icon dashicons dashicons-upload"></i>
                            <i class="gg_woo_feed-spinner dashicons dashicons-update-alt" style="display: none;"></i>
                        </button>
                    </div>
                    <div class="gg-woo-feed-google-sync-status" style="display: none">
                        <p class="gg-woo-feed-google-sync-message" style="display: none"></p>
                        <p class="gg-woo-feed-google-sync-message-error" style="display: none"><?php esc_html_e( 'Error!', 'gg-woo-feed' ); ?></p>
                        <div class="gg-woo-feed-google-sync-status-detail" style="display: none">
                            <span class="gg-woo-feed-google-error-detail__reason"></span>:
                            <span class="gg-woo-feed-google-error-detail__mess"></span>
                        </div>
                    </div>
                </div>
			<?php endif; ?>

            <div class="clear"></div>
        </div>
    </div>
</div>
