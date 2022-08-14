<?php

session_start();
define('unisitecms', true);

$config = require "./../../config.php";
include_once( $config["basePath"] . "/systems/classes/UniSite.php" );

verify_csrf_token();

$Profile = new Profile();
$Main = new Main();
$Admin = new Admin();
$Geo = new Geo();
$Blog = new Blog();
$Banners = new Banners();
$ULang = new ULang();

if(isAjax() == true){

  if($_POST["action"] == "add_comment"){

      if(!$_SESSION['profile']['id']){ echo json_encode( ["status"=>false] ); exit; }

      $id_article = (int)$_POST["id_article"];
      $id_msg = (int)$_POST["id_msg"];
      $text = clear($_POST["text"]);

      if($id_msg){
         if( $_POST["token"] != md5($config["private_hash"].$id_msg.$id_article) ){
             echo json_encode( ["status"=>false] ); exit;
         }
      }

      if($text){

         insert("INSERT INTO uni_blog_comments(blog_comments_id_user,blog_comments_text,blog_comments_date,blog_comments_id_parent,blog_comments_id_article)VALUES(?,?,?,?,?)", [$_SESSION['profile']['id'],$text,date("Y-m-d H:i:s"),$id_msg,$id_article]);

         echo json_encode( ["status"=>true] );

      }else{
         echo json_encode( ["status"=>false] );
      }

  }

  if($_POST["action"] == "delete_comment"){

     $id = intval($_POST["id"]);

     if( $_SESSION['cp_auth'][ $config["private_hash"] ] ){

         $getMsg = findOne("uni_blog_comments", "blog_comments_id=?", [$id]);

         $nested_ids = idsBuildJoin($Blog->idsComments($id,$Blog->getComments($getMsg["blog_comments_id_article"])),$id);
        
         if($nested_ids){
            foreach (explode(",", $nested_ids) as $key => $value) {
              
               update( "delete from uni_blog_comments where blog_comments_id=?", array( $value ) );

            }
         }

     }else{
    
         $getMsg = findOne("uni_blog_comments", "blog_comments_id=? and blog_comments_id_user=?", [$id,intval($_SESSION["profile"]["id"])]);
         
         $nested_ids = idsBuildJoin($Blog->idsComments($id,$Blog->getComments($getMsg["blog_comments_id_article"])),$id);

         if($nested_ids && $getMsg){
            foreach (explode(",", $nested_ids) as $key => $value) {
              
               update( "delete from uni_blog_comments where blog_comments_id=?", array( $value ) );

            }
         }

     }

     echo json_encode( ["status"=>true] );

  }  



}

?>