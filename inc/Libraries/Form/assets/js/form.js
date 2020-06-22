/**
 * Controls the behaviours of custom metabox fields.
 *
 * @author Opalteam
 */
window.OPAL = window.OPAL || {};
( function ( window, document, $, opal, undefined ) {
    'use strict';

    // localization strings
    var l10n = window.opalJob_l10;
    var setTimeout = window.setTimeout;
    var $document;
    var $id = function ( selector ) {
        return $( document.getElementById( selector ) );
    };

    var defaults = {
        idNumber: false,
        mediaHandlers: {},
        media: {
            frames: {},
        },
        defaults: {
            date_picker: l10n.defaults.date_picker,
            color_picker: {},
        },
    };

    /**
     * Constructor.
     */
    opal.init = function () {
        $document = $( document );

        // Setup the OPAL object defaults.
        $.extend( opal, defaults );

        opal.trigger( 'opal_pre_init' );

        var $metabox = opal.metabox();

        // Init tab form.
        opal.initTabForm( $( '.gg_woo_feed-form-data-tabs' ) );

        // Make File List drag/drop sortable:
        opal.makeListSortable();

        // Init time/date/color pickers
        opal.initPickers( $metabox.find( 'input[type="text"].gg_woo_feed-colorpicker' ),
            $metabox.find( 'input[type="text"].gg_woo_feed-datepicker' ) );

        $metabox
            .on( 'change', '.gg_woo_feed_upload_file', function () {
                opal.media.field = $( this ).attr( 'id' );
                $id( opal.media.field + '_id' ).val( '' );
            } )
            // Media/file management
            .on( 'click', '.gg_woo_feed-upload-button', opal.handleMedia )
            .on( 'click', '.gg_woo_feed-attach-list li, .gg_woo_feed-media-status .img-status img, .gg_woo_feed-media-status' +
                ' .file-status >' +
                ' span', opal.handleFileClick )
            .on( 'click', '.gg_woo_feed-remove-file-button', opal.handleRemoveMedia );


        /////
        ///
        opal.groupFields( $(".gg_woo_feed-group-field-wrap", $metabox) );
        ///
        $( '.gg_woo_feed-select' ).select2();
        ///
        opal.loadAutosugesstion();
        ///        ///
        opal.trigger( 'opal_init' );



        $metabox.find( '.gg_woo_feed-iconpicker' ).each( function () {
            $( this ).fontIconPicker();
        } );
    };

    /**
     * Constructor for tab form.
     *
     * @param $tabs
     */
    opal.initTabForm = function ( $tabs ) {
        $( $tabs ).each( function () {
            var $tab = $( this );
            var $parent = $( this ).parent().parent();

            $( 'a', $tab ).click( function () {
                $( '.gg_woo_feed_options_panel', $parent ).hide();
                $( '#' + $( this ).data( 'tab-id' ) ).show();

                $( 'li', $parent ).removeClass( 'active' );

                $( this ).parent().addClass( 'active' );

                return false;
            } );
            $( 'a:first', $tab ).click();
        } );

        var url = window.location.href;
        if( url.indexOf("#") != -1 ) {
            var activeTab = url.substring( url.indexOf("#") + 1);
            $('a[href="#'+ activeTab +'"]').click();
        }
    };

    opal.makeListSortable = function() {
        var $filelist = opal.metabox().find( '.gg_woo_feed-media-status.gg_woo_feed-attach-list' );
        if ( $filelist.length ) {
            $filelist.sortable({ cursor: 'move' }).disableSelection();
        }
    };

    /**
     * Contructor for picker controls.
     *
     * @param $datePickers
     * @param $colorPickers
     */
    opal.initPickers = function ( $colorPickers, $datePickers ) {
        opal.trigger( 'opal_init_pickers', {
            date: $datePickers,
            color: $colorPickers
        } );

        // Initialize jQuery UI datepickers
        opal.initDateTimePickers( $datePickers, 'datepicker', 'date_picker' );

        // Initialize color picker
        opal.initColorPickers( $colorPickers );
    };

    opal.initDateTimePickers = function ( $selector, method, defaultKey ) {
        if ( $selector.length ) {
            $selector[ method ]( 'destroy' ).each( function () {
                var $this = $( this );
                var fieldOpts = $this.data( method ) || {};
                var options = $.extend( {}, opal.defaults[ defaultKey ], fieldOpts );
                $this[ method ]( opal.datePickerSetupOpts( fieldOpts, options, method ) );
            } );
        }
    };

    opal.datePickerSetupOpts = function ( fieldOpts, options, method ) {
        var existing = $.extend( {}, options );

        options.beforeShow = function ( input, inst ) {
            if ( 'timepicker' === method ) {
                opal.addTimePickerClasses( inst.dpDiv );
            }

            // Wrap datepicker w/ class to narrow the scope of jQuery UI CSS and prevent conflicts
            $id( 'ui-datepicker-div' ).addClass( 'gg_woo_feed-element' );

            // Let's be sure to call beforeShow if it was added
            if ( 'function' === typeof existing.beforeShow ) {
                existing.beforeShow( input, inst );
            }
        };

        if ( 'timepicker' === method ) {
            options.onChangeMonthYear = function ( year, month, inst, picker ) {
                opal.addTimePickerClasses( inst.dpDiv );

                // Let's be sure to call onChangeMonthYear if it was added
                if ( 'function' === typeof existing.onChangeMonthYear ) {
                    existing.onChangeMonthYear( year, month, inst, picker );
                }
            };
        }

        options.onClose = function ( dateText, inst ) {
            // Remove the class when we're done with it (and hide to remove FOUC).
            var $picker = $id( 'ui-datepicker-div' ).removeClass( 'gg_woo_feed-element' ).hide();
            if ( 'timepicker' === method && !$( inst.input ).val() ) {
                // Set the timepicker field value if it's empty.
                inst.input.val( $picker.find( '.ui_tpicker_time' ).text() );
            }

            // Let's be sure to call onClose if it was added
            if ( 'function' === typeof existing.onClose ) {
                existing.onClose( dateText, inst );
            }
        };

        return options;
    };

    // Adds classes to timepicker buttons.
    opal.addTimePickerClasses = function ( $picker ) {
        var func = opal.addTimePickerClasses;
        func.count = func.count || 0;

        // Wait a bit to let the timepicker render, since these are pre-render events.
        setTimeout( function () {
            if ( $picker.find( '.ui-priority-secondary' ).length ) {
                $picker.find( '.ui-priority-secondary' ).addClass( 'button-secondary' );
                $picker.find( '.ui-priority-primary' ).addClass( 'button-primary' );
                func.count = 0;
            } else if ( func.count < 5 ) {
                func.count++;
                func( $picker );
            }
        }, 10 );
    };

    /**
     * Handle colorpicker.
     *
     * @return {void}
     */
    opal.initColorPickers = function ( $selector ) {
        if ( !$selector.length ) {
            return;
        }
        if ( 'object' === typeof jQuery.wp && 'function' === typeof jQuery.wp.wpColorPicker ) {

            $selector.each( function () {
                var $this = $( this );
                var fieldOpts = $this.data( 'colorpicker' ) || {};
                $this.wpColorPicker( $.extend( {}, opal.defaults.color_picker, fieldOpts ) );
            } );

        } else {
            $selector.each( function ( i ) {
                $( this )
                    .after( '<div id="picker-' + i +
                        '" style="z-index: 1000; background: #EEE; border: 1px solid #CCC; position: absolute; display: block;"></div>' );
                $id( 'picker-' + i ).hide().farbtastic( $( this ) );
            } )
                     .focus( function () {
                         $( this ).next().show();
                     } )
                     .blur( function () {
                         $( this ).next().hide();
                     } );
        }
    };

    opal.handleMedia = function ( evt ) {
        evt.preventDefault();

        var $el = $( this );
        opal.attach_id = !$el.hasClass( 'gg_woo_feed-upload-list' ) ? $el.closest( '.gg_woo_feed-field-wrap' )
                                                                     .find( '.gg_woo_feed-upload-file-id' )
                                                                     .val() : false;
        // Clean up default 0 value
        opal.attach_id = '0' !== opal.attach_id ? opal.attach_id : false;

        opal._handleMedia( $el.prev( 'input.gg_woo_feed-upload-file' ).attr( 'id' ),
            $el.hasClass( 'gg_woo_feed-upload-list' ) );
    };

    opal.handleFileClick = function ( evt ) {
        if ( $( evt.target ).is( 'a' ) ) {
            return;
        }

        evt.preventDefault();
        var $el = $( this );
        var $td = $el.closest( '.gg_woo_feed-field-wrap' );
        var isList = $td.find( '.gg_woo_feed-upload-button' ).hasClass( 'gg_woo_feed-upload-list' );
        opal.attach_id = isList ? $el.find( 'input[type="hidden"]' ).data( 'id' ) : $td.find(
            '.gg_woo_feed-upload-file-id' ).val();

        if ( opal.attach_id ) {
            opal._handleMedia( $td.find( 'input.gg_woo_feed-upload-file' ).attr( 'id' ), isList, opal.attach_id );
        }
    };

    opal._handleMedia = function ( id, isList ) {
        if ( !wp ) {
            return;
        }

        var media, handlers;

        handlers = opal.mediaHandlers;
        media = opal.media;
        media.field = id;
        media.$field = $id( media.field );
        media.fieldData = media.$field.data();
        media.previewSize = media.fieldData.previewsize;
        media.sizeName = media.fieldData.sizename;
        media.fieldName = media.$field.attr( 'name' );
        media.isList = isList;

        // If this field's media frame already exists, reopen it.
        if ( id in media.frames ) {
            return media.frames[ id ].open();
        }

        // Create the media frame.
        media.frames[ id ] = wp.media( {
            title: opal.metabox().find( 'label[for="' + id + '"]' ).text(),
            library: media.fieldData.queryargs || {},
            button: {
                text: l10n.strings[ isList ? 'upload_files' : 'upload_file' ]
            },
            multiple: isList ? 'add' : false
        } );

        media.frames[ id ].states.first().set( 'filterable', 'all' );

        opal.trigger( 'opal_media_modal_init', media );

        handlers.list = function ( selection, returnIt ) {

            // Setup our fileGroup array
            var fileGroup = [];
            var attachmentHtml;

            if ( !handlers.list.templates ) {
                handlers.list.templates = {
                    image: wp.template( 'gg_woo_feed-list-image' ),
                    file: wp.template( 'gg_woo_feed-list-file' ),
                };
            }

            // Loop through each attachment
            selection.each( function ( attachment ) {

                // Image preview or standard generic output if it's not an image.
                attachmentHtml = handlers.getAttachmentHtml( attachment, 'list' );

                // Add our file to our fileGroup array
                fileGroup.push( attachmentHtml );
            } );

            if ( !returnIt ) {
                // Append each item from our fileGroup array to .gg_woo_feed-media-status
                media.$field.siblings( '.gg_woo_feed-media-status' ).append( fileGroup );
            } else {
                return fileGroup;
            }

        };

        handlers.single = function ( selection ) {
            if ( !handlers.single.templates ) {
                handlers.single.templates = {
                    image: wp.template( 'gg_woo_feed-single-image' ),
                    file: wp.template( 'gg_woo_feed-single-file' ),
                };
            }

            // Only get one file from the uploader
            var attachment = selection.first();

            media.$field.val( attachment.get( 'url' ) );
            $id( media.field + '_id' ).val( attachment.get( 'id' ) );

            // Image preview or standard generic output if it's not an image.
            var attachmentHtml = handlers.getAttachmentHtml( attachment, 'single' );

            // add/display our output
            media.$field.siblings( '.gg_woo_feed-media-status' ).slideDown().html( attachmentHtml );
        };

        handlers.getAttachmentHtml = function ( attachment, templatesId ) {
            var isImage = 'image' === attachment.get( 'type' );
            var data = handlers.prepareData( attachment, isImage );

            // Image preview or standard generic output if it's not an image.
            return handlers[ templatesId ].templates[ isImage ? 'image' : 'file' ]( data );
        };

        handlers.prepareData = function ( data, image ) {
            if ( image ) {
                // Set the correct image size data
                handlers.getImageData.call( data, 50 );
            }

            data = data.toJSON();
            data.mediaField = media.field;
            data.mediaFieldName = media.fieldName;
            data.stringRemoveImage = l10n.strings.remove_image;
            data.stringFile = l10n.strings.file;
            data.stringDownload = l10n.strings.download;
            data.stringRemoveFile = l10n.strings.remove_file;

            return data;
        };

        handlers.getImageData = function ( fallbackSize ) {

            // Preview size dimensions
            var previewW = media.previewSize[ 0 ] || fallbackSize;
            var previewH = media.previewSize[ 1 ] || fallbackSize;

            // Image dimensions and url
            var url = this.get( 'url' );
            var width = this.get( 'width' );
            var height = this.get( 'height' );
            var sizes = this.get( 'sizes' );

            // Get the correct dimensions and url if a named size is set and exists
            // fallback to the 'large' size
            if ( sizes ) {
                if ( sizes[ media.sizeName ] ) {
                    url = sizes[ media.sizeName ].url;
                    width = sizes[ media.sizeName ].width;
                    height = sizes[ media.sizeName ].height;
                } else if ( sizes.large ) {
                    url = sizes.large.url;
                    width = sizes.large.width;
                    height = sizes.large.height;
                }
            }

            // Fit the image in to the preview size, keeping the correct aspect ratio
            if ( width > previewW ) {
                height = Math.floor( previewW * height / width );
                width = previewW;
            }

            if ( height > previewH ) {
                width = Math.floor( previewH * width / height );
                height = previewH;
            }

            if ( !width ) {
                width = previewW;
            }

            if ( !height ) {
                height = 'svg' === this.get( 'filename' ).split( '.' ).pop() ? '100%' : previewH;
            }

            this.set( 'sizeUrl', url );
            this.set( 'sizeWidth', width );
            this.set( 'sizeHeight', height );

            return this;
        };

        handlers.selectFile = function () {
            var selection = media.frames[ id ].state().get( 'selection' );
            var type = isList ? 'list' : 'single';

            if ( opal.attach_id && isList ) {
                $( '[data-id="' + opal.attach_id + '"]' )
                    .parents( 'li' )
                    .replaceWith( handlers.list( selection, true ) );
            } else {
                handlers[ type ]( selection );
            }

            opal.trigger( 'opal_media_modal_select', selection, media );
        };

        handlers.openModal = function () {
            var selection = media.frames[ id ].state().get( 'selection' );
            var attach;

            if ( !opal.attach_id ) {
                selection.reset();
            } else {
                attach = wp.media.attachment( opal.attach_id );
                attach.fetch();
                selection.set( attach ? [ attach ] : [] );
            }

            opal.trigger( 'opal_media_modal_open', selection, media );
        };

        // When a file is selected, run a callback.
        media.frames[ id ]
            .on( 'select', handlers.selectFile )
            .on( 'open', handlers.openModal );

        // Finally, open the modal
        media.frames[ id ].open();
    };

    opal.handleRemoveMedia = function ( evt ) {
        evt.preventDefault();
        var $this = $( this );
        if ( $this.is( '.gg_woo_feed-attach-list .gg_woo_feed-remove-file-button' ) ) {
            $this.parents( '.gg_woo_feed-media-item' ).remove();
            return false;
        }

        opal.media.field = $this.attr( 'rel' );

        opal.metabox().find( document.getElementById( opal.media.field ) ).val( '' );
        opal.metabox().find( document.getElementById( opal.media.field + '_id' ) ).val( '' );
        $this.parents( '.gg_woo_feed-media-status' ).html( '' );

        return false;
    };
    opal.loadAutosugesstion = function () {

        function formatRepo (repo) {
            if ( repo.loading ) {
                return repo.text;
            }
            var markup = "<div class='select2-result-repository clearfix'>" +
            "<div class='select2-result-repository__avatar'><img width=\"50\" src='" + repo.avatar_url + "' /></div>" +
            "<div class='select2-result-repository__meta'>" +
              "<div class='select2-result-repository__title'>" + repo.full_name + "</div>";
            markup +=  "</div></div>";
            return markup;
        }

        function formatRepoSelection (repo) {
          return repo.full_name || repo.text;
        }

        function load_select2_member ( id, action ) {

            $( id ).select2( {
                width: '100%',
                ajax: {
                    url: ajaxurl+"?action="+action,
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                      return {
                            q: params.term, // search term
                            page: params.page
                      };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;

                           return {
                                    results: data.items,
                                    pagination: {
                                        more: (params.page * 30) < data.total_count
                                    }
                               };
                        },
                        cache: true
                     },
                    placeholder: 'Search for a repository',
                    escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
                    minimumInputLength: 1,
                    templateResult: formatRepo,
                    templateSelection: formatRepoSelection
            } );
        }
       load_select2_member( '#post_author_override', 'gg_woo_feed_search_users' );
    };
    opal.groupFields = function ( $group_containers ) {

        var changeIndexes = function ( _container ) {
            $( ".opal-row-repeater", _container ).each( function( i ) {
                $(".form-control" , this ).each( function(){
                    var name = $( this ).attr( 'name' ).replace( /\[\d+\]/, "["+i+"]" );
                    var id = $( this ).attr( 'id' ).replace( /\d+/, i );
                    $(this).attr( "name", name );
                    $(this).attr( "id", id );
                } );
                $(".form-group" , this ).each( function(){
                    var id = $( this ).attr( 'id' ).replace( /\d+/, i );
                    $(this).attr( "id", id );
                } );

                $(".repeat-counter", this ).html( (i+1) );
            } );
        }

        $group_containers.each( function() {
            var $_this = $( this );
            var $template = $( ".gg_woo_feed-template", this );

            $(".opal-row-body", $_this ).hide();
            $(".opal-row-body", $_this ).eq(1).show();


            $(".add-repeat-group-btn", $_this ).on( "click" , function() {
                var index = $( ".opal-row-repeater", $_this ).length;
                var group = $template.clone();
                group.html( group.html().replace(  /{{row-count-placeholder}}/gi, index  ) );
                group.attr( "class", "opal-row-repeater" );
                $(".opal-row-body:visible", $_this ).toggle();

                $("tbody", $_this ).append( group );
                changeIndexes( $_this );
            } );

            $(  $_this ).on( 'click', ".opal-row-repeater button" , function() {
                var group = $( this ).parent().parent().parent().parent();
                var s = $(".opal-row-body", group ).is(":visible");

                if( s == false ){
                     $(".opal-row-body:visible", $_this ).toggle();
                }
                $(".opal-row-body", group ).toggle();
                changeIndexes( $_this );
            } );

             $(  $_this ).on( 'click', ".opal-row-repeater .gg_woo_feed-remove" , function() {
                var group = $( this ).parent().parent().parent().parent();
                $( group ).remove();
                changeIndexes( $_this );
            } );

            $( "tbody", $group_containers ).sortable( {
                placeholder: "ui-state-highlight",
                update: function (){
                    changeIndexes( $_this );
                }
            } );
        } );
    }

    /**
     * Gets jQuery object containing all metaboxes. Caches the result.
     *
     * @return {Object} jQuery object containing all metaboxes.
     */
    opal.metabox = function () {
        if ( opal.$metabox ) {
            return opal.$metabox;
        }
        opal.$metabox = $( '.js-gg_woo_feed-metabox-wrap' );
        return opal.$metabox;
    };

    /**
     * Triggers a jQuery event on the document object.
     *
     * @param  {string} evtName The name of the event to trigger.
     *
     * @return {void}
     */
    opal.trigger = function ( evtName ) {
        var args = Array.prototype.slice.call( arguments, 1 );
        args.push( opal );
        $document.trigger( evtName, args );
    };

    // Kick it off!
    $( opal.init );

} )( window, document, jQuery, window.OPAL );
