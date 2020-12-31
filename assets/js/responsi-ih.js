(function ($) {

	window.ihPos = false;

	window.ihAnimationSpeed = 0

	if (typeof responsi_ih_paramaters !== 'undefined') {
		if (typeof responsi_ih_paramaters._position !== 'undefined') {
			window.ihPos 			= responsi_ih_paramaters._position;
			window.ihAnimationSpeed = parseInt( parseInt(responsi_ih_paramaters._animation_speed) - 0.1 + '00' )  ;
		}
	}

	window.ihNavPositon = function( num ) {

		//return;

		var ihHeight = 0;
		
		ihHeight = (window.getComputedStyle(document.getElementById( 'ih-ctn' )).getPropertyValue('height')).toLowerCase().replace("px", "") ;
		ihHeight = parseInt(ihHeight);
		ihHeight = ihHeight + parseInt(num);

		if( document.querySelectorAll(".hasSticky").length <= 0 && window.elAdminbar != null && ( window.getComputedStyle(window.elAdminbar).getPropertyValue('position').toLowerCase() != 'fixed' ) ){
			ihHeight = ihHeight + 46;
		}

		if( document.querySelectorAll(".hasSticky").length <= 0 && window.elAboveHeaderCtn != null && ( window.getComputedStyle(window.elAboveHeaderCtn).getPropertyValue('position').toLowerCase() != 'fixed' ) ){
			ihHeight = ihHeight + window.elAboveHeaderCtn.clientHeight;
		}

		return ihHeight;
	};

	window.ihStickyMenu = function( num ) {
		if ( window.pageYOffset > ( window.elSticky - window.responsiToolbarHeight ) ) {
	    	window.elMenu.classList.add("stickyMenu");
	    	window.elMenu.style.top = parseInt(num) +'px';
	  	} else {
	  		window.elMenu.classList.remove("stickyMenu");
	  		window.elMenu.style.top = 'auto';
	  	}
	};

	// When the user scrolls the page, execute myFunction
	window.onscroll = function() {
		//window.ihFunction()
	};

	window.ihFunction = function() {
		if (window.location.href.indexOf("test") <= -1) {

			var styleTop = 0;
			window.inToolbar = document.getElementById( 'responsi-toolbar' ).querySelector(".ih-layout");
				
			
	
			if( window.inToolbar != null && window.inToolbar.length > 0 ){
				styleTop = window.responsiToolbarHeight ;
			}else{
				styleTop = window.wpAdminbarHeight + window.responsiToolbarHeight + window.elAboveHeaderHeight;
			}

			if( 'true' == window.ihPos || true == window.ihPos ){
			  	
			  	var offsetTopMain = window.elSticky + window.responsiToolbarHeight + window.elAboveHeaderHeight2 + window.wpAdminbarHeight2;

			  	if ( document.body.scrollTop > offsetTopMain || document.documentElement.scrollTop > offsetTopMain ) {
			    	window.elHeaderCtn.style.top = parseInt(styleTop) +'px';
			    	window.elHeaderCtn.classList.add("ih-sticky");
			    	window.elBody.classList.add("hasSticky");
			  	} else {
			  		if( typeof window.elBody != "undefined" ){
			  			window.elBody.classList.remove("hasSticky");
						window.elHeaderCtn.classList.remove("ih-sticky");
					}
			  	}

			  	if ( document.querySelector(".ih-ctn").classList.contains("ih-mobile-nonsticky") || document.querySelector(".ih-ctn").classList.contains("ih-tablet-nonsticky") ) {
					window.ihStickyMenu( styleTop );
				}

			}else{
				window.ihStickyMenu( styleTop );
			}

		}
	}

	window.onscroll = function() { window.ihFunction(); };

	$(window).on( 'load resize', function() {

		window.elBody 					= ( document.getElementsByTagName('body')[0] );
		window.elMenu 					= ( document.getElementById( 'ih-area-2' ) && document.getElementById( 'ih-area-2' ).innerHTML.length ) ? document.getElementById( 'ih-area-2' ) : null;

		window.elAdminbar 				= ( document.getElementById( 'wpadminbar' ) && document.getElementById( 'wpadminbar' ).innerHTML.length ) ? document.getElementById( 'wpadminbar' ) : null;
		window.elToolbar 				= ( document.getElementById( 'responsi-toolbar' ) && document.getElementById( 'responsi-toolbar' ).innerHTML.length ) ? document.getElementById( 'responsi-toolbar' ) : null;
		window.elHeaderMain 			= ( document.getElementById( 'ih-layout' ) && document.getElementById( 'ih-layout' ).innerHTML.length ) ? document.getElementById( 'ih-layout' ) : null;
		window.elHeaderCtn 				= ( document.getElementById( 'ih-ctn' ) && document.getElementById( 'ih-ctn' ).innerHTML.length ) ? document.getElementById( 'ih-ctn' ) : null;

		window.elAboveHeaderCtn 		= ( document.getElementById( 'responsi-ahw-container' ) && document.getElementById( 'responsi-ahw-container' ).innerHTML.length ) ? document.getElementById( 'responsi-ahw-container' ) : null;

		window.elSticky 				= window.elHeaderMain.offsetTop;

		window.wpAdminbarHeight 		= ( window.elAdminbar != null ) && ( window.getComputedStyle(window.elAdminbar).getPropertyValue('position').toLowerCase() == 'fixed' ) ? window.elAdminbar.clientHeight : 0;
		window.wpAdminbarHeight2 		= ( window.elAdminbar != null ) && ( window.getComputedStyle(window.elAdminbar).getPropertyValue('position').toLowerCase() != 'fixed' ) ? window.elAdminbar.clientHeight : 0;
		window.responsiToolbarHeight 	= window.elToolbar != null ? window.elToolbar.clientHeight : 0;
		window.elAboveHeaderHeight 		= ( window.elAboveHeaderCtn != null ) && ( window.getComputedStyle(window.elAboveHeaderCtn).getPropertyValue('position').toLowerCase() == 'fixed' ) ? window.elAboveHeaderCtn.clientHeight : 0;
		window.elAboveHeaderHeight2 	= ( window.elAboveHeaderCtn != null ) && ( window.getComputedStyle(window.elAboveHeaderCtn).getPropertyValue('position').toLowerCase() != 'fixed' ) ? window.elAboveHeaderCtn.clientHeight : 0;

		
	});

})(jQuery);