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

function showLoadProcess(el){
    el.prop('disabled', true);
    el.html('<span class="spinner-border spinner-border-sm spinner-load-process" role="status" ></span> '+el.html());
}

function hideLoadProcess(el){
    el.prop('disabled', false);
    $('.spinner-load-process').remove();
}

$("#modal-ad-share input").val(document.location.href);

$('.lightgallery').lightGallery();

function tippyLoad(){
    tippy('[data-tippy-placement]', {
      delay: 100,
      arrow: true,
      arrowType: 'sharp',
      size: 'regular',
      duration: 200,
      animation: 'shift-away',
      animateFill: true,
      theme: 'dark',
      distance: 10,
    });
}
 
$(document).on('click','.modal-ad-new-close', function () {    

    var hashes = window.location.href.split('?');
    history.pushState("", "", hashes[0]);

});
   
$(document).on('click','.ads-remove-publication', function () {  
  showLoadProcess($(this));    
  $.ajax({type: "POST",url: url_path + "systems/ajax/ads.php",data: "id_ad="+$(this).data("id")+"&action=remove_publication",dataType: "html",cache: false,success: function (data) { 
     location.reload();
  }});
});

$(document).on('click','.ads-status-sell', function () {    
  var el = $(this);
  showLoadProcess(el); 
  $.ajax({type: "POST",url: url_path + "systems/ajax/ads.php",data: "id_ad="+$(this).data("id")+"&action=ads_status_sell",dataType: "html",cache: false,success: function (data) { 
       $("#modal-ads-success-sell").show();
       $("#modal-remove-publication").hide();
       $("body").css("overflow", "hidden");
       hideLoadProcess(el);
  }});
});


$(document).on('click','.ads-publication', function () {  
  showLoadProcess($(this));   
  $.ajax({type: "POST",url: url_path + "systems/ajax/ads.php",data: "id_ad="+$(this).data("id")+"&action=ads_publication",dataType: "html",cache: false,success: function (data) { 
     location.href = data;
  }});
});

$(document).on('click','.ads-delete', function () {  
  showLoadProcess($(this));   
  $.ajax({type: "POST",url: url_path + "systems/ajax/ads.php",data: "id_ad="+$(this).data("id")+"&action=ads_delete",dataType: "html",cache: false,success: function (data) { 
     location.reload();
  }});
});

$(document).on('click','.ads-extend', function () {   
  showLoadProcess($(this));  
  $.ajax({type: "POST",url: url_path + "systems/ajax/ads.php",data: "id_ad="+$(this).data("id")+"&action=ads_extend",dataType: "html",cache: false,success: function (data) { 
     location.reload();
  }});
});

$(document).on('click','.show-phone', function () {    

  $.ajax({type: "POST",url: url_path + "systems/ajax/ads.php",data: "id_ad=" + $(this).data("id") + "&action=show_phone",dataType: "json",cache: false,success: function (data) { 

     if(data["auth"]){
       $("#modal-view-phone").show();
       $("body").css("overflow", "hidden");          
       $(".modal-view-phone-display").html(data["html"]);
     }else{
       $("#modal-auth").show();
       $("body").css("overflow", "hidden");
     }

  }});

});

$(document).on('click','.ads-services-tariffs', function () {      
  $(".ads-services-tariffs").removeClass("active");
  $(this).addClass("active");
  $(".form-ads-services input[name=id_s]").val( $(this).data("id") );
  $(".ads-services-tariffs-btn-order").show();
});

$(document).on('submit','.form-ads-services', function (e) {   
  $(".ads-services-tariffs-btn-order").prop('disabled', true);   
  $.ajax({type: "POST",url: url_path + "systems/ajax/ads.php",data: $(this).serialize()+"&action=service_activation",dataType: "json",cache: false,success: function (data) { 

     var hashes = window.location.href.split('?');
     history.pushState("", "", hashes[0]);

     if(data["status"] == true){
           
           $("#modal-services-access").show();
           $("#modal-order-service").hide();
           $("body").css("overflow", "hidden");

     }else{

        if(data["balance"]){
           
           $("#modal-order-service,#modal-ad-new").hide();
           $("#modal-balance").show();
           $(".modal-balance-summa").html( data["balance"] );
           $("body").css("overflow", "hidden");

        }else{

           alert(data["answer"]);

        }

     }

     $(".ads-services-tariffs-btn-order").prop('disabled', false);

  }});
  e.preventDefault();
});



$('.ads-view-photo-slider').lightSlider({
    autoWidth: $('.ads-view-photo').data('width'),                    
    gallery: true,
    controls: true,
    item: $('.ads-view-photo').data('count'),
    loop: true,
    slideMargin: 5,
    thumbItem: 8,
    thumbWidth: 65,
    freeMove: false,
    thumbMargin: 7,
    keyPress: true,
    enableDrag: true,
    enableTouch: true,
    currentPagerPosition: 'middle',
    prevHtml: '<i class="las la-angle-left"></i>',
    nextHtml: '<i class="las la-angle-right"></i>',
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          //slidesToShow: 4,
          //slidesToScroll: 4,
        }
      },
      {
        breakpoint: 700,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 1,
          autoWidth: false,
        }
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1,
          autoWidth: false,
        }
      }
    ]
});

$(document).on('click','.top-views-change', function (e) { 
  
  $("#modal-top-views").hide();
  $("#modal-order-service").show();
  $("body").css("overflow", "hidden");  

  e.preventDefault();
});

$(document).on('click','.top-views-up', function (e) {   
  $(this).prop('disabled', true);   
  $.ajax({type: "POST",url: url_path + "systems/ajax/ads.php",data: "id_ad="+$(this).data("id")+"&id_s=1&action=service_activation",dataType: "json",cache: false,success: function (data) { 
     
     if(data["status"] == true){
        location.reload();
     }else{
        if(data["balance"]){
           
           $("#modal-order-service,#modal-ad-new,#modal-top-views").hide();
           $("#modal-balance").show();
           $(".modal-balance-summa").html( data["balance"] );
           $("body").css("overflow", "hidden");

        }else{

           alert(data["answer"]);

        }
        $(".top-views-up").prop('disabled', false);
     }

  }});
  e.preventDefault();
});

$(document).on('click','.list-properties-toggle', function (e) { 

    var status = $(this).attr("data-status");

    if(status == 0){
       $(".list-properties-display").addClass("heightAuto");
       $(this).html( $(".lang-js-7").html() );
       $(this).attr("data-status", 1);
    }else{
       $(".list-properties-display").removeClass("heightAuto");
       $(this).html( $(".lang-js-8").html() );
       $(this).attr("data-status", 0);
    }
    
});

function initializeSlider(){

  var slider = $('.photo-mobile-slider').not('.lightSlider').lightSlider({
      item:1,
      loop:false,
      pager:false,
      slideMargin:2,
      slideMove:1,
      speed:600,
      responsive : [
          {
              breakpoint:800,
              settings: {
                item:1,
                slideMove:1
              }
          },
          {
              breakpoint:480,
              settings: {
                item:1,
                slideMove:1
              }
          }
      ]
  });

}

function countDisplay(){
   $.ajax({type: "POST",url: url_path + "systems/ajax/ads.php",data: "action=update_count_display",dataType: "json",cache: false});
}

function similar(){

    $.ajax({type: "POST",url: url_path + "systems/ajax/ads.php",data: "id_cat=" + $("body").attr("data-id-cat") + "&id_ad=" + $("body").attr("data-id-ad") + "&action=ad_similar",dataType: "json",cache: false,success: function (data) { 
        if(data['content']){
            $(".ajax-container-similar").show();
            $(".ajax-container-similar-content").html(data['content']);
            tippyLoad();
            initializeSlider();
            countDisplay();
        }
    }});

}

$(document).on('click','.action-auction-rate', function (e) { 
    
    var rate = $("#modal-auction input[name=rate]").val();
    var id = $(this).data("id");

      $.ajax({type: "POST",url: url_path + "systems/ajax/ads.php",data: "id="+id+"&rate="+rate+"&action=auction_rate",dataType: "json",cache: false,success: function (data) { 
          if( data["auth"] == true ){

            if( data["status"] == true ){
               $("#modal-auction").hide();
               $("#modal-auction-success").show();
            }else{
               alert(data["answer"]);
            }

          }else{
             $("#modal-auction").hide();
             $("#modal-auth").show();
          }
      }});

  e.preventDefault();
});

$(document).on('click','.action-auction-cancel-rate', function (e) { 
    
    var id = $(this).data("id");

    $.ajax({type: "POST",url: url_path + "systems/ajax/ads.php",data: "id="+id+"&action=auction_cancel_rate",dataType: "html",cache: false,success: function (data) { 
        location.reload();
    }});

  e.preventDefault();
});

$('[data-countdown="true"]').each(function (index, element) {
    $(element).countdown( $(element).attr("data-date") )
    .on('update.countdown', function(event) {
      var format = '%M '+$(".lang-js-2").html()+' %S ' + $(".lang-js-3").html();
      $(element).html(event.strftime(format));
    })
    .on('finish.countdown', function(event) {
        $(element).removeClass("pulse-time").html( $(".lang-js-6").html() );
    });

});

$(document).on('click','.module-comments-otvet', function () { 

  $(this).parent().parent().find(".module-comments-form-otvet").toggle();
  $("input[name=id_msg]").val( $(this).data("id") );

});

$(document).on('submit','.module-comments-form', function (e) { 

  $(".module-comments-form button").prop('disabled', true);

    $.ajax({type: "POST",url: url_path + "systems/ajax/ads.php",data: $(this).serialize() + "&action=add_comment",dataType: "json",cache: false,                        
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

    $.ajax({type: "POST",url: url_path + "systems/ajax/ads.php",data: "id=" + $(this).data("id") + "&action=delete_comment",dataType: "json",cache: false,                        
        success: function (data){
            location.reload();                                            
        }
    });

  e.preventDefault();
});

$(document).on('click', function(e) {
  if ( !$(e.target).closest(".board-view-price-currency").length && !$(e.target).closest(".price-currency i").length ) {
    $('.board-view-price-currency').hide();
  }
  e.stopPropagation();
});

$(document).on('click','.price-currency i', function (e) { 

    $('.board-view-price-currency').toggle();

});

$(document).on('click','.toggle-favorite-ad', function () {    
  var _this = $(this); 
  $.ajax({type: "POST",url: url_path + "systems/ajax/profile.php",data: "id_ad=" + _this.data("id") + "&action=favorite",dataType: "json",cache: false,success: function (data) { 

     if(data["auth"]){
       if(data["status"]){
         $(".favorite-ad-icon-box").html(`<svg width="24" height="24" fill="none" xmlns="http://www.w3.org/2000/svg" class="favorite-icon-active" ><path d="M12.39 20.87a.696.696 0 01-.78 0C9.764 19.637 2 14.15 2 8.973c0-6.68 7.85-7.75 10-3.25 2.15-4.5 10-3.43 10 3.25 0 5.178-7.764 10.664-9.61 11.895z" fill="currentColor"></path></svg>`);
       }else{
         $(".favorite-ad-icon-box").html(`<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.026 4.133C4.398 4.578 3 6.147 3 8.537c0 3.51 2.228 6.371 4.648 8.432A23.633 23.633 0 0012 19.885a23.63 23.63 0 004.352-2.916C18.772 14.909 21 12.046 21 8.537c0-2.39-1.398-3.959-3.026-4.404-1.594-.436-3.657.148-5.11 2.642a1 1 0 01-1.728 0C9.683 4.281 7.62 3.697 6.026 4.133zM12 21l-.416.91-.003-.002-.008-.004-.027-.012a15.504 15.504 0 01-.433-.214 25.638 25.638 0 01-4.762-3.187C3.773 16.297 1 12.927 1 8.538 1 5.297 2.952 2.9 5.499 2.204c2.208-.604 4.677.114 6.501 2.32 1.824-2.206 4.293-2.924 6.501-2.32C21.048 2.9 23 5.297 23 8.537c0 4.39-2.772 7.758-5.352 9.955a25.642 25.642 0 01-4.762 3.186 15.504 15.504 0 01-.432.214l-.027.012-.008.004-.003.001L12 21zm0 0l.416.91c-.264.12-.568.12-.832 0L12 21z" fill="currentColor"></path></svg>`);
       }
     }else{
       $("#modal-auth").show();
       $("body").css("overflow", "hidden");
     }

  }});
});

$(document).on('click','.action-accept-auction-order-reservation', function (e) { 
    
    var id = $(this).data("id");

    $(this).prop('disabled', true);

    $.ajax({type: "POST",url: url_path + "systems/ajax/ads.php",data: "id="+id+"&action=auction_accept_order_reservation",dataType: "html",cache: false,success: function (data) { 
        location.reload();
    }});

  e.preventDefault();
});

$(function(){ 
    
    $(".ads-view-photo").css("visibility", "visible");
    similar();

});


});


