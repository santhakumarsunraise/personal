jQuery(document).on('click', 'button#send-query', function() {
    jQuery.ajax({
        type:'POST',
        url:baseurl+'contact/sendData',
        data:jQuery("form#ajax-contact-frm").serialize(),
        dataType:'json',    
        beforeSend: function () {
            jQuery('button#send-query').button('loading');
        },
        complete: function () {
            jQuery('button#send-query').button('reset');
            jQuery("form#ajax-contact-frm").find('textarea, input').each(function () {
                jQuery(this).val('');
            });
            setTimeout(function () {
                jQuery('span#success-msg').html('');
            }, 4000);
        },                
        success: function (json) {           
           $('.text-danger').remove();
            if (json['error']) {
             
                for (i in json['error']) {

                  var element = $('.input-acf-' + i.replace('_', '-'));
                  if ($(element).parent().hasClass('input-group')) {
                       
                    $(element).parent().after('<small class="text-danger">' + json['error'][i] + '</small>');
                  } else {
                    $(element).after('<small class="text-danger">' + json['error'][i] + '</small>');
                  }
                }
            } else {
                jQuery('span#success-msg').html('<div class="alert alert-success" role="alert">Your query has been successfully submitted.</div>');
            }                       
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }        
    });
});