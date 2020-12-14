/**
 * ResponsiThemes Admin Interface JavaScript
 *
 * All JavaScript logic for the theme options admin interface.
 * @since 4.8.0
 *
 */

(function ($) {
	
	$( window ).on( 'load', function() {
        $('#sub-accordion-section-header_style').on('expanded', function() {
        	$('#customize-control-responsi_header_border_top').addClass('hidden');
        	$('#customize-control-responsi_header_border_bottom').addClass('hidden');
        	$('#customize-control-responsi_header_border_lr').addClass('hidden');
        	$('#customize-control-responsi_header_box_shadow').addClass('hidden');
        	$('#customize-control-responsi_header_margin').addClass('hidden');
        });
    });
})(jQuery);