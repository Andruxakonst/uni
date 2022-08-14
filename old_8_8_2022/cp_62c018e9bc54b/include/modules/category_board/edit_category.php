<?php if( !defined('unisitecms') ) exit;

include("fn.php");

$array_data = findOne("uni_category_board","category_board_id=?", array($id));
if(count($array_data) == 0){
   exit;
}

$cat_ids[] = $array_data["category_board_id"];

$Filters = new Filters();
?>


<div class="row">
   <div class="page-header">
      <div class="d-flex align-items-center">
         <h2 class="page-header-title"><?php echo $array_data->category_board_name;?></h2>
         <div>
            <ul class="breadcrumb">
               <li class="breadcrumb-item"><a href="?route=category_board">Категории</a></li>
            </ul>
         </div>
      </div>
   </div>
</div>  


<div class="row" >
   <div class="col-lg-12" >

      <div class="widget has-shadow">

         <div class="widget-body">

            <form class="form-data" >

              <div class="form-group row d-flex align-items-center">
                <label class="col-lg-3 form-control-label">Статус видимости</label>
                <div class="col-lg-6">
                    <label class="mb0">
                      <input class="toggle-checkbox-sm toolbat-toggle" type="checkbox" name="visible" <?php if($array_data->category_board_visible){ echo 'checked=""'; } ?> value="1" >
                      <span><span></span></span>
                    </label>
                </div>
              </div>

              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Изображение</label>
                <div class="col-lg-7">

                      <div class="small-image-container" >
                        <span class="small-image-delete" <?php if(!$array_data->category_board_image){ echo 'style="display: none;"'; } ?> > <i class="la la-trash"></i> </span>

                        <?php echo img( array( "img1" => array( "class" => "change-img", "path" => $config["media"]["other"] . "/" . $array_data->category_board_image, "width" => "60px" ), "img2" => array( "class" => "change-img", "path" => $config["media"]["other"] . "/icon_photo_add.png", "width" => "60px" ) ) ); ?>

                        <input type="hidden" name="image_delete" value="0" >
                      </div>

                      <input type="file" name="image" class="input-img" >
                </div>
              </div>

              <hr>

              <div class="form-group row d-flex align-items-center" style="margin-top: 25px;" >
                <label class="col-lg-3 form-control-label">Отображать на главной</label>
                <div class="col-lg-6">
                    <label class="mb0">
                      <input class="toggle-checkbox-sm toolbat-toggle" type="checkbox" name="show_index" <?php if($array_data->category_board_show_index){ echo 'checked=""'; } ?> value="1" >
                      <span><span></span></span>
                    </label>
                </div>
              </div>

              <?php if($settings["functionality"]["marketplace"]){ ?>
              <div class="form-group row d-flex align-items-center" >
                <label class="col-lg-3 form-control-label">Маркетплейс</label>
                <div class="col-lg-6">
                    <label class="mb0">
                      <input class="toggle-checkbox-sm toolbat-toggle" type="checkbox" name="marketplace" <?php if($array_data->category_board_marketplace){ echo 'checked=""'; } ?> value="1" >
                      <span><span></span></span>
                    </label>
                </div>
              </div>
              <?php } ?>

              <div class="form-group row d-flex align-items-center" >
                <label class="col-lg-3 form-control-label">Цена при подаче объявления</label>
                <div class="col-lg-6">
                    <label class="mb0">
                      <input class="toggle-checkbox-sm toolbat-toggle" type="checkbox" name="display_price" <?php if($array_data->category_board_display_price){ echo 'checked=""'; } ?> value="1" >
                      <span><span></span></span>
                    </label>
                </div>
              </div>

              <div class="category-block-variant-price" <?php if($array_data->category_board_display_price){ echo 'style="display: block;"'; } ?> >
                
                <div class="form-group row d-flex align-items-center">
                  <label class="col-lg-3 form-control-label">Название поля</label>
                  <div class="col-lg-7">
                     <select name="variant_price" class="selectpicker" >
                        <option value="0" <?php if($array_data->category_board_variant_price == 0) echo 'selected=""'; ?> >Цена</option>
                        <option value="1" <?php if($array_data->category_board_variant_price == 1) echo 'selected=""'; ?> >Зарплата</option>
                        <option value="2" <?php if($array_data->category_board_variant_price == 2) echo 'selected=""'; ?> >Стоимость услуги</option>
                        <option value="3" <?php if($array_data->category_board_variant_price == 3) echo 'selected=""'; ?> >Арендная плата в месяц</option>
                        <option value="4" <?php if($array_data->category_board_variant_price == 4) echo 'selected=""'; ?> >Арендная плата за сутки</option>                        
                     </select>
                     <div style="margin-top: 5px;" ><small>Отображается при подаче объявления и в каталоге</small></div>
                  </div>
                </div>
                
                <div class="category-block-conditional-function" <?php if($array_data->category_board_variant_price != 0 && $array_data->category_board_variant_price != 2){ echo 'style="display: none;"'; } ?> >

                <?php if($settings["functionality"]["auction"]){ ?>
                <div class="form-group row d-flex align-items-center">
                  <label class="col-lg-3 form-control-label">Аукционы</label>
                  <div class="col-lg-6">
                      <label class="mb0" >
                        <input class="toggle-checkbox-sm" type="checkbox"  <?php if($array_data->category_board_auction){ echo 'checked=""'; } ?> name="auction" value="1" >
                        <span><span></span></span>
                      </label>
                  </div>
                </div>
                <?php } ?>
                
                <?php if($settings["functionality"]["secure"]){ ?>
                <div class="form-group row d-flex align-items-center">
                  <label class="col-lg-3 form-control-label">Безопасная сделка</label>
                  <div class="col-lg-6">
                      <label class="mb0" >
                        <input class="toggle-checkbox-sm" type="checkbox" name="secure" <?php if($array_data->category_board_secure){ echo 'checked=""'; } ?> value="1" >
                        <span><span></span></span>
                      </label>
                  </div>
                </div>
                <?php } ?>

                </div>
                
                <div class="category-block-conditional-online-view" <?php if($array_data->category_board_variant_price == 1 || $array_data->category_board_variant_price == 2){ echo 'style="display: none;"'; } ?> >

                <div class="form-group row d-flex align-items-center">
                  <label class="col-lg-3 form-control-label">Онлайн-показ</label>
                  <div class="col-lg-6">
                      <label class="mb0" >
                        <input class="toggle-checkbox-sm" type="checkbox" name="online_view" <?php if($array_data->category_board_online_view){ echo 'checked=""'; } ?> value="1" >
                        <span><span></span></span>
                      </label>
                  </div>
                </div>

                </div>

              </div>

              <div class="form-group row d-flex align-items-center">
                <label class="col-lg-3 form-control-label">Платная категория</label>
                <div class="col-lg-6">
                    <label class="mb0" >
                      <input class="toggle-checkbox-sm toolbat-toggle" type="checkbox" name="paid" <?php if($array_data->category_board_status_paid){ echo 'checked=""'; } ?> value="1" >
                      <span><span></span></span>
                    </label>
                </div>
              </div>

              <div class="category-block-options" <?php if($array_data->category_board_status_paid){ echo 'style="display: block"'; } ?> >
                
              <div class="form-group row d-flex align-items-center">
                <label class="col-lg-3 form-control-label">Цена размещения</label>
                <div class="col-lg-2">
                    <div class="input-group mb-2">
                       <input type="number" class="form-control" name="price" value="<?php echo $array_data->category_board_price; ?>" >
                       <div class="input-group-prepend">
                          <div class="input-group-text"><?php echo $settings["currency_main"]["sign"]; ?></div>
                       </div>                       
                    </div>
                </div>
              </div>

              <div class="form-group row d-flex align-items-center">
                <label class="col-lg-3 form-control-label">Бесплатных размещений</label>
                <div class="col-lg-2">
                     <input type="text" class="form-control" name="count_free" value="<?php echo $array_data->category_board_count_free; ?>" >
                </div>
              </div>

              </div>
              
              <?php if( !findOne("uni_category_board", "category_board_id_parent=?", [$id]) ){ ?>
              <div class="form-group row d-flex align-items-center">
                <label class="col-lg-3 form-control-label">Автогенерация заголовка объявления</label>
                <div class="col-lg-6">
                    <label class="mb0">
                      <input class="toggle-checkbox-sm" type="checkbox" name="auto_title" <?php if($array_data->category_board_auto_title){ echo 'checked=""'; } ?> value="1" >
                      <span><span></span></span>
                    </label>
                </div>
              </div>

              <div class="category-block-options-auto-title" <?php if($array_data->category_board_auto_title){ echo 'style="display: block"'; } ?> >
                
                  <div class="form-group row d-flex align-items-center">
                    <label class="col-lg-3 form-control-label">Шаблон заголовка</label>
                    <div class="col-lg-7">

                        <div class="row" >
                            <div class="col-lg-9" >
                              
                                <input type="text" class="form-control" name="auto_title_template" value="<?php echo $array_data->category_board_auto_title_template; ?>" > 

                                <small>Укажите через запятую фильтры которые будут участвовать в формировании заголовка.</small>                      

                            </div>
                            <div class="col-lg-3" >
                              
                                <a href="#" data-toggle="modal" data-target="#modal-list-filters" class="btn btn-gradient-04 mr-1 mb-2" >Фильтры</a>

                            </div>
                        </div>

                    </div>
                  </div>

              </div>

              <?php } ?>

              <hr>

              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Название</label>
                <div class="col-lg-7">

                     <div class="row" >
                         <div class="col-lg-6" >
                            <input type="text" class="form-control" name="name" value="<?php echo $array_data->category_board_name; ?>" >
                         </div>
                         <div class="col-lg-6" >
                            <input type="text" class="form-control" name="alias" value="<?php echo $array_data->category_board_alias; ?>" > 
                         </div>
                     </div>

                </div>
              </div>

              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label"></label>
                <div class="col-lg-7">
                    <div class="alert alert-primary alert-dissmissible fade show" style="margin-top: 10px;" >
                        Макросы: {domen}, {url}, {country}, {city}, {region}, {geo}, {site_name}
                    </div>
                </div>
              </div>

              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Заголовок (Meta title)</label>
                <div class="col-lg-7">
                     <input type="text" class="form-control" name="title" value="<?php echo $array_data->category_board_title; ?>" >
                </div>
              </div>

              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Заголовок (h1)</label>
                <div class="col-lg-7">
                     <input type="text" class="form-control" name="h1" value="<?php echo $array_data->category_board_h1; ?>" >
                </div>
              </div>

              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Категория</label>
                <div class="col-lg-7">
                    <select name="id_cat" class="selectpicker" data-live-search="true" > 
                      <option value="0" >Основная категория</option>
                      <?php echo outCategoryOptions(); ?>     
                    </select>                      
                </div>
              </div>

              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Краткое описание (Meta Description)</label>
                <div class="col-lg-7">
                     <textarea class="form-control" name="desc" ><?php echo $array_data->category_board_description; ?></textarea>
                </div>
              </div>

              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Полное описание</label>
                <div class="col-lg-7">
                     <textarea name="text" class="ckeditor" ><?php echo urldecode($array_data->category_board_text); ?></textarea>
                </div>
              </div>              
              
              <input type="hidden" name="id" value="<?php echo $id;?>" />

              <div class="sticky-action" >

                    <hr>

                    <div class="sticky-action-box" >
                        <?php if( findOne("uni_category_board", "category_board_id_parent=?", [$id]) ){ ?>

                        <div class="sticky-action-button text-right" >
                            <label>
                              <input class="toggle-checkbox-sm toolbat-toggle" type="checkbox" name="subcategories" value="1" >
                              <span><span></span></span>
                            </label>
                            <div><small>Применить настройки к подкатегориям</small></div>
                        </div>

                        <?php } ?>

                        <div class="sticky-action-button" >
                            <span class="btn btn-success edit-category">Сохранить</span>
                        </div>
                    </div>
                      
               </div>

            </form>

         </div>

      </div>

   </div>
</div>

<div id="modal-list-filters" class="modal fade">
   <div class="modal-dialog modal-md">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title">Фильтры</h4>
            <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span>
            <span class="sr-only">close</span>
            </button>
         </div>
         <div class="modal-body">
            
             <?php

                $getCategoryFilters = $Filters->getCategory( [ "id_cat" => $array_data["category_board_id"] ] );
                
                if( count($getCategoryFilters) ){

                    $getFilters = getAll( "select * from uni_ads_filters where ads_filters_id IN(".implode(",", $getCategoryFilters).")" );

                    if( count($getFilters) ){
                        foreach ($getFilters as $key => $value) {

                            ?>
                            <div>
                              <span style="color: #98a8b4;" ><?php echo $value["ads_filters_name"]; ?></span> <span class="filter-copy" style="cursor: pointer;" >{<?php echo $value["ads_filters_alias"]; ?>}</span>
                            </div>
                            <?php

                        }
                    }

                }else{
                  ?>
                  <div> Фильтров нет </div>
                  <?php
                }
                
             ?>

         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-shadow" data-dismiss="modal">Закрыть</button>
         </div>
      </div>
   </div>
</div>

<script type="text/javascript" src="include/modules/category_board/script.js"></script>