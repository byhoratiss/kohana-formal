(function($) {

    $.fn.formal = function(options) {

        var settings = {
            // pane = add messages to a message pane
            // highlight = add formal-error class to form fields
            reportMethod: ['pane', 'highlight'],
            messagePane: 'formal-messages',
            paneSuccessClass: 'formal-success',
            paneErrorClass: 'formal-error',
            highlightClass: 'formal-highlight',
            // should form be submitted anyway, should the data be passed back to a callback or simply report?
            // values: submit, callback or report
            afterSubmit: 'submit',
            beforeSubmit: null,
            submitCallback: 'report'
        };

        $.extend(settings, options);

        settings.form = this;
		
        var methods = {

            init: function() {
                $('div.'+settings.messagePane).hide();

		settings.form.submit(function(ev) {
                    if(settings.beforeSubmit && typeof(settings.beforeSubmit) === 'function') {
                        settings.beforeSubmit(settings.form);
                    }
                    methods.validate();
                    ev.preventDefault();
                    return false;
                });
            },

            validate: function() {
                var formData = settings.form.serializeArray();
                $.post(settings.form.attr('action'), formData, methods.report);
            },

            report: function(data) {
                // reset error reports
                methods.clearErrors();
                try {
                    var result = $.parseJSON(data);
                } catch (e) {
                    methods.reportPane('{status: "error", errors: ["Got an invalid response: [' + data + ']"]"}');
                }

                if(result.status == 'ok') {
                    if(settings.afterSubmit == 'report') {
                        methods.reportSuccess(result);
                    } else if(settings.afterSubmit == 'submit') {
                        $('<input>').attr({
                                type: 'hidden',
                                name: 'formal_validated',
                                value: 'true'
                            }).appendTo(settings.form);
                        settings.form.unbind('submit');
                        //settings.form.submit(function() { alert('bl@@oe'); return true; });
                        settings.form.submit();
                    } else if(settings.afterSubmit == 'callback') {
                        methods.clearErrors();
                        if(settings.submitCallback && typeof(settings.submitCallback) === 'function') {
                            settings.submitCallback(result.custom_data);
                        } else {
                            alert('no valid callback provided...');
                        }
                    } else {
                        alert('everything ok, but i don\'t know what to do...');
                    }
                } else {
                    $.each(settings.reportMethod, function() {
                        if(this == 'pane') {
                            methods.reportPane(result);
                        } else if(this == 'highlight') {
                            methods.reportHighlight(result);
                        } else {
                            alert('Unknown report method');
                        }
                    });
                }
            },

            clearErrors: function() {
                // remove highlight errors
                $(':input', settings.form).each(function() {
                    $(this).removeClass(settings.highlightClass);
                });
                // reset errorDiv:
                $('div.'+settings.messagePane).html(' ');
                $('div.'+settings.messagePane).removeClass(settings.paneSuccessClass).addClass(settings.paneErrorClass);
            },

            reportHighlight: function(data) {
                // reset error highlights
                $(':input', settings.form).each(function() {
                    $(this).removeClass(settings.highlightClass);
                });

                $.each(data.errors, function(field) {
                    $('input[name="'+field+'"]', settings.form).addClass(settings.highlightClass);
                });
            },

            reportPane: function(data) {
                // reset errorDiv:
                $('div.'+settings.messagePane).html(' ');
                $('div.'+settings.messagePane).removeClass(settings.paneSuccessClass).addClass(settings.paneErrorClass);
                $.each(data.errors, function(i) {
                    var mssg = '';

                    $.each(this.messages, function(k) {
                        mssg = mssg + '<span class="errorMsg">'+this+'</span><br />';
                    });

                    $('div.'+settings.messagePane).append(mssg);
                });
                $.each(data.messages, function(i) {
                    mssg = '<span class="errorMsg">'+this+'</span><br />';
                    $('div.'+settings.messagePane).append(mssg);
                });
                $('div.'+settings.messagePane).show();
            },

            reportSuccess: function(data) {
                // reset errorDiv:
                $('div.'+settings.messagePane).html(' ').removeClass(settings.paneErrorClass).addClass(settings.paneSuccessClass);
                var mssg = '';
                $.each(data.messages, function(i) {
                    mssg = mssg + '<span>'+this+'</span><br />';
                });
                $('div.'+settings.messagePane).append(mssg);


                $('div.'+settings.messagePane).show();
            }

        };

        methods.init();

        return methods;
    };
})(jQuery);