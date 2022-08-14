$(document).ready(function() {
    $.getScript('files/js/javascript.js');
    
    $(document).on('click','.load-defaul-logo', function () {  $('.input-defaul-logo').click(); });
    $(document).on('change','.input-defaul-logo', function () {    
        fileReader(this, ".load-defaul-logo", "150px");
    });

    $(document).on('click','.load-mobile-logo', function () {  $('.input-mobile-logo').click(); });
    $(document).on('change','.input-mobile-logo', function () {    
        fileReader(this, ".load-mobile-logo", "32px");
    });

    $(document).on('click','.load-favicon', function () {  $('.input-favicon').click(); });
    $(document).on('change','.input-favicon', function () {    
        fileReader(this, ".load-favicon", "32px");
    });

    $(document).on('click','.load-pwa', function () {  $('.input-pwa').click(); });
    $(document).on('change','.input-pwa', function () {    
        fileReader(this, ".load-pwa", "32px");
    });

    $(document).on('click','.save-settings', function (e) {    
        var data_form = new FormData($('.form-data')[0]);

        $('.proccess_load').show(); 
            $.ajax({
                type: "POST",url: "include/modules/settings/handlers/edit.php",data: data_form,dataType: "html",cache: false,
                contentType: false,processData: false,                        
                success: function (data) {
                    location.reload();                                           
                }
            });
        e.preventDefault();
    });

    
    $(document).on('click','.nav-item a', function (e) {
       history.pushState('', '', $(this).attr("data-route"));
    }); 
    

    $(document).on('click','.test-send-smtp', function () {   

      $(".result-log").val("");  

            $.ajax({
                type: "POST",url: "include/modules/settings/handlers/test_send_smtp.php",dataType: "html",
                cache: false,
                success: function (data) {
                    $(".result-log").val(data);   
                    notification();                 
                }
            });

    });
    
    $(document).on('click','.test-send-sms', function () {     

      $(".result-log").val("");

            $.ajax({
                type: "POST",url: "include/modules/settings/handlers/test_send_sms.php",dataType: "html",
                cache: false,
                success: function (data) {
                    $(".result-log").val(data);   
                    notification();                 
                }
            });            
    });

    $(document).on('click','.test-send-telegram', function () {     

      $(".result-log").val("");
      
            $.ajax({
                type: "POST",url: "include/modules/settings/handlers/test_send_telegram.php",dataType: "html",
                cache: false,
                success: function (data) {
                    $(".result-log").val(data);   
                    notification();                 
                }
            });            
    });

    $(document).on('change click','input[name=api_id_telegram]', function () {     

            $.ajax({
                type: "POST",url: "include/modules/settings/handlers/get_telegram_chat_id.php",data: "token=" + $(this).val() ,dataType: "json",
                cache: false,
                success: function (data) {
                  if(data["status"]){
                    $("input[name=chat_id_telegram]").val( data["answer"] );                 
                  }else{

                    if( data["answer"]["result"] ){
                       alert( data["answer"] );
                    }
                    
                  }
                }
            });            
    });

    $('.settings-add-currency').click(function () {  

    $('.proccess_load').show();   

        $.ajax({
            type: "POST",url: "include/modules/settings/handlers/add_currency.php",data: $(".form-add-currency").serialize(),dataType: "html",
            cache: false,success: function (data) {

                $('.proccess_load').hide();
                if(data == true){
                  location.reload();                    
                }else{
                  notification();                          
                }
                   
            }
        });        
    });

    $('.delete-currency').click(function () {     
     var uid = $(this).attr("uid");

       swal({
          title: "Вы действительно хотите выполнить удаление?",
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "Да",
          cancelButtonText: "Нет"
        }).then((result) => {
          if (result.value) {
              $('.proccess_load').show();
              $.ajax({
                  type: "POST",url: "include/modules/settings/handlers/delete_currency.php",data: "id="+uid,dataType: "html",cache: false,
                  success: function (data) {
                    $('.proccess_load').hide();
                    if(data == true){
                      location.reload();                    
                    }else{
                      notification();                          
                    }
                  }
              });                 
          }
        })
         
    });

    $(document).on("change", ".settings-access-site", function() {

        if($( this ).prop("checked") == false){
             $(".settings-access-out-text").attr("disabled", false).prop("checked", true);
             $(".settings-access-redirect").attr("disabled", false).prop("checked", false);
             $(".settings-access-redirect-link").attr("disabled", true);
             $(".settings-access-text").attr("disabled", false);   
             $(".settings-access-ip").attr("disabled", false);         
        }else{
             $(".settings-access-out-text").attr("disabled", true).prop("checked", false);
             $(".settings-access-redirect").attr("disabled", true).prop("checked", false);
             $(".settings-access-redirect-link").attr("disabled", true);
             $(".settings-access-text").attr("disabled", true);
             $(".settings-access-ip").attr("disabled", true);              
        }

     });

    $(document).on("change", ".settings-access-redirect", function() {

             $(".settings-access-redirect-link").attr("disabled", false);
             $(".settings-access-text").attr("disabled", true);            

    });

    $(document).on("change", ".settings-access-out-text", function() {

             $(".settings-access-redirect-link").attr("disabled", true);
             $(".settings-access-text").attr("disabled", false);                 

    });

    $(document).on("change", "input[name=robots_manual_setting]", function() {

             $(".robots_manual_setting").toggle();
             $(".robots_index_site").toggle();

    });

    $(document).on("click", ".setting-open-email", function() {

        var uid = $(this).attr("data-id");

              $('.proccess_load').show();
              $.ajax({
                  type: "POST",url: "include/modules/settings/handlers/load_email_tpl.php",data: "id="+uid,dataType: "html",cache: false,
                  success: function (data) {
                      $('.proccess_load').hide();
                      $(".container-templates").html(data); 
                      $("#modal-email-templates").modal("show");                    
                  }
              });                

    });

    $('.settings-edit-email-template').click(function () {

        var data_form = new FormData($(".form-email-templates")[0]);

        var email_text = CKEDITOR.instances['email_text'];
        if(email_text){
          data_form.append('email_text', CKEDITOR.instances.email_text.getData());
        }

        $('.proccess_load').show();

            $.ajax({type: "POST",url: "include/modules/settings/handlers/edit_email_tpl.php",data: data_form,dataType: "html",cache: false,contentType: false,processData: false,                        
                success: function (data) {
                    $('.proccess_load').hide();
                    notification();                                           
                }
            });

    });

    $(document).on("change", "#responder-rad-1", function() {

            $("input[name=smtp_host]").attr("disabled", true);
            $("input[name=smtp_port]").attr("disabled", true);
            $("input[name=smtp_username]").attr("disabled", true);
            $("input[name=smtp_password]").attr("disabled", true);                   

    });

    $(document).on("change", "#responder-rad-2", function() {

            $("input[name=smtp_host]").attr("disabled", false);
            $("input[name=smtp_port]").attr("disabled", false);
            $("input[name=smtp_username]").attr("disabled", false);
            $("input[name=smtp_password]").attr("disabled", false);         

    });

    $(document).on("change", ".change-payment", function() {
      var code = $( this ).val();
        $('.proccess_load').show();

            $.ajax({type: "POST",url: "include/modules/settings/handlers/load_payment.php",data: "code="+code,dataType: "html",cache: false,                        
                success: function (data) {
                    $('.proccess_load').hide();
                    notification();  
                    $(".param-payment").html(data); 
                    $(".selectpicker").selectpicker('refresh');                                        
                }
            });          

    });

    $(document).on("change", "#option-toggle-editor1", function() {
          CKEDITOR.replace("email_text");               
    });

    $(document).on("change", "#option-toggle-editor2", function() {
        var instance = CKEDITOR.instances['email_text'];
        if(instance){      
           CKEDITOR.instances['email_text'].destroy(true);  
        }   
    });

    var fixHelper = function(e, ui) {
        ui.children().each(function() {
            $(this).width($(this).width());
        });
        return ui;
    };

    $('.sort-container').sortable({ 
        axis: 'y',
        opacity: 0.5,
        cursor: "move",
        helper: fixHelper,
        handle:'.move-sort',
        stop: function(){
        var arr = $('.sort-container').sortable("toArray");
        $.ajax({url: "include/modules/settings/handlers/sorting_lang.php",type: 'POST',data: {arrays:arr},
          success: function(data){
              notification();
          }
        });
      }         
    });
    
    $(document).on("click", ".delete-lang", function() {     
     var uid = $(this).attr("data-id");

       swal({
          title: "Вы действительно хотите выполнить удаление?",
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "Да",
          cancelButtonText: "Нет"
        }).then((result) => {
          if (result.value) {
              $('.proccess_load').show();
              $.ajax({
                  type: "POST",url: "include/modules/settings/handlers/delete_lang.php",data: "id="+uid,dataType: "html",cache: false,
                  success: function (data) {
                    $('.proccess_load').hide();
                    if(data == true){
                      location.reload();                    
                    }else{
                      notification();                          
                    }
                  }
              });                 
          }
        })
         
    });
    
    $(document).on("click", ".add-lang", function() {

    var data_form = new FormData($('.form-add-lang')[0]);

    $('.proccess_load').show();   

        $.ajax({
            type: "POST",url: "include/modules/settings/handlers/add_lang.php",data: data_form,dataType: "html",
            cache: false,contentType: false,processData: false,success: function (data) { console.log(data);

                $('.proccess_load').hide();
                if(data == true){
                  location.reload();                    
                }else{
                  notification();                          
                }
                   
            }
        });        
    });
    
    $(document).on("click", ".edit-lang", function() {

    var data_form = new FormData($('.form-edit-lang')[0]);

    $('.proccess_load').show();   

        $.ajax({
            type: "POST",url: "include/modules/settings/handlers/edit_lang.php",data: data_form,dataType: "html",
            cache: false,contentType: false,processData: false,success: function (data) { console.log(data);

                $('.proccess_load').hide();
                if(data == true){
                  location.reload();                    
                }else{
                  notification();                          
                }
                   
            }
        });        
    });
    
    $(document).on('click','.load_edit_lang', function () {     
        var data_id = $(this).attr("data-id");
        $.ajax({type: "POST",url: "include/modules/settings/handlers/load_edit_lang.php", data: "id="+data_id,dataType: "html",cache: false,                     
            success: function (data) {
                $('.proccess_load').hide();
                $('.form-edit-lang').html(data);  
                $('#modal-edit-lang').modal("show");                                         
            }
        });        
    });

    $(document).on("change", "select[name=map_vendor]", function() {

        if($(this).val() == "google"){
           $(".map-google-key").show();
           $(".map-yandex-key").hide();
           $(".map-openstreetmap-key").hide();
        }else if($(this).val() == "yandex"){
           $(".map-yandex-key").show();
           $(".map-google-key").hide();
           $(".map-openstreetmap-key").hide();
        }else if($(this).val() == "openstreetmap"){
           $(".map-openstreetmap-key").show();
           $(".map-google-key").hide();
           $(".map-yandex-key").hide();
        }else{
           $(".map-google-key").hide();
           $(".map-yandex-key").hide();
           $(".map-openstreetmap-key").hide();
        }                

    });

    $(document).on("change", "select[name=country_default]", function() {

        var country = $( this ).find('option:selected').attr("data-id");

          $('.proccess_load').show();
          $.ajax({
              type: "POST",url: "include/modules/settings/handlers/load_region.php",data: "country="+country,dataType: "html",cache: false,
              success: function (data) {
                  $('.proccess_load').hide();
                  if(data){
                    $('.settings-region-box').html(data).show();
                  }else{
                    $('.settings-city-box').hide();
                  }
                  $('.selectpicker').selectpicker();
              }
          });                

    });

    $(document).on("change", "select[name=region_id]", function() {

        var region = $( this ).find('option:selected').val();

          $('.proccess_load').show();
          $.ajax({
              type: "POST",url: "include/modules/settings/handlers/load_city.php",data: "region="+region,dataType: "html",cache: false,
              success: function (data) {
                  $('.proccess_load').hide();
                  if(data){
                    $('.settings-city-box').html(data).show();
                  }else{
                    $('.settings-city-box').hide();
                  }
                  $('.selectpicker').selectpicker();
              }
          });                

    });

    $(document).on("change", "select[name=sms_service]", function() {

        if( $(this).find('option:selected').data("param") == "id" ){
          $('.sms_service_id').show();
          $('.sms_service_login_pass').hide();
        }else if( $(this).find('option:selected').data("param") == "login:pass" ){
          $('.sms_service_id').hide();
          $('.sms_service_login_pass').show();
        }else{
          $('.sms_service_id').hide();
          $('.sms_service_login_pass').hide();          
        }  

        if( $(this).find('option:selected').data("label") == "1" ){
            $(".sms_service_label").show();
        }else{
            $(".sms_service_label").hide();
        }        

        if( $(this).find('option:selected').data("call") == "1" ){
            $(".sms_service_method_send").show();
        }else{
            $(".sms_service_method_send").hide();
            $('.sms_service_method_send_sms').show();
        }

        $('.sms_service_method_send option:first').prop('selected', true);

        $(".selectpicker").selectpicker('refresh');

    });

    $(document).on("change", "select[name=sms_service_method_send]", function() {

        if( $(this).val() == "sms" ){
            $(".sms_service_method_send_sms").show();
        }else{
            $(".sms_service_method_send_sms").hide();
        }        

    });

    $(document).on('click','.test-payment', function () {     
        var name = $(this).data("name");
        $.ajax({type: "POST",url: "include/modules/settings/handlers/test_payment.php", data: "payment="+name,dataType: "json",cache: false,                     
            success: function (data) { console.log(data);
               if( data["link"] ){
                  location.href = data["link"];
               }else if( data["form"] ){
                  $("body").append( '<div class="temp-payment-form" >' + data["form"] + '</div>' );
                  $(".form-pay .pay-trans").click();
               }                                         
            }
        });        
    });

    $(document).on("change", ".checkbox-receipt", function() {

        if($( this ).prop("checked") == true){ 
             $(".payment-receipt").show();         
        }else{
             $(".payment-receipt").hide();             
        }

     });

    $(document).on("change", "select[name=watermark_type]", function() {

        if($( this ).val() == "caption"){ 
             $(".watermark-box-caption").show();         
             $(".watermark-box-img").hide();         
        }else{
             $(".watermark-box-caption").hide();         
             $(".watermark-box-img").show();              
        }

     });

    $(document).on('click','.cache-clear', function (e) {    

        $('.proccess_load').show(); 
            $.ajax({
                type: "POST",url: "include/modules/settings/handlers/cache_clear.php",data: "",dataType: "html",cache: false,                        
                success: function (data) {
                    location.reload();                                           
                }
            });

        e.preventDefault();
    });

    $(document).on("change", "input[name=user_shop_discount_status]", function() {

        if($( this ).prop("checked") == false){
             $(".settings-shop-discount").hide();        
        }else{
             $(".settings-shop-discount").show();             
        }

     });

     $(document).on("change", "select[name=secure_payment_service_name]", function() {
        
        var code = $( this ).val();
        $('.proccess_load').show();

            $.ajax({type: "POST",url: "include/modules/settings/handlers/load_payment_secure.php",data: "code="+code,dataType: "html",cache: false,                        
                success: function (data) {
                    $('.proccess_load').hide();
                    notification();  
                    $(".container-secure-service").html(data).show(); 
                    $(".selectpicker").selectpicker('refresh');                                        
                }
            });          

     });


}); 