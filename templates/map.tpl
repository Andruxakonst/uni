<!doctype html>
<html lang="<?php echo $settings["lang_site_default"]; ?>">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title><?php echo $ULang->t("Объявления на карте"); ?> - <?php echo $settings["site_name"]; ?></title>

    <?php include $config["template_path"] . "/head.tpl"; ?>

  </head>

  <body data-prefix="<?php echo $config["urlPrefix"]; ?>" data-template="<?php echo $config["template_folder"]; ?>" >

      <?php include $config["template_path"] . "/header.tpl"; ?>
    
      <div class="map-search-container" >

        <div class="map-search-sidebar" >

              <span class="map-search-sidebar-mobile-open-toggle" ><i class="las la-angle-up"></i></span>
              <span class="map-search-sidebar-mobile-close-toggle" ><i class="las la-angle-down"></i></span>
        
              <div class="map-search-controls" >
                 
                   <div class="map-search-controls-filters open-modal" data-id-modal="modal-map-filters" >
                     <span><svg height="20" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M11 9a4.002 4.002 0 01-3 3.874V21a1 1 0 11-2 0v-8.126a4.002 4.002 0 010-7.748V3a1 1 0 112 0v2.126c1.725.444 3 2.01 3 3.874zm5 12a1 1 0 102 0v-2.126a4.002 4.002 0 000-7.748V3a1 1 0 10-2 0v8.126a4.002 4.002 0 000 7.748V21zM9 9a2 2 0 10-4 0 2 2 0 004 0zm8 4a2 2 0 110 4 2 2 0 010-4z" fill="currentColor"></path></svg> <?php if($Filters->mapCountChangeFilters($data["param_filter"]["filter"])){ ?> <span class="map-label-count" ><?php echo $Filters->mapCountChangeFilters($data["param_filter"]["filter"]); ?></span> <?php } ?></span>
                   </div>

                   <div class="map-search-controls-subscriptions catalog-ads-subscriptions-add" style="display: none;" >
                     <span><i class="las la-bell"></i></span>
                   </div>

                   <?php if( count($data["points"]) && $settings["map_vendor"] == "yandex" ){ ?>
                   <div class="map-search-controls-draw" id="map-search-controls-draw" <?php if($_GET["coordinates"]){ echo 'style="display: none;"'; } ?> >
                     <span><svg xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 0 28 25">
                        <g fill="none" fill-rule="evenodd" stroke="currentColor" transform="rotate(90 12.5 14)">
                            <path stroke-width="2" d="M5.9 3.273c2.466-2.195 13.518-5.957 14.01-.302.156 1.79-1.874 3.436-1.475 5.187.393 1.723 3.61 2.214 3.565 3.98-.042 1.61-3.015 1.694-3.75 3.135-1.863 3.658 4.84 5.727 2.15 8.984-3.06 3.714-20.53-5.01-20.398-7.9.164-3.59 4.257-1.693 6.206-3.797 2.53-2.73-8.662-6.157-2.704-8.322L5.9 3.273z"></path>
                            <circle cx="8.5" cy="22.5" r="1.5" fill="#FFF"></circle>
                        </g>
                    </svg></span>
                   </div>

                   <div class="map-search-controls-draw-close" <?php if($_GET["coordinates"]){ echo 'style="display: inline-block;"'; }else{ echo 'style="display: none;"'; } ?> >
                     <span><i class="las la-times"></i></span>
                   </div>
                   <?php } ?>  

              </div>

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

              <div class="map-search-offer" >

                   <div class="map-search-offer-header" >
                      <span class="map-search-offer-back-catalog" ><i class="las la-arrow-left"></i> <?php echo $ULang->t("Вернуться"); ?></span>
                   </div>

                   <div class="map-search-offer-item" ></div>   

              </div>

        </div>

        <div class="map-search-instance" id="map_instance" ></div>

        <canvas id="draw-canvas" style="position: absolute; left: 0; top: 0; display: none;"></canvas>

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
                      countDisplay();                                                    
                  }
              });

         }

         function loadOffer(id){

              $.ajax({type: "POST",url: $("body").data("prefix") + "systems/ajax/ads.php",data: "id=" + id + "&action=load_offer_map",dataType: "html",cache: false,                        
                  success: function (data){
                      $('.map-search-offers').hide();
                      $('.map-search-offer-item').html(data);
                      $('.map-search-offer').show();     

                      if ($(window).width() <= 992) {
                         $('.map-search-sidebar').css('height', 'auto');
                         $('.map-search-sidebar').css('top', 'auto');
                         $('.map-search-sidebar').css('bottom', '15px');
                         $('.map-search-sidebar-mobile-open-toggle').hide();
                      }else{
                         $('.map-search-sidebar').css('bottom', 'auto');
                      }                                              
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
              geoObjectHideIconOnBalloonOpen: false,
              openBalloonOnClick: false,
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

            map.geoObjects.events.add('click', function (e) {
                var target = e.get('target');
                if(target.properties.get('id') != undefined){
                  loadOffer(target.properties.get('id'));
                }
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

                          if(this.id && this.id != undefined){
                             loadOffer(this.id);
                          }

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
                  
                   var markerLocation = new L.LatLng(lat, lon);
                   var marker = new L.Marker(markerLocation, {
                       id: markers[i][2]
                   });

                    if (minlat > lat) minlat = lat;
                    if (minlon > lon) minlon = lon;
                    if (maxlat < lat) maxlat = lat;
                    if (maxlon < lon) maxlon = lon;

                   map.addLayer(marker);

                   marker.on('click', function(e) {

                     if(e.target.options.id && e.target.options.id != undefined){
                        loadOffer(e.target.options.id);
                     }

                   });
               
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

            <h4> <strong><?php echo $ULang->t("Категории и фильтры"); ?></strong> </h4>
              
                <form class="modal-form-filter mt25" >

                  <?php echo $Filters->outFormFilters('map',['data'=>$data, 'categories'=>$getCategoryBoard]); ?>

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