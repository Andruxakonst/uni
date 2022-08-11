$(document).ready(function() {
    $.getScript('files/js/javascript.js');
    
    $(document).on('click','.add-category', function (e) {
        var data_form = new FormData($('.form-data')[0]);
        data_form.append('text', CKEDITOR.instances.text.getData());        
        $('.proccess_load').show(); 
        $.ajax({
            type: "POST",url: "include/modules/category_board/handlers/add.php",data: data_form,dataType: "html",cache: false,contentType: false,processData: false,                                                
            success: function (data) { console.log(data);
                if (data==true){
                    location.href = "?route=category_board";  
                }else{
                    $('.proccess_load').hide();
                    notification();  
                }                                           
            }
        });
        e.preventDefault();
    });

    $(document).on('click','.edit-category', function (e) {
        var data_form = new FormData($('.form-data')[0]);
        data_form.append('text', CKEDITOR.instances.text.getData());        
        $('.proccess_load').show(); 
        $.ajax({
            type: "POST",url: "include/modules/category_board/handlers/edit.php",data: data_form,dataType: "html",cache: false,contentType: false,processData: false,                                                
            success: function (data) { console.log(data);
                if (data==true){
                    location.href="?route=category_board";  
                }else{
                    $('.proccess_load').hide();
                    notification();  
                }                                           
            }
        });
        e.preventDefault();
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
        containment:'.sort-container',
        handle:'.move-sort',
        stop: function(){
            var arr = $('.sort-container').sortable("toArray");
            $.ajax({url: "include/modules/category_board/handlers/sorting.php",type: 'POST',data: {arrays:arr},
                success: function(data){
                   notification();
                }
            });
        }           
    });

    $('.sort-container-filter').sortable({ 
        axis: 'y',
        opacity: 0.5,
        cursor: "move",
        helper: fixHelper,
        containment:'.sort-container-filter',
        handle:'.move-sort-filter',
        stop: function(){
            var arr = $('.sort-container-filter').sortable("toArray");
            $.ajax({url: "include/modules/category_board/handlers/sorting_filter.php",type: 'POST',data: {arrays:arr},
                success: function(data){
                   notification();
                }
            });
        }           
    });

    $(document).on('click','.delete-board-category', function () {    
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
                      type: "POST",url: "include/modules/category_board/handlers/delete.php",data: "id="+uid,dataType: "html",cache: false,
                      success: function (data) {
                        if(data == true){
                          location.reload();                    
                        }else{
                          $('.proccess_load').hide();
                          notification();                          
                        }
                      }
                  });                 
              }
            })

        return false;         
    });


    $(document).on('click','.filter-list-cat >li>span', function () {      
      if($(this).attr("status")==0){ $(".filter-list-cat >li>div").hide(); $(this).next().next().show(); $(this).attr("status","1"); }else{ $(".filter-list-cat >li>div").hide(); $(".filter-list-cat >li>span").attr("status","0"); } 
    });


    $(document).on('click','.board-open-podcat', function () {
      var uid = $(this).attr("uid");
      var ids = $(this).attr("data-ids");
      if($(this).attr("status") == "hide"){
        $("tr[parent-id='"+uid+"']").show();
        $(this).attr("status","show");
        $(this).parent().find(".icon-open-cat").attr("class", 'la la-minus icon-open-cat');
      }else{
        $("tr[parent-id='"+uid+"']").hide(); 
        $(ids).hide();
        $(ids).find(".board-open-podcat").attr("status","hide");
        $(this).attr("status","hide");
        $(this).parent().find(".icon-open-cat").attr("class", 'la la-plus icon-open-cat');
        $(ids).find(".icon-open-cat").attr("class", 'la la-plus icon-open-cat');
      }      
    });

    $(document).on("change", "input[name=paid]", function() {

        if($( this ).prop("checked") == false){
             $(".category-block-options").hide();         
        }else{
             $(".category-block-options").show();            
        }

     });

    $(document).on("change", "input[name=display_price]", function() {

        if($( this ).prop("checked") == false){
             $(".category-block-variant-price").hide();         
        }else{
             $(".category-block-variant-price").show();            
        }

     });

    $(document).on("change", "input[name=auto_title]", function() {

        if($( this ).prop("checked") == false){
             $(".category-block-options-auto-title").hide();         
        }else{
             $(".category-block-options-auto-title").show();            
        }

     });

    $(document).on('click','.filters-open-category', function () {
      var id = $(this).data("id");
      var status = $(this).attr("data-status");
      if(status == 0){
          $("tr[data-cat-id="+id+"]").show();
          $(this).find("i").attr("class", 'la la-minus');
          $(this).attr("data-status",1);
      }else{
          $("tr[data-cat-id="+id+"]").hide();
          $(this).find("i").attr("class", 'la la-plus'); 
          $(this).attr("data-status",0);
      }  
    });

    $(document).on("change", "select[name=variant_price]", function() {

        if( $( this ).val() == "0" ){
             $(".category-block-conditional-function, .category-block-conditional-online-view").show();         
        }else if( $( this ).val() == "1" ){
             $(".category-block-conditional-function, .category-block-conditional-online-view").hide();            
        }else if( $( this ).val() == "2" ){
             $(".category-block-conditional-function").show();   
             $(".category-block-conditional-online-view").hide();         
        }else if( $( this ).val() == "3" ){
             $(".category-block-conditional-function").hide();   
             $(".category-block-conditional-online-view").show();             
        }else if( $( this ).val() == "4" ){
             $(".category-block-conditional-function").hide();   
             $(".category-block-conditional-online-view").show();             
        }

    });

    function copyLink(el) {
        var $tmp = $("<input>");
        $("body").append($tmp);
        $tmp.val($(el).html()).select();
        document.execCommand("copy");
        $tmp.remove();
    }  

    $(document).on('click','.filter-copy', function () {    
       
        notification("success", "Фильтр скопирован");

        copyLink($(this));

        $("#modal-list-filters").modal("hide");

    });


  
});