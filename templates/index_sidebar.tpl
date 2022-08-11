<div class="left-list-category d-flex flex-wrap" >
 
     <?php

        if(count($getCategoryBoard["category_board_id_parent"][0])){
              $cnt = 0;
              foreach ($getCategoryBoard["category_board_id_parent"][0] as $key => $value) {
                if($cnt == 0 || $cnt % 3 == 0){echo '<div class="row-tri mr-3">';}
                $cnt ++;
                ?>
                    <a href="<?php echo $CategoryBoard->alias($value["category_board_chain"]); ?>" <?php echo $active; ?> > 
                        <?php echo $ULang->t( $value["category_board_name"], [ "table" => "uni_category_board", "field" => "category_board_name" ] ); ?> 
                        <span class="category-label-count" ><?php echo $CategoryBoard->getCountAd( $value["category_board_id"] ); ?>
                        </span>
                      </a>
                <?php
                if($cnt % 3 == 0){echo '</div>';}

              }
        }

     ?>

</div>

<?php if($data["article_rand"]["count"]){ ?>

<div class="blog-view-article-rand mt40" >   
  <div class="row" >
  <?php
  
    foreach ($data["article_rand"]["all"] as $key => $value) {
      ?>
      <div class="col-lg-12 col-6" >
        <a href="<?php echo $Blog->aliasArticle($value); ?>" title="<?php echo $ULang->t( $value["blog_articles_title"], [ "table"=>"uni_blog_articles", "field"=>"blog_articles_title" ] ); ?>"  >
         <div class="view-article-rand-image" ><img src="<?php echo Exists($config["media"]["big_image_blog"],$value["blog_articles_image"],$config["media"]["no_image"]); ?>" alt="<?php echo $ULang->t( $value["blog_articles_title"], [ "table"=>"uni_blog_articles", "field"=>"blog_articles_title" ] ); ?>" ></div>
         <p><?php echo $ULang->t( $value["blog_articles_title"], [ "table"=>"uni_blog_articles", "field"=>"blog_articles_title" ] ); ?></p>
        </a>
      </div>
      <?php
    }
  
  ?>
  </div>
</div>
<?php } ?>

<?php echo $Banners->out( ["position_name"=>"index_sidebar"] ); ?>