(function($){
    var Formal = function(element, options) {
        
        //settings
        var settings = $.extend({
            reportMethod: ['pane', 'field'],
            messagePane: 'formal-messages',
            paneErrorClass: 'formal-pane-error',
            paneSuccessClass: 'formal-pane-success',
            inputErrorClass: 'formal-field-error',
            inputValidatedClass: 'formal-field-validated',
            beforeSubmitCallback: null,
            afterSubmit: 'submit',
            afterSubmitCallback: null,
            postData: {}
        }, options || {});
        
        // private vars
        var domElem = $(element);
        var postData = {}; // custom post data
        
        // constants (sort of...)
        var FORMAL_STATE_OK = 0,
            FORMAL_STATE_NOT_INITIALIZED = 1,
            FORMAL_STATE_REGISTERED = 2,
            FORMAL_STATE_NOTICE = 3,
            FORMAL_STATE_WARNING = 4,
            FORMAL_STATE_ERROR = 5;
        
        // initialize plugin
        (function() {
            $('div.'+settings.messagePane).hide();
            $.extend(settings.postData, {'formal_key': domElem.attr('id')});
            
            domElem.submit(function(e) {
                postForm();
                e.preventDefault();
                return false;
            });
        })(this);
        
        
        ////////////////////////////////////////////////////////////////////////
        var postForm = function() {
            if(settings.beforeSubmitCallback && typeof(settings.beforeSubmitCallback) === 'function') {
                // by extending postData with the output of beforeSubmitCallback
                // you can send whatever you want to send to the server
                $.extend(settings.postData, settings.beforeSubmitCallback(domElem, settings.postData));
            }
            
            // prepare postdata
            var pd = [];
            $.each(settings.postData, function(n, v) {
                pd.push({name: n, value: v});
            });
            $.extend(pd, domElem.serializeArray());
            //console.debug(pd);
            
            $.post(domElem.attr('action'), pd, validate);
        };
        
        var validate = function(data) {
            // reset error reports
            clearErrors();
            
            try {
                var result = $.parseJSON(data);
            } catch (e) {
                reportPane('{"status": "error", "errors": ["Got an invalid response: [' + data + ']"]"}');
            }

            if(result.status == FORMAL_STATE_OK) {
                if(settings.afterSubmit == 'report') {
                    reportSuccess(result);
                } else if(settings.afterSubmit == 'submit') {
                    domElem.unbind('submit');
                    domElem.submit();
                } else if(settings.afterSubmit == 'callback') {
                    if(settings.afterSubmitCallback && typeof(settings.afterSubmitCallback) === 'function') {
                        settings.afterSubmitCallback(result);
                    } else {
                        alert('no valid callback provided...');
                    }
                } else {
                    alert('everything ok, but i don\'t know what to do...');
                }
            } else {
                $.each(settings.reportMethod, function() {
                    if(this == 'pane') {
                        reportPane(result);
                    } else if(this == 'field') {
                        reportField(result);
                    } else {
                        alert('Unknown report method');
                    }
                });
            }
        };
        
        var clearErrors = function() {
            // remove field errors
            $(':input', domElem).each(function() {
                $(this).removeClass(settings.inputErrorClass).removeClass(settings.inputValidatedClass);
            });
            // reset errorDiv:
            $('div.'+settings.messagePane).html(' ');
            $('div.'+settings.messagePane).removeClass(settings.paneSuccessClass).removeClass(settings.paneErrorClass);
            $('div.'+settings.messagePane).hide();
        };
        
        var reportField =  function(data) {
            $.each(data.messages, function() {
                if(this.field !== undefined) {
                    if(this.status == FORMAL_STATE_OK) {
                        $('input[name="'+this.field+'"]', domElem).addClass(settings.inputValidatedClass);
                    } else {
                        $('input[name="'+this.field+'"]', domElem).removeClass(settings.inputValidatedClass).addClass(settings.inputErrorClass);
                    }
                }
            });
        };

        var reportPane =  function(data) {
            $('div.'+settings.messagePane).addClass(settings.paneErrorClass);
            $.each(data.messages, function(i) {
                if(this.status != FORMAL_STATE_OK) {
                    mssg = '<span class="errorMsg">'+this.message+'</span><br />';
                    $('div.'+settings.messagePane).append(mssg);
                }
            });
            $('div.'+settings.messagePane).show();
        };

        var reportSuccess = function(result) {
            // reset errorDiv:
            clearErrors();
            $('div.'+settings.messagePane).addClass(settings.paneSuccessClass);

            mssg = '';
            $(result.messages).each(function() {
                if(this.message != '') {
                    mssg = mssg + this.message + '<br />';
                }
            });

            if(mssg == '') {
                mssg = '<span>The form has been succesfully submitted</span><br />';
            }

            $('div.'+settings.messagePane).append(mssg).show();
        };
    }
    
    $.fn.formal = function(settings) {
        return this.each(function() {
            var element = $(this);
            if(element.data('formal')) return;
            
            var formal =  new Formal(this, settings);
            
            element.data('formal', formal);
        });
    }
})(jQuery);