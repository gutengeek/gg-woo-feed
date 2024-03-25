(function () {
  'use strict';

  (function ($) {
    var GG_Woo_Feed_Feed = {
      init: function init() {
        GG_Woo_Feed_Feed.sortable_init();
        GG_Woo_Feed_Feed.disable_init();
        GG_Woo_Feed_Feed.feed_init();
        GG_Woo_Feed_Feed.generate_feed();
        GG_Woo_Feed_Feed.delete_feed();
        GG_Woo_Feed_Feed.add_new_row();
        GG_Woo_Feed_Feed.delete_row();
        GG_Woo_Feed_Feed.submit_google_merchant();
        GG_Woo_Feed_Feed.google_sync_metabox();
        GG_Woo_Feed_Feed.add_new_filter_condition();
        GG_Woo_Feed_Feed.delete_filter_condition();
        GG_Woo_Feed_Feed.condition_sortable_init();
        GG_Woo_Feed_Feed.no_conditions();
        GG_Woo_Feed_Feed.add_new_filter_by_attributes_condition();
        GG_Woo_Feed_Feed.delete_filter_by_attributes_condition();
        GG_Woo_Feed_Feed.attributes_condition_sortable_init();
        GG_Woo_Feed_Feed.no_attributes_conditions();
        GG_Woo_Feed_Feed.utils();
      },
      sortable_init: function sortable_init() {
        var $table_body = $('.gg_woo_feed-table-template tbody');

        if ($table_body.length) {
          $table_body.sortable({
            cursor: 'move'
          }).disableSelection();
        }
      },
      disable_init: function disable_init() {
        $.fn.disabled = function (status) {
          $(this).each(function () {
            var self = $(this),
                prop = 'disabled';

            if (typeof self.prop(prop) !== 'undefined') {
              self.prop(prop, status === void 0 || status === true);
            } else {
              !0 === status ? self.addClass(prop) : self.removeClass(prop);
            }
          });
          return self;
        };

        $.fn.isDisabled = function () {
          var self = $(this),
              prop = 'disabled';
          return typeof self.prop(prop) !== 'undefined' ? self.prop(prop) : self.hasClass(prop);
        };
      },
      add_new_row: function add_new_row() {
        $(document).on('click', '#gg_woo_feed-add-new-row', function () {
          $('#gg_woo_feed-table-template tbody tr:first').clone().find('input').val('').end().find('select:not(\'.gg_woo_feed-not-empty\')').val('').end().insertAfter('#gg_woo_feed-table-template tbody tr:last');
          $('.output_type').each(function (index) {
            $(this).attr('name', 'output_type[' + index + '][]');
          });
        });
      },
      delete_row: function delete_row() {
        $(document).on('click', '.gg_woo_feed-del-row', function (e) {
          e.preventDefault();
          $(this).closest('tr').remove();
        });
      },
      feed_init: function feed_init() {
        var mapping_editor = {
          form: null,
          init: function init() {
            var self = this;
            self.form = $('.gg_woo_feed-generate-form');
            if (!self.form.length) return;
            GG_Woo_Feed_Feed.sortable_init();
            $('.gg_woo_feed-google-taxonomy-select').select2();
            $(document).on('change', '.attr_type', function () {
              var type = $(this).val(),
                  row = $(this).closest('tr');

              if (type === 'pattern') {
                row.find('.gg_woo_feed-attr-val').hide();
                row.find('.gg_woo_feed-attr-val').val('');
                row.find('.gg_woo_feed-default-val').show();
              } else {
                row.find('.gg_woo_feed-attr-val').show();
                row.find('.gg_woo_feed-default-val').hide();
                row.find('.gg_woo_feed-default-val').val('');
              }
            }).on('change', '.gg_woo_feed-map-attributes, .attr_type', function () {
              var row = $(this).closest('tr'),
                  attribute = row.find('.gg_woo_feed-map-attributes'),
                  type = row.find('.attr_type'),
                  value_column = row.find('td:eq(4)');

              if (attribute.val() !== 'google_taxonomy' && value_column.find('input.gg_woo_feed-default-val').length === 0) {
                value_column.find('span').remove();
                value_column.append('<input autocomplete="off" class="gg_woo_feed-default-val gg_woo_feed-attributes"  type="text" name="default[]" value="">');

                if (type.val() !== 'pattern') {
                  value_column.find('input.gg_woo_feed-default-val').hide();
                }
              }
            }).trigger('change');
          },
          render_provider_mapping: function render_provider_mapping($feed_form, res, $feed_type) {
            $feed_form.html(res['mapping_template']);
            $('[name="feed_type"]').find('[value="' + res['feed_type'] + '"]').prop('selected', true);
            $('[name="items_wrap"]').val(res['items_wrap']);
            $('[name="item_wrap"]').val(res['item_wrap']);
            $('[name="delimiter"]').find('[value="' + res['delimiter'] + '"]').prop('selected', true);
            $('[name="enclosure"]').find('[value="' + res['enclosure'] + '"]').prop('selected', true);
            $feed_type.disabled(!1);
            $feed_type.trigger('change');
            $feed_type.parent().find('.spinner').removeClass('is-active');
            mapping_editor.init();
          }
        },
            mapping_cache = [];
        mapping_editor.init();
        $('.gg_woo_feed-provider-select').on('change', function (event) {
          event.preventDefault();

          if (!$(this).closest('.gg_woo_feed-generate-form').hasClass('add-new')) {
            return;
          }

          var provider = $(this).val(),
              $feed_type = $('.gg_woo_feed-feedtype-select'),
              $feed_form = $('#gg_woo_feed_core_mapping_fields');
          $feed_type.disabled(!0);
          $feed_type.parent().find('.spinner').addClass('is-active');

          if (mapping_cache.hasOwnProperty(provider)) {
            mapping_editor.render_provider_mapping($feed_form, mapping_cache[provider], $feed_type);
          } else {
            $.ajax({
              url: ajaxurl,
              method: 'POST',
              data: {
                action: 'gg_woo_feed_provider_mapping_view',
                _ajax_nonce: ggWooFeed.nonce,
                provider: provider
              },
              beforeSend: function beforeSend() {}
            }).always(function () {}).done(function (res) {
              mapping_cache[provider] = res.data;
              mapping_editor.render_provider_mapping($feed_form, res.data, $feed_type);
            }).fail(function (err) {});
          }
        });
        $('.gg_woo_feed-feedtype-select, .gg_woo_feed-provider-select').on('change', function () {
          var type = $('.gg_woo_feed-feedtype-select').val(),
              provider = $('.gg_woo_feed-provider-select').val(),
              item_wrap = $('.item_wrap'),
              $csv_txt = $('.gg_woo_feed-type-csvtxt');

          if (type === 'xml') {
            item_wrap.show();
            $csv_txt.hide();
          } else if (type === 'csv' || type === 'txt') {
            item_wrap.hide();
            $csv_txt.show();
          } else if (type === '') {
            item_wrap.hide();
            $csv_txt.hide();
          }

          if (type !== '' && ['google', 'facebook', 'pinterest'].indexOf(provider) !== -1) {
            item_wrap.hide();
          }
        }).trigger('change');
      },
      generate_feed: function generate_feed() {
        var $close_button = $('.close-gg_woo_feed-modal');
        var $spinner = $('.gg_woo_feed-spinner');
        var $view_feed = $('.button-view-feed');
        var $stay_edit = $('.button-stay-edit');
        $(document).on('click', '.js-regenerate-feed', function (e) {
          e.preventDefault();
          $('.gg_woo_feed-modal ').css({
            'display': 'block'
          });
          var feed_name = $(this).attr('id');
          get_products(feed_name);
        });
        $(document).on('click', '.gg_woo_feed-generate', function (e) {
          e.preventDefault();
          $('.gg_woo_feed-modal ').css({
            'display': 'block'
          });
          var form = document.forms.namedItem('feed');
          var formData = new FormData(form);
          formData.append('action', 'gg_woo_feed_save_config');
          $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: formData,
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function beforeSend() {
              $spinner.show();
              set_progress_status('...');
            }
          }).always(function () {}).done(function (res) {
            if (res.data.status) {
              set_progress_status(res.data.message);
              set_progress_bar_fill(10);
              set_progress_percentage(10);
              get_products(res.data.file_name);
            } else {
              set_progress_status(res.data.message);
            }
          }).fail(function (err) {
            set_progress_status(err.responseJSON.data.message);
            $close_button.show();
            $spinner.hide();
          });
        });

        function get_products(file_name) {
          $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
              action: 'gg_woo_feed_get_products_for_feed',
              _ajax_nonce: ggWooFeed.nonce,
              file_name: file_name
            },
            beforeSend: function beforeSend() {}
          }).always(function () {}).done(function (res) {
            if (res.data.status) {
              set_progress_status(res.data.message);
              set_progress_bar_fill(20);
              set_progress_percentage(20);
              generate(file_name, res.data.products, res.data.total);
            }
          }).fail(function (err) {
            set_progress_status(err.responseJSON.data.message);
            $close_button.show();
            $spinner.hide();
          });
        }

        function generate(file_name, batches, total_products, n) {
          if (typeof n === 'undefined') {
            n = 0;
          }

          var total_batch = batches.length;
          var progress_batch = 70 / total_batch;
          var batch = batches[n];
          var product_batch = n + 1;
          $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
              action: 'gg_woo_feed_make_batch_feed',
              file_name: file_name,
              products: batch,
              _ajax_nonce: ggWooFeed.nonce,
              loop: n
            },
            beforeSend: function beforeSend() {
              set_progress_status('Processing batch ' + product_batch + ' of ' + total_batch + ' per total ' + total_products + ' products.');
            }
          }).always(function () {}).done(function (res) {
            if (product_batch < total_batch) {
              n = n + 1;
              generate(file_name, batches, total_products, n);
              set_progress_bar_fill(20 + progress_batch * product_batch);
              set_progress_percentage(Math.round(20 + progress_batch * product_batch));
            }

            if (product_batch === total_batch) {
              save_file(file_name);
            }
          }).fail(function (err) {
            set_progress_status(err.responseJSON.data.message);
            $close_button.show();
            $spinner.hide();
          });
        }

        function save_file(file_name) {
          $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
              action: 'gg_woo_feed_save_feed_file',
              _ajax_nonce: ggWooFeed.nonce,
              file_name: file_name
            },
            beforeSend: function beforeSend() {
              set_progress_status('Saving file...');
            }
          }).always(function () {}).done(function (res) {
            if (res.data.status) {
              set_progress_status(res.data.message);
              set_progress_bar_fill(100);
              set_progress_percentage(100);
              $spinner.remove();
              $close_button.remove();
              $('.gg_woo_feed-redirect-message').show();
              $view_feed.attr('href', res.data.url);
              $stay_edit.attr('href', res.data.edit_link);
              $view_feed.show();
              $stay_edit.show();
              var timeleft = 5;
              var downloadTimer = setInterval(function () {
                timeleft--;
                document.getElementById('countdowntimer').textContent = timeleft;
                if (timeleft <= 0) clearInterval(downloadTimer);
              }, 1000);
              setTimeout(function () {
                window.location.href = ggWooFeed.manage_feeds_link;
              }, 5000);
            }
          }).fail(function (err) {
            set_progress_status(err.responseJSON.data.message);
            $close_button.show();
            $spinner.hide();
          });
        }

        function set_progress_status(text) {
          $('.gg_woo_feed-progress-status').html(text);
        }

        function set_progress_bar_fill(percent) {
          $('.gg_woo_feed-progress-bar-fill').css('width', percent + '%');
        }

        function set_progress_percentage(text) {
          $('.gg_woo_feed-progress-percentage').html(text + '%');
        }
      },
      delete_feed: function delete_feed() {
        $('#manage-feeds-form .submitdelete').on('click', function (e) {
          e.preventDefault();

          if (confirm('Press a button!\nEither OK or Cancel.')) {
            window.location.href = $(this).attr('href');
          }
        });
      },
      submit_google_merchant: function submit_google_merchant() {
        $('#submit-google-merchant').on('click', function (e) {
          e.preventDefault();
          var form = document.forms.namedItem('feed');
          var formData = new FormData(form);
          formData.append('action', 'gg_woo_feed_submit_google_merchant');
          var $submit_button = $(this),
              $el_status = $('.gg-woo-feed-google-sync-status'),
              $spinner = $('.gg_woo_feed-spinner'),
              $icon = $('.gg-sync-upload-icon');
          $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: formData,
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function beforeSend() {
              $submit_button.attr('disabled', true);
              $icon.hide();
              $spinner.show();
            }
          }).always(function () {}).done(function (res) {
            $el_status.hide();
            $el_status.removeClass('error');
            $el_status.addClass('updated');
            $el_status.find('.gg-woo-feed-google-sync-message').html(res.data.message);
            $el_status.find('.gg-woo-feed-google-sync-message').show();
            $el_status.find('.gg-woo-feed-google-sync-status-detail').hide();
            $el_status.find('.gg-woo-feed-google-sync-message-error').hide();
            $el_status.find('.gg-woo-feed-google-error-detail__reason').html('');
            $el_status.find('.gg-woo-feed-google-error-detail__mess').html('');
            $el_status.show();
            $icon.show();
            $spinner.hide();
            $submit_button.attr('disabled', false);
            location.reload();
          }).fail(function (err) {
            $el_status.addClass('error');
            $el_status.removeClass('updated');
            $el_status.find('.gg-woo-feed-google-error-detail__reason').html(err.responseJSON.data.reason);
            $el_status.find('.gg-woo-feed-google-error-detail__mess').html(err.responseJSON.data.message);
            $el_status.find('.gg-woo-feed-google-sync-message').hide();
            $el_status.find('.gg-woo-feed-google-sync-status-detail').show();
            $el_status.find('.gg-woo-feed-google-sync-message-error').show();
            $el_status.show();
            $icon.show();
            $spinner.hide();
            $submit_button.attr('disabled', false);
          });
        });
      },
      google_sync_metabox: function google_sync_metabox() {
        $(document).ready(function () {
          var schedule = $('#google_schedule').val();
          change_schedule(schedule);
        });
        $('#google_schedule').on('change', function (e) {
          var schedule = $(this).val();
          change_schedule(schedule);
        });

        function change_schedule(schedule) {
          var $month = $('#google_schedule_month_wrapper'),
              $week = $('#google_schedule_week_day_wrapper');

          if ('monthly' === schedule) {
            $month.show();
            $week.hide();
          } else if ('weekly' === schedule) {
            $month.hide();
            $week.show();
          } else {
            $month.hide();
            $week.hide();
          }
        }
      },
      add_new_filter_condition: function add_new_filter_condition() {
        var condition_cache = '';
        $('#gg_woo_feed-add-new-condition').on('click', function (e) {
          e.preventDefault();
          var $filter_body = $('#gg_woo_feed-table-filter tbody');

          if (condition_cache) {
            $filter_body.append(condition_cache);
            GG_Woo_Feed_Feed.no_conditions();
            GG_Woo_Feed_Feed.condition_sortable_init();
          } else {
            $.ajax({
              url: ajaxurl,
              method: 'POST',
              data: {
                action: 'gg_woo_feed_add_new_filter_condition',
                _ajax_nonce: ggWooFeed.nonce
              }
            }).always(function () {}).done(function (res) {
              $filter_body.append(res.data.row);
              condition_cache = res.data.row;
              GG_Woo_Feed_Feed.no_conditions();
              GG_Woo_Feed_Feed.condition_sortable_init();
            }).fail(function (err) {});
          }
        });
      },
      delete_filter_condition: function delete_filter_condition() {
        $(document).on('click', '.gg_woo_feed-del-condition', function (e) {
          e.preventDefault();
          $(this).closest('tr').remove();
          GG_Woo_Feed_Feed.no_conditions();
          GG_Woo_Feed_Feed.condition_sortable_init();
        });
      },
      condition_sortable_init: function condition_sortable_init() {
        var $table_body = $('.gg_woo_feed-table-filter tbody');

        if ($table_body.length) {
          $table_body.sortable({
            cursor: 'move'
          }).disableSelection();
        }
      },
      no_conditions: function no_conditions(e) {
        var no_conditions = $('#gg_woo_feed-table-filter tbody tr:not(.gg_woo_feed-no-conditions)').length;

        if (no_conditions >= 1) {
          $('.gg_woo_feed-no-conditions').hide();
        } else {
          $('.gg_woo_feed-no-conditions').show();
        }
      },
      add_new_filter_by_attributes_condition: function add_new_filter_by_attributes_condition() {
        var condition_cache = '';
        $('#gg_woo_feed-add-new-condition-attribites').on('click', function (e) {
          e.preventDefault();
          var $filter_body = $('#gg_woo_feed-table-filter-attributes tbody');

          if (condition_cache) {
            $filter_body.append(condition_cache);
            GG_Woo_Feed_Feed.no_attributes_conditions();
            GG_Woo_Feed_Feed.attributes_condition_sortable_init();
          } else {
            $.ajax({
              url: ajaxurl,
              method: 'POST',
              data: {
                action: 'gg_woo_feed_add_new_filter_by_attributes_condition',
                _ajax_nonce: ggWooFeed.nonce
              }
            }).always(function () {}).done(function (res) {
              $filter_body.append(res.data.row);
              condition_cache = res.data.row;
              GG_Woo_Feed_Feed.no_attributes_conditions();
              GG_Woo_Feed_Feed.attributes_condition_sortable_init();
            }).fail(function (err) {});
          }
        });
      },
      delete_filter_by_attributes_condition: function delete_filter_by_attributes_condition() {
        $(document).on('click', '.gg_woo_feed-del-condition-attributes', function (e) {
          e.preventDefault();
          $(this).closest('tr').remove();
          GG_Woo_Feed_Feed.no_attributes_conditions();
          GG_Woo_Feed_Feed.attributes_condition_sortable_init();
        });
      },
      attributes_condition_sortable_init: function attributes_condition_sortable_init() {
        var $table_body = $('.gg_woo_feed-table-filter-attributes tbody');

        if ($table_body.length) {
          $table_body.sortable({
            cursor: 'move'
          }).disableSelection();
        }
      },
      no_attributes_conditions: function no_attributes_conditions(e) {
        var no_conditions = $('#gg_woo_feed-table-filter-attributes tbody tr:not(.gg_woo_feed-no-conditions_attributes)').length;

        if (no_conditions >= 1) {
          $('.gg_woo_feed-no-conditions_attributes').hide();
        } else {
          $('.gg_woo_feed-no-conditions_attributes').show();
        }
      },
      utils: function utils(e) {
        $(document).ready(function () {
          var $filter_by_attributes = $('[name="filter_by_attributes"]');
          use_filter_by_attributes($filter_by_attributes);
          $filter_by_attributes.change(function () {
            var $el = $(this);
            use_filter_by_attributes($el);
          });
          $('#gg_woo_feed-form-filter_by_date').change(function () {
            var $depend = $('.filter_by_date_section');

            if ($(this).prop('checked')) {
              $depend.show();
            } else {
              $depend.hide();
            }
          });
        });

        function use_filter_by_attributes($el) {
          var $depend = $('.filter_by_attributes_section');
          var $ex_depend = $('#gg_woo_feed-select-product_type, #gg_woo_feed-form-exclude_variations-wrap,' + ' #gg_woo_feed-form-show_main_variable_product-wrap');

          if ($el.prop('checked')) {
            $depend.show();
            $ex_depend.hide();
          } else {
            $depend.hide();
            $ex_depend.show();
          }
        }
      }
    };
    $(GG_Woo_Feed_Feed.init);
  })(jQuery);

}());

//# sourceMappingURL=feed.js.map
