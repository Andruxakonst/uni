$(document).ready(function () {
   
var url_path = $("body").data("prefix");

$.ajaxSetup({
 headers: {
   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
 }
});

$(".map-search-instance, .map-search-offers").css('height', ($( window ).height() - $('.header-wow').height()) + 'px');

function setFiltersUrl(){

  var hashes = window.location.href.split('?');
      
  var params = $.param($('.modal-form-filter').serializeArray().filter(function(el) {
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






});



