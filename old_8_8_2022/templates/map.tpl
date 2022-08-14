<!doctype html>
<html lang="<?php echo $settings["lang_site_default"]; ?>">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title><?php echo $ULang->t("Объявления на карте"); ?> - <?php echo $settings["site_name"]; ?></title>

    <?php include $config["template_path"] . "/head.tpl"; ?>

  </head>

  <body data-prefix="<?php echo $config["urlPrefix"]; ?>" data-template="<?php echo $config["template_folder"]; ?>" >
    
      <div class="row no-gutters" >
          <div class="col-lg-8" >
            
            <div class="map-search-container" >

              <div class="map-search-controls-top" >
                   
                   <div class="map-back-site-link" >
                   <a href="<?php echo _link($_SESSION["geo"]["alias"]); ?>" >
                     <i class="las la-arrow-left"></i> <?php echo $ULang->t("Каталог"); ?>
                   </a>
                   </div>

                   <div class="map-search-controls-filters open-modal" data-id-modal="modal-map-filters" >
                     <i class="las la-filter"></i> <?php echo $ULang->t("Категории и фильтры"); ?> <?php if(count($data["param_filter"]["filter"])){ ?> <span class="label-count" ><?php echo count($data["param_filter"]["filter"]); ?></span> <?php } ?>
                   </div>

                   <div class="map-search-controls-subscriptions catalog-ads-subscriptions-add" style="display: none;" >
                     <i class="las la-bell"></i> <?php echo $ULang->t("Подписаться"); ?>
                   </div>

                   <?php if( count($data["points"]) && $settings["map_vendor"] == "yandex" ){ ?>
                   <div class="map-search-controls-draw" id="map-search-controls-draw" <?php if($_GET["coordinates"]){ echo 'style="display: none;"'; } ?> >
                    <svg xmlns="http://www.w3.org/2000/svg" height="15" viewBox="0 0 28 25">
                        <g fill="none" fill-rule="evenodd" stroke="currentColor" transform="rotate(90 12.5 14)">
                            <path stroke-width="2" d="M5.9 3.273c2.466-2.195 13.518-5.957 14.01-.302.156 1.79-1.874 3.436-1.475 5.187.393 1.723 3.61 2.214 3.565 3.98-.042 1.61-3.015 1.694-3.75 3.135-1.863 3.658 4.84 5.727 2.15 8.984-3.06 3.714-20.53-5.01-20.398-7.9.164-3.59 4.257-1.693 6.206-3.797 2.53-2.73-8.662-6.157-2.704-8.322L5.9 3.273z"></path>
                            <circle cx="8.5" cy="22.5" r="1.5" fill="#FFF"></circle>
                        </g>
                    </svg>
                   <?php echo $ULang->t("Нарисовать область"); ?>
                   </div>

                   <div class="map-search-controls-draw-close" <?php if($_GET["coordinates"]){ echo 'style="display: inline-block;"'; }else{ echo 'style="display: none;"'; } ?> >
                    <i class="las la-times"></i> <?php echo $ULang->t("Отменить"); ?>
                   </div>
                   <?php } ?>  

              </div>

              <div class="map-search-instance" id="map_instance" ></div>

              <canvas id="draw-canvas" style="position: absolute; left: 0; top: 0; display: none;"></canvas>

            </div>

          </div>
          <div class="col-lg-4" >
            <div class="map-search-offers" >

                 <div class="map-search-offers-header" >
                    <h6><strong class="map-search-offers-header-count" ></strong></h6>
                    <div class="map-search-offers-subscribe" >
                      <span class="catalog-ads-subscriptions-add" ><i class="las la-bell"></i> <?php echo $ULang->t("Подписаться на новые объявления"); ?></span>
                    </div>
                 </div>

                 <div class="map-search-offers-list" >
                     <?php
                       if( !count($data["points"]) ){
                           ?>
                            <div class="map-no-result" >
                            <i class="las la-search-location"></i>
                            <h6><strong><?php echo $ULang->t("Ничего не найдено"); ?></strong></h6>
                            <p><?php echo $ULang->t("Увы, мы не нашли то, что вы искали. Смягчите условия поиска и попробуйте еще раз."); ?></p>
                            </div>                          
                           <?php
                       }
                     ?>
                 </div>                 
            </div>
          </div>
      </div>

    <?php include $config["template_path"] . "/footer.tpl"; ?>

    <?php if($settings["map_vendor"] == "yandex"){ ?>

      <script src="//api-maps.yandex.ru/2.1/?apikey=<?php echo $settings["map_yandex_key"]; ?>&lang=ru_RU" type="text/javascript"></script>

    <?php }elseif($settings["map_vendor"] == "google"){ ?>

      <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $settings["map_google_key"]; ?>&libraries=places"></script>
      <script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"></script>

    <?php }elseif($settings["map_vendor"] == "openstreetmap"){ ?>

      <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
              integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
              crossorigin=""/>
      <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
              integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
              crossorigin=""></script> 

    <?php } ?>

    <script type="text/javascript">
      
      $(document).ready(function () {

          $.ajaxSetup({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
          });

         function tippy_load(){

            tippy('[data-tippy-placement]', {
              delay: 100,
              arrow: true,
              arrowType: 'sharp',
              size: 'regular',
              duration: 200,
              animation: 'shift-away',
              animateFill: true,
              theme: 'dark',
              distance: 10,
            });

         }

         function countDisplay(){
            $.ajax({type: "POST",url: $("body").data("prefix") + "systems/ajax/ads.php",data: "action=update_count_display",dataType: "json",cache: false});
         }

         tippy_load();

         function initializeSlider(){

          var slider = $('.photo-mobile-slider').not('.lightSlider').lightSlider({
              item:1,
              loop:false,
              pager:false,
              slideMargin:2,
              slideMove:1,
              speed:600,
              responsive : [
                  {
                      breakpoint:800,
                      settings: {
                        item:2,
                        slideMove:1
                      }
                  },
                  {
                      breakpoint:480,
                      settings: {
                        item:2,
                        slideMove:1
                      }
                  }
              ]
          });

         }

         function loadOffers(ids,page){

              $(".map-search-offers-list").html(`
                      <div class="preload" >
                          <div class="spinner-grow preload-spinner" role="status">
                            <span class="sr-only"></span>
                          </div>
                      </div>
              `);

              $.ajax({type: "POST",url: $("body").data("prefix") + "systems/ajax/ads.php",data: "page=" + page + "&ids=" + ids + "&action=load_offers_map",dataType: "json",cache: false,                        
                  success: function (data){
                      $(".map-search-offers-list").html( data["offers"] );     
                      $(".map-search-offers-header-count").html( data["countHtml"] ); 
                      if( data["count"] != 0 ) $(".map-search-offers-header").show(); else $(".map-search-offers-header").hide();
                      tippy_load();  
                      initializeSlider();    
                      countDisplay();                                                    
                  }
              });

         }


         <?php if($settings["map_vendor"] == "yandex"){ ?>

          var polygonOptions = {
            strokeColor: '#0000ff',
            fillColor: '#8080ff',
            interactivityModel: 'default#transparent',
            strokeWidth: 2,
            opacity: 0.7
          };

          var canvasOptions = {
            strokeStyle: '#0000ff',
            lineWidth: 2,
            opacity: 0.7
          };

          var map = null;
          var polygon = null;

          ymaps.ready(['Map', 'Polygon']).then(function() {
        
            map = new ymaps.Map('map_instance', { center: [55.75, 37.62], zoom: 12, controls: [] });

            geoObjects = [];
            
            <?php if($_GET["coordinates"]){ ?>

            polygon = new ymaps.Polygon( <?php echo $_GET["coordinates"]; ?> );
            map.geoObjects.add(polygon);
            map.setBounds(map.geoObjects.getBounds());

            <?php } ?>

            clusterer = new ymaps.Clusterer({
              preset: "islands#invertedDarkBlueClusterIcons",
              groupByCoordinates: false,
              clusterDisableClickZoom: true,
              clusterHideIconOnBalloonOpen: false,
              geoObjectHideIconOnBalloonOpen: false
            }),

            getPointData = [
               <?php echo implode("," ,$data["balloonContentBody"]); ?>
            ],

            points = [
               <?php echo implode("," ,$data["points"]); ?>
            ]
            

            for (var i = 0, len = points.length; i < len; i++) {
              geoObjects[i] = new ymaps.Placemark(points[i], getPointData[i], { preset: "islands#redDotIcon" } );
            }

            clusterer.options.set({
              gridSize: 70,
              clusterDisableClickZoom: false
            });

            clusterer.add(geoObjects);
            map.geoObjects.add(clusterer);
            
            <?php if(!$_GET["coordinates"]){ ?>

            map.setBounds(clusterer.getBounds(), {
              checkZoomRange: true
            }).then(function(){ if(map.getZoom() > 12) map.setZoom(12);});

            <?php } ?>

            geoObjectsQuery = ymaps.geoQuery(geoObjects);
            
            <?php if($_GET["coordinates"]){ ?>

            var labelGeoObjects = geoObjectsQuery.searchInside(polygon);

            refreshLabels( labelGeoObjects );

            <?php } ?>

            map.events.add('boundschange', function() { 
              var labelGeoObjects = geoObjectsQuery.searchIntersect(map);
              refreshLabels(labelGeoObjects);
            });

            var drawButton = document.querySelector('#map-search-controls-draw');

            drawButton.onclick = function() {
              drawButton.disabled = true;
              $(".map-search-controls-draw-close").show();
              $(".map-search-controls-draw").hide();
              drawLineOverMap(map)
                .then(function(coordinates) {

                  coordinates = coordinates.filter(function (_, index) {
                    return index % 3 === 0;
                  });

                  if (polygon) {
                    map.geoObjects.remove(polygon);
                  }

                  polygon = new ymaps.Polygon([coordinates], {}, polygonOptions);
                  map.geoObjects.add(polygon);

                  geoObjectsQuery = ymaps.geoQuery(geoObjects);
                  var labelGeoObjects = geoObjectsQuery.searchInside(polygon);

                  refreshLabels( labelGeoObjects );

                  var hashes = window.location.href;
                  if( hashes.indexOf('?') === -1 ){
                     history.pushState("", "", hashes+"?coordinates="+JSON.stringify(polygon.geometry.getCoordinates())); 
                  }else{
                     history.pushState("", "", hashes+"&coordinates="+JSON.stringify(polygon.geometry.getCoordinates()));
                  }

                  drawButton.disabled = false;
                });
            };

          });

          function drawLineOverMap(map) {
            
              var canvas = document.querySelector('#draw-canvas');
              var ctx2d = canvas.getContext('2d');
              var drawing = false;
              var coordinates = [];
              var offsets = [];

              var rect = map.container.getParentElement().getBoundingClientRect();
              canvas.style.width = rect.width + 'px';
              canvas.style.height = rect.height + 'px';
              canvas.width = rect.width;
              canvas.height = rect.height;

              ctx2d.strokeStyle = canvasOptions.strokeStyle;
              ctx2d.lineWidth = canvasOptions.lineWidth;
              canvas.style.opacity = canvasOptions.opacity;

              ctx2d.clearRect(0, 0, canvas.width, canvas.height);
              canvas.style.display = 'block';

              canvas.onmousedown = function(e) {
                  drawing = true;
                  coordinates.push([e.pageX, e.pageY]);
                  offsets.push([e.offsetX, e.offsetY]);
              };

              canvas.onmousemove = function(e) {
                  if (drawing) {
                      var last = offsets[offsets.length - 1];
                      ctx2d.beginPath();
                      ctx2d.moveTo(last[0], last[1]);
                      ctx2d.lineTo(e.offsetX, e.offsetY);
                      ctx2d.stroke();

                      coordinates.push([e.pageX, e.pageY]);
                      offsets.push([e.offsetX, e.offsetY]);
                  }
              };

              return new Promise(function(resolve) {
                  canvas.onmouseup = function(e) {

                      coordinates.push([e.pageX, e.pageY]);
                      canvas.style.display = 'none';
                      drawing = false;

                      var projection = map.options.get('projection');
                      coordinates = coordinates.map(function(x) {
                          return projection.fromGlobalPixels(
                              map.converter.pageToGlobal([x[0], x[1]]), map.getZoom()
                          );
                      });
                      
                      resolve(coordinates);
                  };
              });
          }

          var idsString = "";

          function refreshLabels(labelGeoObjects) {

            var ids = [];
            
            labelGeoObjects.each(function(x) {
              ids.push( x.properties.get('id') ); 
            });

            if( !ids.length ){
              $(".map-search-offers-list").html(`
                      <div class="map-no-result" >
                      <i class="las la-search-location"></i>
                      <h6><strong><?php echo $ULang->t("К сожалению, нет объявлений в этой области карты"); ?></strong></h6>
                      <p><?php echo $ULang->t("Попробуйте сменить масштаб или область карты."); ?></p>
                      </div>
                `);
            }

            if( ids.join(',') != idsString ){

                loadOffers(ids.join(','),1);
                idsString = ids.join(',');

            }

          }

          $(document).on('click','.map-search-controls-draw-close', function (e) { 

              var path = location.href.split('&');
              if( path.length == 1 ){
                  var path = location.href.split('?');
              }
              path.pop();
              path = path.join("&");
              history.pushState("", "", path);

              geoObjectsQuery = ymaps.geoQuery(geoObjects);
              var labelGeoObjects = geoObjectsQuery.searchIntersect(map);
              refreshLabels(labelGeoObjects);

              map.geoObjects.remove(polygon);
              $(".map-search-controls-draw-close").hide();
              $(".map-search-controls-draw").show();

          });

          $(document).on('click','.pagination-map-offers a', function (e) { 

              loadOffers(idsString, $(this).data("page") );

              e.preventDefault();
          });


      <?php 
      }elseif($settings["map_vendor"] == "google"){

         ?>

          var map;
          var idsString = "";
          var gMapsLoaded = false;

          window.gMapsCallback = function(){
              gMapsLoaded = true;
              $(window).trigger("gMapsLoaded");
          }
          window.loadGoogleMaps = function(){
              if(gMapsLoaded) return window.gMapsCallback();
              var script_tag = document.createElement("script");
              script_tag.setAttribute("type","text/javascript");
              script_tag.setAttribute("src","https://maps.googleapis.com/maps/api/js?key=<?php echo $settings["map_google_key"]; ?>&callback=gMapsCallback");
              (document.getElementsByTagName("head")[0] || document.documentElement).appendChild(script_tag);
          }

          $(window).bind("gMapsLoaded", initMap);
          window.loadGoogleMaps();

          function initMap() {

              var marker;
              var markerArray = [];

              var infoWindow = new google.maps.InfoWindow();
              var marker, i;
              var clusterMarkers = [

                  <?php echo implode(",",$data["balloonContentBody"]); ?>
                  
              ]
              
              var options_googlemaps = {
                  zoom: 6,
                  center: new google.maps.LatLng(<?php echo $settings["country_lat"]; ?>,<?php echo $settings["country_lng"]; ?>),
                  mapTypeId: google.maps.MapTypeId.ROADMAP
              }

              map = new google.maps.Map(document.getElementById("map_instance"), options_googlemaps);

              var markerCluster = new MarkerClusterer(map, clusterMarkers,
                          { imagePath: "https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m" });

              for(i = 0; i < clusterMarkers.length; i++) {
                 var marker = clusterMarkers[i];

                  google.maps.event.addListener(marker, "click", (function(marker) {
                      return function() {

                           infoWindow.setContent(this.content);
                           infoWindow.open(map, this);

                      }
                  })(marker));
              }

              google.maps.event.addListener(map, 'bounds_changed', function() 
              {
                  refreshLabels(clusterMarkers);
              });


          }

          function refreshLabels(clusterMarkers) {

              var ids = [];
              
              for (var i = 0; i < clusterMarkers.length; i++) 
              {
                  if (map.getBounds().contains(clusterMarkers[i].getPosition())) 
                  {
                      ids.push(clusterMarkers[i].id);
                  }
              }

              if( !ids.length ){
                $(".map-search-offers-list").html(`
                        <div class="map-no-result" >
                        <i class="las la-search-location"></i>
                        <h6><strong><?php echo $ULang->t("К сожалению, нет объявлений в этой области карты"); ?></strong></h6>
                        <p><?php echo $ULang->t("Попробуйте сменить масштаб или область карты."); ?></p>
                        </div>
                  `);
              }

              if( ids.join(',') != idsString ){

                  loadOffers(ids.join(','),1);
                  idsString = ids.join(',');

              }

          }

          $(document).on('click','.pagination-map-offers a', function (e) { 

              loadOffers(idsString, $(this).data("page") );

              e.preventDefault();
          });

          google.maps.event.addDomListener(window, "load", initMap);

         <?php

      }elseif($settings["map_vendor"] == "openstreetmap"){ ?>

          var map = null;

          map = L.map('map_instance').setView([<?php echo $settings["country_lat"]; ?>,<?php echo $settings["country_lng"]; ?>], 12);

          L.tileLayer('https://api.mapbox.com/styles/v1/mapbox/streets-v11/tiles/{z}/{x}/{y}?access_token=<?php echo $settings["map_openstreetmap_key"]; ?>', {
              attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
          }).addTo(map);

           var markers = [
              <?php echo implode(",", $data["balloonContentBody"]); ?> 
           ];

           var minlat = 200, minlon = 200, maxlat = -200, maxlon = -200;
           
           if( markers.length ){
               for (var i=0; i<markers.length; i++) {
                 
                   var lon = markers[i][0];
                   var lat = markers[i][1];
                   var popupText = markers[i][2];
                  
                   var markerLocation = new L.LatLng(lat, lon);
                   var marker = new L.Marker(markerLocation, {
                       id: markers[i][3]
                   });

                    if (minlat > lat) minlat = lat;
                    if (minlon > lon) minlon = lon;
                    if (maxlat < lat) maxlat = lat;
                    if (maxlon < lon) maxlon = lon;

                   map.addLayer(marker);
                   marker.bindPopup(popupText);
               
               }

                c1 = L.latLng(minlat, minlon);
                c2 = L.latLng(maxlat, maxlon);

                map.fitBounds(L.latLngBounds(c1, c2));  
           }

          map.on('moveend', function(e) {

            refreshLabels();

          });

          var idsString = "";

          function refreshLabels() {

            var ids = [];
            
            map.eachLayer(function (layer) { 

                  if(layer instanceof L.Marker){
                    if( map.getBounds().contains(layer.getLatLng()) ){
                       ids.push(layer.options.id);
                    }
                  }

            });

            if( !ids.length ){
              $(".map-search-offers-list").html(`
                      <div class="map-no-result" >
                      <i class="las la-search-location"></i>
                      <h6><strong><?php echo $ULang->t("К сожалению, нет объявлений в этой области карты"); ?></strong></h6>
                      <p><?php echo $ULang->t("Попробуйте сменить масштаб или область карты."); ?></p>
                      </div>
                `);
            }

            if( ids.join(',') != idsString ){

                loadOffers(ids.join(','),1);
                idsString = ids.join(',');

            }

          }

          $(document).on('click','.pagination-map-offers a', function (e) { 

              loadOffers(idsString, $(this).data("page") );

              e.preventDefault();
          });

          refreshLabels();
 
      <?php } ?>

      }); 

    </script> 
  
    <div class="modal-custom-bg bg-click-close" id="modal-ads-subscriptions" style="display: none;" >
        <div class="modal-custom" style="max-width: 500px;" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>
          
          <div class="modal-ads-subscriptions-block-1" >

              <h4> <strong><?php echo $ULang->t("Подписка на объявления"); ?></strong> </h4>

              <p><?php echo $ULang->t("Новые объявления будут приходить на электронную почту"); ?></p>
              
              <?php if( !$_SESSION["profile"]["id"] ){ ?>
              <div class="create-info" >
                <?php echo $ULang->t("Для удобного управления подписками"); ?> - <a href="<?php echo _link("auth"); ?>"><?php echo $ULang->t("войдите в личный кабинет"); ?></a>
              </div>
              <?php } ?>
              
              <form class="modal-ads-subscriptions-form mt20" >
                 
                 <label><?php echo $ULang->t("Ваш e-mail"); ?></label>

                 <input type="text" name="email" class="form-control" value="<?php echo $_SESSION["profile"]["data"]["clients_email"]; ?>" >
                 
                 <label class="mt15" ><?php echo $ULang->t("Частота уведомлений"); ?></label>

                 <select name="period" class="form-control" >
                    <option value="1" selected="" ><?php echo $ULang->t("Раз в день"); ?></option>
                    <option value="2" ><?php echo $ULang->t("Сразу при публикации"); ?></option>
                 </select>

                 <input type="hidden" name="url" value="<?php echo $Ads->buildUrlCatalog( $data ); ?>" >

              </form>

              <div class="mt30" >
                 <button class="btn-custom btn-color-blue width100 modal-ads-subscriptions-add mb5" ><?php echo $ULang->t("Подписаться"); ?></button>
              </div>

              <p style="font-size: 13px; color: #7a7a7a;" class="mt15" ><?php echo $ULang->t("При подписке вы принимаете условия"); ?> <a href="<?php echo _link("polzovatelskoe-soglashenie"); ?>"><?php echo $ULang->t("Пользовательского соглашения"); ?></a> <?php echo $ULang->t("и"); ?> <a href="<?php echo _link("privacy-policy"); ?>"><?php echo $ULang->t("Политики конфиденциальности"); ?></a></p>

          </div>

          <div class="modal-ads-subscriptions-block-2" style="text-align: center;" >

              <i class="las la-check checkSuccess"></i>

              <h3> <strong><?php echo $ULang->t("Подписка оформлена"); ?></strong> </h3>

              <p><?php echo $ULang->t("Если вы захотите отписаться от рассылки - просто нажмите на соответствующую кнопку в тексте письма, либо перейдите в раздел"); ?> <a href="<?php if($_SESSION["profile"]["id"]){ echo _link( "user/" . $_SESSION["profile"]["data"]["clients_id_hash"] . "/subscriptions" ); }else{ echo _link( "auth" ); } ?>"><?php echo $ULang->t("управления подписками"); ?></a></p>

          </div>

        </div>
    </div>

    <div class="modal-custom-bg" id="modal-map-filters" style="display: none;" >
        <div class="modal-custom" style="max-width: 750px;" >

          <span class="modal-custom-close" ><i class="las la-times"></i></span>
          
          <div class="modal-map-container" >

            <h4> <strong><?php echo $ULang->t("Фильтры"); ?></strong> </h4>
              
                <form class="modal-form-filter mt25" >

                  <div class="row" >
                     <div class="col-lg-4" >
                       <label>
                          <?php echo $ULang->t("Город или регион"); ?>                             
                       </label>
                     </div>
                     <div class="col-lg-5" >

                        <div class="modal-search-geo-container" >
                        <input type="text" class="form-control modal-search-geo-input" value="<?php echo $ULang->t($Geo->change()["name"], [ "table"=>"geo", "field"=>"geo_name" ] ); ?>" > 
                        <div class="modal-search-geo-results" style="display: none;"></div>
                        </div>

                        <div class="select-box-city-options" >

                            <?php
                              $city_areas = getAll("select * from uni_city_area where city_area_id_city=? order by city_area_name asc", [ intval($_SESSION["geo"]["data"]["city_id"]) ]);
                              $city_metro = getAll("select * from uni_metro where city_id=? and parent_id!=0 Order by name ASC", [ intval($_SESSION["geo"]["data"]["city_id"]) ]); 

                              if($city_areas){
                              ?>

                                  <div class="uni-select" data-status="0" >

                                       <div class="uni-select-name" data-name="<?php echo $ULang->t("Район"); ?>" > <span><?php echo $ULang->t("Район"); ?></span> <i class="la la-angle-down"></i> </div>
                                       <div class="uni-select-list" >
                                           <?php
                                           foreach ($city_areas as $value) {

                                              if( $_GET['filter']['area'] ){

                                                  if( in_array($value["city_area_id"], $_GET['filter']['area'] ) ){
                                                          $active = 'class="uni-select-item-active"'; $checked = 'checked=""';      
                                                  }else{
                                                          $active = ''; $checked = '';
                                                  }

                                              }

                                              ?>
                                              <label <?php echo $active; ?> > <input type="checkbox" <?php echo $checked; ?> name="filter[area][]" value="<?php echo $value["city_area_id"]; ?>" > <span><?php echo $ULang->t( $value["city_area_name"], [ "table" => "uni_city_area", "field" => "city_area_name" ] ); ?></span> <i class="la la-check"></i> </label>
                                              <?php
                                           }
                                           ?>
                                       </div>
                                  
                                  </div>

                              <?php
                              }

                              if($city_metro){
                              ?>

                                  <div class="container-custom-search">
                                    <input type="text" class="ads-create-input action-input-search-metro" placeholder="<?php echo $ULang->t("Поиск станций метро"); ?>">
                                    <div class="custom-results SearchMetroResults" style="display: none;"></div>
                                  </div>

                                  <div class="ads-container-metro-station">
                                    <?php
                                        if($_GET['filter']['metro']){
                                            $getMetro = getAll("select * from uni_metro where id IN(".implode(',',$_GET['filter']['metro']).")");

                                            if(count($getMetro)){
                                              foreach ($getMetro as $key => $value) {
                                                $main = findOne("uni_metro", "id=?", [$value["parent_id"]]);
                                                if($main){
                                                  echo '
                                                         <span><i style="background-color:'.$main["color"].';"></i>'.$value["name"].' <i class="las la-times ads-metro-delete"></i><input type="hidden" value="'.$value["id"].'" name="filter[metro][]"></span>
                                                  ';
                                                  }
                                              }
                                            }
                                        }
                                    ?>
                                  </div>

                              <?php
                              }
                            ?>

                        </div> 

                        <input type="hidden" name="city_id" value="<?php echo $_SESSION["geo"]["data"]["city_id"]; ?>" >

                        <div class="mb10" ></div>                      
                       
                     </div>
                  </div>

                  <?php if(count($getCategoryBoard["category_board_id_parent"][0])){ ?>
                  <div class="row" >
                     <div class="col-lg-4" >
                       <label>
                          <?php echo $ULang->t("Категория"); ?>                             
                       </label>
                     </div>
                     <div class="col-lg-5" >

                        <?php
                        if($_GET['id_c']){
                           $ids_cat = $CategoryBoard->reverseId($getCategoryBoard,$_GET['id_c']);
                           $ids_cat = explode(',', $ids_cat);
                           $main_id_c = $ids_cat[0];

                           foreach ($ids_cat as $key => $value) {
                               $array_cats[$value] = $ids_cat[ $key + 1 ];
                           }
                        }
                        ?>
                        
                        <div class="uni-select" data-status="0" >

                             <div class="uni-select-name" data-name="<?php echo $ULang->t("Не выбрано"); ?>" > <span><?php echo $ULang->t("Не выбрано"); ?></span> <i class="la la-angle-down"></i> </div>
                             <div class="uni-select-list" >
                                 <label> <input type="radio" class="modal-filter-select-category" value="0" > <span><?php echo $ULang->t("Все категории"); ?></span> <i class="la la-check"></i> </label>
                                 <?php
                                 foreach ($getCategoryBoard["category_board_id_parent"][0] as $value) {
                                    ?>
                                    <label <?php if($value["category_board_id"] == $main_id_c){ echo 'class="uni-select-item-active"'; } ?> > <input type="radio" class="modal-filter-select-category" value="<?php echo $value["category_board_id"]; ?>" > <span><?php echo $ULang->t( $value["category_board_name"], [ "table" => "uni_category_board", "field" => "category_board_name" ] ); ?></span> <i class="la la-check"></i> </label>
                                    <?php
                                 }
                                 ?>
                             </div>
                        
                        </div> 

                        <div class="select-box-subcategory" >

                            <?php

                              if($array_cats){

                                 foreach ($array_cats as $id_main_cat => $id_sub_cat) {

                                       if($getCategoryBoard["category_board_id_parent"][$id_main_cat]){
                                       ?>

                                        <div class="uni-select" data-status="0" >

                                             <div class="uni-select-name" data-name="<?php echo $ULang->t("Не выбрано"); ?>" > <span><?php echo $ULang->t("Не выбрано"); ?></span> <i class="la la-angle-down"></i> </div>
                                             <div class="uni-select-list" >
                                                 <label> <input type="radio" class="modal-filter-select-category" value="<?php echo $id_main_cat; ?>" > <span><?php echo $ULang->t("Все категории"); ?></span> <i class="la la-check"></i> </label>
                                                 <?php
                                                 foreach ($getCategoryBoard["category_board_id_parent"][$id_main_cat] as $value) {
                                                    ?>
                                                    <label <?php if($value["category_board_id"] == $id_sub_cat){ echo 'class="uni-select-item-active"'; } ?> > <input type="radio" class="modal-filter-select-category" value="<?php echo $value["category_board_id"]; ?>" > <span><?php echo $ULang->t( $value["category_board_name"], [ "table" => "uni_category_board", "field" => "category_board_name" ] ); ?></span> <i class="la la-check"></i> </label>
                                                    <?php
                                                 }
                                                 ?>
                                             </div>
                                        
                                        </div>

                                       <?php
                                       }

                                 }

                              }

                            ?>

                        </div> 

                        <div class="mb15" ></div>                      
                       
                     </div>
                  </div>
                  <?php } ?>

                  <div class="select-box-filters" >
                    
                      <?php 
                      if($data["category"]["category_board_id"]){
                          if( $getCategoryBoard["category_board_id"][ $data["category"]["category_board_id"] ]["category_board_display_price"] ){ 
                          ?>
                          <div class="row" >
                             <div class="col-lg-4" >
                               <label>
                                  <?php 
                                  if( $getCategoryBoard["category_board_id"][ $data["category"]["category_board_id"] ]["category_board_variant_price"] == 1 ){
                                    echo $ULang->t('Зарплата'); 
                                  }else{ 
                                    echo $ULang->t('Цена'); 
                                  }
                                  ?>                             
                               </label>
                             </div>
                             <div class="col-lg-5" >
                               
                                <div class="filter-input" >
                                  <div><span><?php echo $ULang->t("от"); ?></span><input type="text" class="inputNumber" name="filter[price][from]" value="<?php if($data["param_filter"]["filter"]["price"]["from"]) echo $data["param_filter"]["filter"]["price"]["from"]; ?>" /></div>
                                  <div><span><?php echo $ULang->t("до"); ?></span><input type="text" class="inputNumber" name="filter[price][to]" value="<?php if($data["param_filter"]["filter"]["price"]["to"]) echo $data["param_filter"]["filter"]["price"]["to"]; ?>" /></div>
                                </div>

                             </div>
                          </div>
                          <?php 
                          } 
                      }else{
                          ?>
                          <div class="row" >
                             <div class="col-lg-4" >
                               <label>
                                  <?php echo $ULang->t("Цена"); ?>                             
                               </label>
                             </div>
                             <div class="col-lg-5" >
                               
                                <div class="filter-input" >
                                  <div><span><?php echo $ULang->t("от"); ?></span><input type="text" class="inputNumber" name="filter[price][from]" value="<?php if($data["param_filter"]["filter"]["price"]["from"]) echo $data["param_filter"]["filter"]["price"]["from"]; ?>" /></div>
                                  <div><span><?php echo $ULang->t("до"); ?></span><input type="text" class="inputNumber" name="filter[price][to]" value="<?php if($data["param_filter"]["filter"]["price"]["to"]) echo $data["param_filter"]["filter"]["price"]["to"]; ?>" /></div>
                                </div>
                               
                             </div>
                          </div>  
                          <?php
                      }

                      ?>

                      <div class="row mt15" >
                         <div class="col-lg-4" >
                           <label>
                              <?php echo $ULang->t("Статус"); ?>                             
                           </label>
                         </div>
                         <div class="col-lg-8" >
                            
                            <div class="filter-items-spacing" >
                              <?php if( $getCategoryBoard["category_board_id"][ $data["category"]["category_board_id"] ]["category_board_secure"] && $settings["secure_status"] ){ ?>
                              <div class="custom-control custom-checkbox" >
                                  <input type="checkbox" class="custom-control-input" name="filter[secure]" <?php if($data["param_filter"]["filter"]["secure"]){ echo 'checked=""'; } ?> id="flsecure" value="1" >
                                  <label class="custom-control-label" for="flsecure"><?php echo $ULang->t("Безопасная сделка"); ?></label>
                              </div>
                              <?php } ?>
                              
                              <?php if( $getCategoryBoard["category_board_id"][ $data["category"]["category_board_id"] ]["category_board_auction"] ){ ?>
                              <div class="custom-control custom-checkbox">
                                  <input type="checkbox" class="custom-control-input" name="filter[auction]" <?php if($data["param_filter"]["filter"]["auction"]){ echo 'checked=""'; } ?> id="flauction" value="1" >
                                  <label class="custom-control-label" for="flauction"><?php echo $ULang->t("Аукционный товар"); ?></label>
                              </div>
                              <?php } ?>
                              
                              <?php if( $getCategoryBoard["category_board_id"][ $data["category"]["category_board_id"] ]["category_board_online_view"] ){ ?>
                              <div class="custom-control custom-checkbox">
                                  <input type="checkbox" class="custom-control-input" name="filter[online_view]" <?php if($data["param_filter"]["filter"]["online_view"]){ echo 'checked=""'; } ?> id="online_view" value="1" >
                                  <label class="custom-control-label" for="online_view"><?php echo $ULang->t("Онлайн-показ"); ?></label>
                              </div>
                              <?php } ?>
                              
                              <div class="custom-control custom-checkbox">
                                  <input type="checkbox" class="custom-control-input" name="filter[vip]" <?php if($data["param_filter"]["filter"]["vip"]){ echo 'checked=""'; } ?> id="flvip" value="1" >
                                  <label class="custom-control-label" for="flvip"><?php echo $ULang->t("VIP объявление"); ?></label>
                              </div>

                            </div>
                           
                         </div>
                      </div>

                      <?php echo $data["filters"]; ?>

                  </div>

                  <div class="row mt15" >
                     <div class="col-lg-4" >
                       <label>
                          <?php echo $ULang->t("Срок размещения"); ?>                             
                       </label>
                     </div>
                     <div class="col-lg-8" >
                        
                          <div class="custom-control custom-radio">
                              <input type="radio" class="custom-control-input" name="filter[period]" <?php if($data["param_filter"]["filter"]["period"] == 1){ echo 'checked=""'; } ?> id="flPeriod1" value="1" >
                              <label class="custom-control-label" for="flPeriod1"><?php echo $ULang->t("За 24 часа"); ?></label>
                          </div>                        

                          <div class="custom-control custom-radio">
                              <input type="radio" class="custom-control-input" name="filter[period]" <?php if($data["param_filter"]["filter"]["period"] == 7){ echo 'checked=""'; } ?> id="flPeriod2" value="7" >
                              <label class="custom-control-label" for="flPeriod2"><?php echo $ULang->t("За 7 дней"); ?></label>
                          </div>

                          <div class="custom-control custom-radio">
                              <input type="radio" class="custom-control-input" name="filter[period]" <?php if(!$data["param_filter"]["filter"]["period"]){ echo 'checked=""'; } ?> id="flPeriod3" value="" >
                              <label class="custom-control-label" for="flPeriod3"><?php echo $ULang->t("За все время"); ?></label>
                          </div>
                       
                     </div>
                  </div>

                  <input type="hidden" name="id_c" value="<?php echo $_GET['id_c']; ?>" >

                </form>

          </div>

          <div class="modal-map-footer adaptive-buttons" >

                <?php if($data["param_filter"]["filter"] && !$data["filter"]){ ?>
                <div><button class="btn-custom btn-color-light action-clear-filter" > <?php echo $ULang->t("Сбросить"); ?> </button></div>
                <?php } ?>

                <div><button class="btn-custom btn-color-blue filter-accept" > <?php echo $ULang->t("Применить"); ?> </button></div>

          </div>

        </div>
    </div>


  </body>
</html>