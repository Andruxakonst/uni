<?php 
if( !defined('unisitecms') ) exit;

include( "templates/include/category_board/fn.php");

$getImport = findOne("uni_ads_import", "ads_import_id=? and ads_import_status=?", [$id,0]);
if(!$getImport) exit;

$path = "templates/include/ads_import/temp/" . $getImport["ads_import_file"];

$fields = ["title"=>"Название","price"=>"Цена","datetime_add"=>"Дата","phone"=>"Телефон","email"=>"Email","name_user"=>"Контактное лицо","mode_user"=>"Тип автора","city"=>"Город","region"=>"Регион","metro/area"=>"Метро/Район","address"=>"Адрес","text"=>"Описание","category"=>"Категория","filters"=>"Доп.параметры/Свойства","latitude"=>"lat (Широта)","longitude"=>"lng (Долгота)","images"=>"Ссылки на картинки"];

$char = csvSplitChar( fopen($path, "rb") );

function csvCombine( $handle, $char = ';' ){

  $all_rows = array(); 
  $header = fgetcsv($handle, 0, $char); 

  while ($row = fgetcsv($handle, 0, $char)) { 
    return array_combine($header, $row); 
    break;
  }

}

?>

<div class="row">
   <div class="page-header">
      <div class="d-flex align-items-center">
         <h2 class="page-header-title">Настройка импорта</h2>
      </div>
   </div>
</div> 

<div class="row" >
   <div class="col-lg-12" >
      <div class="widget has-shadow">
          
          <div class="widget-body">
            
            <form class="form-import" >

            <input type="hidden" name="params[csv_char]" value="<?php echo $char; ?>" >

            <div class="form-group row d-flex align-items-center mb-5">
              <label class="col-lg-3 form-control-label">Файл импорта</label>
              <div class="col-lg-6">
                  <strong><?php echo $getImport["ads_import_file"]; ?></strong>
              </div>
            </div>

            <div class="form-group row d-flex align-items-center mb-5">
              <label class="col-lg-3 form-control-label">Позиций</label>
              <div class="col-lg-6">
                  <strong><?php echo $getImport["ads_import_count"]; ?></strong>
              </div>
            </div>

            <div class="form-group row  mb-5">
              <label class="col-lg-3 form-control-label">Загружать позиций за 1 раз</label>
              <div class="col-lg-9">
                    <input type="text" name="params[count_import]" value="1000" style="max-width: 120px;" class="form-control" > 
              </div>
            </div>

            <div class="form-group row  mb-5">
              <label class="col-lg-3 form-control-label">Категория</label>
              <div class="col-lg-6">
                    <select name="params[change_category]" class="selectpicker" >
                        <option value="0" >Определять автоматически</option>
                        <?php echo outCategoryOptions(); ?>
                    </select>
              </div>
            </div>

            <div class="form-group row  mb-5">
              <label class="col-lg-3 form-control-label">Автоматическое продление объявлений</label>
              <div class="col-lg-6">
                    <label>
                      <input class="toggle-checkbox-sm" type="checkbox" name="params[auto_renewal]" checked="" value="1" >
                      <span><span></span></span>
                    </label>                  
              </div>
            </div>

            <div class="form-group row  mb-5">
              <label class="col-lg-3 form-control-label">Загружать только с фото</label>
              <div class="col-lg-6">
                    <label>
                      <input class="toggle-checkbox-sm" type="checkbox" name="params[always_image]" checked="" value="1" >
                      <span><span></span></span>
                    </label>                  
              </div>
            </div>

            <div class="form-group row  mb-5">
              <label class="col-lg-3 form-control-label">Watermark</label>
              <div class="col-lg-6">
                    <label>
                      <input class="toggle-checkbox-sm" type="checkbox" name="params[watermark]" checked="" value="1" >
                      <span><span></span></span>
                    </label>                  
              </div>
            </div>

            <div class="form-group row  mb-5">
              <label class="col-lg-3 form-control-label">Сколько загружать изображений</label>
              <div class="col-lg-9">
                    <input type="text" name="params[count_load_image]" value="3" style="max-width: 120px;" class="form-control" > 
              </div>
            </div>

            <div class="form-group row  mb-5">
              <label class="col-lg-3 form-control-label">Минимальный размер изображений</label>
              <div class="col-lg-9">
                    <input type="text" name="params[min_size_image]" value="50000" style="max-width: 120px;" class="form-control" > 
                    <small>Данный параметр позволяет отсеить слишком маленькие и некачественные изображения. <br> Значение должно быть в килобайтах.</small>
              </div>
            </div>

            <hr>

            <?php

            if( file_exists($path) ){

               $csvCombine = csvCombine( fopen($path, "rb"), $char );

               foreach ($fields as $key => $field) {
                 ?>

                  <div class="form-group row d-flex align-items-center mb-5">
                    <label class="col-lg-3 form-control-label"><strong><?php echo $field; ?> <?php if( in_array($key, ["title", "phone", "name_user", "city", "region", "text", "category"] ) ){ echo '<span style="color: red;" >*</span>'; } ?> </strong></label>
                    <div class="col-lg-9">
                       <select name="params[<?php echo $key; ?>]" class="selectpicker" >
                       <option value="" >Не выбрано</option>
                       <?php
                           foreach($csvCombine AS $name => $value){
                              
                             ?>
                              <optgroup label="<?php echo $name; ?>">
                                <?php if($value){ ?>
                                <option value="<?php echo $name; ?>" <?php if(strpos($field,$name) !== false){ echo 'selected=""'; } ?> ><?php echo custom_substr($value, 50); ?></option>
                                <?php }else{ ?>
                                <option value="<?php echo $name; ?>" ><?php echo $name; ?></option>
                                <?php } ?>
                              </optgroup>
                             <?php
                             
                           }
                       ?>
                       </select>
                    </div>
                  </div>

                 <?php
               }


            }


            ?>
            
            <input type="hidden" name="id" value="<?php echo $id; ?>" >

            </form>

          </div>

      </div>

      <div class="text-right" >
      <button type="button" class="btn btn-success import-start">Создать импорт</button>
      </div>

   </div>
</div>

<script type="text/javascript" src="templates/include/ads_import/script.js"></script>     




