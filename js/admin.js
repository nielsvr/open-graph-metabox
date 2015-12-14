( function( $ ) {
;
    $( function() {
	    
        $.fn.OGImageSelector = function( options ) {
        
            var selector = $( this ).selector; // Get the selector
                        
            // Set default options
            var defaults = {
                'preview' : '#open_graph_image_preview',
                'text'    : '#open_graph_image_value',
                'button'  : '#open_graph_image'
            };
            var options  = $.extend( defaults, options );
 
            // When the Button is clicked...
            $( options.button ).click( function() {
		
                // Get the Text element.
                var text = $( this ).siblings( options.text );
 
                // Show WP Media Uploader popup
                tb_show( 'Select a image', 'media-upload.php?referer=open_graph_metabox&type=image&TB_iframe=true&post_id=0', false );
 
                // Re-define the global function 'send_to_editor'
                // Define where the new value will be sent to
                window.send_to_editor = function( html ) {
                    // Get the URL of new image
                    var src = $( 'img', html ).attr( 'src' );
                    // Send this value to the Text field.
                    text.attr( 'value', src ).trigger( 'change' );
                    tb_remove(); // Then close the popup window
                }
                return false;
            } );
            
        }
 
        // Usage
        $( '#open_graph_image' ).OGImageSelector(); 
        
        $( '#home_open_graph_image' ).OGImageSelector({
	        'text': '#home_open_graph_image_value',
	        'button': '#home_open_graph_image'
        }); 
        
    } );
} ( jQuery ) );