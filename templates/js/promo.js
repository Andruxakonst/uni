$(document).ready(function () {

var url_path = $("body").data("prefix");
var click_el = "";
var edit_el = "";

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

$("input[name=body_width]").val( $(".container").width() );
$("input[name=body_color]").val( $("body").css("background-color") );

$('#summernote').summernote( {
    minHeight: 200
});

$(document).on('click','.promo-element-text-edit-accept', function () {    
  
   edit_el.html( $(".promo-element-text-edit").val() );

   $("#modal-promo-element-text-edit").hide();
   $("body").css("overflow", "auto");

});

$(document).on('click','.promo-element-text', function () {    
  
  edit_el = $(this); 

  $('#summernote').summernote('code', edit_el.html());

  $("#modal-promo-element-text-edit").show();
  $("body").css("overflow", "hidden");
  
});

$(document).on('click','.promo-add-element > span', function () {    
  
  click_el = $(this).parent(); 
  
}); 

$(document).on('click','.modal-promo-elements-list > div', function () {    
  
  var el = $(this); 
  
  $.ajax({type: "POST",url: url_path + "systems/ajax/promo.php",data: "name=" + el.data("name") + "&action=load_element",dataType: "html",cache: false,success: function (data) { 

      click_el.before(data);
      $("#modal-promo-elements").hide();
      $("body").css("overflow", "auto");

  }});

});

$(document).on('input','input[name=body_color]', function () {    
   $("body").css("background-color", $(this).val() );
});

$(document).on('input','input[name=body_width]', function () {    
   $(".container").css("max-width", $(this).val() + "px" );
});

$(document).on('change','input[name=logotip]', function () {    
   
  if($(this).prop("checked") == true){
      $(".promo-logo").show();
  }else{
      $(".promo-logo").hide();
  }

});

$(".minicolors").minicolors({

  swatches: [],
  change: function(value, opacity) {
    if( !value ) return;
    if( opacity ) value += ', ' + opacity;
  },
  theme: 'bootstrap'
});

function loadLogo(input) { 

    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {    
              $(".promo-logo").html( '<img src="'+e.target.result+'" />' );          
        };

        reader.readAsDataURL(input.files[0]);
    }
}

$(document).on('click','.promo-logo span, .promo-logo img', function () { $('.input-logo-img').click(); });
$(document).on('change','.input-logo-img', function () {    
    loadLogo(this);
});

});


