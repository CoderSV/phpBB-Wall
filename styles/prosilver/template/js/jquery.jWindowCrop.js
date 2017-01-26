/*
 * jWindowCrop v1.0.0
 *
 * Copyright (c) 2012 Tyler Brown
 * Licensed under the MIT license.
 *
 */

(function($){
	function fillContainer(val, targetLength, containerLength) { // ensure that no gaps are between target's edges and container's edges
		if(val + targetLength < containerLength) val = containerLength-targetLength;
		if(val > 0) val = 0;
		return val;
	}

	$.jWindowCrop = function(image, options){
		var base = this;
		base.$image = $(image); // target image jquery element
		base.image = image; // target image dom element
		base.$image.data("jWindowCrop", base); // target frame jquery element

		base.namespace = 'jWindowCrop';
		base.originalWidth = 0;
		base.isDragging = false;
		
		base.init = function(){
			base.$image.css({display:'none'}); // hide image until loaded
			base.options = $.extend({},$.jWindowCrop.defaultOptions, options);
			if(base.options.zoomSteps < 2) base.options.zoomSteps = 2;

			base.$image.addClass('jwc_image').wrap('<div class="jwc_frame" />'); // wrap image in frame
			base.$frame = base.$image.parent();
			base.$frame.append('<div class="jwc_loader">' + base.options.loadingText + '</div>');
			base.$frame.append('<div class="jwc_controls" style="position:relative;display:'+(base.options.showControlsOnStart ? 'block' : 'none')+';"><span class="dragger">'+ base.options.dragText +' </span></div>');
			base.$frame.css({'overflow': 'hidden', 'position': 'relative', 'width': base.options.targetWidth, 'height': base.options.targetHeight});
			base.$image.css({'position': 'absolute', 'margin-top': '0px', 'left': '0px'});
			initializeDimensions();

			base.$frame.find('.jwc_zoom_in').on('click.'+base.namespace, base.zoomIn);
			base.$frame.find('.jwc_zoom_out').on('click.'+base.namespace, base.zoomOut);
			base.$frame.on('mouseenter.'+base.namespace, handleMouseEnter);
			base.$frame.on('mouseleave.'+base.namespace, handleMouseLeave);
			base.$image.on('load.'+base.namespace, handeImageLoad);
			base.$image.on('mousedown.'+base.namespace, handleMouseDown);
			$(document).on('mousemove.'+base.namespace, handleMouseMove);
			$(document).on('mouseup.'+base.namespace, handleMouseUp);
		};

		base.destroy = function() {
			base.$image.removeData("jWindowCrop"); // remove data
			$(document).unbind('mousemove.'+base.namespace+' touchmove.'+base.namespace); // remove body binds
                        $(document).unbind('mouseup.'+base.namespace+' touchend.'+base.namespace); // remove body binds
			base.$image.unbind(); // remove image binds
			base.$frame.unbind(); // remove frame binds
			base.$frame.find('.jwc_zoom_out').unbind(); // remove zoom triggers
			base.$frame.find('.jwc_zoom_in').unbind();  // remove zoom triggers
			$('.jwc_loader').remove();   // remove the added text
			$('.jwc_controls').remove(); // remove the added controls
			base.$image.removeAttr( 'style' ); // undo the style
			base.$image.unwrap(); // undo the wrap
		};
		
		base.setZoom = function(percent) {
			if(base.minPercent >= 1) {
				percent = base.minPercent;
			} else if(percent > 1.0) {
				percent = 1;
			} else if(percent < base.minPercent) {
				percent = base.minPercent;	
			}
			base.$image.width(Math.ceil(base.originalWidth*percent));
			base.workingPercent = percent;
		//	focusOnCenter(); //Don't focus the image on center.Keep it at top instead
			updateResult();
		};
		base.zoomIn = function() {
			var zoomIncrement = (1.0 - base.minPercent) / (base.options.zoomSteps-1);
			base.setZoom(base.workingPercent+zoomIncrement);
			return false;
		};
		base.zoomOut = function() {
			var zoomIncrement = (1.0 - base.minPercent) / (base.options.zoomSteps-1);
			base.setZoom(base.workingPercent-zoomIncrement);
			return false;
		};

		function initializeDimensions() {
			if(base.originalWidth == 0) {
				base.originalWidth = base.$image.width();
				base.originalHeight = base.$image.height();
			}
			if(base.originalWidth > 0) {
				var widthRatio = base.options.targetWidth / base.originalWidth;
				var heightRatio = base.options.targetHeight / base.originalHeight;
				//base.minPercent = (widthRatio >= heightRatio) ? widthRatio : heightRatio;
				if(widthRatio >= heightRatio) {
					base.minPercent = (base.originalWidth < base.options.targetWidth) ? (base.options.targetWidth / base.originalWidth) : widthRatio;
				} else {
					base.minPercent = (base.originalHeight < base.options.targetHeight) ? (base.options.targetHeight / base.originalHeight) : heightRatio;
				}
				base.focalPoint = {'x': Math.round(base.originalWidth/2), 'y': Math.round(base.originalHeight/2)};
				base.setZoom(base.minPercent);
				base.$image.fadeIn('fast'); //display image now that it has loaded
			}
		}
		function storeFocalPoint() {
			var x = (parseInt(base.$image.css('left'))*-1 + base.options.targetWidth/2) / base.workingPercent;
			var y = (parseInt(base.$image.css('margin-top'))*-1 + base.options.targetHeight/2) / base.workingPercent;
			base.focalPoint = {'x': Math.round(x), 'y': Math.round(y)};
		}
		function focusOnCenter() {
			var left = fillContainer((Math.round((base.focalPoint.x*base.workingPercent) - base.options.targetWidth/2)*-1), base.$image.width(), base.options.targetWidth);
			var top = fillContainer((Math.round((base.focalPoint.y*base.workingPercent) - base.options.targetHeight/2)*-1), base.$image.height(), base.options.targetHeight);
			base.$image.css({'margin-top': (top.toString()+'px')})
			storeFocalPoint();
		}
		function updateResult() {
			var top = fillContainer((Math.round((base.focalPoint.y*base.workingPercent) - base.options.targetHeight/2)*-1), base.$image.height(), base.options.targetHeight);
			base.result = {
				cropX: Math.floor(parseInt(base.$image.css('left'))/base.workingPercent*-1),
				cropY: top.toString(),
				cropW: Math.round(base.options.targetWidth/base.workingPercent),
				cropH: Math.round(base.options.targetHeight/base.workingPercent),
				mustStretch: (base.minPercent > 1)
			};
			base.options.onChange.call(base.image, base.result);
		}
		function handeImageLoad() {
			initializeDimensions();
		}
		function handleMouseDown(event) {
			event.preventDefault(); //some browsers do image dragging themselves
			base.isDragging = true;
			base.dragMouseCoords = { y: event.pageY};
			base.dragImageCoords = {y: parseInt(base.$image.css('margin-top'))}
		}
		function handleMouseUp() {
			base.isDragging = false;
		}
		function handleMouseMove(event) {
			if(base.isDragging) {
				var xDif = event.pageX - base.dragMouseCoords.x;
				var yDif = event.pageY - base.dragMouseCoords.y;
				var newTop = fillContainer((base.dragImageCoords.y + yDif), base.$image.height(), base.options.targetHeight);
				base.$image.css({ 'margin-top' : (newTop.toString()+'px')});
				storeFocalPoint();
				updateResult();
			}
		}
		function handleMouseEnter() {
			if(base.options.smartControls) base.$frame.find('.jwc_controls').fadeIn('fast');
		}
		function handleMouseLeave() {
			if(base.options.smartControls) base.$frame.find('.jwc_controls').fadeOut('fast');
		}
		
		base.init();
	};
	
	$.jWindowCrop.defaultOptions = {
		targetWidth: 320,
		targetHeight: 180,
		zoomSteps: 10,
		loadingText: 'Loading...',
		dragText: 'Drag to reposition',
		smartControls: true,
		showControlsOnStart: true,
		onChange: function() {}
	};
	
	$.fn.jWindowCrop = function(options){
		return this.each(function(){
			(new $.jWindowCrop(this, options));
		});
	};
	
	$.fn.getjWindowCrop = function(){
		return this.data("jWindowCrop");
	};
})(jQuery);

