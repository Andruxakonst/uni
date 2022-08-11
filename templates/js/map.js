$(document).ready(function () {
   
var url_path = $("body").data("prefix");
var statusOpenSidebar = false;

$.ajaxSetup({
 headers: {
   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
 }
});

function sidebarMobileDefault(){

    if($('.map-search-offer').css('display') == 'none'){
        $('.map-search-sidebar').height( $(window).height() - $('.header-wow-mobile').height() );
        $('.map-search-sidebar').css('top', (parseInt($('.map-search-sidebar').height()) - 100)+'px');
        $('.map-search-sidebar').css('left', '5px');
        $('.map-search-sidebar').css('right', '5px');
        $('.map-search-sidebar').css('bottom', 'auto');
        $('.map-search-sidebar').css('width', 'auto');  

        $('.map-search-sidebar-mobile-close-toggle').hide();
        $('.map-search-sidebar-mobile-open-toggle').show();
    }else{
        $('.map-search-sidebar').css('height', 'auto');
        $('.map-search-sidebar').css('top', 'auto');
        $('.map-search-sidebar').css('bottom', '5px');
        $('.map-search-sidebar').css('left', '5px');
        $('.map-search-sidebar').css('right', '5px');
        $('.map-search-sidebar').css('width', 'auto');
        $('.map-search-sidebar-mobile-open-toggle').hide();  
        $('.map-search-sidebar-mobile-close-toggle').hide();    
    }

}

function sidebarDefault(){

    $('.map-search-sidebar').css('top',  '15px');
    $('.map-search-sidebar').css('left', 'auto');
    $('.map-search-sidebar').css('right', '15px');

    if($('.map-search-offer').css('display') == 'none'){
      $('.map-search-sidebar').css('bottom', '15px');
    }else{
      $('.map-search-sidebar').css('bottom', 'auto');
    }

    $('.map-search-sidebar').css('width', '500px');  
    $('.map-search-sidebar').css('height', 'auto');

    $('.map-search-sidebar-mobile-close-toggle').hide();
    $('.map-search-sidebar-mobile-open-toggle').hide(); 

}

function detectMobile(height){

    if(!height){
       height = $(window).height();
    }

    if ($(window).width() <= 992) {
      $(".map-search-container, .map-search-instance").height(height - $('.header-wow-mobile').height());
      if(!statusOpenSidebar){
          sidebarMobileDefault();
      }
    }else{
      statusOpenSidebar = false;
      $(".map-search-container, .map-search-instance").height(height - $('.header-wow').height());
      sidebarDefault();
    }

    $('.map-search-sidebar').show();

    $(".map-search-offers-list").height( $('.map-search-sidebar').outerHeight() - $(".map-search-offers-header").outerHeight() - 20 );

}

detectMobile();

$(document).on('click','.map-search-sidebar-mobile-open-toggle', function () {
    statusOpenSidebar = true;
    $(this).hide();
    $('.map-search-sidebar-mobile-close-toggle').show();
    $('.map-search-sidebar').css('height', 'auto');
    $('.map-search-sidebar').css('top', '50px');
    $('.map-search-sidebar').css('left', '5px');
    $('.map-search-sidebar').css('right', '5px');
    $('.map-search-sidebar').css('bottom', '5px');
    $(".map-search-offers-list").height( $('.map-search-sidebar').outerHeight() - $(".map-search-offers-header").outerHeight() - 20 );
});

$(document).on('click','.map-search-sidebar-mobile-close-toggle', function () {
    statusOpenSidebar = false;
    sidebarMobileDefault();
});

$(document).on('click','.map-search-offer-back-catalog', function () { 
    
    statusOpenSidebar = false;

    $('.map-search-offers').show();
    $('.map-search-offer-item').html('');
    $('.map-search-offer').hide();

    if ($(window).width() <= 992) {
       sidebarMobileDefault();
    }else{
       sidebarDefault();
    }

});

function setFiltersUrl(){

  var hashes = window.location.href.split('?');
      
  var params = $.param($('.modal-form-filter, .modal-geo-options-form').serializeArray().filter(function(el) {
          return $.trim(el.value);
      }));

      history.pushState("", "", hashes[0]+"?"+params);

}

$(document).on('click','.filter-accept', function () { 
    setFiltersUrl();
    location.reload();
});

$(document).on("click", ".action-clear-filter", function(e) {
     
     if( $("input[name=id_c]").val() == "0" ){
        location.href = window.location.href.split("?")[0];
     }else{
        location.href = window.location.href.split("?")[0] + "?id_c=" + $("input[name=id_c]").val();
     }

     e.preventDefault();

});

$(document).on('change','.modal-form-filter input', function (e) { 

    var id_filter = $(this).closest(".filter-items").attr("id-filter");
    var id_parent = $(this).closest(".filter-items").attr("main-id-filter");
    var id_item = $(this).val();
    var element = $(this);
    
    if($(this).closest(".filter-items").attr("data-ids") != undefined){
       var ids = $(this).closest(".filter-items").attr("data-ids").split(",");
    }

    if(ids){

      $.each(ids,function(index,value){

        $('div[id-filter="'+value+'"]').remove();

      });

    }

    if($(this).val() != "null"){ 
        
          $.ajax({type: "POST",url: url_path + "systems/ajax/ads.php",data: "id_filter="+id_filter+"&id_item="+id_item+"&view=modal&action=load_items_filter",dataType: "html",cache: false,success: function (data) {

              element.closest(".filter-items").after(data);

          }});
     
    }

    e.preventDefault();
});

$(window).on('resize', function(){

  detectMobile($(this).height());

});

});



