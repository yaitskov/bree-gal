/**
 *  common code used in many places
 */

$.fn.find2 = function(selector) {
  return this.filter(selector).add(this.find(selector));
};

// ajax failed
function showErrorMessage(msg) {
    var msgDiv = $("<div class='errmsg'></div>");
    msgDiv.append(msg);
    msgDiv.click(function () {
                     $(this).remove();
                 });
    $('#errbox').append(msgDiv);
}

// popup dialog - fixed div
// callback is called (without arguments) if user confirmed
function confirmOperation(question, callback) {
    var qbox = $('.templates .questionbox').clone();
    qbox.find2('.questiontext').text(question);
    $('#errbox').append(qbox);
    qbox.find2('#btnyes').click(
        function (e) {
            e.preventDefault();
            qbox.remove();
            callback();
        }
    );
    qbox.find2('#btnno').click(
        function (e) {
            e.preventDefault();
            qbox.remove();
        }
    );
}

function fixCommentHeaderMore() {
    var l = $('#comments .comment:visible').length;
    if (l == 0) {
        $('#nocomments').hide();
        $('#onecomment').show();
    } else if (l == 1) {
        $('#onecomment').hide();
        $('#ncomments').show();
    }
    $('#ncomments .numx').text(1 + parseInt($('#ncomments .numx').text()));    
}

function fixCommentHeaderLow() {
    var l = $('#comments .comment:visible').length;
    if (l == 1) {
        $('#nocomments').show();
        $('#onecomment').hide();
    } else if (l == 2) {
        $('#onecomment').show();
        $('#ncomments').hide();
    }    
    $('#ncomments .numx').text(parseInt($('#ncomments .numx').text()) - 1);
}

// new chunks comment doms can appear dynamically 
function hookCommentHandlers(root) {
    var newDels = root.find2('a.delete');
    newDels.click(
        function (e) {
            e.preventDefault();
	    var th = $(this),
	    container = th.closest('div.comment'),
	    id=container.attr('id').slice(1);
            confirmOperation(
                'Are you sure you want to delete comment #'+id+'?',
                function () {
                    $.ajax({ url:th.attr('href') }).done(
                        function (data) {
                            if (data.error) {
                                showErrorMessage(data.error);
                            } else {
                                fixCommentHeaderLow();
                                container.slideUp();                                
                            }
                        });
                });
        });
}

function uploadFileDialog(requestUrl) {
    $.ajax({ url: requestUrl }).done(
        function (data) {
            if (data.error) {
                showErrorMessage(data.error);
            } else { // got form
                var form = $(data.body);
                form.submit(
                    function (e) {                        
                        return false;
                    });
                form.find2('.upload-button').click(
                    function () {
                        form.submit();
                    }
                );
                form.find2('.cancel-upload').click(
                    function () {
                        form.remove();                        
                    }
                );
                $('#errbox').append(form);                
            }
        });    
}

function reloadIfOk(data) {
    if (data.error) {
        showErrorMessage(data.error);
    } else {
        var pattern = $(this).data('replace-in');
        if (pattern) {
            var dom = $(pattern);
            if (dom) {
                dom.empty();
                var newDoc = $(data.body);
                initDocument(newDoc);
                dom.append(newDoc);                
            } else {
                console.error("failed to find dom by pattern '" + pattern + "'");
            }            
        } 
        var hide = $(this).data('hide');
        $(hide).each(function (i, node) {
                         node = $(node);
                         if (node.is('.modal')) {
                             node.modal('hide');
                         } else {
                             node.hide();
                         }
                     });

        var closest = $(this).data('hide-closest');
        if (closest) {
            $(this).closest(closest).hide();
        }
        var hideEmpty = $(this).data('hide-empty');
        var showEmpty = $(this).data('show-empty');
        if (hideEmpty || showEmpty) {
            if (closest == 'tr') {
                if ($(this).closest('table').find2('tr:not(.not-data-row):visible').length == 0) {
                    if (hideEmpty) {                        
                        $(hideEmpty).hide();
                    }
                    if (showEmpty) {
                        $(showEmpty).show();                        
                    }
                }
            }
        }
        if ($(this).data('reload-ok')) {
            window.location.reload();                    
        }
    }
}

// if user clicks on a link marked with ajax-reload class then
// ajax get request is sent to address in href attribute and the page is refreshed.
!function ($) {
    'use strict'; // jshint ;_;
    
    $(function () {
          $('body').on('click', 'a.ajax-reload',
                       function (e) {
                           var url = $(this).attr('href');
                           e.preventDefault();
                           $.get(url, function () { location.reload(); });
                       });
          $('.activable a').each(
              function (i, ref) {
                  if (location.pathname == $(ref).attr('href')) {
                      $(ref).addClass('active');
                  }
              });
      });
}(window.jQuery);


var AutoResizer = function (textArea, options) {
    var self = this;
 
    this.$textArea = $(textArea);
    this.minHeight = this.$textArea.height();
    
    this.options = $.extend({}, $.fn.autoResizer.defaults, options);
    
    this.$shadowArea = $('<div></div>').css({
                                                position: 'absolute',
                                                top: -10000,
                                                left: -10000,
                                                fontSize: this.$textArea.css('fontSize') || 'inherit',
                                                fontFamily: this.$textArea.css('fontFamily') || 'inherit',
                                                lineHeight: this.$textArea.css('lineHeight') || 'inherit',
                                                resize: 'none'
                                            }).appendTo(document.body);
 
    var startWidth = this.$textArea.width() || $(window).width();
    this.$shadowArea.width(startWidth);
    if (this.options.resizeOnChange) {
        function onChange() {
            window.setTimeout(function() {
                                  self.checkResize();
                              }, 0);
        }
        this.$textArea.change(onChange).keyup(onChange).keydown(onChange).focus(onChange);
    }
    
    this.checkResize();
};

AutoResizer.prototype = {
    constructor: AutoResizer,
    checkResize: function() {
        // No sense in auto-growing non-visible textarea, which height of 0 implies
        if (this.$textArea.height() === 0) {
            return;
        }
        // If this is first time we've seen text area rendered, remember the height
        if (this.minHeight === 0) {
            this.minHeight = this.$textArea.height();
        }
        // If the text area has changed in size past a certain threshold of difference
        // like when it becomes visible or viewport changes
        if (this.$textArea.width() !== 0 && Math.abs(this.$shadowArea.width() - this.$textArea.width()) > 20) {
            this.$shadowArea.width(this.$textArea.width());
        }
        
        var val = this.$textArea.val().replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/&/g, '&amp;')
            .replace(/\n/g, '<br/>&nbsp;');
        
        if ($.trim(val) === '') {
            val = 'a';
        }
        this.$shadowArea.html(val);
        var nextHeight = Math.max(this.$shadowArea[0].offsetHeight + 10, this.minHeight);
        if (!this.prevHeight || nextHeight != this.prevHeight) {
            this.$textArea.css('height', nextHeight);
            this.prevHeight = nextHeight;
        }
    }
};

 
$.fn.autoResizer = function ( option ) {
    return this.each(function () {
                         var $this = $(this);
                         var data = $this.data('autoresizer');
                         var options = typeof option == 'object' && option;
                         if (!data) $this.data('autoresizer', (data = new AutoResizer(this, options)));
                         if (typeof option == 'string') data[option]();
                         else data.checkResize();
                     });
};
 
$.fn.autoResizer.defaults = {
    resizeOnChange: true
};
 
$.fn.autoResizer.Constructor = AutoResizer;

function importJs(url) {
    $.ajax({
               url: "/js/" + url + ".js",
               dataType: 'script',
               success: function (){},
               async: false
           });
};

(function() {
     var ev = new $.Event('remove');
     var oldhide = $.fn.hide;
     $.fn.hide = function(speed, callback) {
         $(this).trigger('hide');
         return oldhide.apply(this, arguments);
     };
     var orig = $.fn.remove;
     $.fn.remove = function() {
         $(this).trigger(ev);
         return orig.apply(this, arguments);
     };
 })();

var hookRemoveComment = function (matcher) {
    matcher.click(
        function (e) {
            e.preventDefault();
            var obj = $(this);
            $.ajax({ url: obj.attr('href')}).done(
                function (data) {
                    if (data.error) {
                        showErrorMessage(data.error);
                    } else {
                        $(obj.data('slideup')).slideUp();
                    }
                });
        });    
};

var hideCommentHook = function (e) {
    $.ajax({ url: '/comment/title',
             data: { id:  $('#comments').data('id'),
                     type: $('#comments').data('type') }
           }).done(
               function (data) {
                   if (data.error) {
                       showErrorMessage(data.error);
                   } else {
                       $('#comments h4').text(data.header);
                   }
               });
};

function packAjaxParameters(form) {
    form.find('.dereference').each(
        function (i, input) {
            input = $(input);
            var httpParamRef = input.data("http-param-ref");
            if (httpParamRef) {
                input.val(buildHttpParamValue(httpParamRef, input));
            }
        });
    var type ='POST';
    if (form.attr('method')) {
        type = form.attr('method');
    }
    if (form.attr('enctype') === 'multipart/form-data') {
        var formData = new FormData();
        form.find('input, textarea, select').each(
            function (i, input) {
                if (input.type === 'file') {
                    if (input.files) {
                        formData.append(input.name, input.files[0]);                        
                    }
                } else {
                    formData.append(input.name, input.value);
                }
            }
        );
        return {
            url: form.attr('action'),
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            type: type
        };
    } else {
        return { url: form.attr('action'),
                 type: type,
                 data: form.serialize() };
    }
}

function stub(a, b, c) {
    console.log("stub");
    return a;
}

var dynamicJsInitHooks = [];


function loadJsFile(files, continuation) {
    if (!files || !files.length) {
        continuation();
        return;
    }
    var loaderNode = document.createElement("script");
    loaderNode.setAttribute("type", "text/javascript");
    loaderNode.setAttribute("src", files.shift());
    loaderNode.onload = function (event) {
        loadJsFile(files, continuation);
    };
    document.getElementsByTagName("head")[0].appendChild(loaderNode);
}

function loadJsFiles(fileNodes, continuation) {
    var files = [];
    fileNodes.each(function (i, node) {                       
                       var node = $(node);
                       if (node.data('js-files')) {
                           files = files.concat(node.data('js-files').split(" ").filter(
                                                    function (elem) { return elem; }).map(
                                                    function (elem) { return "/js/" + elem + ".js" ; }));
                       }
                   });
    $("head script").each(function (i, script) {
                              script = $(script);
                              var index = files.indexOf(script.attr('src'));
                              if (index >= 0) {
                                  files.splice(index, 1);
                              }
                          });
    loadJsFile(files, continuation);
}

function buildHttpParamValue(httpParamRef, input) {
    var join = input.data("http-param-join");
    if (join) {
        var list = [];
        $(httpParamRef).each(function (i, element) {
                                 list.push($(element).text().trim());
                             });
        return list.join(join);
    } else {
        return $(httpParamRef).text().trim();
    }        
}

function completeUrl(a) {    
    var url = a.attr('href');
    var httpParamRef = a.attr('data-http-param-ref');
    var httpParamName = a.attr('data-http-param-name');                
    if (httpParamName && httpParamRef) {
        var paramVal = buildHttpParamValue(httpParamRef, a);
        var httpParam = httpParamName + "=" + window.encodeURIComponent(paramVal);
        if (url.indexOf("?") < 0) {                       
            url += "?" + httpParam;
        } else {
            url += "&" + httpParam;
        }
    }
    return url;
}

function initDocument(root) {
    var continuation = function () { 
        for (var ihook in dynamicJsInitHooks) {
            dynamicJsInitHooks[ihook](root);
        }
        // bootstract doesnt init dom loaded from ajax
        root.find2('.modal').each(
            function (i, item) {
                item = $(item);
                if (!item.data("modal")) {
                    item.modal('hide');
                }
                item.on('shown', function () { 
                            $(this).find("[autofocus=true]").focus(); 
                        });
                // with ($(item).data("modal").options) {
                //     backdrop = 'static';
                //     keyboard = false;
                // }
            });
        root.find2('.create-comment').submit(
            function () {
                $.ajax({
                           url: $(this).attr('action'),
                           type: 'POST',
                           data: $(this).serialize()                           
                       }).done(function (data) {
                                   if (data.error) {
                                       $(this).find('.alert-error').show().empty().append(err.responseText);
                                   } else {
                                       $(this).find('.alert-error').hide().empty();
                                       var comment = $(data.body);
                                       initDocument(comment);
                                       $("#comments").append(comment);
                                   }
                               }).fail(function (err) {
                                           $(this).find('.alert-error').show().empty().append(err.responseText);
                                       });
                return false;
            });

        // clear to prevent accumulation old handlers
        //dynamicJsInitHooks.splice(0, dynamicJsInitHooks.length);
        root.find2('.submit-form').click(
            function (e) {
                e.preventDefault();
                if ($(this).data('disable-link') == true) {
                    $($(this).data('disable-message')).show();
                    return;                    
                }
                var ref = $(this).data('form-id');
                if (ref) {
                    var form = $(ref);
                    form.attr('action', $(this).attr('href'));
                    form.submit();
                }
            });
        
        root.find2('.ajax-validation').submit(
            function () {
                var iam = $(this);
                var showIfOk = iam.data('show-if-ok');
                var hideIfOk = iam.data('hide-if-ok');
                var appendTo = iam.data('append-to');
                var scrollTo = iam.data('scroll-to');
                var errorOutput = iam.data('error-output');
                var replaceDomInPattern = iam.data('replace-in'); 
                var parent = null;
                if (replaceDomInPattern) {
                    parent = $(replaceDomInPattern);
                    if (!parent) {
                        console.error("replace-in patter '" + replaceDomInPattern + "' is not found");
                    }
                } else {
                    parent = iam.parent();                
                }
                $.ajax(packAjaxParameters(iam)).done(
                    function (data, a, bb) {// TODO: check http status
                        if (data.toBeAppended && appendTo) {                           
                            var toApp = $(data.toBeAppended);
                            initDocument(toApp);
                            $(appendTo).append(toApp);
                        }
                        var toBeRemoved = parent.children();
                        if (data.error) {
                            var forErrorOutput = $(errorOutput);
                            if (forErrorOutput) {
                                toBeRemoved = forErrorOutput.children();
                                parent = forErrorOutput;                
                                parent.show();
                            }
                            var b = $(data.error);
                            initDocument(b);
                            parent.append(b);    
                            toBeRemoved.remove();                       
                        } else {
                            var url = iam.data('ok-target');
                            if (url) {
                                location.href = url;
                            } else {
                                if (iam.data('not-reload')) {
                                    $(showIfOk).show();
                                    var node = $(hideIfOk);
                                    if (node.is('.modal')) {
                                        node.modal('hide');
                                    } else {
                                        node.hide();
                                    }                                    
                                    var b = $(data.body);
                                    initDocument(b);
                                    parent.append(b);
                                    toBeRemoved.remove();
                                    if (scrollTo) {
                                        $('html').animate({
                                                              scrollTop: $(scrollTo).offset().top
                                                          }, 2000);
                                    }
                                } else {
                                    location.reload();
                                }
                            }
                        }
                    }).error(function (a,b,c) { 
                                 var forErrorOutput = $(errorOutput);
                                 if (forErrorOutput) {
                                     parent = forErrorOutput;
                                     parent.show();
                                 }
                                 parent.empty(); 
                                 parent.append(a.responseText); 
                             });
                return false;
            });
        root.find2('#comments textarea').autoResizer();
        root.find2('.form-horizontal textarea').autoResizer();        
        hookRemoveComment(root.find2('a.ajax-remove'));
        root.find2('#comments .comment').on('hide', hideCommentHook);

        root.find2('.ajax-tab').click(
            function () {
                var tar = $($(this).data('target'));
                tar.load($(this).attr('href'), '',
                         function (data, a, b) {
                             if (b.status != 200) {
                                 showErrorMessage("Http Error: " + b.statusText + ". Code: " + b.status);
                                 tar.html(b.responseText);
                             } else {
                                 initDocument(tar);
                             }
                         });
            });
        root.find2('#comments form').submit(
            function (e) {
                $.ajax({ url: $(this).attr('action'), type: 'POST', data: $(this).serialize()}).done(
                    function (data) {
                        if (data.error) {
                            showErrorMessage(data.error);
                        } else {
                            $('#comments h4').text(data.header);
                            $('#comments .comment-bodies').append(data.body);
                            hookRemoveComment($('#comments .comment:last .ajax-remove'));
                            $('#comments .comment:last').on('hide', hideCommentHook);
                            $('#comments textarea').val('');
                        }
                    });
                return false;
            });
        root.find2('.set-location').click(
            function (e) {
                var iam = $(this);
                e.preventDefault();                
                window.location = completeUrl(iam);
            }
        );
        root.find2('.ajax-link').click(
            function (e) {
                var iam = $(this);
                e.preventDefault();                
                $.ajax({
                           url: completeUrl(iam)
                       }).done(
                           function (data) { // TODO: check http status
                               reloadIfOk.call(iam, data);
                           }
                       ).fail(function (a,b,c) {
                                  showErrorMessage(a.responseText);
                              });
            }
        );
    };
    var files = root.find(".include-js");
    loadJsFiles(files, continuation);

};

function getUserTimeZone() {
    return new Date().getTimezoneOffset()*-1;
}

function loadPhoto(iam) {
    $.ajax({
               url: iam.attr('href')
           }).done(
               function (data) {
                   if (data.error) {
                       showErrorMessage(data.error);
                   } else {
                       $('.prj-galery li a').removeClass('current');
                       iam.addClass('current');
                       var body = $(data.body);
                       initDocument(body);
                       $("#photo-view .modal-body").empty().append(body);
                       $("#photo-view").modal('show');

                       $('#photo-view .modal-body .big-image').click(
                           function (e) {
                               e.preventDefault();
                               var next = $('.current').closest('li').next().find('.view-this-photo');
                               if (!next.length) {
                                   next = $('.prj-galery li:first-child .view-this-photo');
                               }
                               loadPhoto(next);
                           });                               
                   }
               }).fail(
                   function (err) {
                       showErrorMessage(err.responseText);
                   });            
}

$(document).ready(
    function () {
        initDocument($(document));                
    });



