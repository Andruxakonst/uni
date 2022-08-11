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

   $('.lightgallery').lightGallery();
   
   function getRandomInt(min, max)
   {   
       return Math.floor(Math.random() * (max - min + 1)) + min;
   }

   $(document).on('click','.confirm-cancel-order-marketplace', function (e) { 
      
      var id = $(this).data("id");

      $(".confirm-cancel-order-marketplace").prop('disabled', true);

        $.ajax({type: "POST",url: url_path + "systems/ajax/ads.php",data: "id="+id+"&action=order_cancel_deal_marketplace",dataType: "html",cache: false,success: function (data) { 
            location.reload();
        }});

      e.preventDefault();
   });

   $(document).on('click','.confirm-delete-order-marketplace', function (e) { 
      
      var id = $(this).data("id");

      $(".confirm-delete-order-marketplace").prop('disabled', true);

        $.ajax({type: "POST",url: url_path + "systems/ajax/ads.php",data: "id="+id+"&action=order_delete_marketplace",dataType: "json",cache: false,success: function (data) { 
            location.href = data['link'];
        }});

      e.preventDefault();
   });

   function attach_order_message(input) {

      var data = new FormData();
      $.each( input.files, function( key, value ){
          data.append( key, value );
      });

      data.append('action', 'load_reviews_attach_files');
     
     var i = 0;
     var count_load_img = input.files.length;

      while (i < count_load_img) {

        if (input.files && input.files[i]) {
            var reader = new FileReader();
            
            reader.onload = function (e) { 


                    var uid = getRandomInt(10000, 90000);
                    
                    $(".order-message-attach-files").append('<div class="id'+uid+' attach-files-preview attach-files-loader" ><img class="image-autofocus" src="'+e.target.result+'" /></div>'); 
            
               
            };

            reader.readAsDataURL(input.files[i]);
        }
        
        i++
      }
   
      $.ajax({url: url_path + "systems/ajax/profile.php",type: 'POST',data: data,cache: false,dataType: 'html',processData: false,contentType: false,
          success: function( respond, textStatus, jqXHR ){

               $(".order-message-attach-files").append(respond);
               $(".attach-files-loader").hide();

          }
      });

      $(".input_attach_files").val("");

   }

   $(document).on('click','.order-message-attach-change', function () { $('.input_attach_files').click(); });
   $(document).on('change','.input_attach_files', function () {  
       if(this.files.length > 0){  
          attach_order_message(this);
       }   
   });

    $(document).on("click", ".attach-files-delete", function(e) {
        $(this).parents(".attach-files-preview").hide().remove();
        e.preventDefault();
    });
    
    $(document).on("keydown", ".order-send-message", function(e){

        var val = $(this).val();
        if ((e.keyCode == 10 || e.keyCode == 13) && e.ctrlKey)
        {
            $(this).val( $(this).val() + '\r\n' );
        }else if (e.keyCode == 10 || e.keyCode == 13)
        { 
            $(this).val('');
            $.ajax({type: "POST",url: url_path + "systems/ajax/profile.php",data: $('.form-order-message').serialize() + "&message=" + val + "&action=order_send_message",dataType: "json",cache: false,success: function (data) { 

                $('.attach-files-preview').hide().remove();

                if(data['status'] == true){
                    $('.order-messages-box').html(data['messages']);
                    $('.lightgallery').lightGallery();
                }else{
                    alert(data['answer']);
                }

                
            }});
            return false;
        }
     
    });

    function updateMessages(){
        $.ajax({type: "POST",url: url_path + "systems/ajax/profile.php",data: "order_id=" + $('input[name=order_id]').val() + "&action=order_update_message",dataType: "json",cache: false,success: function (data) { 

            if(data['status'] == true){
                $('.order-messages-box').html(data['messages']);
                $('.lightgallery').lightGallery();
            }
            
        }});        
    }

    $(document).on('change','.order-change-status', function (e) {

         $.ajax({type: "POST",url: url_path + "systems/ajax/ads.php",data: "id="+$(this).data('id')+"&status="+$('.order-change-status option:selected').val()+"&action=order_change_status",dataType: "html",cache: false,success: function (data) { 
             location.reload();
         }});

    });

    setInterval(function() {
      updateMessages();
    }, 3000);


});

