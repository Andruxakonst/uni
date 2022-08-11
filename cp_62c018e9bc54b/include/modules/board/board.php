<?php 
if( !defined('unisitecms') ) exit;

include( $config["basePath"] . "/" . $config["folder_admin"] . "/include/modules/category_board/fn.php");

$Ads = new Ads();
$Main = new Main();
$CategoryBoard = new CategoryBoard();
$Profile = new Profile();
$Elastic = new Elastic();

$url[] = "route=board";
$query = "";

$_GET["page"] = empty($_GET["page"]) ? 1 : intval($_GET["page"]);

if(isset($_GET['sort'])){
    
if($_GET['sort'] == 1){
    $query = "ads_status=0 and clients_status='1'"; 
    $sort_name = "На модерации";  
}elseif ($_GET['sort'] == 2){                    
    $query = "ads_period_publication > now() and ads_status='1' and clients_status='1'";  
    $sort_name = "Активные";                          
}elseif ($_GET['sort'] == 3){                    
    $query = "(ads_status = '3' or clients_status='2') and clients_status!='3' and ads_status!='8'";  
    $sort_name = "Заблокированные";                          
}elseif ($_GET['sort'] == 4){                    
    $query = "ads_period_publication < now() and ads_status='1' and clients_status='1'";  
    $sort_name = "Истек срок публикации";                          
}elseif ($_GET['sort'] == 5){                    
    $query = "ads_status='4' and clients_status='1'";
    $sort_name = "Зарезервированные";                          
}elseif ($_GET['sort'] == 6){                    
    $query = "ads_status='5' and clients_status='1'";
    $sort_name = "Проданные";                          
}elseif ($_GET['sort'] == 7){                    
    $query = "ads_status='6' and clients_status='1'"; 
    $sort_name = "Ждут оплаты";                          
}elseif ($_GET['sort'] == 8){                    
    $query = "ads_status='7' and clients_status='1'"; 
    $sort_name = "Отклоненные";                          
}elseif ($_GET['sort'] == 9){                    
    $query = "(ads_images='' or ads_images='null' or ads_images='[]') and clients_status!='3' and ads_status!='8'"; 
    $sort_name = "Без фото";                          
}elseif ($_GET['sort'] == 10){                    
    $query = "date(ads_datetime_add) = date(now()) and clients_status!='3' and ads_status!='8'";                           
}elseif ($_GET['sort'] == 11){                    
    $query = "ads_status='8'";                           
}
           
$url[] = 'sort='.$_GET['sort'];

}

if($_GET["search"]){

  $_GET["search"] = clearSearch($_GET["search"]);

  if (!preg_match("/^([0-9])+$/", $_GET["search"])) {                                                

     $query = "(city_name LIKE '%".$_GET["search"]."%' OR clients_email LIKE '%".$_GET["search"]."%' OR ads_id LIKE '%".$_GET["search"]."%' OR clients_phone LIKE '%".$_GET["search"]."%' OR clients_name LIKE '%".$_GET["search"]."%' OR clients_surname LIKE '%".$_GET["search"]."%' OR ads_title LIKE '%".$_GET["search"]."%' OR clients_id_hash LIKE '%".$_GET["search"]."%' OR category_board_name LIKE '%".$_GET["search"]."%') and clients_status!='3' and ads_status!='8'"; 

     $param_search = '
      {
        "query": {
          "bool": {  
            "must" : {
              "query_string" : {
                "query":      "*'.$_GET["search"].'*",
                "fields":     [ "ads_title", "city_name", "category_board_name", "clients_name", "clients_phone", "clients_surname", "clients_email" ],
                "analyze_wildcard": "true",
                "allow_leading_wildcard": "true"
              }
            },
            "filter": [ 
              { "terms":  { "ads_status": [1,2,3,4,5,6,7] }},
              { "terms":  { "clients_status": [0,1,2] }}
            ]
          }
        }
      }
     ';

  }else{

     $query = "ads_id='".intval($_GET["search"])."' and clients_status!='3' and ads_status!='8'"; 

  }


  $url[] = 'search='.$_GET["search"];
   
}else{

    if($_GET["cat_id"]){

       $ids = idsBuildJoin($CategoryBoard->idsBuild($_GET["cat_id"], $CategoryBoard->getCategories()),$_GET["cat_id"]);

       $query = "ads_id_cat IN(".$ids.") and clients_status!='3' and ads_status!='8'";
       $url[] = 'cat_name='.$_GET['cat_name'];
       $url[] = 'cat_id='.$_GET['cat_id'];

    }

}

if(!$query){
   $query = "clients_status!='3' and ads_status!='8'";  
}

$LINK = "?".implode("&",$url);

?>

<div class="row">
   <div class="page-header">
      <div class="d-flex align-items-center">
         <h2 class="page-header-title">Объявления</h2>
      </div>
   </div>
</div>  

<div class="row" >
  <div class="col-lg-12" >
    
      <form method="get" action="<?php echo $config["urlPrefix"].$config["folder_admin"]; ?>" >
        <input type="text" class="form-control" placeholder="Укажите название объявления,города или данные клиента" style="height: 44px;" value="<?php echo $_GET["search"]; ?>" name="search">
        <input type="hidden" name="route" value="board" >
      </form>

  </div>
</div>

<div class="form-group" style="margin-bottom: 20px; margin-top: 25px;" >

 <div class="btn-group mb5" >
   <div class="dropdown">
    <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      Категории <?php if(!empty($_GET["cat_name"])){ echo "(".$_GET["cat_name"].")"; } ?>
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="max-height: 350px; overflow: auto;" >
       <a class="dropdown-item" href="?route=board">Все категории</a>
       <?php echo outCategoryDropdown(); ?>
    </div>
   </div>
 </div>

 <div class="btn-group mb5" >
   <div class="dropdown">
    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      Сортировать <?php if(!empty($sort_name)){ echo "(".$sort_name.")"; } ?>
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
      <a class="dropdown-item" href="?route=board">Без сортировки</a>
      <a class="dropdown-item" href="?route=board&sort=1">На модерации (<?php echo $Ads->statusCount(1); ?>)</a>
      <a class="dropdown-item" href="?route=board&sort=2">Активные (<?php echo $Ads->statusCount(2); ?>)</a>
      <a class="dropdown-item" href="?route=board&sort=3">Заблокированные (<?php echo $Ads->statusCount(3); ?>)</a>
      <a class="dropdown-item" href="?route=board&sort=4">Истек срок публикации (<?php echo $Ads->statusCount(4); ?>)</a>
      <a class="dropdown-item" href="?route=board&sort=5">Зарезервированные (<?php echo $Ads->statusCount(5); ?>)</a>
      <a class="dropdown-item" href="?route=board&sort=6">Проданные (<?php echo $Ads->statusCount(6); ?>)</a>
      <a class="dropdown-item" href="?route=board&sort=7">Ждут оплаты (<?php echo $Ads->statusCount(7); ?>)</a>
      <a class="dropdown-item" href="?route=board&sort=8">Отклоненные (<?php echo $Ads->statusCount(8); ?>)</a>
      <a class="dropdown-item" href="?route=board&sort=9">Без фото (<?php echo $Ads->statusCount(9); ?>)</a>
      <a class="dropdown-item" href="?route=board&sort=11">Удаленные (<?php echo $Ads->statusCount(11); ?>)</a>
    </div>
   </div>
 </div>

 <div class="btn-group mb5" >
   <?php if($_GET['sort'] == 10){ ?>
      <a href="?route=board" class="btn btn-secondary">Объявления за все время</a>
   <?php }else{ ?>
      <a href="?route=board&sort=10" class="btn btn-secondary">Объявления за сегодня</a>
   <?php } ?>
 </div>
 
 <div class="action_group_block" style="display: none;" >
 <div class="btn-group mb5" >
   <div class="dropdown">
    <button class="btn btn-gradient-01 dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      Действия с элементами
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" >
       <a class="dropdown-item action_group" data-action="publication" >Опубликовать</a>
       <a class="dropdown-item action_group" data-action="not_publication" >Снять с публикации</a>
       <a class="dropdown-item action_group" data-action="extend" >Продлить</a>
       <a class="dropdown-item action_group" data-action="delete" >Удалить</a>
    </div>
   </div>
 </div>
 </div>

</div>

<div class="row" >
   <div class="col-lg-12" >
      <div class="widget has-shadow">

         <div class="widget-body">
            <div class="table-responsive">
              <form class="form-data" >

                 <?php
                     
                     if($param_search && $config["elasticsearch"]["status"]){

                        $get = $Ads->getAll( array("navigation"=>false,"page"=>$_GET["page"],"output"=>$_SESSION["ByShow"],"query"=>$query, "sort"=>"ORDER By ads_datetime_add DESC", "param_search" => $param_search) );

                     }else{

                        $get = $Ads->getAll( array("navigation"=>true,"page"=>$_GET["page"],"output"=>$_SESSION["ByShow"],"query"=>$query, "sort"=>"ORDER By ads_datetime_add DESC", "param_search" => $param_search) );

                     }
                      
                     if($get["count"]){   

                     ?>
                     <table class="table mb-0">
                        <thead>
                           <tr>
                            <th><input type="checkbox" class="checkbox_prop" ></th>
                            <th>Объявление</th>
                            <th>Автор</th>
                            <th>Размещено</th>
                            <th>Завершено</th>
                            <th>Статус</th>
                            <th style="text-align: right;" ></th>
                           </tr>
                        </thead>
                        <tbody class="sort-container" >                     
                     <?php

                        foreach($get["all"] AS $value){

                        $image = $Ads->getImages($value["ads_images"]);

                        ?>

                         <tr>
                             <td>
                               <input type="checkbox" class="input_prop_id" value="<?php echo $value["ads_id"]; ?>" name="id[]">
                             </td>
                             <td style="min-width: 300px;" >
                              <div class="item-table-img" >
                                <img src="<?php echo Exists($config["media"]["small_image_ads"],$image[0],$config["media"]["no_image"]); ?>" />
                              </div>
                              <div class="item-table-title">
                              
                              <a target="_blank" href="<?php echo $Ads->alias($value); ?>" target="_blank" title="<?php echo $value["ads_title"]; ?>" ><?php echo custom_substr($value["ads_title"],30); ?></a>
                              
                              <div class="box-labels" >
                                <?php
                                  if($Ads->getStatusSecure($value)){
                                     ?>
                                     <div class="item-secure" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Безопасная сделка" > <i class="la la-shield"></i> </div>
                                     <?php            
                                  }
                                  if($value["ads_auction"]){
                                     ?>
                                     <div class="item-auction" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Аукцион" > <i class="la la-gavel"></i> </div>
                                     <?php
                                  }
                                ?>
                              </div>

                              <br>

                              <i class="la la-bars"></i> <?php echo $value["category_board_name"];?>
                              <i class="la la-map-marker"></i> <?php echo $value["city_name"];?>

                              </div>
                            </td>
                             <td><a href="?route=client_view&id=<?php echo $value["clients_id"]; ?>" ><?php echo custom_substr($Profile->name($value),15); ?></a></td>
                             <td>
                              <?php echo datetime_format_cp($value["ads_datetime_add"]); ?>
                             </td>
                             <td>

                                <?php 
                                if($value["ads_status"] == 1){

                                if( strtotime($value["ads_period_publication"]) > time() ){ 

                                    $start = strtotime($value["ads_period_publication"]) - ($value["ads_period_day"] * 86400);

                                    $progress = ((time() - $start) / (strtotime($value["ads_period_publication"]) - $start)) * 100;

                                ?>
                                
                                <div data-toggle="popover" data-trigger="hover" data-placement="top" data-html="true" data-content="Активно до <?php echo date("d.m.Y", strtotime($value["ads_period_publication"]) ) ?>" >
                                <div class="progress" style="height: 0.5rem!important;"  >
                                  <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo $progress; ?>%" aria-valuenow="<?php echo $progressbar; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                </div>

                                <?php }else{ ?>

                                <div class="progress" style="height: 0.5rem!important;" >
                                  <div class="progress-bar bg-warning" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>

                                <?php
                                 }

                                }else{ ?>

                                <div class="progress" style="height: 0.5rem!important;" >
                                  <div class="progress-bar bg-warning" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>

                                <?php } ?>

                             </td>                             
                             <td>
                               <div class="dropdown">

                                  <?php echo $Ads->adminAdStatus($value); ?>

                               </div>
                             </td> 

                             <td class="td-actions"  style="text-align: right; min-width: 150px;" >
                              <a href="?route=ads_change&id=<?php echo $value["ads_id"]; ?>" ><i class="la la-question-circle edit"></i></a>
                              <a href="<?php echo _link("ad/update/".$value["ads_id"]); ?>" target="_blank" ><i class="la la-edit edit"></i></a>
                              <a href="#" class="delete-ads" data-id="<?php echo $value["ads_id"]; ?>" ><i class="la la-close delete"></i></a>
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
                            <div class="plug" >
                               <i class="la la-exclamation-triangle"></i>
                               <p>Объявлений нет</p>
                            </div>
                         <?php

                     } 

                  ?>

              </form>
            </div>
         </div>
      </div>
   </div>
</div>

<?php if(!$param_search || !$config["elasticsearch"]["status"]){ ?>
<ul class="pagination">  
 <?php echo out_navigation( array("count"=>$get["count"], "output"=>$_SESSION["ByShow"], "url"=>$LINK, "prev"=>'<i class="la la-long-arrow-left"></i>', "next"=>'<i class="la la-arrow-right"></i>', "page_count" => $_GET["page"], "page_variable" => "page" ) );?>
</ul>
<?php } ?>

<script type="text/javascript" src="include/modules/board/script.js"></script>
 