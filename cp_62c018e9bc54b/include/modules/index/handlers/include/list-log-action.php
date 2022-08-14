<div class="widget-list-log-action" >
<?php  

   if(count($getLogs)){   

   ?>
      <div class="widget-title-flex" >
         <h3 class="widget-title" >Уведомления</h3> <span class="delete-notification" >Очистить</span>
      </div>

      <div class="widget-list-log-action-item" >
         
         <?php
         foreach($getLogs AS $value){
          ?>
            <div><a href="<?php echo $value['link']; ?>"><?php echo $value['title']; ?> <span class="label-count" ><?php echo $value['count']; ?></span></a> </div>       
          <?php                                         
         } 
         ?>

      </div>

      <?php               
   }else{
      ?>
        <div class="infoIcon" >
          <span><i class="la la-exclamation-circle"></i></span>
          <p>Уведомлений нет</p>
        </div>
      <?php
   }                  
?>
</div>