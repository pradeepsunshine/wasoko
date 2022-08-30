define([
    'jquery',
    'mage/template'
], function ($, mageTemplate) {
    'use strict';
    return function (config, element) {
        $(element).click(function () {

            //Modify request data in case of form submission
            if (config.form_id) {
                var formData = $(config.form_id).serialize();
                requestData = JSON.stringify({
                    "param": formData
                });
            } else {
                var requestData = ""
            }
            $.ajax({
                url: config.url,
                showLoader: true,
                type: config.type,
                dataType: "json",
                contentType : "application/json",
                data: requestData
            }).done(function (data) {
                if(config.template) {
                    console.log(config);
                    var $element = $(element);
                    // setup the template
                    var template = mageTemplate(config.template);
                    // pass the data to the template
                    var templateHtml = template({
                        datalist: data
                    });
                    // inject the html to the selector
                    $(config.divclass).html(templateHtml);
                } else {
                    $('.tt-messages .tt-message .tt-success').html('Order placed successfully.');
                    $('.tt-success').show();
                    //Unselect all prods and customer
                    $('.uncheck').prop('checked', false);
                    setTimeout(
                        function()
                        {
                            $('.tt-success').hide();
                        }, 3000);
                }

            }).fail(function(response, exception) {
                $('.tt-messages .tt-message .tt-error').html(response.responseJSON.message);
                $('.tt-error').show();
                setTimeout(
                    function()
                    {
                        $('.tt-error').hide();
                    }, 3000);
            })

        })
    }
})

