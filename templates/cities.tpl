<!doctype html>
<html lang="<?php echo getLang(); ?>">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title><?php echo $ULang->t("Все города"); ?></title>

    <?php include $config["template_path"] . "/head.tpl"; ?>

  </head>

  <body data-prefix="<?php echo $config["urlPrefix"]; ?>" data-template="<?php echo $config["template_folder"]; ?>" >
    
    <?php include $config["template_path"] . "/header.tpl"; ?>

    <div class="container" >
       
       <nav aria-label="breadcrumb">
 
          <ol class="breadcrumb" itemscope="" itemtype="http://schema.org/BreadcrumbList">

            <li class="breadcrumb-item" itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
              <a itemprop="item" href="<?php echo $config["urlPath"]; ?>">
              <span itemprop="name"><?php echo $ULang->t("Главная"); ?></span>
              </a>
              <meta itemprop="position" content="1">
            </li>

            <li class="breadcrumb-item" itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
              <span itemprop="name"><?php echo $ULang->t("Все города"); ?></span>
              <meta itemprop="position" content="2">
            </li>                 
          </ol>

        </nav>
          
        <div class="row" >
            <div class="col-lg-12" >

              <h1 class="cities-list-title" ><?php echo $ULang->t("Выберите город"); ?></h1>

              <div class="cities-list minheight400" >

              <?php
               if( !$_GET["country"] ){

                   if( $_SESSION["geo"]["data"] ){
                       $country_alias = $_SESSION["geo"]["data"]["country_alias"];
                   }else{
                       $country_alias = $settings["country_default"];
                   }

               }else{

                   $country_alias = clear( $_GET["country"] );

               }

               if($_SESSION["temp_change_category"]["category_board_chain"]){
                   $all_cities = _link( $country_alias . "/" . $_SESSION["temp_change_category"]["category_board_chain"] );
               }else{
                   $all_cities = _link( $country_alias );
               }

              ?>
              
              <div class="row" >

               <div class="col-lg-3 col-3 col-sm-2 col-md-2" ></div>
               <div class="col-lg-9 col-9 col-sm-10 col-md-10" >
               <div class="row" >
               <div class="col-lg-4 col-md-4 col-sm-4 col-12" ><a class="citiesLinkCountry" href="<?php echo $all_cities; ?>" ><?php echo $ULang->t("Выбрать все города"); ?></a></div>
               </div>
               </div>

               <div class="col-lg-12" >
                  <div class="mt10" ></div>
               </div>

              <?php  

                $getCountry = getAll("SELECT * FROM `uni_country` WHERE country_status = '1' order by country_name asc");

                if( count($getCountry) ){

                   if( count($getCountry) > 1 ){

                       ?>
                       <div class="col-lg-3 col-3 col-sm-2 col-md-2" ></div>
                       <div class="col-lg-9 col-9 col-sm-10 col-md-10" >
                           <div class="row" >
                           <?php

                           foreach ($getCountry as $key => $value) {

                              if( $value["country_alias"] == $country_alias ){ $activeCountry = 'cities_active_country'; }else{ $activeCountry = ''; }

                              echo '<div class="col-lg-4 col-md-4 col-sm-4 col-12" ><a class="citiesLinkCountry '.$activeCountry.'" href="'._link( "cities" ).'?country='.$value["country_alias"].'" >'.$ULang->t( $value["country_name"] , [ "table"=>"geo", "field"=>"geo_name" ] ).'</a></div>';
                           }

                           ?>

                           </div>
                       </div>

                       <div class="col-lg-12" >
                          <div class="mt20" ></div>
                       </div>
                       <?php

                   }

                }

                $getCities = getAll("SELECT * FROM uni_city INNER JOIN `uni_country` ON `uni_country`.country_id = `uni_city`.country_id WHERE `uni_country`.country_status = '1' and `uni_country`.country_alias='".$country_alias."' order by city_alias asc");
                
                if(count($getCities)){

                foreach ($getCities as $key => $value) {

                      $value["city_name"] = $ULang->t( $value["city_name"], [ "table" => "geo", "field" => "geo_name" ] );
                      
                      if($_SESSION["temp_change_category"]["category_board_chain"]){
                         $alias = _link( $value["city_alias"] . "/" . $_SESSION["temp_change_category"]["category_board_chain"] );
                      }else{
                         $alias = _link( $value["city_alias"] );
                      }

                      $list[ mb_substr( mb_strtoupper($value["city_name"] , "UTF-8"), 0, 1, "UTF-8") ][$value["city_name"]] = '<a href="'.$alias.'" >' . $value["city_name"] . '</a>';
                      
                }

                foreach ($list as $letter => $nested) {

                  $alphabet .= '<span data-id="letter-'.translite($letter).'" >'.$letter.'</span>';

                  ?>
                  <div class="col-lg-3 col-3 col-sm-2 col-md-2" > <p class="cities-letter" id="letter-<?php echo translite($letter); ?>" ><?php echo $letter; ?></p> </div>
                  <div class="col-lg-9 col-9 col-sm-10 col-md-10" >
                      <div class="row mb25" >
                      <?php

                      $length = floor(count($list) / 4);

                      $array_chunk = array_chunk($nested, $length ? $length : 1, true);

                      foreach ($array_chunk as $key1 => $value1) {
                          ?>
                          <div class="col-lg-4 col-md-4 col-sm-4 col-12" >
                          <?php
                          foreach ($value1 as $key2 => $value2) {
                              echo $value2;
                          }
                          ?>
                          </div>
                          <?php
                      }

                      ?>
                      </div>
                  </div>
                  <?php

                }

                }
              ?>
              </div>

              </div>

            </div>
        </div>
         
          
       <div class="mt50" ></div>


    </div>


    <?php include $config["template_path"] . "/footer.tpl"; ?>


  </body>
</html>