$(document).ready(function() {
    $.getScript('files/js/javascript.js');

    $(document).on('click','.delete-user', function () {    

           swal({
              title: "Вы действительно хотите выполнить удаление?",
              text: "Пользователь будет удален со всеми объявлениями и отзывами",
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
                      type: "POST",url: "include/modules/clients/handlers/delete.php",data: "id="+$(this).attr("data-id"),dataType: "html",cache: false,
                      success: function (data) {
                         location.href = "?route=clients";
                      }
                  });                 
              }
            })

        return false;         
    });

    $(document).on('click','.clients-add-user', function () {    
     var data_form = new FormData($('.form-add-user')[0]);
     
          $('.proccess_load').show();
          $.ajax({
              type: "POST",url: "include/modules/clients/handlers/add.php",data: data_form,dataType: "json",cache: false, processData: false, contentType: false,
              success: function (data) {
                if(data["status"] == true){
                  location.href = "?route=client_view&id="+data["id"];                    
                }else{
                  $('.proccess_load').hide();
                  notification();                          
                }
              }
          });

        return false;         
    });

    $(document).on('click','.clients-edit-user', function () {    
     var data_form = $('.form-edit-user').serialize();
     
          $('.proccess_load').show();
          $.ajax({
              type: "POST",url: "include/modules/clients/handlers/edit.php",data: data_form,dataType: "html",cache: false,
              success: function (data) {

                  $('.proccess_load').hide();
                  notification();                          

              }
          });

        return false;         
    });

  $(document).on('change','#option-are-you1', function (e) { $(".input-name-company").hide(); });
  $(document).on('change','#option-are-you2', function (e) { $(".input-name-company").show(); });

  $(document).on('click','.button-balance-replenish', function () {    
    
    var data_form = $( ".form-balance-replenish" ).serialize();         

          $('.proccess_load').show();
          $.ajax({
              type: "POST",url: "include/modules/clients/handlers/balance.php",data: data_form,dataType: "html",cache: false,
              success: function (data) {
                if(data == true){
                  location.reload();                    
                }else{
                  $('.proccess_load').hide();
                  notification();                          
                }
              }
          });

        return false;         
   });


  $(document).on('click','.additional-phone-add', function () {   
      $(".container-additional-phone").append('<div class="additional-phone-item" ><input type="text" class="form-control" value="" name="additional_phone[]" ><span class="additional-phone-delete" ><i class="la la-close"></i></span></div>');          
  });

  $(document).on('click','.additional-phone-delete', function () {   
      $(this).parent().remove().hide();          
  });


   $(document).on('input click','.action-input-search-city', function () {     
      var myThis = $(this); 
      $.ajax({type: "POST",url: "include/modules/clients/handlers/city.php",data: "q="+myThis.val(),dataType: "html",cache: false,success: function (data) { if(data != false){ myThis.next().html(data).show(); }else{ myThis.next().html("").hide() }  }});
   });

   $(document).on('click','.SearchCityResults .item-city', function () {      
      $('.SearchCityResults').hide();
      $('input[name="city_id"]').val( $(this).attr("id-city") );
      $(this).parent().parent().find("input").val( $(this).attr("data-city") );
   });

   $(document).on('click', function(e) {
      if (!$(e.target).closest(".action-input-search-city").length && !$(e.target).closest(".custom-results").length) {
        $('.custom-results').hide();
      }
      e.stopPropagation();
   });



});