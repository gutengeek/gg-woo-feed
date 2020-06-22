(function () {
    'use strict';

    (function ($) {
      var GG_Woo_Feed_Admin = {
        init: function init() {
          GG_Woo_Feed_Admin.updateFeedStatus();
          GG_Woo_Feed_Admin.tab();
          GG_Woo_Feed_Admin.popup();
          GG_Woo_Feed_Admin.multicheckToggle();
          GG_Woo_Feed_Admin.copyInit();
          GG_Woo_Feed_Admin.googleTaxonomySelect2();
        },
        updateFeedStatus: function updateFeedStatus() {
          $('.js-gg_woo_feed-change-status').on('change', function () {
            var feed_name = $(this).attr('id'),
                status = $(this)[0].checked ? 1 : 0;
            $.ajax({
              url: ajaxurl,
              method: 'POST',
              data: {
                _ajax_nonce: ggWooFeed.nonce,
                action: 'gg_woo_feed_update_feed_status',
                feed_name: feed_name,
                status: status
              },
              beforeSend: function beforeSend() {}
            }).always(function () {}).done(function (res) {}).fail(function (err) {});
          });
        },
        tab: function tab() {
          $('.gg_woo_feed-tabs__title').on('click', function (event) {
            var $gg_woo_feedTab = $(this).parent();
            var gg_woo_feedIndex = $gg_woo_feedTab.index();

            if ($gg_woo_feedTab.hasClass('gg_woo_feed-active')) {
              return;
            }

            $gg_woo_feedTab.closest('.gg_woo_feed-tabs__nav').find('.gg_woo_feed-active').removeClass('gg_woo_feed-active');
            $gg_woo_feedTab.addClass('gg_woo_feed-active');
            $gg_woo_feedTab.closest('.gg_woo_feed-tabs__wrap').find('.gg_woo_feed-tabs__content.gg_woo_feed-active').hide().removeClass('gg_woo_feed-active');
            $gg_woo_feedTab.closest('.gg_woo_feed-tabs__wrap').find('.gg_woo_feed-tabs__content').eq(gg_woo_feedIndex).fadeIn(200, function () {
              $(this).addClass('gg_woo_feed-active');
            });
          });
        },
        popup: function popup() {
          $(document).on('click', '.gg_woo_feed-open-popup', function (event) {
            event.preventDefault();
            $(this).parents('.gg_woo_feed-open-popup-wrap').find('.gg_woo_feed-popup-wrap').show();
          });
          $(document).on('click', '.gg_woo_feed-popup-close, .gg_woo_feed-popup-done', function () {
            $(this).parents('.gg_woo_feed-popup-wrap').hide();
          });
          $(document).on('click', '.close-gg_woo_feed-modal', function (e) {
            e.preventDefault();
            $('.gg_woo_feed-modal ').hide();
          });
          $(document).on('click', '#gg_woo_feed-popup-categories li input.feed_category', function (e) {
            var cat_id = $(this).attr('id') || '';

            if (cat_id != 'feed_category_all') {
              var allchecked = true;
              $('#gg_woo_feed-popup-categories li input.feed_category').each(function (index, el) {
                var cat_id = $(this).attr('id') || '';
                if (cat_id != 'feed_category_all' && $(this).prop('checked') == false) allchecked = false;
              });

              if (!allchecked) {
                $('#feed_category_all').prop('checked', false);
              } else {
                $('#feed_category_all').prop('checked', true);
              }
            }
          });
          $(document).on('click', '#feed_category_all', function (e) {
            var tick = $(this).prop('checked');
            $('#gg_woo_feed-popup-categories li input.feed_category').prop('checked', tick);
          });
        },
        multicheckToggle: function multicheckToggle() {
          $('.gg_woo_feed-multicheck-toggle').on('click', function (e) {
            e.preventDefault();
            var $this = $(this);
            var $multicheck = $this.closest('.gg_woo_feed-field-main').find('input[type=checkbox]:not([disabled])'); // If the button has already been clicked once...

            if ($this.data('checked')) {
              // clear the checkboxes and remove the flag
              $multicheck.prop('checked', false);
              $this.data('checked', false);
            } // Otherwise mark the checkboxes and add a flag
            else {
                $multicheck.prop('checked', true);
                $this.data('checked', true);
              }
          });
        },
        copyInit: function copyInit() {
          $('.js-copy-feed').on('click', function (e) {
            e.preventDefault();
          });
          new ClipboardJS('.js-copy-feed');
        },
        googleTaxonomySelect2: function googleTaxonomySelect2() {
          $('.gg_woo_feed-google-taxonomy-select, #gg_woo_feed_tax_google_taxonomy').select2();
        }
      };
      $(GG_Woo_Feed_Admin.init);
    })(jQuery);

}());

//# sourceMappingURL=admin.js.map
