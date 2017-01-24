// declare a new module, and inject the $compileProvider
angular.module('wall', ['ui.bootstrap', 'ngAnimate', 'angularSoundManager'], function($compileProvider) {
  // configure new 'compile' directive by passing a directive
  // factory function. The factory function injects the '$compile'
  $compileProvider.directive('compile', function($compile) {
    // directive factory creates a link function
    return function(scope, element, attrs) {
      scope.$watch(
        function(scope) {
           // watch the 'compile' expression for changes
          return scope.$eval(attrs.compile);
        },
        function(value) {
          // when the 'compile' expression changes
          // assign it into the current DOM
          element.html(value);

          // compile the new DOM and link it to the current
          // scope.
          // NOTE: we only compile .childNodes so that
          // we don't get into infinite loop compiling ourselves
          $compile(element.contents())(scope);
        }
      );
    };
  })


$compileProvider.directive('whenScrolled', function() {
    return function(scope, elm, attr) {
        var raw = elm[0];

        elm.bind('scroll', function() {
            if (raw.scrollTop + raw.offsetHeight >= raw.scrollHeight) {
                scope.$apply(attr.whenScrolled);
            }
        });
    };
})
 
$compileProvider.directive('angRoundProgress', [function () {
  var compilationFunction = function (templateElement, templateAttributes, transclude) {
    if (templateElement.length === 1) {
      var node = templateElement[0];

      var width = node.getAttribute('data-round-progress-width') || '120';
      var height = node.getAttribute('data-round-progress-height') || '120';

      var canvas = document.createElement('canvas');
      canvas.setAttribute('width', width);
      canvas.setAttribute('height', height);
      canvas.setAttribute('data-round-progress-model', node.getAttribute('data-round-progress-model'));

      node.parentNode.replaceChild(canvas, node);

      var outerCircleWidth = node.getAttribute('data-round-progress-outer-circle-width') || '10';
      var innerCircleWidth = node.getAttribute('data-round-progress-inner-circle-width') || '3';

      var outerCircleBackgroundColor = node.getAttribute('data-round-progress-outer-circle-background-color') || '#428BCA';
      var outerCircleForegroundColor = node.getAttribute('data-round-progress-outer-circle-foreground-color') || '#5BC0DE';
      var innerCircleColor = node.getAttribute('data-round-progress-inner-circle-color') || '#505769';
      var labelColor = node.getAttribute('data-round-progress-label-color') || '#FFFFFF';

      var outerCircleRadius = node.getAttribute('data-round-progress-outer-circle-radius') || '50';
      var innerCircleRadius = node.getAttribute('data-round-progress-inner-circle-radius') || '43';

      var labelFont = node.getAttribute('data-round-progress-label-font') || '30pt Calibri';

      return {
        pre: function preLink(scope, instanceElement, instanceAttributes, controller) {
          var expression = canvas.getAttribute('data-round-progress-model');
          scope.$watch(expression, function (newValue, oldValue) {
            // Create the content of the canvas
            var ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, width, height);

            // The "background" circle
            var x = width / 2;
            var y = height / 2;
            ctx.beginPath();
            ctx.arc(x, y, parseInt(outerCircleRadius), 0, Math.PI * 2, false);
            ctx.lineWidth = parseInt(outerCircleWidth);
            ctx.strokeStyle = outerCircleBackgroundColor;
            ctx.stroke();

            // The inner circle
            ctx.beginPath();
            ctx.arc(x, y, parseInt(innerCircleRadius), 0, Math.PI * 2, false);
            ctx.lineWidth = parseInt(innerCircleWidth);
            ctx.strokeStyle = innerCircleColor;
            ctx.stroke();

            // The inner number
			if(!newValue){
			newValue = {label:0};
			}
            ctx.font = labelFont;
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillStyle = labelColor;
            ctx.fillText(newValue.label, x, y);

            // The "foreground" circle
            var startAngle = - (Math.PI / 2);
            var endAngle = ((Math.PI * 2 ) * newValue.percentage) - (Math.PI / 2);
            var anticlockwise = false;
            ctx.beginPath();
            ctx.arc(x, y, parseInt(outerCircleRadius), startAngle, endAngle, anticlockwise);
            ctx.lineWidth = parseInt(outerCircleWidth);
            ctx.strokeStyle = outerCircleForegroundColor;
            ctx.stroke();
          }, true);
        },
        post: function postLink(scope, instanceElement, instanceAttributes, controller) {}
      };
    }
  };

  var roundProgress = {
    compile: compilationFunction,
    replace: true
  };
  return roundProgress;
}])
  
$compileProvider.directive('image', function($q) {
        'use strict'

        var URL = window.URL || window.webkitURL;

        var getResizeArea = function () {
            var resizeAreaId = 'fileupload-resize-area';

            var resizeArea = document.getElementById(resizeAreaId);

            if (!resizeArea) {
                resizeArea = document.createElement('canvas');
                resizeArea.id = resizeAreaId;
                resizeArea.style.visibility = 'hidden';
                document.body.appendChild(resizeArea);
            }

            return resizeArea;
        }

        var resizeImage = function (origImage, options) {
            var maxHeight = options.resizeMaxHeight || 300;
            var maxWidth = options.resizeMaxWidth || 250;
            var quality = options.resizeQuality || 0.7;
            var type = options.resizeType || 'image/jpg';

            var canvas = getResizeArea();

            var height = origImage.height;
            var width = origImage.width;

            // calculate the width and height, constraining the proportions
            if (width > height) {
                if (width > maxWidth) {
                    height = Math.round(height *= maxWidth / width);
                    width = maxWidth;
                }
            } else {
                if (height > maxHeight) {
                    width = Math.round(width *= maxHeight / height);
                    height = maxHeight;
                }
            }

            canvas.width = width;
            canvas.height = height;

            //draw image on canvas
            var ctx = canvas.getContext("2d");
            ctx.drawImage(origImage, 0, 0, width, height);

            // get the data from canvas as 70% jpg (or specified type).
            return canvas.toDataURL(type, quality);
        };

        var createImage = function(url, callback) {
            var image = new Image();
            image.onload = function() {
                callback(image);
            };
            image.src = url;
        };

        var fileToDataURL = function (file) {
            var deferred = $q.defer();
            var reader = new FileReader();
            reader.onload = function (e) {
                deferred.resolve(e.target.result);
            };
            reader.readAsDataURL(file);
            return deferred.promise;
        };


        return {
            restrict: 'A',
            scope: {
                image: '=',
                resizeMaxHeight: '@?',
                resizeMaxWidth: '@?',
                resizeQuality: '@?',
                resizeType: '@?',
            },
            link: function postLink(scope, element, attrs, ctrl) {

                var doResizing = function(imageResult, callback) {
                    createImage(imageResult.url, function(image) {
                        var dataURL = resizeImage(image, scope);
                        imageResult.resized = {
                            dataURL: dataURL,
                            type: dataURL.match(/:(.+\/.+);/)[1],
                        };
                        callback(imageResult);
                    });
                };

                var applyScope = function(imageResult) {
                    scope.$apply(function() {
                        //console.log(imageResult);
                        if(attrs.multiple)
                            scope.image.push(imageResult);
                        else
                            scope.image = imageResult; 
                    });
                };


                element.bind('change', function (evt) {
                    //when multiple always return an array of images
                    if(attrs.multiple)
                        scope.image = [];

                    var files = evt.target.files;
                    for(var i = 0; i < files.length; i++) {
                        //create a result object for each file in files
                        var imageResult = {
                            file: files[i],
                            url: URL.createObjectURL(files[i])
                        };

                        fileToDataURL(files[i]).then(function (dataURL) {
                            imageResult.dataURL = dataURL;
                        });

                        if(scope.resizeMaxHeight || scope.resizeMaxWidth) { //resize image
                            doResizing(imageResult, function(imageResult) {
                                applyScope(imageResult);
                            });
                        }
                        else { //no resizing
                            applyScope(imageResult);
                        }
                    }
                });
            }
        };
    });
 
 
 
$compileProvider.directive('imgCropped', function($http) {
  return {
    restrict: 'E',
    replace: true,
    templateUrl: 'coverForm.html',
    scope: { src:'@', id: '@?', user: '@?', selected:'&' },
    link: function(scope,element, attr) {
      
    scope.submitCover = function(){
     $http({
     method : "POST",
     url : "",
     data : "mode=updateCover&id="+scope.cover.id+"&top="+scope.cover.top+"&u="+scope.cover.user,
     }).success(function(data){
      $('.wall-banner-cancel').addClass('ng-hide');
      $('.coverUpdated').removeClass('ng-hide').attr('src', scope.src).css('margin-top', +data.top+'px');
	   clear();
     
     });
            }
					
	 scope.cancelCover = function(){
	  $('.coverUpdated').removeClass('ng-hide');
	  $('.wall-banner-cancel').addClass('ng-hide');
	  clear();
   }
            
      scope.cover = []; 
      var myImg;
      var clear = function() { 
	  $('.jwc_controls').addClass('ng-hide');
	  $('.wall-banner-send').addClass('ng-hide');
	  $('.wall-banner-edit').removeClass('ng-hide');
        if (myImg) {
          myImg.next().remove();
          myImg.remove();
          myImg = undefined; 
        } 
      }; 
      scope.$watch('src + lang', function(nv) {         
        if (nv) { 
		 $('.wall-banner-send').removeClass('ng-hide');
          element.after('<img />');
          myImg = element.next();         
          myImg.attr('src',nv);
		  var myLang = document.getElementById("cover-lang").value;
         $(myImg, myLang).jWindowCrop({
            showControlsOnStart: true,
            smartControls :true,  
		        dragText: myLang,
    		    targetWidth: '100%', //Cover division
    		    targetHeight: 250, //Height of cover division 
            onChange: function(result) { 
             if (result.cropY) {
            scope.$apply(function() {
            scope.cover = scope; 
            scope.cover.top = result.cropY; 
            });
            
               }
			}})
      
    }}) 
}}
  
});

 $compileProvider.directive('myCover', [function() {
        return function(scope, elm, attrs) { 
         scope.$watch(function() { return elm[0].offsetWidth; }, function(newValue, oldValue) {
			 var perc = ((oldValue - newValue)/100);
             scope.coverHeight = perc;
         }); 
   
         // even better would be just to grab the width inside this event
         // handler, but leaving it to match the original spirit of the question
         // regarding $watch.
         elm.on('load', function() { scope.$apply(); } );
        };
 }
]);



 $compileProvider.directive('lastRepeatEvent', function(){
	return function(scope,elem,attr,ctrl){
		if (scope.$last) setTimeout(function(){
               scope.$emit('repeatDone');
			
        }, 1);
	}
				  
});

 $compileProvider.directive('jrnGrid', function() {
  return {
    restrict: 'AE',
    link: {
        post: function(scope, elem,attr,ctrl){
			
				scope.$on('repeatDone', function( domainElement ) {
                var w = window.innerWidth+'px';
                var h = window.innerHeight-50+'px';

		        var superbox      = $('<div class="superbox-show"></div>'),
				superboximg   = $('<img src="" class="superbox-current-img" width="auto" style="height:auto; max-height:'+h+';">'),
				superboxclose = $('<i class="superbox-close glyphicon glyphicon-remove"></i>'),
				$item         = $('.superbox-list');


				superbox.append(superboximg).append(superboxclose);	

				$item.on('click', function(){

			var currentimg = $(this).find('.superbox-img');
			var imgData = currentimg.data('img');
				superboximg.attr('src', imgData);

					if($('.superbox-current-img').css('opacity') == 0) {
						$('.superbox-current-img').animate({opacity: 1});
					}

					if ($(this).next().hasClass('superbox-show')) {
						superbox.toggle();
					} else {
						superbox.insertAfter(this).css('display', 'block');
					}

					$('html, body').animate({
						scrollTop:superbox.position().top - currentimg.width()
					}, 'medium');


				});

				$('.superbox').on('click', '.superbox-close', function() {
					$('.superbox-current-img').animate({opacity: 0}, 200, function() {
						$('.superbox-show').slideUp();
					});
				});



			} );

		}
	}
    
  }
});  
 
 $compileProvider.directive('autoGrow', ['$timeout', '$window', function($timeout, $window){
 'use strict';

    return {
      require: '?ngModel',
      restrict: 'A, C',
      link: function(scope, element, attrs, ngModel) {

        // cache a reference to the DOM element
        var ta = element[0],
            $ta = element;

        // ensure the element is a textarea, and browser is capable
        if (ta.nodeName !== 'TEXTAREA' || !$window.getComputedStyle) {
          return;
        }

        // set these properties before measuring dimensions
        $ta.css({
          'overflow': 'hidden',
          'overflow-y': 'hidden',
          'word-wrap': 'break-word'
        });

        // force text reflow
        var text = ta.value;
        ta.value = '';
        ta.value = text;

        var appendText = attrs.msdElastic || '',
            append = appendText === '\\n' ? '\n' : appendText,
            $win = angular.element($window),
            mirrorStyle = 'position: absolute; top: -999px; right: auto; bottom: auto; left: 0 ;' +
                          'overflow: hidden; -webkit-box-sizing: content-box;' +
                          '-moz-box-sizing: content-box; box-sizing: content-box;' +
                          'min-height: 0 !important; height: 0 !important; padding: 0;' +
                          'word-wrap: break-word; border: 0;',
            $mirror = angular.element('<textarea tabindex="-1" ' +
                                      'style="' + mirrorStyle + '"/>').data('elastic', true),
            mirror = $mirror[0],
            taStyle = getComputedStyle(ta),
            resize = taStyle.getPropertyValue('resize'),
            borderBox = taStyle.getPropertyValue('box-sizing') === 'border-box' ||
                        taStyle.getPropertyValue('-moz-box-sizing') === 'border-box' ||
                        taStyle.getPropertyValue('-webkit-box-sizing') === 'border-box',
            boxOuter = !borderBox ? {width: 0, height: 0} : {
                          width: parseInt(taStyle.getPropertyValue('border-right-width'), 10) +
                                  parseInt(taStyle.getPropertyValue('padding-right'), 10) +
                                  parseInt(taStyle.getPropertyValue('padding-left'), 10) +
                                  parseInt(taStyle.getPropertyValue('border-left-width'), 10),
                          height: parseInt(taStyle.getPropertyValue('border-top-width'), 10) +
                                  parseInt(taStyle.getPropertyValue('padding-top'), 10) +
                                  parseInt(taStyle.getPropertyValue('padding-bottom'), 10) +
                                  parseInt(taStyle.getPropertyValue('border-bottom-width'), 10)
                        },
            minHeightValue = parseInt(taStyle.getPropertyValue('min-height'), 10),
            heightValue = parseInt(taStyle.getPropertyValue('height'), 10),
            minHeight = Math.max(minHeightValue, heightValue) - boxOuter.height,
            maxHeight = parseInt(taStyle.getPropertyValue('max-height'), 10),
            mirrored,
            active,
            copyStyle = ['font-family',
                         'font-size',
                         'font-weight',
                         'font-style',
                         'letter-spacing',
                         'line-height',
                         'text-transform',
                         'word-spacing',
                         'text-indent'];

        // exit if elastic already applied (or is the mirror element)
        if ($ta.data('elastic')) {
          return;
        }

        // Opera returns max-height of -1 if not set
        maxHeight = maxHeight && maxHeight > 0 ? maxHeight : 9e4;

        // append mirror to the DOM
        if (mirror.parentNode !== document.body) {
          angular.element(document.body).append(mirror);
        }

        // set resize and apply elastic
        $ta.css({
          'resize': (resize === 'none' || resize === 'vertical') ? 'none' : 'none'
        }).data('elastic', true);

        /*
* methods
*/

        function initMirror() {
          mirrored = ta;
          // copy the essential styles from the textarea to the mirror
          taStyle = getComputedStyle(ta);
          angular.forEach(copyStyle, function(val) {
            mirrorStyle += val + ':' + taStyle.getPropertyValue(val) + ';';
          });
          mirror.setAttribute('style', mirrorStyle);
        }

        function adjust() {
          var taHeight,
              mirrorHeight,
              width,
              overflow;

          if (mirrored !== ta) {
            initMirror();
          }

          // active flag prevents actions in function from calling adjust again
          if (!active) {
            active = true;

            mirror.value = ta.value + append; // optional whitespace to improve animation
            mirror.style.overflowY = ta.style.overflowY;

            taHeight = ta.style.height === '' ? 'auto' : parseInt(ta.style.height, 10);

            // update mirror width in case the textarea width has changed
            width = parseInt(getComputedStyle(ta).getPropertyValue('width'), 10) - boxOuter.width;
            mirror.style.width = width + 'px';

            mirrorHeight = mirror.scrollHeight;

            if (mirrorHeight > maxHeight) {
              mirrorHeight = maxHeight;
              overflow = 'scroll';
            } else if (mirrorHeight < minHeight) {
              mirrorHeight = minHeight;
            }
            mirrorHeight += boxOuter.height;

            ta.style.overflowY = overflow || 'hidden';

            if (taHeight !== mirrorHeight) {
              ta.style.height = mirrorHeight + 'px';
              scope.$emit('elastic:resize', $ta);
            }

            // small delay to prevent an infinite loop
            $timeout(function() {
              active = false;
            }, 1);

          }
        }

        function forceAdjust() {
          active = false;
          adjust();
        }

        /*
* initialise
*/

        // listen
        if ('onpropertychange' in ta && 'oninput' in ta) {
          // IE9
          ta['oninput'] = ta.onkeyup = adjust;
        } else {
          ta['oninput'] = adjust;
        }

        $win.bind('resize', forceAdjust);

        scope.$watch(function() {
          return ngModel.$modelValue;
        }, function(newValue) {
          forceAdjust();
        });

        scope.$on('elastic:adjust', function() {
          forceAdjust();
        });

        $timeout(adjust);

        /*
* destroy
*/

        scope.$on('$destroy', function() {
          $mirror.remove();
          $win.unbind('resize', forceAdjust);
        });
      }
    };

  }]);
})


.config(function($interpolateProvider) {
    $interpolateProvider.startSymbol('<[').endSymbol(']>');
  })

.filter('newlines', function () {
    return function(text) {
        return text.replace(/\n/g, '<br/>');
    }
})

.service('modalService', ['$modal',
    function ($modal) {

        var modalDefaults = {
            backdrop: true,
            keyboard: true,
            modalFade: true,
            templateUrl: 'myModalContent.html'
        };

        var modalOptions = {
            closeButtonText: 'Close',
            actionButtonText: 'OK',
            headerText: 'Proceed?',
            bodyText: 'Perform this action?'
        };

        this.showModal = function (customModalDefaults, customModalOptions) {
            if (!customModalDefaults) customModalDefaults = {};
            customModalDefaults.backdrop = 'static';
            return this.show(customModalDefaults, customModalOptions);
        };

        this.show = function (customModalDefaults, customModalOptions) {
            //Create temp objects to work with since we're in a singleton service
            var tempModalDefaults = {};
            var tempModalOptions = {};

            //Map angular-ui modal custom defaults to modal defaults defined in service
            angular.extend(tempModalDefaults, modalDefaults, customModalDefaults);

            //Map modal.html $scope custom properties to defaults defined in service
            angular.extend(tempModalOptions, modalOptions, customModalOptions);

            if (!tempModalDefaults.controller) {
                tempModalDefaults.controller = function ($scope, $modalInstance) {
                    $scope.modalOptions = tempModalOptions;
                    $scope.modalOptions.ok = function (result) {
                        $modalInstance.close('ok');
                    };
                    $scope.modalOptions.close = function (result) {
                        $modalInstance.close('cancel');
                    };
                }
            }

            return $modal.open(tempModalDefaults).result;
        };

    }]);
