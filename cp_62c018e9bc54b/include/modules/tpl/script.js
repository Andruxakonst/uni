$(document).ready(function() {
    $.getScript('files/js/javascript.js');

    $(document).on('click','.delete-tpl', function (e) { 
        
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
                    type: "POST",url: "include/modules/tpl/handlers/delete.php",data: "",dataType: "html",cache: false,                        
                    success: function (data) {
                        location.reload();                                         
                    }
                });                 
            }
          })

        e.preventDefault();
    });

}); 