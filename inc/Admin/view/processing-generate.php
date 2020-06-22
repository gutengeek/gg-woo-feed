<div class="gg_woo_feed-modal" id="gg_woo_feed-modal-name">
    <div class="gg_woo_feed-modal-sandbox"></div>
    <div class="gg_woo_feed-modal-box">
        <div class="gg_woo_feed-modal-body rounded box-shadow">
            <div class="inner">
                <div id="opal-import-content">
                    <div class="arow">
                        <div class="generation-status">
                            <div class="inner">
                                <p class="notice"><?php esc_html_e( 'Please don\'t close this tab while generating.', 'gg-woo-feed' ); ?></p>
                                <p class="notice"><?php esc_html_e( 'If you want to generate variation products, you just should set `Product per batch` is 50.', 'gg-woo-feed' ); ?></p>
                                <div id="gg_woo_feed-progress-table">
                                    <table class="table widefat fixed">
                                        <thead>
                                        <tr>
                                            <th>
                                                <b><span class="gg_woo_feed-processing-status"><?php esc_html_e( 'Generate Feed', 'gg-woo-feed' ); ?></span></b>
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>
                                                <div class="gg_woo_feed-progress-container">
                                                    <div class="gg_woo_feed-progress-bar">
                                                        <span class="gg_woo_feed-progress-bar-fill"></span>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="gg_woo_feed-progress-status"></div>
                                                <div class="gg_woo_feed-progress-percentage"></div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <br>
                                </div>
                                <div class="action-buttons text-right">
                                    <a href="#" class="button button-secondary opal-canel-import close-gg_woo_feed-modal" style="display: none;"><?php esc_html_e( 'Cancel', 'gg-woo-feed' ); ?></a>
                                    <a href="#" class="button button-secondary button-stay-edit" style="display: none;"><?php esc_html_e( 'Stay Edit', 'gg-woo-feed' ); ?></a>
                                    <a href="#" class="button button-primary button-view-feed" target="_blank" style="display: none;"><?php esc_html_e( 'View Feed', 'gg-woo-feed' ); ?></a>
                                    <p class="gg_woo_feed-redirect-message" style="display: none;"><?php esc_html_e( 'Redirect to Manage feeds page in ', 'gg-woo-feed' ); ?><span
                                                id="countdowntimer">5</span>(s)</p>
                                    <i class="dashicons dashicons-update-alt gg_woo_feed-spinner" style="display: none;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
