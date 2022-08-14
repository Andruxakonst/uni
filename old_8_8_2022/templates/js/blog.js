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

$(document).on('click','.module-comments-otvet', function () { 

  $(this).parent().parent().find(".module-comments-form-otvet").toggle();
  $("input[name=id_msg]").val( $(this).data("id") );

});

$(document).on('submit','.module-comments-form', function (e) { 

  $(".module-comments-form button").prop('disabled', true);

    $.ajax({type: "POST",url: url_path + "systems/ajax/blog.php",data: $(this).serialize() + "&action=add_comment",dataType: "json",cache: false,                        
        success: function (data){
            if( data["status"] == true ){
               location.reload();
            }else{
               $(".module-comments-form button").prop('disabled', false);
            }                                            
        }
    });

  e.preventDefault();
});

$(document).on('click','.module-comments-delete', function (e) { 

    $.ajax({type: "POST",url: url_path + "systems/ajax/blog.php",data: "id=" + $(this).data("id") + "&action=delete_comment",dataType: "json",cache: false,                        
        success: function (data){
            location.reload();                                            
        }
    });

  e.preventDefault();
});


});


