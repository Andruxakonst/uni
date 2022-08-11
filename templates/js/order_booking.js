$(document).ready(function () {
   
   var url_path = $("body").data("prefix");
   
   $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      error : function(jqXHR, textStatus, errorThrown) {

            if (jqXHR.status == 401){
                alert("Сессия авторизации истекла.");
            }else if (jqXHR.status == 403){
            }else if (jqXHR.status == 500){
                alert("Произошла ошибка! Перезагрузите страницу и попробуйте снова.");
            }

      }
   });

   $(document).on('click','.confirm-delete-order-booking', function (e) { 
      
      var id = $(this).data("id");

      $(".confirm-delete-order-booking").prop('disabled', true);

        $.ajax({type: "POST",url: url_path + "systems/ajax/ads.php",data: "id="+id+"&action=order_delete_booking",dataType: "json",cache: false,success: function (data) { 
            location.href = data['link'];
        }});

      e.preventDefault();
   });

   $(document).on('click','.confirm-cancel-order-booking', function (e) { 
      
      var id = $(this).data("id");

      $(".confirm-cancel-order-booking").prop('disabled', true);

        $.ajax({type: "POST",url: url_path + "systems/ajax/ads.php",data: "id="+id+"&reason="+$('textarea[name=cancel_order_reason]').val()+"&action=order_cancel_booking",dataType: "json",cache: false,success: function (data) { 
            if(data['status'] == true){
                location.reload();
            }else{
                alert(data['answer']);
                $(".confirm-cancel-order-booking").prop('disabled', false);
            }
        }});

      e.preventDefault();
   });

   $(document).on('click','.buy-prepayment-booking', function (e) { 
      
      var id = $(this).data("id");

      $(".buy-prepayment-booking").prop('disabled', true);

        $.ajax({type: "POST",url: url_path + "systems/ajax/ads.php",data: "id="+id+"&action=order_prepayment_booking",dataType: "json",cache: false,success: function (data) { 
            if(data['form']){
                $("body").append('<div style="display:none;" >' + data["form"] + '</div>');
                $(".form-pay .pay-trans").click();                
            }else{
                location.href = data['link'];
            }
        }});

      e.preventDefault();
   });

   $(document).on('click','.order-confirm-booking', function (e) {

       $.ajax({type: "POST",url: url_path + "systems/ajax/ads.php",data: "id="+$(this).data('id')+"&action=order_confirm_booking",dataType: "html",cache: false,success: function (data) { 
           location.reload();
       }});

   });


});

