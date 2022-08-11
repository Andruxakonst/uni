<?php 
if( !defined('unisitecms') ) exit;

if(!$_SESSION["cp_control_settings"]){
    header("Location: ?route=index");
}

$social_auth_params = json_decode(decrypt($settings["social_auth_params"]), true);

?>


<div class="row">
   <div class="page-header">
      <div class="d-flex align-items-center">
         <h2 class="page-header-title">Настройки</h2>
      </div>
   </div>
</div>



<div class="row">
<div class="col-lg-12">

<div class="widget has-shadow">

   <div class="widget-body">
      <ul class="nav nav-tabs nav-fill" role="tablist">
         <li class="nav-item">
            <a class="nav-link <?php if(empty($_GET["tab"])){ echo 'active show'; } ?>" data-route="?route=settings" id="just-tab-1" data-toggle="tab" href="#j-tab-1" role="tab" aria-controls="j-tab-1" aria-selected="false">Основное</a>
         </li>
         <li class="nav-item">
            <a class="nav-link <?php if($_GET["tab"] == "alert"){ echo 'active show'; } ?>" data-route="?route=settings&tab=alert" id="just-tab-2" data-toggle="tab" href="#j-tab-2" role="tab" aria-controls="j-tab-2" aria-selected="false">Оповещения</a>
         </li>         
         <li class="nav-item">
            <a class="nav-link <?php if($_GET["tab"] == "currency"){ echo 'active show'; } ?>" data-route="?route=settings&tab=currency" id="just-tab-3" data-toggle="tab" href="#j-tab-3" role="tab" aria-controls="j-tab-3" aria-selected="true">Валюта</a>
         </li>
         <li class="nav-item">
            <a class="nav-link <?php if($_GET["tab"] == "access"){ echo 'active show'; } ?>" data-route="?route=settings&tab=access" id="just-tab-4" data-toggle="tab" href="#j-tab-4" role="tab" aria-controls="j-tab-4" aria-selected="false">Доступ</a>
         </li>
         <li class="nav-item">
            <a class="nav-link <?php if($_GET["tab"] == "emailmessage"){ echo 'active show'; } ?>" data-route="?route=settings&tab=emailmessage" id="just-tab-5" data-toggle="tab" href="#j-tab-5" role="tab" aria-controls="j-tab-5" aria-selected="false">Шаблоны email писем</a>
         </li>
         <li class="nav-item">
            <a class="nav-link <?php if($_GET["tab"] == "watermark"){ echo 'active show'; } ?>" data-route="?route=settings&tab=watermark" id="just-tab-6" data-toggle="tab" href="#j-tab-6" role="tab" aria-controls="j-tab-6" aria-selected="false">Watermark</a>
         </li>
         <li class="nav-item">
            <a class="nav-link <?php if($_GET["tab"] == "email_mode"){ echo 'active show'; } ?>" data-route="?route=settings&tab=email_mode" id="just-tab-7" data-toggle="tab" href="#j-tab-7" role="tab" aria-controls="j-tab-7" aria-selected="false">Рассылка</a>
         </li>
          <li class="nav-item">
            <a class="nav-link <?php if($_GET["tab"] == "code_script"){ echo 'active show'; } ?>" data-route="?route=settings&tab=code_script" id="just-tab-8" data-toggle="tab" href="#j-tab-8" role="tab" aria-controls="j-tab-8" aria-selected="false">Скрипты/Meta теги</a>
         </li>
          <li class="nav-item dropdown">
             <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
             Еще
             <i class="ion-android-arrow-dropdown"></i>
             </a>
             <div class="dropdown-menu" x-placement="bottom-start">

                <a class="dropdown-item" data-route="?route=settings&tab=integrations" id="just-tab-9" data-toggle="tab" href="#j-tab-9" role="tab" aria-controls="j-tab-9" aria-selected="false">Интеграции</a>              
                <a class="dropdown-item" data-route="?route=settings&tab=payments" id="just-tab-10" data-toggle="tab" href="#j-tab-10" role="tab" aria-controls="j-tab-10" aria-selected="false">Платежные системы</a>
                <?php if($settings["functionality"]["secure"]){ ?>
                <a class="dropdown-item" data-route="?route=settings&tab=secure" id="just-tab-14" data-toggle="tab" href="#j-tab-14" role="tab" aria-controls="j-tab-14" aria-selected="false">Безопасная сделка</a>
                <?php } ?>
                <a class="dropdown-item" data-route="?route=settings&tab=sitemap" id="just-tab-11" data-toggle="tab" href="#j-tab-11" role="tab" aria-controls="j-tab-11" aria-selected="false">Sitemap</a>
                <a class="dropdown-item" data-route="?route=settings&tab=robots" id="just-tab-12" data-toggle="tab" href="#j-tab-12" role="tab" aria-controls="j-tab-12" aria-selected="false">Robots.txt</a>

                <a class="dropdown-item" data-route="?route=settings&tab=lang" id="just-tab-13" data-toggle="tab" href="#j-tab-13" role="tab" aria-controls="j-tab-12" aria-selected="false">Языки</a>

             </div>
          </li>                                                                    
      </ul>
      <form class="form-data" >
      <div class="tab-content pt-3">
         <div class="tab-pane fade <?php if(empty($_GET["tab"])){ echo 'active show'; } ?>" id="j-tab-1" role="tabpanel" aria-labelledby="just-tab-1">
         
         <br>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Основной логотип</label>
                <div class="col-lg-9">
                   <div class="settings-change-img settings-logo" >

                     <?php echo img( array( "img1" => array( "class" => "load-defaul-logo", "path" => $config["media"]["other"] . "/" . $settings["logo-image"], "width" => "100px" ), "img2" => array( "class" => "load-defaul-logo", "path" => $config["media"]["other"] . "/icon_photo_add.png", "width" => "60px" ) ) ); ?>

                     <input type="file" class="input-defaul-logo" name="logo" >
                   </div>
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Мобильный логотип</label>
                <div class="col-lg-9">
                   <div class="settings-change-img settings-logo" >

                     <?php echo img( array( "img1" => array( "class" => "load-mobile-logo", "path" => $config["media"]["other"] . "/" . $settings["logo-image-mobile"], "width" => "32px" ), "img2" => array( "class" => "load-mobile-logo", "path" => $config["media"]["other"] . "/icon_photo_add.png", "width" => "60px" ) ) ); ?>

                     <input type="file" class="input-mobile-logo" name="logo-mobile" >
                   </div>
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Инверсия цвета</label>
                <div class="col-lg-9">
                    <label>
                      <input class="toggle-checkbox-sm" type="checkbox" name="logo_color_inversion" value="1" <?php if($settings["logo_color_inversion"] == 1){ echo ' checked=""'; } ?> >
                      <span><span></span></span>
                    </label>
                    <div><small>Цвет логотипа отображается белым на темном фоне.</small></div>
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Favicon</label>
                <div class="col-lg-9">
                   <div class="settings-change-img" >
                     
                      <?php echo img( array( "img1" => array( "class" => "load-favicon", "path" => $settings["favicon-image"], "width" => "32px" ), "img2" => array( "class" => "load-favicon", "path" => $config["media"]["other"] . "/icon_photo_add.png", "width" => "60px" ) ) ); ?>

                     <input type="file" class="input-favicon" name="favicon" >
                   </div>
                   <div><small>Рекомендуемый размер иконки 120x120 с расширением png.</small></div>
                </div>
             </div>    

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label"></label>
                <div class="col-lg-9">
                   <h3 style="margin-top: 15px" class="setting-tab-toggle" data-tab="1" > <strong>Системное <i class="la la-angle-down"></i></strong> </h3>
                </div>
             </div>

             <div class="setting-tab-content" data-tab="1" >

             <?php if($settings["functionality"]["secure"]){ ?>    
             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Безопасные сделки</label>
                <div class="col-lg-9">
                    <label>
                      <input class="toggle-checkbox-sm" type="checkbox" name="secure_status" value="1" <?php if($settings["secure_status"] == 1){ echo ' checked=""'; } ?> >
                      <span><span></span></span>
                    </label>
                </div>
             </div>
             <?php } ?>
             
             <?php if($settings["functionality"]["multilang"]){ ?>
             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Мультиязычность</label>
                <div class="col-lg-9">
                    <label>
                      <input class="toggle-checkbox-sm" type="checkbox" name="visible_lang_site" value="1" <?php if($settings["visible_lang_site"] == 1){ echo ' checked=""'; } ?> >
                      <span><span></span></span>
                    </label>
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Автоматическое определение языка</label>
                <div class="col-lg-9">
                    <label>
                      <input class="toggle-checkbox-sm" type="checkbox" name="auto_lang_detection" value="1" <?php if($settings["auto_lang_detection"] == 1){ echo ' checked=""'; } ?> >
                      <span><span></span></span>
                    </label>
                </div>
             </div>             
             <?php } ?>
                  
             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Скрывать страницы от индексации без объявлений</label>
                <div class="col-lg-9">
                    <label>
                      <input class="toggle-checkbox-sm" type="checkbox" name="seo_empty_page" value="1" <?php if($settings["seo_empty_page"] == 1){ echo ' checked=""'; } ?> >
                      <span><span></span></span>
                    </label>
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Сокращать цену объявления</label>
                <div class="col-lg-9">
                    <label>
                      <input class="toggle-checkbox-sm" type="checkbox" name="abbreviation_million" value="1" <?php if($settings["abbreviation_million"] == 1){ echo ' checked=""'; } ?> >
                      <span><span></span></span>
                    </label>
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Сжимать css и js файлы</label>
                <div class="col-lg-9">
                    <label>
                      <input class="toggle-checkbox-sm" type="checkbox" name="assets_vendors" value="1" <?php if($settings["assets_vendors"] == 1){ echo ' checked=""'; } ?> >
                      <span><span></span></span>
                    </label>
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Метод вывод контента</label>
                <div class="col-lg-9">
                    <select name="type_content_loading" class="selectpicker" >
                       <option value="1" <?php if($settings["type_content_loading"] == 1){ echo 'selected=""'; } ?> >По нажатию на кнопку "Показать еще"</option>
                       <option value="2" <?php if($settings["type_content_loading"] == 2){ echo 'selected=""'; } ?> >При прокрутки страницы</option>
                    </select>
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Объявления на главной странице</label>
                <div class="col-lg-9">
                    <select name="index_out_content_method" class="selectpicker" >
                       <option value="0" <?php if($settings["index_out_content_method"] == 0){ echo 'selected=""'; } ?> >Отображать все объявления</option>
                       <option value="1" <?php if($settings["index_out_content_method"] == 1){ echo 'selected=""'; } ?> >Отображать с учетом геопозиции</option>
                    </select>
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Метод сортировки объявлений</label>
                <div class="col-lg-9">
                    <select name="ads_sorting_variant" class="selectpicker" >
                       <option value="0" <?php if($settings["ads_sorting_variant"] == 0){ echo 'selected=""'; } ?> >Отображать новые объявления в начале списка</option>
                       <option value="1" <?php if($settings["ads_sorting_variant"] == 1){ echo 'selected=""'; } ?> >Отображать новые объявления в конце списка</option>
                    </select>
                    <div class="mt10" >
                      <small>Сортировка объявлений также будет зависеть от подключенных услуг.</small>
                    </div>
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Часовой пояс</label>
                <div class="col-lg-9">
                   <select name="main_timezone" class="selectpicker" data-live-search="true" >
                      
                      <?php
                      if($config["timezone"]){
                        foreach ($config["timezone"] as $name => $value) {
                           ?>
                           <option <?php if($name == $settings["main_timezone"]){ echo 'selected=""'; } ?> value="<?php echo $name; ?>" ><?php echo $name; ?></option>
                           <?php
                        }
                      }
                      ?>

                   </select>

                </div>
             </div>
             
             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Язык сайта</label>
                <div class="col-lg-9">
                   <select name="lang_site_default" class="selectpicker" >
		            <?php 

		               $get = getAll("SELECT * FROM uni_languages order by id_position asc");
		               
		                if (count($get) > 0)
		                {             
		                     foreach($get AS $array_data){
		                        if($settings["lang_site_default"] == $array_data["iso"]){ $active = 'selected=""'; }else{ $active = ''; }
		                        echo '<option '.$active.' value="'.$array_data["iso"].'" >'.$array_data["name"].'</option>';
		                     }
		                }     

		            ?>
                   </select>
                </div>
             </div>

             <div class="form-group row d-flex mb-5">
                <label class="col-lg-3 form-control-label">Радиус отображения объявлений в ближайших городах</label>
                <div class="col-lg-2">

                    <div class="input-group mb-2">
                       <input type="number" step="any" class="form-control" name="catalog_city_distance" value="<?php echo $settings["catalog_city_distance"]; ?>" >
                       <div class="input-group-prepend">
                          <div class="input-group-text">км</div>
                       </div>                       
                    </div>

                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Объявлений в каталоге по умолчанию</label>
                <div class="col-lg-2">
                    <input type="number" name="catalog_out_content" class="form-control" value="<?php echo $settings["catalog_out_content"]; ?>"  >
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Магазинов в каталоге по умолчанию</label>
                <div class="col-lg-2">
                    <input type="number" name="shops_out_content" class="form-control" value="<?php echo $settings["shops_out_content"]; ?>"  >
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Объявлений на главной странице по умолчанию</label>
                <div class="col-lg-2">
                    <input type="number" name="index_out_content" class="form-control" value="<?php echo $settings["index_out_content"]; ?>"  >
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Магазинов на главной странице по умолчанию</label>
                <div class="col-lg-2">
                    <input type="number" name="index_out_count_shops" class="form-control" value="<?php echo $settings["index_out_count_shops"]; ?>"  >
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Публикаций в блоге по умолчанию</label>
                <div class="col-lg-2">
                    <input type="number" name="blog_out_content" class="form-control" value="<?php echo $settings["blog_out_content"]; ?>"  >
                </div>
             </div>



             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label"></label>
                <div class="col-lg-9">
                   <h3 style="margin-top: 15px" class="setting-tab-toggle" data-tab="2" > <strong>Локация <i class="la la-angle-down"></i></strong> </h3>
                </div>
             </div>

             <div class="setting-tab-content" data-tab="2" >

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Автоматическое определение города посетителя</label>
                <div class="col-lg-9">
                    <label>
                      <input class="toggle-checkbox-sm" type="checkbox" name="city_auto_detect" value="1" <?php if($settings["city_auto_detect"] == 1){ echo ' checked=""'; } ?> >
                      <span><span></span></span>
                    </label>
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Основная страна сайта</label>
                <div class="col-lg-9">
                   <select name="country_default" class="selectpicker" >
      		            <?php 
      		                $country = getAll("SELECT * FROM uni_country WHERE country_status = 1 order by country_name asc");
      		                if(count($country) > 0){
      		                  foreach ($country as $key => $value) {
      		                    if($settings["country_default"] == $value["country_alias"]){
      		                      $selected = 'selected=""';
      		                    }else{
      		                      $selected = '';
      		                    }
      		                    ?>
      		                      <option data-id="<?php echo $value["country_id"]; ?>" value="<?php echo $value["country_alias"]; ?>" <?php echo $selected; ?> ><?php echo $value["country_name"]; ?></option>
      		                    <?php
      		                  }
      		                }
      		             ?>
                   </select>
                </div>
             </div>  

             <div class="settings-region-box" >
              
               <div class="form-group row d-flex align-items-center mb-5">
                  <label class="col-lg-3 form-control-label">Регион</label>
                  <div class="col-lg-9">
                     <select name="region_id" class="selectpicker" >
                        <option value="0" >Все регионы</option>
                        <?php 
                            $region = getAll("SELECT * FROM uni_region WHERE country_id = '".intval($settings["country_id"])."' order by region_name asc");
                            if(count($region) > 0){
                              foreach ($region as $key => $value) {
                                if($settings["region_id"] == $value["region_id"]){
                                  $selected = 'selected=""';
                                }else{
                                  $selected = '';
                                }
                                ?>
                                  <option value="<?php echo $value["region_id"]; ?>" <?php echo $selected; ?> ><?php echo $value["region_name"]; ?></option>
                                <?php
                              }
                            }
                         ?>
                     </select>
                  </div>
               </div>

               <div class="settings-city-box" >
                <?php if($settings["region_id"]){ ?>
                 <div class="form-group row d-flex align-items-center mb-5">
                    <label class="col-lg-3 form-control-label">Город</label>
                    <div class="col-lg-9">
                       <select name="city_id" class="selectpicker" >
                          <option value="0" >Все города</option>
                          <?php 
                              $city = getAll("SELECT * FROM uni_city WHERE region_id = '".intval($settings["region_id"])."' order by city_name asc");
                              if(count($city) > 0){
                                foreach ($city as $key => $value) {
                                  if($settings["city_id"] == $value["city_id"]){
                                    $selected = 'selected=""';
                                  }else{
                                    $selected = '';
                                  }
                                  ?>
                                    <option value="<?php echo $value["city_id"]; ?>" <?php echo $selected; ?> ><?php echo $value["city_name"]; ?></option>
                                  <?php
                                }
                              }
                           ?>
                       </select>
                    </div>
                 </div>
                <?php } ?> 
               </div>
               
             </div>

             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label"></label>
                <div class="col-lg-9">
                   <h3 style="margin-top: 15px" class="setting-tab-toggle" data-tab="9" > <strong>Профиль <i class="la la-angle-down"></i></strong> </h3>
                </div>
             </div>

             <div class="setting-tab-content" data-tab="9" >
               
               <div class="form-group row d-flex align-items-center mb-5">
                  <label class="col-lg-3 form-control-label">Минимальная сумма пополнения баланса</label>
                  <div class="col-lg-2">
                     
                    <div class="input-group mb-2">
                       <input type="number" step="any" class="form-control" name="min_deposit_balance" value="<?php echo $settings["min_deposit_balance"]; ?>" >
                       <div class="input-group-prepend">
                          <div class="input-group-text"><?php echo $settings["currency_main"]["sign"]; ?></div>
                       </div>                       
                    </div>                     

                  </div>
               </div>

               <div class="form-group row d-flex align-items-center mb-5">
                  <label class="col-lg-3 form-control-label">Максимальная сумма пополнения баланса</label>
                  <div class="col-lg-2">
                     
                    <div class="input-group mb-2">
                       <input type="number" step="any" class="form-control" name="max_deposit_balance" value="<?php echo $settings["max_deposit_balance"]; ?>" >
                       <div class="input-group-prepend">
                          <div class="input-group-text"><?php echo $settings["currency_main"]["sign"]; ?></div>
                       </div>                       
                    </div>

                  </div>
               </div>

             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label"></label>
                <div class="col-lg-9">
                   <h3 style="margin-top: 15px" class="setting-tab-toggle" data-tab="11" > <strong>Магазины <i class="la la-angle-down"></i></strong> </h3>
                </div>
             </div>

             <div class="setting-tab-content" data-tab="11" >

               <div class="form-group row d-flex align-items-center mb-5">
                  <label class="col-lg-3 form-control-label">Использовать магазины</label>
                  <div class="col-lg-9">
                      <label>
                        <input class="toggle-checkbox-sm" type="checkbox" name="user_shop_status" value="1" <?php if($settings["user_shop_status"] == 1){ echo ' checked=""'; } ?> >
                        <span><span></span></span>
                      </label>
                  </div>
               </div>

               <div class="form-group row d-flex align-items-center mb-5">
                  <label class="col-lg-3 form-control-label">Алиас для каталога</label>
                  <div class="col-lg-2">
                     
                     <input type="text" name="user_shop_alias_url_all" class="form-control" value="<?php echo $settings["user_shop_alias_url_all"]; ?>" >
                     
                     <div class="mt10" >
                        <small>Укажите название url строки которая будет вести на все магазины</small>
                     </div>

                  </div>
               </div>

               <div class="form-group row d-flex align-items-center mb-5">
                  <label class="col-lg-3 form-control-label">Алиас для магазина</label>
                  <div class="col-lg-2">
                     
                     <input type="text" name="user_shop_alias_url_page" class="form-control" value="<?php echo $settings["user_shop_alias_url_page"]; ?>" >
                     
                     <div class="mt10" >
                        <small>Укажите название url строки которая будет вести на магазин</small>
                     </div>

                  </div>
               </div>

               <div class="form-group row d-flex align-items-center mb-5">
                  <label class="col-lg-3 form-control-label">Лимит слайдов</label>
                  <div class="col-lg-2">
                     
                     <input type="number" step="any" name="user_shop_count_sliders" class="form-control" value="<?php echo $settings["user_shop_count_sliders"]; ?>" >

                     <div class="mt10" >
                        <small>Укажите максимальное количество слайдов которые могут добавлять пользователи в слайдер</small>
                     </div>

                  </div>
               </div>

               <div class="form-group row d-flex align-items-center mb-5">
                  <label class="col-lg-3 form-control-label">Лимит страниц</label>
                  <div class="col-lg-2">
                     
                     <input type="number" step="any" name="user_shop_count_pages" class="form-control" value="<?php echo $settings["user_shop_count_pages"]; ?>" >

                     <div class="mt10" >
                        <small>Укажите максимальное количество страниц которые могут добавлять пользователи</small>
                     </div>

                  </div>
               </div>

             </div>

             <?php if($settings["functionality"]["marketplace"]){ ?>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label"></label>
                <div class="col-lg-9">
                   <h3 style="margin-top: 15px" class="setting-tab-toggle" data-tab="12" > <strong>Маркетплейс <i class="la la-angle-down"></i></strong> </h3>
                </div>
             </div>

             <div class="setting-tab-content" data-tab="12" >

               <div class="form-group row d-flex align-items-center mb-5">
                  <label class="col-lg-3 form-control-label">Активен</label>
                  <div class="col-lg-9">
                     <label>
                        <input class="toggle-checkbox-sm" type="checkbox" name="marketplace_status" value="1" <?php if($settings["marketplace_status"]){ echo 'checked=""'; } ?> >
                        <span><span></span></span>
                     </label>
                  </div>
               </div>

               <div class="form-group row d-flex align-items-center mb-5">
                  <label class="col-lg-3 form-control-label">Корзина доступна</label>
                  <div class="col-lg-2">
                     
                     <select name="marketplace_available_cart" class="selectpicker" >
                           <option value="all" <?php if($settings["marketplace_available_cart"] == 'all'){ echo 'selected=""'; } ?> >Всем пользователям</option>
                           <option value="shop" <?php if($settings["marketplace_available_cart"] == 'shop'){ echo 'selected=""'; } ?> >Только владельцам магазина</option>
                     </select>

                  </div>
               </div>

               <div class="form-group row d-flex align-items-center mb-5">
                  <label class="col-lg-3 form-control-label">Вид корзины</label>
                  <div class="col-lg-2">
                     
                     <select name="marketplace_view_cart" class="selectpicker" >
                           <option value="modal" <?php if($settings["marketplace_view_cart"] == 'modal'){ echo 'selected=""'; } ?> >Модальное окно</option>
                           <option value="sidebar" <?php if($settings["marketplace_view_cart"] == 'sidebar'){ echo 'selected=""'; } ?> >Боковая панель</option>
                           <option value="page" <?php if($settings["marketplace_view_cart"] == 'page'){ echo 'selected=""'; } ?> >Отдельная страница</option>
                     </select>

                  </div>
               </div>

             </div>

            <?php } ?>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label"></label>
                <div class="col-lg-9">
                   <h3 style="margin-top: 15px" class="setting-tab-toggle" data-tab="3" > <strong>Регистрация и авторизация <i class="la la-angle-down"></i></strong> </h3>
                </div>
             </div>

             <div class="setting-tab-content" data-tab="3" >

             <div class="form-group row d-flex align-items-center mb-5">
                  <label class="col-lg-3 form-control-label">Подтверждение номера телефона</label>
                  <div class="col-lg-9">
                      <label>
                        <input class="toggle-checkbox-sm" type="checkbox" name="confirmation_phone" value="1" <?php if($settings["confirmation_phone"]){ echo ' checked=""'; } ?> >
                        <span><span></span></span>
                      </label>
                  </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Метод авторизации</label>
                <div class="col-lg-9">
                   <select name="authorization_method" class="selectpicker">
                      
                      <option value="2"  <?php if($settings["authorization_method"] == 2){ echo ' selected=""'; } ?> >По e-mail/номеру телефона и паролю</option>
                      <option value="1"  <?php if($settings["authorization_method"] == 1){ echo ' selected=""'; } ?> >По номеру телефона и паролю</option>
                      <option value="3"  <?php if($settings["authorization_method"] == 3){ echo ' selected=""'; } ?> >По e-mail и паролю</option>

                   </select>

                </div>
             </div>
             
             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Метод регистрации</label>
                <div class="col-lg-9">
                   <select name="registration_method" class="selectpicker">
                      
                      <option value="2"  <?php if($settings["registration_method"] == 2){ echo ' selected=""'; } ?> >По e-mail/номеру телефона</option>
                      <option value="1"  <?php if($settings["registration_method"] == 1){ echo ' selected=""'; } ?> >По номеру телефона</option>
                      <option value="3"  <?php if($settings["registration_method"] == 3){ echo ' selected=""'; } ?> >По e-mail</option>

                   </select>

                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Вход через сервисы</label>
                <div class="col-lg-9">
                   <select name="authorization_social[]" class="selectpicker" multiple="" title="Отключены" >

                      <?php
                      if($settings["authorization_social"])
                        $authorization_social_list = explode(",", $settings["authorization_social"]);
                      else $authorization_social_list = [];
                      ?>
                      
                      <option value="vk"  <?php if( in_array( "vk" , $authorization_social_list ) ){ echo ' selected=""'; } ?> >VKontakte</option>
                      <option value="google"  <?php if( in_array( "google" , $authorization_social_list ) ){ echo ' selected=""'; } ?> >Google</option>
                      <option value="fb"  <?php if( in_array( "fb" , $authorization_social_list ) ){ echo ' selected=""'; } ?> >FaceBook</option>

                   </select>

                </div>
             </div>

             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label"></label>
                <div class="col-lg-9">
                   <h3 style="margin-top: 15px" class="setting-tab-toggle" data-tab="8" > <strong>Карточка объявления <i class="la la-angle-down"></i></strong> </h3>
                </div>
             </div>

             <div class="setting-tab-content" data-tab="8" >

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Цена в разных валютах</label>
                <div class="col-lg-9">
                    <label>
                      <input class="toggle-checkbox-sm" type="checkbox" name="ads_currency_price" value="1" <?php if($settings["ads_currency_price"] == 1){ echo ' checked=""'; } ?> >
                      <span><span></span></span>
                    </label>
                </div>
             </div>             

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Комментарии в объявлениях</label>
                <div class="col-lg-9">
                    <label>
                      <input class="toggle-checkbox-sm" type="checkbox" name="ads_comments" value="1" <?php if($settings["ads_comments"] == 1){ echo ' checked=""'; } ?> >
                      <span><span></span></span>
                    </label>
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Кто может просматривать номер телефона</label>
                <div class="col-lg-2">
                    <select class="selectpicker" name="ad_view_phone" >
                       <option value="1" <?php if($settings["ad_view_phone"] == 1){ echo 'selected=""'; } ?> >Только авторизованные пользователи</option>
                       <option value="2" <?php if($settings["ad_view_phone"] == 2){ echo 'selected=""'; } ?> >Все</option>
                    </select>
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Выводить кол-во похожих объявлений</label>
                <div class="col-lg-2">
                    <input type="text" name="ad_similar_count" class="form-control" value="<?php echo $settings["ad_similar_count"]; ?>" value="16" >
                </div>
             </div>

             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label"></label>
                <div class="col-lg-9">
                   <h3 style="margin-top: 15px" class="setting-tab-toggle" data-tab="4" > <strong>Публикация объявления <i class="la la-angle-down"></i></strong> </h3>
                </div>
             </div>

             <div class="setting-tab-content" data-tab="4" >
              
             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Запрашивать номер телефона</label>
                <div class="col-lg-9">
                    <label>
                      <input class="toggle-checkbox-sm" type="checkbox" name="ad_create_phone" value="1" <?php if($settings["ad_create_phone"] == 1){ echo ' checked=""'; } ?> >
                      <span><span></span></span>
                    </label>
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Запрещенные слова</label>
                <div class="col-lg-6">
                    <textarea class="form-control" name="ad_black_list_words" ><?php echo $settings["ad_black_list_words"]; ?></textarea>
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Ручная модерация</label>
                <div class="col-lg-9">
                    <label>
                      <input class="toggle-checkbox-sm" type="checkbox" name="ads_publication_moderat" value="1" <?php if($settings["ads_publication_moderat"] == 1){ echo ' checked=""'; } ?> >
                      <span><span></span></span>
                    </label>
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Премодерация</label>
                <div class="col-lg-9">
                    <label>
                      <input class="toggle-checkbox-sm" type="checkbox" name="ads_publication_auto_moderat" value="1" <?php if($settings["ads_publication_auto_moderat"]){ echo ' checked=""'; } ?> >
                      <span><span></span></span>
                    </label>

                    <div>
                       <small>Объявления будут автоматически отклоняться если в них будет: <br> короткий заголовок, ссылки на сайт и email</small>
                    </div>
                </div>
             </div>

             <?php if(!$settings['marketplace_status']){ ?>
             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Смена валюты</label>
                <div class="col-lg-9">
                    <label>
                      <input class="toggle-checkbox-sm" type="checkbox" name="ad_create_currency" value="1" <?php if($settings["ad_create_currency"]){ echo ' checked=""'; } ?> >
                      <span><span></span></span>
                    </label>
                </div>
             </div>
             <?php } ?>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Выбор срока публикации</label>
                <div class="col-lg-9">
                    <label>
                      <input class="toggle-checkbox-sm" type="checkbox" name="ad_create_period" value="1" <?php if($settings["ad_create_period"]){ echo ' checked=""'; } ?> >
                      <span><span></span></span>
                    </label>
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Сроки</label>
                <div class="col-lg-2">
                    <input type="text" name="ad_create_period_list" class="form-control" value="<?php echo $settings["ad_create_period_list"]; ?>" placeholder="1,7,14,30,60" >
                    <small>Укажите дни через запятую</small>
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Срок размещения по умолчанию</label>
                <div class="col-lg-2">
                    <input type="text" name="ads_time_publication_default" class="form-control" value="<?php echo $settings["ads_time_publication_default"]; ?>" >
                    <small>Укажите количество дней</small>
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Обязательная загрузка фото</label>
                <div class="col-lg-2">
                    <label>
                      <input class="toggle-checkbox-sm" type="checkbox" name="ad_create_always_image" value="1" <?php if($settings["ad_create_always_image"]){ echo ' checked=""'; } ?> >
                      <span><span></span></span>
                    </label>                  
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Кол-во загружаемых изображений</label>
                <div class="col-lg-2">
                    <input type="text" name="count_images_add_ad" class="form-control" value="<?php echo $settings["count_images_add_ad"]; ?>" >
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Формат фото</label>
                <div class="col-lg-2">
                    <select name="ad_format_photo" class="selectpicker" >
                        <option value="jpg" <?php if( $settings["ad_format_photo"] == "jpg" ){ echo 'selected=""'; } ?> >jpg</option>
                        <option value="webp" <?php if( $settings["ad_format_photo"] == "webp" ){ echo 'selected=""'; } ?> >webp</option>
                    </select>                  
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Максимальная длина заголовка объявления</label>
                <div class="col-lg-2">
                    <input type="text" name="ad_create_length_title" class="form-control" value="<?php echo $settings["ad_create_length_title"]; ?>" >
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Максимальная длина описания объявления</label>
                <div class="col-lg-2">
                    <input type="text" name="ad_create_length_text" class="form-control" value="<?php echo $settings["ad_create_length_text"]; ?>" >
                </div>
             </div>

             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label"></label>
                <div class="col-lg-9">
                   <h3 style="margin-top: 15px" class="setting-tab-toggle" data-tab="5" > <strong>Бонусная программа <i class="la la-angle-down"></i></strong> </h3>
                </div>
             </div>

             <div class="setting-tab-content" data-tab="5" >

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Бонус за регистрацию</label>
                <div class="col-lg-2">
                    
                    <label>
                      <input class="toggle-checkbox-sm" type="checkbox" name="bonus[register][status]" value="1" <?php if($settings["bonus_program"]["register"]["status"] == 1){ echo ' checked=""'; } ?> >
                      <span><span></span></span>
                    </label>

                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Название</label>
                <div class="col-lg-5">
                    <input type="text" class="form-control" name="bonus[register][name]" value="<?php echo $settings["bonus_program"]["register"]["name"]; ?>" >
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Бонус</label>
                <div class="col-lg-2">
                    <div class="input-group mb-2">
                       <input type="number" step="any" class="form-control" name="bonus[register][price]" value="<?php echo $settings["bonus_program"]["register"]["price"]; ?>" >
                       <div class="input-group-prepend">
                          <div class="input-group-text"><?php echo $settings["currency_main"]["sign"]; ?></div>
                       </div>                       
                    </div>
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Бонус за пополнение баланса</label>
                <div class="col-lg-2">
                    
                    <label>
                      <input class="toggle-checkbox-sm" type="checkbox" name="bonus[balance][status]" value="1" <?php if($settings["bonus_program"]["balance"]["status"] == 1){ echo ' checked=""'; } ?> >
                      <span><span></span></span>
                    </label>

                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Название</label>
                <div class="col-lg-5">
                    <input type="text" class="form-control" name="bonus[balance][name]" value="<?php echo $settings["bonus_program"]["balance"]["name"]; ?>" >
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Процент от суммы пополнения</label>
                <div class="col-lg-2">
                    <div class="input-group mb-2">
                       <input type="number" step="any" class="form-control" name="bonus[balance][price]" value="<?php echo $settings["bonus_program"]["balance"]["price"]; ?>" >
                       <div class="input-group-prepend">
                          <div class="input-group-text">%</div>
                       </div>                       
                    </div>
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Бонус за добавление e-mail адреса</label>
                <div class="col-lg-2">
                    
                    <label>
                      <input class="toggle-checkbox-sm" type="checkbox" name="bonus[email][status]" value="1" <?php if($settings["bonus_program"]["email"]["status"] == 1){ echo ' checked=""'; } ?> >
                      <span><span></span></span>
                    </label>

                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Название</label>
                <div class="col-lg-5">
                    <input type="text" class="form-control" name="bonus[email][name]" value="<?php echo $settings["bonus_program"]["email"]["name"]; ?>" >
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Бонус</label>
                <div class="col-lg-2">
                    <div class="input-group mb-2">
                       <input type="number" step="any" class="form-control" name="bonus[email][price]" value="<?php echo $settings["bonus_program"]["email"]["price"]; ?>" >
                       <div class="input-group-prepend">
                          <div class="input-group-text"><?php echo $settings["currency_main"]["sign"]; ?></div>
                       </div>                       
                    </div>
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Бонус за публикацию первого объявления</label>
                <div class="col-lg-2">
                    
                    <label>
                      <input class="toggle-checkbox-sm" type="checkbox" name="bonus[ad_publication][status]" value="1" <?php if($settings["bonus_program"]["ad_publication"]["status"] == 1){ echo ' checked=""'; } ?> >
                      <span><span></span></span>
                    </label>

                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Название</label>
                <div class="col-lg-5">
                    <input type="text" class="form-control" name="bonus[ad_publication][name]" value="<?php echo $settings["bonus_program"]["ad_publication"]["name"]; ?>" >
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Бонус</label>
                <div class="col-lg-2">
                    <div class="input-group mb-2">
                       <input type="number" step="any" class="form-control" name="bonus[ad_publication][price]" value="<?php echo $settings["bonus_program"]["ad_publication"]["price"]; ?>" >
                       <div class="input-group-prepend">
                          <div class="input-group-text"><?php echo $settings["currency_main"]["sign"]; ?></div>
                       </div>                       
                    </div>
                </div>
             </div>

             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label"></label>
                <div class="col-lg-9">
                   <h3 style="margin-top: 15px" class="setting-tab-toggle" data-tab="6" > <strong>Контактная информация на сайте <i class="la la-angle-down"></i></strong> </h3>
                </div>
             </div>

             <div class="setting-tab-content" data-tab="6" >

              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Номер телефона</label>
                <div class="col-lg-5">
                     <input type="text" class="form-control" value="<?php echo $settings["contact_phone"]; ?>" name="contact_phone" >
                     <small>Если номер телефона не один, то укажите их через запятую.</small>
                </div>
              </div>

              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">E-mail</label>
                <div class="col-lg-5">
                     <input type="text" class="form-control" value="<?php echo $settings["contact_email"]; ?>"  name="contact_email" >
                     <small>Если email адресов много, то укажите их через запятую.</small>
                </div>
              </div>

              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Заголовок сайта</label>
                <div class="col-lg-5">
                     <input type="text" class="form-control" value="<?php echo $settings["title"]; ?>" name="title" >
                     <small>Указанная тут информация будет отображаться в нижней части сайта и в e-mail письмах отправленные пользователям.</small>
                </div>
              </div>

              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Название сайта/проекта</label>
                <div class="col-lg-5">
                     <input type="text" class="form-control" value="<?php echo $settings["site_name"]; ?>"  name="site_name" >
                </div>
              </div>

              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Адрес</label>
                <div class="col-lg-5">
                     <input type="text" class="form-control"  value="<?php echo $settings["contact_address"]; ?>" name="contact_address" >
                </div>
              </div>

             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label"></label>
                <div class="col-lg-9">
                   <h3 style="margin-top: 15px" class="setting-tab-toggle" data-tab="7" > <strong>Ссылки на соцсети <i class="la la-angle-down"></i></strong> </h3>
                </div>
             </div>

              <div class="setting-tab-content" data-tab="7" >

              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">ВКонтакте</label>
                <div class="col-lg-5">
                     <input type="text" class="form-control" value="<?php echo $settings["social_link_vk"]; ?>" name="social_link_vk" >
                </div>
              </div>

              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Одноклассники</label>
                <div class="col-lg-5">
                     <input type="text" class="form-control" value="<?php echo $settings["social_link_ok"]; ?>" name="social_link_ok" >
                </div>
              </div>
              
              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">YouTube</label>
                <div class="col-lg-5">
                     <input type="text" class="form-control" value="<?php echo $settings["social_link_you"]; ?>" name="social_link_you" >
                </div>
              </div>

              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Telegram</label>
                <div class="col-lg-5">
                     <input type="text" class="form-control" value="<?php echo $settings["social_link_telegram"]; ?>" name="social_link_telegram" >
                </div>
              </div>

              </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label"></label>
                <div class="col-lg-9">
                   <h3 style="margin-top: 15px" class="setting-tab-toggle" data-tab="10" > <strong>PWA <i class="la la-angle-down"></i></strong> </h3>
                </div>
             </div>

             <div class="setting-tab-content" data-tab="10" >

              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Статус активности</label>
                <div class="col-lg-2">
                    
                    <label>
                      <input class="toggle-checkbox-sm" type="checkbox" name="pwa_status" value="1" <?php if($settings["pwa_status"]){ echo ' checked=""'; } ?> >
                      <span><span></span></span>
                    </label>

                </div>
              </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Иконка приложения</label>
                <div class="col-lg-9">
                   <div class="settings-change-img" >
                     
                      <?php echo img( array( "img1" => array( "class" => "load-pwa", "path" => "/templates/icons_pwa/" . $settings["pwa_image"], "width" => "32px" ), "img2" => array( "class" => "load-pwa", "path" => $config["media"]["other"] . "/icon_photo_add.png", "width" => "60px" ) ) ); ?>

                     <input type="file" class="input-pwa" name="pwa_icon" >
                   </div>
                   <div><small>Рекомендуемый размер иконки 512x512 с расширением png.</small></div>
                </div>
              </div>

              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Название приложения</label>
                <div class="col-lg-5">
                      
                      <input type="text" class="form-control" value="<?php echo $settings["pwa_name"]; ?>" name="pwa_name" >

                </div>
              </div>

              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Короткое название приложения</label>
                <div class="col-lg-5">
                      
                      <input type="text" class="form-control" value="<?php echo $settings["pwa_short_name"]; ?>" name="pwa_short_name" >

                </div>
              </div>

              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Описание приложения</label>
                <div class="col-lg-5">
                      
                      <textarea class="form-control" name="pwa_desc" ><?php echo $settings["pwa_desc"]; ?></textarea>

                </div>
              </div>

             </div>
             
             <hr>
             <br>

             <div class="form-group row d-flex align-items-center">
                <label class="col-lg-3 form-control-label">Версия системы</label>
                <div class="col-lg-5">
                      <strong><?php echo $settings["system_version"]; ?></strong>
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Объем кэша</label>
                <div class="col-lg-5">
                      <?php echo $Admin->manager_filesize( $Admin->dir_size( $config["basePath"] . "/temp/cache/" ) ); ?>

                      <div style="margin-top: 15px;" >
                        <button class="btn btn-primary cache-clear" >Сбросить</button>
                      </div>
                </div>
             </div>


         </div>
         <div class="tab-pane fade <?php if($_GET["tab"] == "alert"){ echo 'active show'; } ?>" id="j-tab-2" role="tabpanel" aria-labelledby="just-tab-2">

           <br>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Уведомлять о новых объявлениях</label>
                <div class="col-lg-9">
                     <select class="selectpicker" title="Нет" name="notification_method_new_ads[]" multiple="" >
                     	 <?php
                            if($settings["notification_method_new_ads"]){
                            	$notification_method = explode(",",$settings["notification_method_new_ads"]);
                            }else{
                            	$notification_method = array();
                            }
                     	 ?>
                     	 <option value="email" <?php if(in_array("email", $notification_method)){ echo ' selected=""'; } ?> >По e-mail</option>
                     	 <option value="telegram" <?php if(in_array("telegram", $notification_method)){ echo ' selected=""'; } ?> >По telergam</option>
                     </select>
                </div>
             </div>
            
             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Уведомлять о новых пользователях</label>
                <div class="col-lg-9">
                     <select class="selectpicker" title="Нет" name="notification_method_new_user[]" multiple="" >
                     	 <?php
                            if($settings["notification_method_new_user"]){
                            	$notification_method = explode(",",$settings["notification_method_new_user"]);
                            }else{
                            	$notification_method = array();
                            }
                     	 ?>
                     	 <option value="email" <?php if(in_array("email", $notification_method)){ echo ' selected=""'; } ?> >По e-mail</option>
                     	 <option value="telegram" <?php if(in_array("telegram", $notification_method)){ echo ' selected=""'; } ?> >По telergam</option>
                     </select>
                </div>
             </div>  
             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Уведомлять о продажах</label>
                <div class="col-lg-9">
                     <select class="selectpicker" title="Нет" name="notification_method_new_buy[]" multiple="" >
                         <?php
                            if($settings["notification_method_new_buy"]){
                                $notification_method = explode(",",$settings["notification_method_new_buy"]);
                            }else{
                                $notification_method = array();
                            }
                         ?>
                         <option value="email" <?php if(in_array("email", $notification_method)){ echo ' selected=""'; } ?> >По e-mail</option>
                         <option value="telegram" <?php if(in_array("telegram", $notification_method)){ echo ' selected=""'; } ?> >По telergam</option>
                     </select>
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label"></label>
                <div class="col-lg-9">
                   <h3 style="margin-top: 15px" > <strong>Контакты для оповещений</strong> </h3>
                </div>
             </div>

              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">E-mail адрес</label>
                <div class="col-lg-5">
                     <input type="text" class="form-control"  name="email_alert" value="<?php echo $settings["email_alert"]; ?>" >
                     <small>Если email адресов много, то укажите их через запятую.</small>
                </div>
              </div>            

              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Номер телефона</label>
                <div class="col-lg-5">
                     <input type="text" class="form-control"  name="phone_alert" value="<?php echo $settings["phone_alert"]; ?>" >
                     <small>Если номер телефона не один, то укажите их через запятую.</small>
                </div>
              </div>

              
         </div>

         <div class="tab-pane fade <?php if($_GET["tab"] == "currency"){ echo 'active show'; } ?>" id="j-tab-3" role="tabpanel" aria-labelledby="just-tab-3">

          <br>

            <div class="form-group row d-flex align-items-center mb-5">
              <label class="col-lg-3 form-control-label">Основная валюта сайта</label>
              <div class="col-lg-9">

                <select name="main_currency" id="select_main_currency" class="selectpicker" >

                  <?php
                  $get = getAll("SELECT * FROM uni_currency");
                      if (count($get) > 0) {
                          foreach($get as $result){
                            
                                if(!empty($result["main"])){  
                                  $selected = 'selected="selected"';
                                  $main_currency = $result["code"];
                                  $value = "";
                                }else{
                                  $selected = "";
                                  $value = $result["code"];
                                }  
                            
                             ?>
                             <option <?php echo $selected; ?> data-value="<?php echo $result["code"]; ?>" data-price="<?php echo $result["price"]; ?>" value="<?php echo $value; ?>" ><?php echo $result["name"]; ?>(<?php echo $result["code"]; ?>)</option>
                             <?php
                         
                         };
                  
                      }           

                   ?>
                   
                </select>

              </div>
            </div>
            
            <div class="form-group row d-flex mb-5">
              <label class="col-lg-3 form-control-label">Валюты</label>
              <div class="col-lg-9">

                <div id="main-box-currency" >
                
                    <span class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-add-currency" ><i class="la la-plus" ></i> Добавить</span>

                    <div id="container-currency" >
                    
                     <?php
                     
                      $get = getAll("SELECT * FROM uni_currency");
                          if (count($get) > 0) {
                              foreach($get as $result){
           
                                 ?>

                                    <div class="row item<?php echo $result["id"]; ?>" id="<?php echo $result["code"]; ?>" style="margin-bottom: 10px;" >

                                       <div class="col-lg-4 col-sm-4 col-md-4 col-4"><input class="form-control" type="text" placeholder="Название"  name="currency[][<?php echo $result["id"]; ?>][name]" value="<?php echo $result["name"]; ?>" /></div>

                                       <div class="col-lg-2 col-sm-2 col-md-2 col-2"><input class="form-control" type="text" placeholder="Знак"  name="currency[][<?php echo $result["id"]; ?>][sign]" value="<?php echo $result["sign"]; ?>" /></div>

                                       <div class="col-lg-3 col-sm-3 col-md-3 col-3"><input class="form-control" type="text" placeholder="Код"  name="currency[][<?php echo $result["id"]; ?>][code]" value="<?php echo $result["code"]; ?>" /></div>

                                       <div class="col-lg-3 col-sm-3 col-md-3 col-3" ><span class="btn btn-danger btn-sm delete-currency" uid="<?php echo $result["id"]; ?>" ><i class="la la-trash-o" ></i></span></div> 

                                    </div>                       
                                      
                                 <?php
                      
                              }
                            
                          }           
                     
                     
                       ?>

                       </div>

               
                  </div>

              </div>
            </div>


         </div>

         <div class="tab-pane fade <?php if($_GET["tab"] == "access"){ echo 'active show'; } ?>" id="j-tab-4" role="tabpanel" aria-labelledby="just-tab-4">

         <br>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Сайт доступен</label>
                <div class="col-lg-9">
                    <label>
                      <input class="toggle-checkbox-sm settings-access-site" value="1" type="checkbox" name="access_site" <?php if($settings["access_site"] == "1"){ echo 'checked=""'; } ?> >
                      <span><span></span></span>
                    </label>
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label"></label>
                <div class="col-lg-6">
                    <div class="styled-radio">
                      <input type="radio" name="access_action" value="text" id="rad-2" class="settings-access-out-text" <?php if($settings["access_site"] == "0"){ if($settings["access_action"] == "text"){ echo 'checked=""'; } if($settings["access_site"] == "1"){ echo ' disabled=""'; } }else{ echo ' disabled=""'; } ?> >
                      <label for="rad-2">Выводить текст</label>
                    </div>
                    <textarea class="form-control settings-access-text" name="access_text" <?php if($settings["access_site"] == "0"){ if($settings["access_action"] != "text"){ echo 'disabled=""'; } }else{ echo ' disabled=""'; } ?> ><?php echo $settings["access_text"]; ?></textarea>
                </div>
             </div>             

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label"></label>
                <div class="col-lg-6">
                    <div class="styled-radio">
                      <input type="radio" name="access_action" value="redirect" id="rad-3" class="settings-access-redirect" <?php if($settings["access_site"] == "0"){ if($settings["access_action"] == "redirect"){ echo 'checked=""'; } if($settings["access_site"] == "1"){ echo ' disabled=""'; } }else{ echo ' disabled=""'; } ?> />
                      <label for="rad-3">Перенаправлять на другой сайт</label>
                    </div>                  
                    <input type="text" class="form-control settings-access-redirect-link" value="<?php echo $settings["access_redirect_link"]; ?>" name="access_redirect_link" <?php if($settings["access_site"] == "0"){ if($settings["access_action"] != "redirect"){ echo 'disabled=""'; } }else{ echo ' disabled=""'; } ?>  ></input>
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Разрешенные IP для входа</label>
                <div class="col-lg-6">
                    <textarea class="form-control settings-access-ip" name="access_allowed_ip" <?php if($settings["access_site"] == "1"){ echo ' disabled=""'; } ?> ><?php if(!empty($settings["access_allowed_ip"])){echo $settings["access_allowed_ip"];}else{echo $_SERVER["REMOTE_ADDR"];}  ?></textarea>
                    <small>Укажите разрешенные IP через запятую. Ваш IP подставляется автоматически!</small>
                </div>
             </div>

         </div> 

         <div class="tab-pane fade <?php if($_GET["tab"] == "emailmessage"){ echo 'active show'; } ?>" id="j-tab-5" role="tabpanel" aria-labelledby="just-tab-5">
            <br>

             <div class="form-group row d-flex align-items-center mb-5">

                <div class="col-lg-12">
                    
                    <div class="row" >

                        <?php
                           $get = getAll("SELECT * FROM uni_email_message");
                             if (count($get) > 0) {
                                foreach ($get as $key => $value) {
                                    ?>
                                      <div class="col-lg-3" >
                                         <div class="template-item" >
                                             <p><?php echo $value["name"]; ?></p>
                                             <button type="button" data-id="<?php echo $value["id"]; ?>" class="btn btn-secondary setting-open-email mr-1 mb-2 btn-sm">Редактировать</button>
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

         <div class="tab-pane fade <?php if($_GET["tab"] == "watermark"){ echo 'active show'; } ?>" id="j-tab-6" role="tabpanel" aria-labelledby="just-tab-6">

            <br>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Водяной знак на фото</label>
                <div class="col-lg-6">
                    <label>
                      <input class="toggle-checkbox-sm" value="1" type="checkbox" name="watermark_status" <?php if($settings["watermark_status"] == "1"){ echo 'checked=""'; } ?> >
                      <span><span></span></span>
                    </label>
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Вид водяного знака</label>
                <div class="col-lg-6">
                   <select class="selectpicker" name="watermark_type" >
                       <option value="caption" <?php if($settings["watermark_type"] == "caption"){echo 'selected=""';}?> >Надпись</option>
                       <option value="img" <?php if($settings["watermark_type"] == "img"){echo 'selected=""';}?> >Изображение</option>
                   </select>
                </div>
             </div>

             <div class="watermark-box-img" <?php if($settings["watermark_type"] == "img"){echo 'style="display: block;"';}else{ echo 'style="display: none;"'; } ?> >
               
                 <div class="form-group row d-flex align-items-center mb-5">
                    <label class="col-lg-3 form-control-label">Изображение</label>
                    <div class="col-lg-6">
                       <input type="file" name="watermark_img" class="form-control" >
                       <small>Выберите файл в формате PNG</small>
                       <br>
                        <?php  
                          if( file_exists( $config["basePath"] . "/" . $config["media"]["other"] . "/" . $settings["watermark_img"] ) ){
                            ?>
                              <img class="watermark-image" src="<?php echo $config["urlPath"] . "/" . $config["media"]["other"] . "/" . $settings["watermark_img"]; ?>" />
                            <?php 
                          }
                        ?>

                    </div>
                 </div>

                 <div class="form-group row d-flex align-items-center mb-5">
                    <label class="col-lg-3 form-control-label">Позиция</label>
                    <div class="col-lg-6">
                       <select class="selectpicker" name="watermark_pos" >
                           <option value="1" <?php if($settings["watermark_pos"] == 1){echo 'selected=""';}?> >Верхний левый угол</option>
                           <option value="2" <?php if($settings["watermark_pos"] == 2){echo 'selected=""';}?> >Верхний правый угол</option>
                           <option value="3" <?php if($settings["watermark_pos"] == 3){echo 'selected=""';}?> >Нижний левый угол</option>
                           <option value="4" <?php if($settings["watermark_pos"] == 4){echo 'selected=""';}?> >Нижний правый угол</option>
                           <option value="5" <?php if($settings["watermark_pos"] == 5){echo 'selected=""';}?> >Центр</option>
                       </select>
                    </div>
                 </div>

             </div>

             <div class="watermark-box-caption" <?php if($settings["watermark_type"] == "caption"){echo 'style="display: block;"';}else{ echo 'style="display: none;"'; } ?> >
               
                 <div class="form-group row d-flex align-items-center mb-5">
                    <label class="col-lg-3 form-control-label">Надпись</label>
                    <div class="col-lg-6">
                       <input type="text" name="watermark_caption" class="form-control" value="<?php echo $settings["watermark_caption"]; ?>" >
                    </div>
                 </div>

                 <div class="form-group row d-flex align-items-center mb-5">
                    <label class="col-lg-3 form-control-label">Шрифт</label>
                    <div class="col-lg-6">
                       <select name="watermark_caption_font" class="selectpicker" >
                           <?php
                               $dir = $config["basePath"]."/".$config["folder_admin"]."/files/fonts/watermark/";
                               if(is_dir($dir)){
                                $name = scandir($dir);
                                for($i=2; $i<=(sizeof($name)-1); $i++) {                         
                                      
                                    if($settings["watermark_caption_font"] == $name[$i]){
                                      $selected = 'selected=""';
                                    }else{ $selected = ''; }

                                   if(is_file($dir.$name[$i]) && $name[$i] != '.' && pathinfo($name[$i], PATHINFO_EXTENSION) == 'ttf'){                           
                                      echo '<option '.$selected.' value="'.$name[$i].'" >'.$name[$i].'</option>';

                                   }

                                }
                             }                               
                           ?>
                       </select>
                    </div>
                 </div>

                 <div class="form-group row d-flex align-items-center mb-5">
                    <label class="col-lg-3 form-control-label">Размер надписи</label>
                    <div class="col-lg-2">
                       <select class="selectpicker" name="watermark_caption_size" >
                           <option value="big" <?php if($settings["watermark_caption_size"] == "big"){echo 'selected=""';}?> >Большой</option>
                           <option value="medium" <?php if($settings["watermark_caption_size"] == "medium"){echo 'selected=""';}?> >Средний</option>
                           <option value="small" <?php if($settings["watermark_caption_size"] == "small"){echo 'selected=""';}?> >Маленький</option>
                       </select>
                    </div>
                 </div>

                 <div class="form-group row d-flex align-items-center mb-5">
                    <label class="col-lg-3 form-control-label">Прозрачность</label>
                    <div class="col-lg-2">
                       <input type="text" name="watermark_caption_opacity" class="form-control" value="<?php echo $settings["watermark_caption_opacity"]; ?>" >
                    </div>
                 </div>

             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label"></label>
                <div class="col-lg-6 text-right">
                   <a class="btn btn-primary" href="<?php echo $config["urlPath"] . "/" . $config["folder_admin"] . "/include/modules/settings/watermark_preview.php" ?>" target="_blank" >Предпросмотр</a>
                </div>
             </div>


         </div> 
         
         <div class="tab-pane fade <?php if($_GET["tab"] == "email_mode"){ echo 'active show'; } ?>" id="j-tab-7" role="tabpanel" aria-labelledby="just-tab-7">

           <br>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Имя отправителя</label>
                <div class="col-lg-6">
                    <input type="text" class="form-control" name="name_responder" value="<?php echo $settings["name_responder"]; ?>" >
                </div>
             </div>           

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Ответный E-Mail</label>
                <div class="col-lg-6">
                    <input type="text" class="form-control" value="<?php echo $settings["email_noreply"]; ?>" name="email_noreply">
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label"></label>
                <div class="col-lg-6">
                    <h3 style="margin-top: 15px">Метод рассылки</h3>
                    <hr>
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label"></label>
                <div class="col-lg-6">

					<div class="styled-radio">
						<input type="radio" name="variant_send_mail" value="1" id="responder-rad-1" <?php if($settings["variant_send_mail"] == 1){echo 'checked=""';}?> >
						<label for="responder-rad-1">Стандартная отправка Email</label>
					</div>
					<div class="styled-radio">
						<input type="radio" name="variant_send_mail" value="2" id="responder-rad-2" <?php if($settings["variant_send_mail"] == 2){echo 'checked=""';}?> >
						<label for="responder-rad-2">Отправка через SMTP</label>
					</div>

                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Хост</label>
                <div class="col-lg-6">
                    <input type="text" class="form-control" value="<?php echo $settings["smtp_host"]?>" name="smtp_host" <?php if($settings["variant_send_mail"] == 1){echo 'disabled=""';}?> >
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Порт</label>
                <div class="col-lg-6">
                    <input type="text" class="form-control" value="<?php echo $settings["smtp_port"]?>" name="smtp_port" <?php if($settings["variant_send_mail"] == 1){echo 'disabled=""';}?> >
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Email</label>
                <div class="col-lg-6">
                    <input type="text" class="form-control" value="<?php echo $settings["smtp_username"]?>" name="smtp_username" <?php if($settings["variant_send_mail"] == 1){echo 'disabled=""';}?> >
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Пароль от почты</label>
                <div class="col-lg-6">
                    <input type="text" class="form-control" autocomplete="new-password" placeholder="<?php if($settings["smtp_password"]){ echo 'Пароль задан'; } ?>" name="smtp_password" <?php if($settings["variant_send_mail"] == 1){echo 'disabled=""';}?> >

                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Шифрование</label>
                <div class="col-lg-6">
                    
                    <select class="selectpicker" name="smtp_secure"  >
                       <option value="ssl" <?php if($settings["smtp_secure"] == "ssl"){echo 'selected=""';}?> >SSL</option>
                       <option value="tsl" <?php if($settings["smtp_secure"] == "tsl"){echo 'selected=""';}?> >TSL</option>
                    </select>

                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label"></label>
                <div class="col-lg-6">

                    <a href="https://unisite.org/doc/otpravka-email-cherez-smtp"> <strong><i class="la la-question-circle question-circle"></i> Как настроить SMTP?</strong> </a>

                    <div class="text-right" >
                    	<a data-toggle="modal" data-target="#modal-log" class="test-send-smtp btn btn-primary" >Тестовая отправка письма</a>
                    </div>

                </div>
             </div>

         </div>

         <div class="tab-pane fade <?php if($_GET["tab"] == "code_script"){ echo 'active show'; } ?>" id="j-tab-8" role="tabpanel" aria-labelledby="just-tab-8">

	       <br />
          <div class="accordion" id="accordion_code_script">
            
          <div class="card">
               <div class="card-header" id="headingOne">
                  <h5 class="mb-0">
                  <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                     Meta теги
                  </button>
                  </h5>
               </div>

               <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion_code_script">
                  <div class="card-body">
                  
                     <textarea class="form-control" name="header_meta" style="min-height: 300px;" ><?php echo trim($settings["header_meta"]);?></textarea>
                     
                  </div>
               </div>
            </div>
            <div class="card">
               <div class="card-header" id="headingTwo">
                  <h5 class="mb-0">
                  <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        Скрипты/виджеты
                  </button>
                  </h5>
               </div>
               <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion_code_script">
                  <div class="card-body">
                      
                      <textarea class="form-control" name="code_script" style="min-height: 300px;" ><?php echo trim($settings["code_script"]);?></textarea>

                  </div>
               </div>
            </div>
          </div>

         </div>

         <div class="tab-pane fade <?php if($_GET["tab"] == "integrations"){ echo 'active show'; } ?>" id="j-tab-9" role="tabpanel" aria-labelledby="just-tab-9">

            <br>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label"></label>
                <div class="col-lg-6">
                    <h3> <strong>СМС рассылка</strong> </h3>
                </div>
             </div>

              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Выберите сервис</label>
                <div class="col-lg-5">
                     <select name="sms_service" class="selectpicker" >
                       <option value="" >Не выбрано</option>
                       <?php
                         if($config["sms_services"]){
                             foreach ($config["sms_services"] as $name => $value) {
                                 ?>
                                 <option <?php if($name == $settings["sms_service"]){ echo 'selected=""'; } ?> value="<?php echo $name; ?>" data-param="<?php echo $value["param"]; ?>" data-label="<?php echo $value["label"]; ?>" data-call="<?php echo $value["call"]; ?>" ><?php echo $name; ?></option>
                                 <?php
                             }
                         }
                       ?>
                     </select>
                </div>
              </div>
              
              <div class="sms_service_method_send" <?php if( $config["sms_services"][$settings["sms_service"]]["call"] ){ echo 'style="display: block;"'; } ?> >
                
                 <div class="form-group row d-flex align-items-center mb-5">
                   <label class="col-lg-3 form-control-label">Метод подтверждения</label>
                   <div class="col-lg-5">
                        <select name="sms_service_method_send" class="selectpicker" >
                          <option value="sms" <?php if( $settings["sms_service_method_send"] == 'sms' ){ echo 'selected=""'; } ?> >По смс</option>
                          <option value="call" <?php if( $settings["sms_service_method_send"] == 'call' ){ echo 'selected=""'; } ?> >По звонку</option>
                        </select>
                   </div>
                 </div>

              </div>

              <div class="sms_service_label" <?php if( $config["sms_services"][$settings["sms_service"]]["label"] ){ echo 'style="display: block;"'; } ?> >
                
                  <div class="form-group row d-flex align-items-center mb-5">
                    <label class="col-lg-3 form-control-label">Имя отправителя</label>
                    <div class="col-lg-5">
                         <input type="text" class="form-control"  name="sms_service_label" value="<?php echo $settings["sms_service_label"]; ?>" >
                    </div>
                  </div>

              </div>

              <div class="sms_service_method_send_sms" <?php if( $settings["sms_service_method_send"] == 'sms' ){ echo 'style="display: block;"'; } ?> >

                 <div class="form-group row d-flex align-items-center mb-5">
                    <label class="col-lg-3 form-control-label">Сообщение</label>
                    <div class="col-lg-5">
                         <input type="text" class="form-control"  name="sms_prefix_confirmation_code" value="<?php echo $settings["sms_prefix_confirmation_code"]; ?>" >
                    </div>
                 </div>

              </div>

              <div class="sms_service_id" <?php if( $config["sms_services"][$settings["sms_service"]]["param"] == "id" ){ echo 'style="display: block;"'; } ?> >

                <div class="form-group row d-flex align-items-center mb-5">
                  <label class="col-lg-3 form-control-label">Api id</label>
                  <div class="col-lg-5">
                       <input type="text" class="form-control"  name="sms_service_id" value="<?php echo decrypt($settings["sms_service_id"]); ?>" >
                  </div>
                </div>

                <?php if($settings["sms_service_id"]){ ?>

                  <div class="form-group row d-flex align-items-center mb-5">
                    <label class="col-lg-3 form-control-label"></label>
                    <div class="col-lg-5">
                         <a data-toggle="modal" data-target="#modal-log" class="test-send-sms btn btn-primary" >Тестовая отправка</a>
                    </div>
                  </div>

                <?php } ?>

              </div>
              
              <div class="sms_service_login_pass" <?php if( $config["sms_services"][$settings["sms_service"]]["param"] == "login:pass" ){ echo 'style="display: block;"'; } ?>  >
                
                <div class="form-group row d-flex align-items-center mb-5">
                  <label class="col-lg-3 form-control-label">Логин</label>
                  <div class="col-lg-5">
                       <input type="text" class="form-control"  name="sms_service_login" value="<?php echo $settings["sms_service_login"]; ?>" >
                  </div>
                </div>              

                <div class="form-group row d-flex align-items-center mb-5">
                  <label class="col-lg-3 form-control-label">Пароль</label>
                  <div class="col-lg-5">
                       <input type="text" class="form-control"  name="sms_service_pass" value="<?php echo decrypt($settings["sms_service_pass"]); ?>" >
                  </div>
                </div>

                <?php if($settings["sms_service_login"] && $settings["sms_service_pass"]){ ?>
                  <div class="form-group row d-flex align-items-center mb-5">
                    <label class="col-lg-3 form-control-label"></label>
                    <div class="col-lg-5">
                         <a data-toggle="modal" data-target="#modal-log" class="test-send-sms btn btn-primary" >Тестовая отправка</a>
                    </div>
                  </div>
                <?php } ?>

              </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label"></label>
                <div class="col-lg-6">
                    <h3> <strong>Telegram bot</strong> </h3>
                    <a href="https://unisite.org/doc/nastroyka-telegram-opoveshcheniy"> <strong><i class="la la-question-circle question-circle"></i> Как настроить telegram bot?</strong> </a>
                </div>
             </div>

              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Ключ токена</label>
                <div class="col-lg-5">
                     <input type="text" class="form-control" value="<?php echo decrypt($settings["api_id_telegram"]); ?>"  name="api_id_telegram" >
                </div>
              </div>

              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">ID чата</label>
                <div class="col-lg-5">
                     <input type="text" class="form-control" value="<?php echo $settings["chat_id_telegram"]; ?>"  name="chat_id_telegram" >
                </div>
              </div>
              
              <?php if($settings["api_id_telegram"] && $settings["chat_id_telegram"]){ ?>
              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label"></label>
                <div class="col-lg-5">
                     <a data-toggle="modal" data-target="#modal-log" class="test-send-telegram btn btn-primary" >Проверить подключение</a>
                </div>
              </div>
              <?php } ?>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label"></label>
                <div class="col-lg-6">
                    <h3> <strong>Карты</strong> </h3>
                    <a href="https://unisite.org/doc/nastroyka-yandeks-i-google-kart"> <strong><i class="la la-question-circle question-circle"></i> Как настроить карту?</strong> </a>
                </div>
             </div>

              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Выберите поставщика карт</label>
                <div class="col-lg-5">
                     <select name="map_vendor" class="selectpicker" >
                        <option value="yandex" <?php if($settings["map_vendor"] == "yandex"){ echo 'selected=""'; } ?> >Yandex</option>
                        <option value="google" <?php if($settings["map_vendor"] == "google"){ echo 'selected=""'; } ?> >Google</option>
                        <option value="openstreetmap" <?php if($settings["map_vendor"] == "openstreetmap"){ echo 'selected=""'; } ?> >OpenStreetMap</option>
                     </select>
                </div>
              </div>
              
              <div class="map-google-key" <?php if($settings["map_vendor"] == "google"){ echo 'style="display: block;"'; } ?> >
              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Ключ</label>
                <div class="col-lg-5">
                     <input type="text" class="form-control" value="<?php echo $settings["map_google_key"]; ?>"  name="map_google_key" >
                </div>
              </div>
              </div>

              <div class="map-yandex-key" <?php if($settings["map_vendor"] == "yandex"){ echo 'style="display: block;"'; } ?> >
              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Ключ</label>
                <div class="col-lg-5">
                     <input type="text" class="form-control" value="<?php echo $settings["map_yandex_key"]; ?>"  name="map_yandex_key" >
                </div>
              </div>
              </div>

              <div class="map-openstreetmap-key" <?php if($settings["map_vendor"] == "openstreetmap"){ echo 'style="display: block;"'; } ?> >
              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Ключ</label>
                <div class="col-lg-5">
                     <input type="text" class="form-control" value="<?php echo $settings["map_openstreetmap_key"]; ?>"  name="map_openstreetmap_key" >
                </div>
              </div>
              </div>

              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label"></label>
                <div class="col-lg-6">
                    <h3> <strong>Подключение сервисов для авторизации</strong> </h3>
                </div>
              </div>

              <div class="form-group row d-flex align-items-center">
                <label class="col-lg-3 form-control-label"></label>
                <div class="col-lg-5">

                     <h4> <strong>ВКонтакте</strong> </h4>

                </div>
              </div>

              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">ID приложения</label>
                <div class="col-lg-5">

                     <input type="text" class="form-control mt5" value="<?php echo $social_auth_params["vk"]["id_client"] ?>"  name="social_auth_params[vk][id_client]" >  

                </div>
              </div>

              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Секретный ключ</label>
                <div class="col-lg-5">

                     <input type="text" class="form-control mt5" value="<?php echo $social_auth_params["vk"]["key"] ?>"  name="social_auth_params[vk][key]" >                
                </div>
              </div>

              <div class="form-group row d-flex align-items-center">
                <label class="col-lg-3 form-control-label"></label>
                <div class="col-lg-5">

                     <h4> <strong>Google</strong> </h4>

                </div>
              </div>

              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Идентификатор клиента</label>
                <div class="col-lg-5">

                     <input type="text" class="form-control mt5" value="<?php echo $social_auth_params["google"]["id_client"] ?>"  name="social_auth_params[google][id_client]" >  

                </div>
              </div>

              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Секретный ключ</label>
                <div class="col-lg-5">

                     <input type="text" class="form-control mt5" value="<?php echo $social_auth_params["google"]["key"] ?>"  name="social_auth_params[google][key]" >                
                </div>
              </div>

              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Обработчик</label>
                <div class="col-lg-5">

                     <?php echo $config["urlPath"] . "/systems/ajax/oauth.php?network=google"; ?>

                </div>
              </div>

              <div class="form-group row d-flex align-items-center">
                <label class="col-lg-3 form-control-label"></label>
                <div class="col-lg-5">

                     <h4> <strong>FaceBook</strong> </h4>

                </div>
              </div>

              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">ID приложения</label>
                <div class="col-lg-5">

                     <input type="text" class="form-control mt5" value="<?php echo $social_auth_params["fb"]["id_app"]; ?>"  name="social_auth_params[fb][id_app]" >  

                </div>
              </div>

              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Секретный ключ</label>
                <div class="col-lg-5">

                     <input type="text" class="form-control mt5" value="<?php echo $social_auth_params["fb"]["key"]; ?>"  name="social_auth_params[fb][key]" >                
                </div>
              </div>

              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">URL перенаправления</label>
                <div class="col-lg-5">

                     <?php echo $config["urlPath"] . "/systems/ajax/oauth.php?network=fb"; ?>  

                </div>
              </div>

         </div>

         <div class="tab-pane fade <?php if($_GET["tab"] == "payments"){ echo 'active show'; } ?>" id="j-tab-10" role="tabpanel" aria-labelledby="just-tab-10">
         
         <br>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Платежные системы</label>
                <div class="col-lg-9">
                     <select class="selectpicker" name="payment_variant[]" multiple="" title="Не выбрано" >

                        <?php
                           $get = getAll("select * from uni_payments");
                           if(count($get) > 0){
                             foreach ($get as $key => $value) {
                              
                              if($settings["payment_variant"]){
                                if(in_array($value["id"], explode(",",$settings["payment_variant"]))){
                                   $selected = 'selected=""';
                                }else{
                                   $selected = '';
                                }
                              }else{ $selected = ''; }

                              ?>
                              <option value="<?php echo $value["id"]; ?>" <?php echo $selected; ?> ><?php echo $value["name"]; ?></option>                          
                              <?php
                             }
                           }
                        ?>
                        
                     </select>
                     <div class="mt10" ></div>
                     <small>Выберите платежные системы которые будут использоваться на сайте при оплате.</small>
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label"></label>
                <div class="col-lg-9">

                    <?php
                       $get = getAll("select * from uni_payments");
                       if(count($get) > 0){
                       	 foreach ($get as $key => $value) {
                       	 	?>
          								<div class="styled-radio" style="display: inline-block; margin-right: 25px;" >

          									<input type="radio" name="payment" class="change-payment" value="<?php echo $value["code"]; ?>" id="payment-radio-<?php echo $value["id"]; ?>" >
          									<label for="payment-radio-<?php echo $value["id"]; ?>"><img src="<?php echo Exists($config["media"]["other"],$value["logo"],$config["media"]["no_image"]); ?>" style="max-height: 40px; max-width: 70px;" ></label>

          								</div>                       	 	
                       	 	<?php
                       	 }
                       }
                    ?>	

                </div>
             </div>         
            
             <div class="param-payment" ></div>

         </div>


         <div class="tab-pane fade <?php if($_GET["tab"] == "sitemap"){ echo 'active show'; } ?>" id="j-tab-11" role="tabpanel" aria-labelledby="just-tab-11">

          <br />

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Sitemap</label>
                <div class="col-lg-9">
                   <a href="<?php echo $config["urlPath"]; ?>/sitemap.xml" target="_blank" ><?php echo $config["urlPath"]; ?>/sitemap.xml</a>
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Выводить города</label>
                <div class="col-lg-9">
                    <label>
                      <input class="toggle-checkbox-sm" type="checkbox" name="sitemap_cities" value="1" <?php if($settings["sitemap_cities"] == 1){ echo ' checked=""'; } ?> >
                      <span><span></span></span>
                    </label>
                    <div>
                      <small>Будут учитываться только те города, где есть объявления</small>
                    </div>
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Выводить категории</label>
                <div class="col-lg-9">
                    <label>
                      <input class="toggle-checkbox-sm" type="checkbox" name="sitemap_category" value="1" <?php if($settings["sitemap_category"] == 1){ echo ' checked=""'; } ?> >
                      <span><span></span></span>
                    </label>
                    <div>
                      <small>Будут учитываться только те категории, где есть объявления</small>
                    </div>
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Выводить именованные фильтры</label>
                <div class="col-lg-9">
                    <label>
                      <input class="toggle-checkbox-sm" type="checkbox" name="sitemap_alias_filters" value="1" <?php if($settings["sitemap_alias_filters"] == 1){ echo ' checked=""'; } ?> >
                      <span><span></span></span>
                    </label>
                    <div>
                      <small>Будут учитываться только те фильтры, где есть объявления</small>
                    </div>
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Выводить seo фильтры</label>
                <div class="col-lg-9">
                    <label>
                      <input class="toggle-checkbox-sm" type="checkbox" name="sitemap_seo_filters" value="1" <?php if($settings["sitemap_seo_filters"] == 1){ echo ' checked=""'; } ?> >
                      <span><span></span></span>
                    </label>
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Выводить статьи</label>
                <div class="col-lg-9">
                    <label>
                      <input class="toggle-checkbox-sm" type="checkbox" name="sitemap_blog" value="1" <?php if($settings["sitemap_blog"] == 1){ echo ' checked=""'; } ?> >
                      <span><span></span></span>
                    </label>
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Выводить категории статей</label>
                <div class="col-lg-9">
                    <label>
                      <input class="toggle-checkbox-sm" type="checkbox" name="sitemap_blog_category" value="1" <?php if($settings["sitemap_blog_category"] == 1){ echo ' checked=""'; } ?> >
                      <span><span></span></span>
                    </label>
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Выводить сервисные страницы</label>
                <div class="col-lg-9">
                    <label>
                      <input class="toggle-checkbox-sm" type="checkbox" name="sitemap_services" value="1" <?php if($settings["sitemap_services"] == 1){ echo ' checked=""'; } ?> >
                      <span><span></span></span>
                    </label>
                </div>
             </div>

             <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Выводить магазины</label>
                <div class="col-lg-9">
                    <label>
                      <input class="toggle-checkbox-sm" type="checkbox" name="sitemap_shops" value="1" <?php if($settings["sitemap_shops"] == 1){ echo ' checked=""'; } ?> >
                      <span><span></span></span>
                    </label>
                </div>
             </div>

         </div>

         <div class="tab-pane fade <?php if($_GET["tab"] == "robots"){ echo 'active show'; } ?>" id="j-tab-12" role="tabpanel" aria-labelledby="just-tab-12">

          <br />

           <div class="form-group row d-flex align-items-center mb-5">
              <label class="col-lg-3 form-control-label">Ручная настройка</label>
              <div class="col-lg-9">
                  <label>
                    <input class="toggle-checkbox-sm" type="checkbox" name="robots_manual_setting" value="1" <?php if($settings["robots_manual_setting"] == 1){ echo ' checked=""'; } ?> >
                    <span><span></span></span>
                  </label>
              </div>
           </div>

           <div class="robots_index_site" <?php if($settings["robots_manual_setting"]){ echo 'style="display: none;"'; } ?> >
             
               <div class="form-group row d-flex align-items-center mb-5">
                  <label class="col-lg-3 form-control-label">Индексировать сайт</label>
                  <div class="col-lg-9">
                      <label>
                        <input class="toggle-checkbox-sm" type="checkbox" name="robots_index_site" value="1" <?php if($settings["robots_index_site"] == 1){ echo ' checked=""'; } ?> >
                        <span><span></span></span>
                      </label>
                  </div>
               </div>

               <div class="form-group row d-flex align-items-center mb-5">
                  <label class="col-lg-3 form-control-label">Исключенные страницы</label>
                  <div class="col-lg-9">
                      <textarea  class="form-control" rows="3" name="robots_exclude_link" ><?php echo $settings["robots_exclude_link"]; ?></textarea>
                      <small>Укажите с новой строки ссылки которые не будут учавствовать в индексации.</small>
                  </div>
               </div>

           </div>

           <div class="robots_manual_setting" <?php if(!$settings["robots_manual_setting"]){ echo 'style="display: none;"'; } ?> >

            <textarea  class="form-control robots-textarea" name="robots" ><?php echo getFile($config["basePath"]."/robots.txt"); ?></textarea>

          </div>

         </div>

         <div class="tab-pane fade <?php if($_GET["tab"] == "lang"){ echo 'active show'; } ?>" id="j-tab-13" role="tabpanel" aria-labelledby="just-tab-13">

          <br />

            <div class="form-group">
                <a  href="#" data-toggle="modal" data-target="#modal-add-lang" class="btn btn-gradient-04 mr-1 mb-2">Добавить язык</a>
            </div>

            <div class="table-responsive">

                 <?php
                    $get = getAll("SELECT * FROM uni_languages order by id_position asc");     

                     if(count($get) > 0){   

                     ?>
                     <table class="table mb-0">
                        <thead>
                           <tr>
                            <th></th>
                            <th>Язык</th>
                            <th>iso</th>
                            <th>Статус</th>
                            <th style="text-align: right;" ></th>
                           </tr>
                        </thead>
                        <tbody class="sort-container" >                     
                     <?php

                        foreach($get AS $array_data){
 
                        ?>

                         <tr id="item<?php echo $array_data["id"]; ?>" >
                             <td><span class="icon-move move-sort" ><i class="la la-arrows-v"></i></span></td>
                             <td>
                              
                              <div class="float-flex" >  
                                <div class="adaptive-box-icon" >
                                  <img src="<?php echo Exists($config["media"]["other"],$array_data["image"],$config["media"]["no_image"]); ?>" width="32px" >
                                </div>

                                <div class="adaptive-box-name" >
                                <?php echo $array_data["name"]; ?>
                                </div>
                              </div>
                                    
                            </td>
                             <td><?php echo $array_data["iso"]; ?></td>
                             <td>
                               <?php if($array_data["status"]){ ?>
                                <span class="badge-text badge-text-small info">Виден</span>
                               <?php }else{ ?>
                                <span class="badge-text badge-text-small danger">Скрыт</span>
                               <?php } ?>
                             </td> 
                             <td style="text-align: right;" class="td-actions" >
                              <a class="load_edit_lang" data-id="<?php echo $array_data["id"]; ?>" ><i class="la la-edit edit"></i></a>
                              <a class="delete-lang" data-id="<?php echo $array_data["id"]; ?>" ><i class="la la-close delete"></i></a>
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
                               <p>Языков нет</p>
                            </div>
                         <?php

                     }                  
                  ?>

            </div>


         </div>

         <div class="tab-pane fade <?php if($_GET["tab"] == "secure"){ echo 'active show'; } ?>" id="j-tab-14" role="tabpanel" aria-labelledby="just-tab-14">

           <br>

           <div class="form-group row d-flex align-items-center mb-5" >
              <label class="col-lg-3 form-control-label">Платежная система</label>
              <div class="col-lg-2">

                  <select class="selectpicker" name="secure_payment_service_name" title="Не выбрано" >
                      <?php
                        $getPayments = getAll("select * from uni_payments where secure=?", [1]);
                        if (count($getPayments)) {
                            foreach ($getPayments as $key => $value) {
                                ?>
                                <option <?php if( $settings["secure_payment_service_name"] == $value["code"] ){ echo 'selected=""'; } ?> value="<?php echo $value["code"]; ?>" ><?php echo $value["name"]; ?></option>
                                <?php
                            }
                        }
                      ?>
                  </select>

              </div>
           </div>

           <div class="container-secure-service" <?php if( $settings["secure_payment_service_name"] ){ echo 'style="display: block;"'; } ?> >

           <?php
              if( $settings["secure_payment_service_name"] ){
                  $getPayment = findOne( "uni_payments", "code=?" , [ $settings["secure_payment_service_name"] ] );
              }
           ?>
         
           <div class="form-group row d-flex align-items-center" style="margin-bottom: 0px;" >
              <label class="col-lg-3 form-control-label">Доход от безопасной сделки</label>
              <div class="col-lg-2">

                  <div class="input-group mb-2">
                     <input type="text" class="form-control" name="secure_percent_service" value="<?php echo $getPayment["secure_percent_service"]; ?>" >
                     <div class="input-group-prepend">
                        <div class="input-group-text">%</div>
                     </div>                       
                  </div>

              </div>
           </div>

           <div class="form-group row d-flex align-items-center mb-5">
              <label class="col-lg-3 form-control-label"></label>
              <div class="col-lg-9">

                  <small>Укажите процент который вы будете получать от безопасной сделки</small>

              </div>
           </div>

           <div class="form-group row d-flex align-items-center"  style="margin-bottom: 0px;" >
              <label class="col-lg-3 form-control-label">Процент платежной системы</label>
              <div class="col-lg-2">

                  <div class="input-group mb-2">
                     <input type="text" class="form-control" name="secure_percent_payment" value="<?php echo $getPayment["secure_percent_payment"]; ?>" >
                     <div class="input-group-prepend">
                        <div class="input-group-text">%</div>
                     </div>                       
                  </div>

              </div>
           </div>

           <div class="form-group row d-flex align-items-center mb-5">
              <label class="col-lg-3 form-control-label"></label>
              <div class="col-lg-9">

                  <small>Укажите процент который списывает платежная система при переводе денег по безопасной сделке</small>

              </div>
           </div>

           <div class="form-group row d-flex align-items-center mb-5">
              <label class="col-lg-3 form-control-label">Дополнительные вычеты платежной системы</label>
              <div class="col-lg-2">

                  <div class="input-group mb-2">
                     <input type="text" class="form-control" name="secure_other_payment" value="<?php echo $getPayment["secure_other_payment"]; ?>" >
                     <div class="input-group-prepend">
                        <div class="input-group-text"><?php echo $settings["currency_main"]["sign"]; ?></div>
                     </div>                       
                  </div>

              </div>
           </div>

           <div class="form-group row d-flex align-items-center mb-5">
              <label class="col-lg-3 form-control-label"></label>
              <div class="col-lg-9">
                 <h3 style="margin-top: 15px" > <strong>Лимиты</strong> </h3>
                 <small>Лимиты на массовую выплату средств устанавливает платежная система. Безопасная сделка не будет действовать если сумма товара будет меньше <?php echo $getPayment["secure_min_amount_payment"]; ?> или больше <?php echo $getPayment["secure_max_amount_payment"]; ?></small>
              </div>
           </div>           

           <div class="form-group row d-flex align-items-center mb-5">
              <label class="col-lg-3 form-control-label">Минимальная сумма безопасной сделки</label>
              <div class="col-lg-2">

                  <div class="input-group mb-2">
                     <input type="text" class="form-control" name="secure_min_amount_payment" value="<?php echo $getPayment["secure_min_amount_payment"]; ?>" >
                     <div class="input-group-prepend">
                        <div class="input-group-text"><?php echo $settings["currency_main"]["sign"]; ?></div>
                     </div>                       
                  </div>

              </div>
           </div>

           <div class="form-group row d-flex align-items-center mb-5">
              <label class="col-lg-3 form-control-label">Максимальная сумма безопасной сделки</label>
              <div class="col-lg-2">

                  <div class="input-group mb-2">
                     <input type="text" class="form-control" name="secure_max_amount_payment" value="<?php echo $getPayment["secure_max_amount_payment"]; ?>" >
                     <div class="input-group-prepend">
                        <div class="input-group-text"><?php echo $settings["currency_main"]["sign"]; ?></div>
                     </div>                       
                  </div>

              </div>
           </div>

           </div>


         </div>


      </div>
      </form>


   </div>
</div>

<div class="text-right" >
<button type="button" class="btn btn-success save-settings">Сохранить</button>
</div>

</div>
</div>       


<div id="modal-add-currency" class="modal fade">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title">Добавление валюты</h4>
            <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span>
            <span class="sr-only">close</span>
            </button>
         </div>
         <div class="modal-body">
            
               <form method="post" class="form-add-currency" >
                <div class="row" >
                   <div class="col-lg-6" >
                   <label>Название</label>
                   <input type="text" class="form-control" name="name" placeholder="Доллары" />
                   </div>

                   <div  class="col-lg-3" >
                   <label>Знак</label>
                   <input type="text" class="form-control" name="sign" placeholder="$" />
                   </div>

                   <div  class="col-lg-3" >
                   <label>Код</label>
                   <input type="text" class="form-control" name="code" placeholder="USD" />
                   </div>

                </div>   
               </form>

         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-shadow" data-dismiss="modal">Закрыть</button>
            <button type="button" class="btn btn-primary settings-add-currency">Добавить</button>
         </div>
      </div>
   </div>
</div>


<div id="modal-email-templates" class="modal fade">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title">Редактирование письма</h4>
            <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span>
            <span class="sr-only">close</span>
            </button>
         </div>
         <div class="modal-body container-templates"></div>
         <div class="modal-footer">
            <button type="button" class="btn btn-shadow" data-dismiss="modal">Закрыть</button>
            <button type="button" class="btn btn-primary settings-edit-email-template">Сохранить</button>
         </div>
      </div>
   </div>
</div>


<div id="modal-log" class="modal fade">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title">Результат теста</h4>
            <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span>
            <span class="sr-only">close</span>
            </button>
         </div>
         <div class="modal-body">
         	<textarea class="result-log form-control" style="min-height: 200px;" ></textarea>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-shadow" data-dismiss="modal">Закрыть</button>
         </div>
      </div>
   </div>
</div>

<div id="modal-add-lang" class="modal fade">
   <div class="modal-dialog" style="max-width: 650px;" >
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title">Добавление языка'</h4>
            <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span>
            <span class="sr-only">close</span>
            </button>
         </div>
         <div class="modal-body">
            
               <form method="post" class="form-add-lang" >

                  <div class="form-group row mb-5">
                    <label class="col-lg-4 form-control-label">Статус</label>
                    <div class="col-lg-8">
                        <label>
                          <input class="toggle-checkbox-sm" type="checkbox" name="status" checked="" value="1" >
                          <span><span></span></span>
                        </label>
                    </div>
                  </div>

                  <div class="form-group row d-flex align-items-center mb-5">
                    <label class="col-lg-4 form-control-label">Иконка</label>
                    <div class="col-lg-8">
                          <img class="change-img" src="<?php echo $settings["path_other"]; ?>/icon_photo_add.png" width="60px" >
                          <input type="file" name="image" class="input-img" >
                    </div>
                  </div>

                  <div class="form-group row d-flex align-items-center mb-5">
                    <label class="col-lg-4 form-control-label">Название</label>
                    <div class="col-lg-8">
                         <input type="text" class="form-control setTranslate" name="name" >
                    </div>
                  </div>
  
                  <div class="form-group row d-flex align-items-center mb-5">
                    <label class="col-lg-4 form-control-label">Алиас</label>
                    <div class="col-lg-8">
                         <input type="text" class="form-control outTranslate" name="alias" >
                    </div>
                  </div>

                  <div class="form-group row d-flex align-items-center mb-5">
                    <label class="col-lg-4 form-control-label">ISO</label>
                    <div class="col-lg-8">
                         <input type="text" class="form-control" name="iso" >
                    </div>
                  </div>

               </form>

         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-shadow" data-dismiss="modal">Закрыть</button>
            <button type="button" class="btn btn-primary add-lang">Добавить</button>
         </div>
      </div>
   </div>
</div>


<div id="modal-edit-lang" class="modal fade" >
   <div class="modal-dialog"  style="max-width: 650px;" >
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title">Редактирование языка</h4>
            <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span>
            <span class="sr-only">close</span>
            </button>
         </div>
         <div class="modal-body">
            
               <form method="post" class="form-edit-lang" ></form>

         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-shadow" data-dismiss="modal">Закрыть</button>
            <button type="button" class="btn btn-primary edit-lang">Сохранить</button>
         </div>
      </div>
   </div>
</div>
    
<script type="text/javascript" src="include/modules/settings/script.js"></script>
