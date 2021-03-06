/** Handles pre-built Panel layouts. */
jQuery(function($){
    $( '#grid-prebuilt-dialog' ).show().dialog( {
        dialogClass: 'panels-admin-dialog',
        autoOpen:    false,
        resizable:   false,
        draggable:   false,
        modal:       false,
        title:       $( '#grid-prebuilt-dialog' ).attr( 'data-title' ),
        minWidth:    600,
        height:      450,
        create:      function(event, ui){
        },
        open:        function(){
            var overlay = $('<div class="siteorigin-panels-ui-widget-overlay ui-widget-overlay ui-front"></div>').css('z-index', 80001);
            $(this).data('overlay', overlay).closest('.ui-dialog').before(overlay);

            // Turn the dropdown into a chosen selector
            $( '#grid-prebuilt-dialog' ).find('select').chosen({
                disable_search_threshold: 8,
                search_contains: true,
                placeholder_text: $( '#grid-prebuilt-dialog' ).find('select' ).attr('placeholder')
            });

        },
        close :      function(){
            $(this).data('overlay').remove();
        },
        buttons : [
            {
                text: 'Insert',
                click: function(){
                    var dialog = $(this).closest('.ui-dialog');
                    if(dialog.hasClass('panels-ajax-loading')){
	                    return;
	                    }
                    dialog.addClass('panels-ajax-loading');

                    var s = $('#grid-prebuilt-input' ).find(':selected');
                    if(s.attr('data-layout-id') == null) {
                        $( '#grid-prebuilt-dialog' ).dialog('close');
                        return;
                    }

                    $.get( ajaxurl, {action: 'so_panels_prebuilt', layout: s.attr('data-layout-id')}, function(data){
                        dialog.removeClass('panels-ajax-loading');

                        if(typeof data.name !== 'undefined') {
                            if(confirm('Are you sure you want to load this layout? It will overwrite your current page.')){
                                // Clear the grids and load the prebuilt layout
                                panels.clearGrids();
                                panels.loadPanels(data);
                                $( '#grid-prebuilt-dialog' ).dialog('close');
                            }
                        }
                    } );

                }
            }
        ]
    } );
    
    // Button for adding prebuilt layouts
    $( '#add-to-panels .prebuilt-set' )
        .button( {} )
        .click( function () {
            $( '#grid-prebuilt-dialog' ).dialog( 'open' );
            return false;
        } );
});