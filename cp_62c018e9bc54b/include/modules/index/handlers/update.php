<?php

define('unisitecms', true);
session_start();

$config = require "../../../../../config.php";
include_once( $config["basePath"] . "/systems/classes/UniSite.php");
include_once( $config["basePath"] . "/" . $config["folder_admin"] . "/lang/" . $settings["lang_admin_default"].".php" );

if( !(new Admin())->accessAdmin($_SESSION['cp_control_statistics']) ){
   exit;
}

$Main = new Main();
$Ads = new Ads();
$Profile = new Profile();
$Geo = new Geo();
$Mobile_Detect = new Mobile_Detect;

if(isAjax() == true){

    $now = date("Y-m-d");

    $x=0;
    while ($x++<6){
       $week[ date('Y-m-d', strtotime("-".$x." day")) ] = date('Y-m-d', strtotime("-".$x." day"));
    }

    $week[ date('Y-m-d') ] = date('Y-m-d');

    ksort($week);


    foreach ($week as $key => $value) {

       $getClients = getOne("select count(*) as total from uni_clients where clients_status!='3' and date(clients_datetime_add) = '".$value."'");
       $data_clients[] = array( $value ,intval($getClients["total"]) );

       $getSubscribe = getOne("select count(*) as total from uni_subscription where date(subscription_datetime_add) = '".$value."'");
       $data_subscribe[] = array( $value ,intval($getSubscribe["total"]) );

       $getAds = getOneCache("select count(*) as total from uni_ads where ads_status!='8' and date(ads_datetime_add) = '".$value."'");
       $data_ads[] = array( $value ,intval($getAds["total"]) );

       $getOrders = getOne("select sum(orders_price) as total from uni_orders where orders_status_pay=1 and date(orders_date) = '".$value."'");
       $data_orders[] = array( $value ,round($getOrders["total"],2) );

       $getTraffic = getOne("select count(*) as total from uni_metrics where date(date_view) = '".$value."'");
       $data_traffic[] = array( $value ,intval($getTraffic["total"]) );

    }


    $getAds = $Ads->getAll( array( "query" => "ads_status=0 and clients_status!=3 and clients_status!=2", "sort" => "order by ads_datetime_add desc" ) );

    ob_start();
    require __dir__ . "/include/list-ads.php";
    $list_ads = ob_get_clean();

    $getUsers = getAll("select * from uni_clients where clients_status!='3' and unix_timestamp(clients_datetime_view)+3*60 > unix_timestamp(NOW())");

    ob_start();
    require __dir__ . "/include/list-users.php";
    $list_users = ob_get_clean();
    
    $where = $settings["statistics_variant"] == 1 ? "" : "where date(date_view) = '$now'";
    if($where){
      $countMetrics = getOne("select count(*) as total from uni_metrics $where")["total"];
    }else{
      $countMetrics = getOne("select count(*) as total from uni_metrics where unique_visit=1")["total"];
    }
    $count = getOne("select count(*) as total from uni_metrics $where")["total"];
    $getMetrics = getAll("SELECT * FROM uni_metrics $where order by date_view desc " . navigation_offset( array( "count"=>$count, "output"=>20, "page"=>$_POST["page"] ) )  );
    
    ob_start();
    require __dir__ . "/include/list-traffic.php";
    $list_traffic = ob_get_clean(); 

    $getLogs = getAll("SELECT * FROM uni_notifications order by id desc");   

    ob_start();
    require __dir__ . "/include/list-log-action.php";
    $list_log_action = ob_get_clean();

    if($settings["statistics_variant"] == 1){
  
      echo json_encode( 
                  array( 

                    "clients" => array( "count" => (int)getOne("select count(*) as total from uni_clients where clients_status!='3'")["total"], "data" => $data_clients ), 
                    "subscribe" => array( "count" => (int)getOne("select count(*) as total from uni_subscription")["total"], "data" => $data_subscribe ), 
                    "ads" => array( "count" => (int)getOneCache("select count(*) as total from uni_ads INNER JOIN `uni_clients` ON `uni_clients`.clients_id = `uni_ads`.ads_id_user where ads_status!='8' and clients_status!='3'")["total"], "data" => $data_ads ), 
                    "orders" => array( "count" => $Main->price(getOne("select sum(orders_price) as total from uni_orders where orders_status_pay=1")["total"]) , "data" => $data_orders ), 
                    "traffic" => array( "count" => (int)getOne("select count(*) as total from uni_metrics where date(date_view) = '$now'")["total"] , "data" => $data_traffic ), 
                    "list_ads" => $list_ads,
                    "list_users" => $list_users,
                    "list_traffic" => $list_traffic,
                    "list_log_action" => $list_log_action,

                  ) 
      );

    }else{

      echo json_encode( 
                  array( 

                    "clients" => array( "count" => (int)getOne("select count(*) as total from uni_clients where clients_status!='3' and date(clients_datetime_add)='$now'")["total"], "data" => $data_clients ), 
                    "subscribe" => array( "count" => (int)getOne("select count(*) as total from uni_subscription where date(subscription_datetime_add)='$now'")["total"], "data" => $data_subscribe ), 
                    "ads" => array( "count" => (int)getOneCache("select count(*) as total from uni_ads INNER JOIN `uni_clients` ON `uni_clients`.clients_id = `uni_ads`.ads_id_user where ads_status!='8' and clients_status!='3' and date(ads_datetime_add)='$now'")["total"], "data" => $data_ads ), 
                    "orders" => array( "count" => $Main->price(getOne("select sum(orders_price) as total from uni_orders where orders_status_pay=1 and date(orders_date)='$now'")["total"]) , "data" => $data_orders ), 
                    "traffic" => array( "count" => (int)getOne("select count(*) as total from uni_metrics where date(date_view) = '$now'")["total"] , "data" => $data_traffic ), 
                    "list_ads" => $list_ads,
                    "list_users" => $list_users,
                    "list_traffic" => $list_traffic,
                    "list_log_action" => $list_log_action,

                  ) 
      );

    }

}
?>