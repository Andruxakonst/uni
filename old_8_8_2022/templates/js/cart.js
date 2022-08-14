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

	function openCart(view_cart, link_cart){

        if(view_cart == 'modal'){
            $("#modal-cart").show();
            $("body").css("overflow", "hidden");            
        }

        if(view_cart == 'sidebar'){

            if($('.sidebar-cart').width() > $(document).width()){
                $('.sidebar-cart').css('width', $(document).width() + 'px');
            }

            $(".sidebar-cart-bg").fadeIn(200);
            $(".sidebar-cart").animate({"right": "0px"}, 300);
            $("body").css("overflow", "hidden");
        }

        if(view_cart == 'page'){
        	$("#modal-notification-cart").show();
		    $("body").css("overflow", "hidden");
        }

   }

    function actionAfterOrder(view_cart){

        if(view_cart == 'modal'){
            $("#modal-cart").hide();
            $("#modal-order-accept").show();
            $("body").css("overflow", "hidden");     
            loadCart();       
        }

        if(view_cart == 'sidebar'){
            $(".sidebar-cart").animate({"right": "-" + $(".sidebar-cart").width() + "px"}, 300, function(){
                $(".sidebar-cart-bg").hide();
                $("body").css("overflow", "hidden");
                $("#modal-order-accept").show();
                loadCart();
            });
        }

        if(view_cart == 'page'){
            $(".cart-box-1").hide();
            $(".cart-box-2").show();
        }

   }

   function loadCart(){

         $.ajax({type: "POST",url: url_path + "systems/ajax/cart.php",data: "id=" + $(this).data('id') + "&action=load_cart",dataType: "json",cache: false,success: function (data) { 
              $('.cart-container').html( data['items'] );
              $('.cart-info').html( data['info'] );
              $('.cart-item-counter').html( data['counter'] );
              $('.cart-itog').html( data['itog'] );
              if( data['counter'] ){
                  $('.label-count-cart').show();
              }else{
                  $('.label-count-cart').hide();
              }
         }});

   }

   $(document).on('click','.ad-add-to-cart', function (e) {

         var this_button = $(this);
     
         $.ajax({type: "POST",url: url_path + "systems/ajax/cart.php",data: "id=" + $(this).data('id') + "&action=add_to_cart",dataType: "json",cache: false,success: function (data) { 
             if( data['status'] == true ){
                 
                 loadCart();

                 if(data['action'] == 'add'){
                    this_button.html(this_button.data('name-delete'));
                    openCart(data['view_cart'], data['link_cart']);
                 }else{
                    this_button.html(this_button.data('name-add'));
                 }
                 
             }else{

                alert(data['answer']);

             }
         }});

   });

   $(document).on('click','.sidebar-cart-open', function (e) {

         if($('.sidebar-cart').width() > $(document).width()){
            $('.sidebar-cart').css('width', $(document).width() + 'px');
         }

         $(".sidebar-cart-bg").fadeIn(200);
         $(".sidebar-cart").animate({"right": "0px"}, 300);
         $("body").css("overflow", "hidden");
   });

   $(document).on('click','.sidebar-cart-close', function (e) {
     
         $(".sidebar-cart").animate({"right": "-" + $(".sidebar-cart").width() + "px"}, 300, function(){
            $(".sidebar-cart-bg").hide();
            $("body").css("overflow", "auto");
         });

   });

   $(document).on('click','.cart-goods-item-count-change', function (e) {

         var _this = $(this);
         var id = $(this).parents('.cart-goods-item').data('id');

         $.ajax({type: "POST",url: url_path + "systems/ajax/cart.php",data: "id=" + id + "&variant=" + $(this).data('action') + "&action=change_count",dataType: "json",cache: false,success: function (data) { 
              
              if(data["status"]){
                  _this.parents('.cart-goods-item').find('input.cart-goods-item-count').val(data["count"]);
                  _this.parents('.cart-goods-item').find('div.cart-goods-item-content-price-info').html(data["total"]);

                  $('.cart-info').html( data['info'] );
                  $('.cart-item-counter').html( data['counter'] );
                  $('.cart-itog').html( data['itog'] );

                  if( data['counter'] ){
                      $('.label-count-cart').show();
                  }else{
                      $('.label-count-cart').hide();
                  }
              }else{
                  loadCart();
              }

         }});

   });

   $(document).on('click','.cart-goods-item-delete', function (e) {

         var id = $(this).parents('.cart-goods-item').data('id');

         $.ajax({type: "POST",url: url_path + "systems/ajax/cart.php",data: "id=" + id + "&action=delete",dataType: "html",cache: false,success: function (data) { 
              
              loadCart();
              $('.ad-add-to-cart[data-id="'+id+'"]').html($('.ad-add-to-cart[data-id="'+id+'"]').data('name-add'));

         }});

   });

   $(document).on('click','.cart-page-goods-item-delete', function (e) {

         var id = $(this).parents('.cart-goods-item').data('id');

         $.ajax({type: "POST",url: url_path + "systems/ajax/cart.php",data: "id=" + id + "&action=delete",dataType: "html",cache: false,success: function (data) { 
              
              location.reload();

         }});

   });

   $(document).on('click','.cart-clear', function (e) {

         $.ajax({type: "POST",url: url_path + "systems/ajax/cart.php",data: "action=clear",dataType: "html",cache: false,success: function (data) { 
              
              location.reload();

         }});

   });

   $(document).on('click','.cart-order', function (e) {

         var _this = $(this);

         $.ajax({type: "POST",url: url_path + "systems/ajax/cart.php",data: "action=order",dataType: "json",cache: false,success: function (data) { 
              
              if(data['status']){
                 actionAfterOrder(_this.data('page-type'));
              }else{
                 if(!data['auth']){
                    $('#modal-auth').show();
                    $("body").css("overflow", "hidden");
                 }
              }

         }});

   });

   loadCart();    

});