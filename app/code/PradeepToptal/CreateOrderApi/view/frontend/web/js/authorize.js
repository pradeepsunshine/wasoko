require(
    [
        'jquery',
        'Magento_Ui/js/model/messageList',
        'Magento_Customer/js/customer-data',
        'mage/translate'
    ],
    function(
        $,
        messageList,
        customerData,
        $t
    ) {
         $('#auth-form').submit(function(event){
             event.preventDefault();
             var username = $('#auth-form #username').val();
             var password = $('#auth-form #password').val();
             var data = JSON.stringify({user:username,password:password});
             url = $('#auth-form').attr('action');
             $.ajax({
                 url: url,
                 showLoader: true,
                 type: 'POST',
                 dataType: "json",
                 contentType : "application/json",
                 data: data
             }).done(function (data) {
                 $('.tt-messages .tt-message .tt-success').html('Authorized successfully. Please wait...');
                 $('.tt-success').show();
                 setTimeout(
                     function()
                     {
                         $('.tt-success').hide();
                     }, 3000);
                 setTimeout(function(){},3000);
                 location.reload();
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
);
