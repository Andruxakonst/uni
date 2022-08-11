<!doctype html>
<html lang="<?php echo getLang(); ?>">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <meta name="description" content="<?php echo $Seo->out(array("page" => "index", "field" => "meta_desc")); ?>">

    <title><?php echo $Seo->out(array("page" => "index", "field" => "meta_title")); ?></title>
    
    <?php include $config["template_path"] . "/head.tpl"; ?>

  </head>

  <body data-prefix="<?php echo $config["urlPrefix"]; ?>" data-template="<?php echo $config["template_folder"]; ?>" data-header-sticky="true" data-type-loading="<?php echo $settings["type_content_loading"]; ?>" data-page-name="<?php echo $route_name; ?>" >

    <?php include $config["template_path"] . "/header.tpl"; ?>
    
    <div class="container" >
       
        <div class="row" >
          <div class="col-lg-12 col-12" >
            <?php include $config["template_path"] . "/index_sidebar.tpl"; ?>
          </div>
        </div>
       <div class="row" >
          <div class="col-lg-12 col-12" >

           <div class="load-content-index-container" >
               
                <div class="preload" >

                    <div class="spinner-grow mt80 preload-spinner" role="status">
                      <span class="sr-only"></span>
                    </div>

                </div>

           </div>

           <div class="schema-text" >
             <?php if($data["seo_text"]){ ?> <div class="mt35" > <?php echo $data["seo_text"]; ?> </div> <?php } ?>
           </div>

           <?php echo $Banners->out( ["position_name"=>"index_bottom"] ); ?>

          </div>
       </div>

    </div>

    <div class="mt35" ></div>

    <?php include $config["template_path"] . "/footer.tpl"; ?>

  </body>
</html>