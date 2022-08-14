<?php if( !defined('unisitecms') ) exit;

include("fn.php");
?>

<div class="row">
   <div class="page-header">
      <div class="d-flex align-items-center">
         <h2 class="page-header-title">Новая категория</h2>
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
                      <input class="toggle-checkbox-sm" type="checkbox" name="visible" checked="" value="1" >
                      <span><span></span></span>
                    </label>
                </div>
              </div>

              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Изображение</label>
                <div class="col-lg-7">

                      <div class="small-image-container" >
                        <span class="small-image-delete" style="display: none;" > <i class="la la-trash"></i> </span>

                        <img class="change-img" src="<?php echo $config["urlPath"] . "/" . $config["media"]["other"]; ?>/icon_photo_add.png" width="60px" >

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
                      <input class="toggle-checkbox-sm toolbat-toggle" type="checkbox" name="show_index" value="1" >
                      <span><span></span></span>
                    </label>
                </div>
              </div>
              
              <?php if($settings["functionality"]["marketplace"]){ ?>
              <div class="form-group row d-flex align-items-center" >
                <label class="col-lg-3 form-control-label">Маркетплейс</label>
                <div class="col-lg-6">
                    <label class="mb0">
                      <input class="toggle-checkbox-sm toolbat-toggle" type="checkbox" name="marketplace" value="1" >
                      <span><span></span></span>
                    </label>
                </div>
              </div>
              <?php } ?>

              <div class="form-group row d-flex align-items-center" >
                <label class="col-lg-3 form-control-label">Цена при подаче объявления</label>
                <div class="col-lg-6">
                    <label  class="mb0">
                      <input class="toggle-checkbox-sm" type="checkbox" checked="" name="display_price" value="1" >
                      <span><span></span></span>
                    </label>
                </div>
              </div>

              <div class="category-block-variant-price" style="display: block;" >
                
                <div class="form-group row d-flex align-items-center">
                  <label class="col-lg-3 form-control-label">Название поля</label>
                  <div class="col-lg-7">
                         <select name="variant_price" class="selectpicker" >
                            <option value="0" selected="" >Цена</option>
                            <option value="1" >Зарплата</option>
                            <option value="2" >Стоимость услуги</option>
                            <option value="3" >Арендная плата в месяц</option>
                            <option value="4" >Арендная плата за сутки</option>
                         </select>
                         <div style="margin-top: 5px;" ><small>Отображается при подаче объявления и в каталоге</small></div>
                  </div>
                </div>
                
                <div class="category-block-conditional-function" >

                <?php if($settings["functionality"]["auction"]){ ?>
                <div class="form-group row d-flex align-items-center">
                  <label class="col-lg-3 form-control-label">Аукционы</label>
                  <div class="col-lg-6">
                      <label class="mb0" >
                        <input class="toggle-checkbox-sm" type="checkbox" name="auction" value="1" >
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
                        <input class="toggle-checkbox-sm" type="checkbox" name="secure" value="1" >
                        <span><span></span></span>
                      </label>
                  </div>
                </div>
                <?php } ?>

                </div>

                <div class="category-block-conditional-online-view" >

                <div class="form-group row d-flex align-items-center">
                  <label class="col-lg-3 form-control-label">Онлайн-показ</label>
                  <div class="col-lg-6">
                      <label class="mb0" >
                        <input class="toggle-checkbox-sm" type="checkbox" name="online_view" value="1" >
                        <span><span></span></span>
                      </label>
                  </div>
                </div>

                </div>


              </div>

              <div class="form-group row d-flex align-items-center">
                <label class="col-lg-3 form-control-label">Платная категория</label>
                <div class="col-lg-6">
                    <label class="mb0">
                      <input class="toggle-checkbox-sm" type="checkbox" name="paid" value="1" >
                      <span><span></span></span>
                    </label>
                </div>
              </div>

              <div class="category-block-options" >
                
              <div class="form-group row d-flex align-items-center">
                <label class="col-lg-3 form-control-label">Цена размещения</label>
                <div class="col-lg-2">
                    <div class="input-group mb-2">
                       <input type="number" class="form-control" name="price" >
                       <div class="input-group-prepend">
                          <div class="input-group-text"><?php echo $settings["currency_main"]["sign"]; ?></div>
                       </div>                       
                    </div>
                </div>
              </div>

              <div class="form-group row d-flex align-items-center">
                <label class="col-lg-3 form-control-label">Бесплатных размещений</label>
                <div class="col-lg-2">
                     <input type="text" class="form-control" name="count_free" value="1" >
                </div>
              </div>

              </div>

              <hr>

              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Название</label>
                <div class="col-lg-7">

                     <div class="row" >
                         <div class="col-lg-6" >
                            <input type="text" class="form-control setTranslate" name="name" >
                         </div>
                         <div class="col-lg-6" >
                            <input type="text" class="form-control outTranslate" name="alias" placeholder="Алиас" > 
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
                     <input type="text" class="form-control" name="title" >
                </div>
              </div>

              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Заголовок (h1)</label>
                <div class="col-lg-7">
                     <input type="text" class="form-control" name="h1" >                    
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
                     <textarea class="form-control" name="desc" ></textarea>
                </div>
              </div>

              <div class="form-group row d-flex align-items-center mb-5">
                <label class="col-lg-3 form-control-label">Полное описание</label>
                <div class="col-lg-7">
                     <textarea name="text" class="ckeditor" ></textarea>                     
                </div>
              </div>              
              

              <div class="sticky-action" >

                <hr>

                <div class="sticky-action-box" >
                    <div class="sticky-action-button" >
                        <span type="button" class="btn btn-success add-category">Добавить</span>
                    </div>
                </div>
                  
              </div>

            </form>

         </div>

      </div>
      
   </div>
</div>

<script type="text/javascript" src="include/modules/category_board/script.js"></script>