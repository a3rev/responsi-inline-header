/**
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */
(function($) {

	function responsi_ih_preview() {
		
		var css = '', animation_speed = 0, out_plus = 0, in_plus = 0, corner_top = 0, corner_right = 0;

		var responsi_enable_header_bg_image = wp.customize.value('responsi_enable_header_bg_image')(),
		responsi_header_bg_image = wp.customize.value('responsi_header_bg_image')(),
		responsi_bg_header_position_vertical = wp.customize.value('responsi_bg_header_position_vertical')(),
		responsi_bg_header_position_horizontal = wp.customize.value('responsi_bg_header_position_horizontal')(),
		responsi_header_bg_image_repeat = wp.customize.value('responsi_header_bg_image_repeat')();

        var responsi_enable_header_inner_bg_image = wp.customize.value('responsi_enable_header_inner_bg_image')(),
        responsi_header_inner_bg_image = wp.customize.value('responsi_header_inner_bg_image')(),
        responsi_bg_header_inner_position_vertical = wp.customize.value('responsi_bg_header_inner_position_vertical')(),
        responsi_bg_header_inner_position_horizontal = wp.customize.value('responsi_bg_header_inner_position_horizontal')(),
        responsi_header_inner_bg_image_repeat = wp.customize.value('responsi_header_inner_bg_image_repeat')();

        out_plus 		= parseInt(wp.customize.value('responsi_header_inner_margin_top')()) + parseInt(wp.customize.value('responsi_header_inner_margin_bottom')()) + parseInt(wp.customize.value('responsi_header_inner_padding_top')()) + parseInt(wp.customize.value('responsi_header_inner_padding_bottom')()) + parseInt(wp.customize.value('responsi_header_inner_border_top[width]')()) + parseInt(wp.customize.value('responsi_header_inner_border_bottom[width]')());
	    in_plus  		= parseInt(wp.customize.value('responsi_header_margin_top')()) + parseInt(wp.customize.value('responsi_header_margin_bottom')()) + parseInt(wp.customize.value('responsi_header_padding_top')()) + parseInt(wp.customize.value('responsi_header_padding_bottom')()) + parseInt(wp.customize.value('responsi_header_border_top[width]')()) + parseInt(wp.customize.value('responsi_header_border_bottom[width]')());

	    corner_top 		= parseInt(wp.customize.value('responsi_header_inner_margin_top')()) + parseInt(wp.customize.value('responsi_header_inner_padding_top')()) + parseInt(wp.customize.value('responsi_header_inner_border_top[width]')()) + parseInt(wp.customize.value('responsi_header_margin_top')()) + parseInt(wp.customize.value('responsi_header_padding_top')()) + parseInt(wp.customize.value('responsi_header_border_top[width]')());
	    corner_right	= parseInt(wp.customize.value('responsi_header_inner_margin_right')()) + parseInt(wp.customize.value('responsi_header_inner_padding_right')()) + parseInt(wp.customize.value('responsi_header_inner_border_lr[width]')()) + parseInt(wp.customize.value('responsi_header_margin_right')()) + parseInt(wp.customize.value('responsi_header_padding_right')()) + parseInt(wp.customize.value('responsi_header_border_lr[width]')());

		css += '.ih-wide{';
			css += _cFn.renderMarPad('responsi_ih_padding','padding');
		css += '}';

        css += '.responsi-ih-wide{';
        	css += 'max-width:' + parseInt(wp.customize.value('responsi_ih_width')()) + 'px;';
        css += '}';

        if( 0 == wp.customize.value('responsi_ih_animation_speed')() ){
            animation_speed = 0;
        }else if( 10 == wp.customize.value('responsi_ih_animation_speed')() ){
            animation_speed = 1;
        }else{
            animation_speed = '0.'+wp.customize.value('responsi_ih_animation_speed')();
        }

        css += '.ih-ctn{';
	        css += _cFn.renderBG('responsi_header_bg');
	        if (responsi_enable_header_bg_image == 'true') {
	            css += 'background-image: url(' + responsi_header_bg_image + ');';
	            css += 'background-position:' + responsi_bg_header_position_horizontal + ' ' + responsi_bg_header_position_vertical + ';';
	            css += 'background-repeat:' + responsi_header_bg_image_repeat + ';';
	        } else {
	            css += 'background-image: none;';
	        }
	        css += _cFn.renderMarPad('responsi_header_padding', 'padding');
	        css += _cFn.renderMarPad('responsi_header_margin', 'margin');
	        css += _cFn.renderBorder('responsi_header_border_top', 'top');
	        css += _cFn.renderBorder('responsi_header_border_bottom', 'bottom');
	        css += _cFn.renderBorder('responsi_header_border_lr', 'left');
	        css += _cFn.renderBorder('responsi_header_border_lr', 'right');
	        css += _cFn.renderShadow('responsi_header_box_shadow');
        css += '}';

        css += '.ih-content-wrap{';
	        css += _cFn.renderBG('responsi_header_inner_bg');
	        if (responsi_enable_header_inner_bg_image == 'true') {
	            css += 'background-image: url(' + responsi_header_inner_bg_image + ');';
	            css += 'background-position:' + responsi_bg_header_inner_position_horizontal + ' ' + responsi_bg_header_inner_position_vertical + ';';
	            css += 'background-repeat:' + responsi_header_inner_bg_image_repeat + ';';
	        } else {
	            css += 'background-image: none;';
	        }
	        css += _cFn.renderMarPad('responsi_header_inner_padding', 'padding');
	        css += _cFn.renderMarPad('responsi_header_inner_margin', 'margin');
	        css += _cFn.renderBorder('responsi_header_inner_border_top', 'top');
	        css += _cFn.renderBorder('responsi_header_inner_border_bottom', 'bottom');
	        css += _cFn.renderBorder('responsi_header_inner_border_lr', 'left');
	        css += _cFn.renderBorder('responsi_header_inner_border_lr', 'right');
	        css += _cFn.renderShadow('responsi_header_inner_box_shadow');
        css += '}';

        css += '.ih-ctn .logo-ctn img{';
			css += 'transition: '+animation_speed+'s;';
		css += '}';

		css += '.ih-content{';
			css += 'transition: height '+animation_speed+'s;';
		css += '}';

		css += '.ih-area-widget .widget-title h3 {';
        css += _cFn.renderTypo('responsi_font_header_widget_title');
        css += '}';
        css += '.ih-area-widget .widget .textwidget, .ih-area-widget .widget:not(div), .ih-area-widget .widget p,.ih-area-widget .widget label,.ih-area-widget .widget .textwidget,.in_widget .login-username label, .in_widget .login-password label, .ih-area-widget .widget .textwidget .tel, .ih-area-widget .widget .textwidget .tel a, .ih-area-widget .widget .textwidget a[href^=tel], .ih-area-widget .widget * a[href^=tel], .ih-area-widget .widget a[href^=tel]{';
        css += _cFn.renderTypo('responsi_font_header_widget_text');
        css += 'text-decoration: none;';
        css += '}';
        css += '.ih-area-widget .widget a,.ih-area-widget .widget ul li a,.ih-area-widget .widget ul li{';
        css += _cFn.renderTypo('responsi_font_header_widget_link');
        css += 'text-decoration: none;';
        css += '}';
        
        css += '.ih-area-widget .widget a:hover{color:' + wp.customize.value('responsi_font_header_widget_link_hover')() + ';}';
        
        css += '.ih-area-widget .widget{text-align:text-align:inherit;}';

        /* var header_widgets_margin = 0;
        if (wp.customize.value('responsi_header_widget_mobile_margin')() == 'true' && wp.customize.value('responsi_header_widget_mobile_margin_between')() >= 0) {
            header_widgets_margin = wp.customize.value('responsi_header_widget_mobile_margin_between')();
        }

        if (wp.customize.value('responsi_font_header_widget_text_alignment_mobile')() == 'true') {
            var center_header_widget_mobile = '.ih-area-widget .widget, .ih-area-widget * .widget, .ih-area-widget .widget *, .ih-area-widget .widget .widget-title h3 {text-align:center !important;}.logo.site-logo,.logo-ctn,.desc-ctn{margin:auto;}';
        } else {
            var center_header_widget_mobile = '.ih-area-widget .widget, .ih-area-widget * .widget, .ih-area-widget .widget *, .ih-area-widget .widget .widget-title h3 {text-align:' + wp.customize.value('responsi_font_header_widget_text_alignment')() + ' !important;}.logo.site-logo,.logo-ctn,.desc-ctn{margin:auto;}';
        }

        css += '@media only screen and (max-width: 782px) {';
        css += '.ih-area-widget .widget{ margin-bottom:' + header_widgets_margin + 'px !important;}';
        css += center_header_widget_mobile;
        css += '}';
        */

        css += '@media (min-width:783px) {';

        	css += '.ih-ctn .logo-ctn img{';
        		css += 'max-height:'+parseInt(wp.customize.value('responsi_ih_max')())+'px';
			css += '}';

			css += '.ih-sticky .logo-ctn img{';
			  	css += 'max-height:'+parseInt(wp.customize.value('responsi_ih_min')())+'px';
			css += '}';
			css += '.ih-content{';
				css += 'height:'+parseInt(wp.customize.value('responsi_ih_max')())+'px';
			css += '}';
			css += '.ih-sticky .ih-content{';
				css += 'height:'+parseInt(wp.customize.value('responsi_ih_min')())+'px';
			css += '}';
			css += '.ih-sticky1,.hasSticky #ih-layout {';
				css += 'margin-bottom:'+ ( parseInt(wp.customize.value('responsi_ih_max')() ) + parseInt( in_plus ) + parseInt( out_plus ) ) +'px';
			css += '}';

			css += '.ih-area-widget1{';
				css += 'text-align: '+wp.customize.value('responsi_ih_widget1_alignment')()+';';
			css += '}';

			css += '.ih-area-widget2{';
				css += 'text-align: '+wp.customize.value('responsi_ih_widget2_alignment')()+';';
			css += '}';

        css += '}';

        css += '@media only screen and (min-width:600px) and (max-width:782px) {';

        	css += '.ih-area-widget1{';
				css += 'text-align: '+wp.customize.value('responsi_ih_widget1_alignment_tablet')()+';';
			css += '}';

			css += '.ih-area-widget2{';
				css += 'text-align: '+wp.customize.value('responsi_ih_widget2_alignment_tablet')()+';';
			css += '}';

			if( wp.customize.value('responsi_ih_column_tablet[col]')() == 4 ){

		        css += '.ih-tablet-0 #ih-area-1{';
	                css += 'width:'+wp.customize.value('responsi_ih_column_tablet[col1]')()+'% !important;';
	            css += '}';

	            css += '.ih-tablet-0 #ih-area-2{';
	                css += 'width:'+wp.customize.value('responsi_ih_column_tablet[col2]')()+'% !important;';
	            css += '}';

	            css += '.ih-tablet-0 #ih-area-3{';
	                css += 'width:'+wp.customize.value('responsi_ih_column_tablet[col3]')()+'% !important;';
	                css += 'display:inline !important;';
	            css += '}';

	            css += '.ih-tablet-0 #ih-area-4{';
	                css += 'width:'+wp.customize.value('responsi_ih_column_tablet[col4]')()+'% !important;';
	                css += 'display:inline !important;';
	            css += '}';
	    	}

	    	if( wp.customize.value('responsi_ih_column_tablet[col]')() == 3 ){

		        css += '.ih-tablet-0 #ih-area-1{';
	                css += 'width:'+wp.customize.value('responsi_ih_column_tablet[col1]')()+'% !important;';
	            css += '}';

	            css += '.ih-tablet-0 #ih-area-2{';
	                css += 'width:'+wp.customize.value('responsi_ih_column_tablet[col2]')()+'% !important;';
	            css += '}';

	            css += '.ih-tablet-0 #ih-area-3{';
	                css += 'width:'+wp.customize.value('responsi_ih_column_tablet[col3]')()+'% !important;';
	                css += 'display:inline !important;';
	            css += '}';

	            css += '.ih-tablet-0 #ih-area-4{';
	                css += 'width:'+wp.customize.value('responsi_ih_column_tablet[col4]')()+'% !important;';
	                css += 'display:none !important;';
	            css += '}';

	    	}

	    	if( wp.customize.value('responsi_ih_column_tablet[col]')() == 2 ){

		        css += '.ih-tablet-0 #ih-area-1{';
	                css += 'width:'+wp.customize.value('responsi_ih_column_tablet[col1]')()+'% !important;';
	            css += '}';

	            css += '.ih-tablet-0 #ih-area-2{';
	                css += 'width:'+wp.customize.value('responsi_ih_column_tablet[col2]')()+'% !important;';
	            css += '}';

	            css += '.ih-tablet-0 #ih-area-3{';
	                css += 'width:'+wp.customize.value('responsi_ih_column_tablet[col3]')()+'% !important;';
	                css += 'display:none !important;';
	            css += '}';

	            css += '.ih-tablet-0 #ih-area-4{';
	                css += 'width:'+wp.customize.value('responsi_ih_column_tablet[col4]')()+'% !important;';
	                css += 'display:none !important;';
	            css += '}';

	    	}

	    	if( wp.customize.value('responsi_ih_column_tablet[col]')() == 1 ){

		        css += '.ih-tablet-0 #ih-area-1{';
	                css += 'width:'+wp.customize.value('responsi_ih_column_tablet[col1]')()+'% !important;';
	            css += '}';

	            css += '.ih-tablet-0 #ih-area-2{';
	                css += 'width:'+wp.customize.value('responsi_ih_column_tablet[col2]')()+'% !important;';
	            css += '}';

	            css += '.ih-tablet-0 #ih-area-3{';
	                css += 'width:'+wp.customize.value('responsi_ih_column_tablet[col3]')()+'% !important;';
	                css += 'display:none !important;';
	            css += '}';

	            css += '.ih-tablet-0 #ih-area-4{';
	                css += 'width:'+wp.customize.value('responsi_ih_column_tablet[col4]')()+'% !important;';
	                css += 'display:none !important;';
	            css += '}';

	    	}

	    	css += '.ih-ctn .logo-ctn img{';
        		css += 'max-height:'+parseInt(wp.customize.value('responsi_ih_max_tablet')())+'px';
			css += '}';

			css += '.ih-sticky .logo-ctn img{';
			  	css += 'max-height:'+parseInt(wp.customize.value('responsi_ih_min_tablet')())+'px';
			css += '}';
			
			css += '.ih-content{';
				css += 'height:'+parseInt(wp.customize.value('responsi_ih_max_tablet')())+'px';
			css += '}';
			
			css += '.ih-sticky .ih-content{';
				css += 'height:'+parseInt(wp.customize.value('responsi_ih_min_tablet')())+'px';
			css += '}';

			css += '.ih-sticky1,.hasSticky #ih-layout {';
				css += 'margin-bottom:'+ ( parseInt(wp.customize.value('responsi_ih_max_tablet')() ) + parseInt( in_plus ) + parseInt( out_plus ) ) +'px';
			css += '}';

			css += '.ih-layout:not(.ihSticky) .stickyMenu{';
			    css += 'margin-top:'+parseInt(corner_top)+'px !important;';
			    css += 'right:'+parseInt(corner_right)+'px !important;';
			css += '}';

			css += '.ih-layout.ihSticky .ih-tablet-nonsticky .stickyMenu{';
				css += 'margin-top:'+parseInt(corner_top)+'px !important;';
			    css += 'right:'+parseInt(corner_right)+'px !important;';
			css += '}';

        css += '}';

        css += '@media ( max-width: 600px ) {';

        	css += '.ih-area-widget1{';
				css += 'text-align: '+wp.customize.value('responsi_ih_widget1_alignment_mobile')()+';';
			css += '}';

			css += '.ih-area-widget2{';
				css += 'text-align: '+wp.customize.value('responsi_ih_widget2_alignment_mobile')()+';';
			css += '}';

            if( wp.customize.value('responsi_ih_column_mobile[col]')() == 4 ){

		        css += '.ih-mobile-0 #ih-area-1{';
	                css += 'width:'+wp.customize.value('responsi_ih_column_mobile[col1]')()+'% !important;';
	            css += '}';

	            css += '.ih-mobile-0 #ih-area-2{';
	                css += 'width:'+wp.customize.value('responsi_ih_column_mobile[col2]')()+'% !important;';
	            css += '}';

	            css += '.ih-mobile-0 #ih-area-3{';
	                css += 'width:'+wp.customize.value('responsi_ih_column_mobile[col3]')()+'% !important;';
	                css += 'display:inline !important;';
	            css += '}';

	            css += '.ih-mobile-0 #ih-area-4{';
	                css += 'width:'+wp.customize.value('responsi_ih_column_mobile[col4]')()+'% !important;';
	                css += 'display:inline !important;';
	            css += '}';

	    	}

	    	if( wp.customize.value('responsi_ih_column_mobile[col]')() == 3 ){

		        css += '.ih-mobile-0 #ih-area-1{';
	                css += 'width:'+wp.customize.value('responsi_ih_column_mobile[col1]')()+'% !important;';
	            css += '}';

	            css += '.ih-mobile-0 #ih-area-2{';
	                css += 'width:'+wp.customize.value('responsi_ih_column_mobile[col2]')()+'% !important;';
	            css += '}';

	            css += '.ih-mobile-0 #ih-area-3{';
	                css += 'width:'+wp.customize.value('responsi_ih_column_mobile[col3]')()+'% !important;';
	                css += 'display:inline !important;';
	            css += '}';

	            css += '.ih-mobile-0 #ih-area-4{';
	                css += 'width:'+wp.customize.value('responsi_ih_column_mobile[col4]')()+'% !important;';
	                css += 'display:none !important;';
	            css += '}';

	    	}

	    	if( wp.customize.value('responsi_ih_column_mobile[col]')() == 2 ){

		        css += '.ih-mobile-0 #ih-area-1{';
	                css += 'width:'+wp.customize.value('responsi_ih_column_mobile[col1]')()+'% !important;';
	            css += '}';

	            css += '.ih-mobile-0 #ih-area-2{';
	                css += 'width:'+wp.customize.value('responsi_ih_column_mobile[col2]')()+'% !important;';
	            css += '}';

	            css += '.ih-mobile-0 #ih-area-3{';
	                css += 'width:'+wp.customize.value('responsi_ih_column_mobile[col3]')()+'% !important;';
	                css += 'display:none !important;';
	            css += '}';

	            css += '.ih-mobile-0 #ih-area-4{';
	                css += 'width:'+wp.customize.value('responsi_ih_column_mobile[col4]')()+'% !important;';
	                css += 'display:none !important;';
	            css += '}';

	    	}

	    	if( wp.customize.value('responsi_ih_column_mobile[col]')() == 1 ){

		        css += '.ih-mobile-0 #ih-area-1{';
	                css += 'width:'+wp.customize.value('responsi_ih_column_mobile[col1]')()+'% !important;';
	            css += '}';

	            css += '.ih-mobile-0 #ih-area-2{';
	                css += 'width:'+wp.customize.value('responsi_ih_column_mobile[col2]')()+'% !important;';
	            css += '}';

	            css += '.ih-mobile-0 #ih-area-3{';
	                css += 'width:'+wp.customize.value('responsi_ih_column_mobile[col3]')()+'% !important;';
	                css += 'display:none !important;';
	            css += '}';

	            css += '.ih-mobile-0 #ih-area-4{';
	                css += 'width:'+wp.customize.value('responsi_ih_column_mobile[col4]')()+'% !important;';
	                css += 'display:none !important;';
	            css += '}';

	    	}

    		css += '.ih-ctn .logo-ctn img{';
        		css += 'max-height:'+parseInt(wp.customize.value('responsi_ih_max_mobile')())+'px';
			css += '}';

			css += '.ih-sticky .logo-ctn img{';
			  	css += 'max-height:'+parseInt(wp.customize.value('responsi_ih_min_mobile')())+'px';
			css += '}';
			
			css += '.ih-content{';
				css += 'height:'+parseInt(wp.customize.value('responsi_ih_max_mobile')())+'px';
			css += '}';
			
			css += '.ih-sticky .ih-content{';
				css += 'height:'+parseInt(wp.customize.value('responsi_ih_min_mobile')())+'px';
			css += '}';

			css += '.ih-sticky1,.hasSticky #ih-layout {';
				css += 'margin-bottom:'+ ( parseInt(wp.customize.value('responsi_ih_max_mobile')() ) + parseInt( in_plus ) + parseInt( out_plus ) ) +'px';
			css += '}';

			css += '.ih-layout:not(.ihSticky) .stickyMenu{';
			    css += 'margin-top:'+parseInt(corner_top)+'px !important;';
			    css += 'right:'+parseInt(corner_right)+'px !important;';
			css += '}';

			css += '.ih-layout.ihSticky .ih-mobile-nonsticky .stickyMenu{';
			    css += 'margin-top:'+parseInt(corner_top)+'px !important;';
			    css += 'right:'+parseInt(corner_right)+'px !important;';
			css += '}';

        css += '}';

        if ($('#responsi_ih_preview').length > 0) {
            $('#responsi_ih_preview').html(css);
        } else {
            $('head').append('<style id="responsi_ih_preview">' + css + '</style>');
        }

        $(window).trigger('resize');
	}

	var fonts_fields = [
        'responsi_font_logo',
        'responsi_font_desc',
        'responsi_font_header_widget_title',
        'responsi_font_header_widget_text',
        'responsi_font_header_widget_link'
    ];

	var single_fields = [
		'responsi_ih_width',
		'responsi_ih_max',
		'responsi_ih_min',
		'responsi_ih_animation_speed',
		'responsi_ih_column_tablet[col1]',
		'responsi_ih_column_tablet[col2]',
		'responsi_ih_column_tablet[col3]',
		'responsi_ih_column_tablet[col4]',
		'responsi_ih_column_mobile[col1]',
		'responsi_ih_column_mobile[col2]',
		'responsi_ih_column_mobile[col3]',
		'responsi_ih_column_mobile[col4]',
		'responsi_ih_max_tablet',
		'responsi_ih_min_tablet',
		'responsi_ih_max_mobile',
		'responsi_ih_min_mobile',
		'responsi_ih_widget1_alignment',
		'responsi_ih_widget2_alignment',
		'responsi_ih_widget1_alignment_tablet',
		'responsi_ih_widget2_alignment_tablet',
		'responsi_ih_widget1_alignment_mobile',
		'responsi_ih_widget2_alignment_mobile',

		'responsi_font_header_widget_text_alignment_mobile',
        'responsi_enable_header_bg_image',
        'responsi_header_bg_image',
        'responsi_header_bg_image_repeat',
        'responsi_enable_header_inner_bg_image',
        'responsi_header_inner_bg_image',
        'responsi_header_inner_bg_image_repeat',
        'responsi_font_header_widget_link_hover',
        'responsi_font_header_widget_text_alignment',
        'responsi_header_widget_mobile_margin',
        'responsi_header_widget_mobile_margin_between'
    ];

    var bg_fields = [
    	'responsi_header_bg',
        'responsi_header_inner_bg'
    ];

    var border_fields = [
    	'responsi_header_border_top',
        'responsi_header_border_bottom',
        'responsi_header_border_lr',
        'responsi_header_inner_border_top',
        'responsi_header_inner_border_bottom',
        'responsi_header_inner_border_lr'
    ];

    var border_boxes_fields = [
    ]

    var border_radius_fields = [
    ];

    var shadow_fields = [
        'responsi_header_box_shadow',
        'responsi_header_inner_box_shadow'
    ];

    var margin_padding_fields = [
        'responsi_header_margin',
        'responsi_header_padding',
        'responsi_header_inner_margin',
        'responsi_header_inner_padding'
    ];

    var position_fields = [
        'responsi_bg_header_position',
        'responsi_bg_header_inner_position',
    ];

    $.each(single_fields, function(inx, val) {
        wp.customize(val, function(value) {
            value.bind(function(to) {
                responsi_ih_preview();
            });
        });
    });

    $.each(border_boxes_fields, function(inx, val) {
        $.each(ctrlBorderBoxes, function(i, v) {
            wp.customize(val + '[' + v + ']', function(value) {
                value.bind(function(to) {
                    responsi_ih_preview();
                });
            });
        });
    });

    $.each(border_fields, function(inx, val) {
        $.each(ctrlBorder, function(i, v) {
            wp.customize(val + '[' + v + ']', function(value) {
                value.bind(function(to) {
                    responsi_ih_preview();
                });
            });
        });
    });

    $.each(border_radius_fields, function(inx, val) {
        $.each(ctrlRadius, function(i, v) {
            wp.customize(val + '[' + v + ']', function(value) {
                value.bind(function(to) {
                    responsi_ih_preview();
                });
            });
        });
    });

    $.each(shadow_fields, function(inx, val) {
        $.each(window.ctrlShadow, function(i, v) {
            wp.customize(val + '[' + v + ']', function(value) {
                value.bind(function(to) {
                    responsi_ih_preview();
                });
            });
        });
    });

    $.each(margin_padding_fields, function(inx, val) {
        $.each(ctrlMarPad, function(i, v) {
            wp.customize(val + v, function(value) {
                value.bind(function(to) {
                    responsi_ih_preview();
                });
            });
        });
    });

    $.each(position_fields, function(inx, val) {
        $.each(window.ctrlPos, function(i, v) {
            wp.customize(val + v, function(value) {
                value.bind(function(to) {
                    responsi_ih_preview();
                });
            });
        });
    });

    $.each(bg_fields, function(inx, val) {
        $.each(ctrlBG, function(i, v) {
            wp.customize(val + '[' + v + ']', function(value) {
                value.bind(function(to) {
                    responsi_ih_preview();
                });
            });
        });
    });

    wp.customize('responsi_ih_column[col1]', function(value) {
        value.bind(function(to) {
            $('#ih-area-1').width(to+'%');
        });
    });

    wp.customize('responsi_ih_column[col2]', function(value) {
        value.bind(function(to) {
            $('#ih-area-2').width(to+'%');
        });
    });

    wp.customize('responsi_ih_column[col3]', function(value) {
        value.bind(function(to) {
            $('#ih-area-3').width(to+'%');
        });
    });

    wp.customize('responsi_ih_column[col4]', function(value) {
        value.bind(function(to) {
            $('#ih-area-4').width(to+'%');
        });
    });

	wp.customize('responsi_ih_custom_width', function(value) {
        value.bind(function(to) {
            if( to == 'true' ){
                $('.ih-wide').addClass('responsi-ih-wide');
            }else{
                $('.ih-wide').removeClass('responsi-ih-wide');
            }
        });
    });

    wp.customize('responsi_ih_mobile_scroll', function(value) {
        value.bind(function(to) {
            if( to == 'true' ){
                $('.ih-ctn, .ih-layout').addClass('ih-mobile-nonsticky');
            }else{
                $('.ih-ctn, .ih-layout').removeClass('ih-mobile-nonsticky');
            }
        });
    });

    wp.customize('responsi_ih_tablet_scroll', function(value) {
        value.bind(function(to) {
            if( to == 'true' ){
                $('.ih-ctn, .ih-layout').addClass('ih-tablet-nonsticky');
            }else{
                $('.ih-ctn, .ih-layout').removeClass('ih-tablet-nonsticky');
            }
        });
    });

	wp.customize('responsi_ih_animation[type]', function(value) {
        value.bind(function(to) {
            _cFn.renderAnimation( '.ih-animation', 'responsi_ih_animation' );
        });
    });

    wp.customize('responsi_ih_animation[duration]', function(value) {
        value.bind(function(to) {
            _cFn.renderAnimation( '.ih-animation', 'responsi_ih_animation' );
        });
    });

    wp.customize('responsi_ih_animation[delay]', function(value) {
        value.bind(function(to) {
            _cFn.renderAnimation( '.ih-animation', 'responsi_ih_animation' );
        });
    });

    wp.customize('responsi_ih_animation[direction]', function(value) {
        value.bind(function(to) {
            _cFn.renderAnimation( '.ih-animation', 'responsi_ih_animation' );
        });
    });

})(jQuery);