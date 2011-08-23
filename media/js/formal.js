(function($) {

    $.fn.formal = function(options) {

        var settings = {
            // pane = add messages to a message pane
            // field = add formal-error class to form fields
            reportMethod: ['pane', 'field'],
            
            messagePane: 'formal-messages',
            paneErrorClass: 'formal-pane-error',
            paneSuccessClass: 'formal-pane-success',
            
            inputErrorClass: 'formal-field-error',
            inputValidatedClass: 'formal-field-validated',
            
            beforeSubmitCallback: null,
            
            afterSubmit: 'submit',
            afterSubmitCallback: null
        };

        $.extend(settings, options);

        settings.form = this;
        
        var FORMAL_STATE_OK = 0,
            FORMAL_STATE_NOT_INITIALIZED = 1,
            FORMAL_STATE_REGISTERED = 2,
            FORMAL_STATE_NOTICE = 3,
            FORMAL_STATE_WARNING = 4,
            FORMAL_STATE_ERROR = 5;
		
        var methods = {

            init: function() {
                $('div.'+settings.messagePane).hide();
                settings.form.append('<input type="hidden" name="formal_key" value="' + settings.form.attr('id') +'" />')

		settings.form.submit(function(ev) {
                    methods.validate();
                    ev.preventDefault();
                    return false;
                });
            },

            validate: function() {
                if(settings.beforeSubmitCallback && typeof(settings.beforeSubmitCallback) === 'function') {
                    var formData = settings.beforeSubmitCallback(settings.form);
                } else {
                    var formData = settings.form.serializeArray();
                }

                $.post(settings.form.attr('action'), formData, methods.report);
            },

            report: function(data) {
                // reset error reports
                methods.clearErrors();
                try {
                    var result = $.parseJSON(data);
                } catch (e) {
                    methods.reportPane('{"status": "error", "errors": ["Got an invalid response: [' + data + ']"]"}');
                }

                if(result.status == FORMAL_STATE_OK) {
                    if(settings.afterSubmit == 'report') {
                        methods.reportSuccess(result);
                    } else if(settings.afterSubmit == 'submit') {
                        $('<input>').attr({
                                type: 'hidden',
                                name: 'formal_validated',
                                value: 'true'
                            }).appendTo(settings.form);
                        settings.form.unbind('submit');
                        settings.form.submit();
                    } else if(settings.afterSubmit == 'callback') {
                        methods.clearErrors();
                        if(settings.afterSubmitCallback && typeof(settings.afterSubmitCallback) === 'function') {
                            settings.afterSubmitCallback(result);
                        } else {
                            alert('no valid callback provided...');
                        }
                    } else {
                        alert('everything ok, but i don\'t know what to do...');
                    }
                } else {
                    methods.clearErrors();
                    $.each(settings.reportMethod, function() {
                        if(this == 'pane') {
                            methods.reportPane(result);
                        } else if(this == 'field') {
                            methods.reportField(result);
                        } else {
                            alert('Unknown report method');
                        }
                    });
                }
            },

            clearErrors: function() {
                // remove field errors
                $(':input', settings.form).each(function() {
                    $(this).removeClass(settings.inputErrorClass).removeClass(settings.inputValidatedClass);
                });
                // reset errorDiv:
                $('div.'+settings.messagePane).html(' ');
                $('div.'+settings.messagePane).removeClass(settings.paneSuccessClass).removeClass(settings.paneErrorClass);
                $('div.'+settings.messagePane).hide();
            },

            reportField: function(data) {
                $.each(data.messages, function() {
                    if(this.field !== undefined) {
                        if(this.status == FORMAL_STATE_OK) {
                            $('input[name="'+this.field+'"]', settings.form).addClass(settings.inputValidatedClass);
                        } else {
                            $('input[name="'+this.field+'"]', settings.form).removeClass(settings.inputValidatedClass).addClass(settings.inputErrorClass);
                        }
                    }
                });
            },

            reportPane: function(data) {
                $('div.'+settings.messagePane).addClass(settings.paneErrorClass);
                $.each(data.messages, function(i) {
                    if(this.status != FORMAL_STATE_OK) {
                        mssg = '<span class="errorMsg">'+this.message+'</span><br />';
                        $('div.'+settings.messagePane).append(mssg);
                    }
                });
                $('div.'+settings.messagePane).show();
            },

            reportSuccess: function(result) {
                // reset errorDiv:
                methods.clearErrors();
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
                
                $('div.'+settings.messagePane).append(mssg);


                $('div.'+settings.messagePane).show();
            }

        };
        
        methods.init();

        return methods;
    };
})(jQuery);