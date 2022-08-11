<?php  

   if(count($getLogs) > 0){   

   ?>
   <h3 class="widget-title" >Уведомления</h3>

   <table class="table mb-0">
      <thead>
         <tr>
            <th>Действия</th>
            <th></th>
         </tr>
      </thead>
      <tbody> 

      <?php

      foreach($getLogs AS $value){
         
      ?>

         <tr class="item-notification" >
            <td><a href="<?php echo $value['link']; ?>"><?php echo $value['title']; ?> <span class="label-count" ><?php echo $value['count']; ?></span></a></td>
            <td class="td-actions" style="text-align: right;" >
               <a href="#" class="delete-notification" data-id="<?php echo $value['id']; ?>"><i class="la la-close delete"></i></a>
            </td>
         </tr>
                    
       <?php                                         
      } 

      ?>
      </tbody>
      </table>
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