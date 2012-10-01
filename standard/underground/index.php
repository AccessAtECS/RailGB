<?php
$contents = null;
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<?
			$path = $_SERVER['DOCUMENT_ROOT'];
			require_once($path.'/../library/Mobile_Detect.php');
			require_once($path.'/includes/mobile.php');
			require_once($path.'/includes/header.php');
		?>
		
		<title>Tube London - Accessible London Underground Map</title>
		
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
		<script type="text/javascript">
			var map = null;
			var circle = null;
			var currentMarker = null;
			var stationsDisplayed = new Array();
			var radius = 1600.0; //1 miles
			var initialLatLong = new google.maps.LatLng(51.508129, -0.128005);
			
			if (typeof(Number.prototype.toRad) === "undefined") {
  				Number.prototype.toRad = function() {
    				return this * Math.PI / 180;
  				}
			}
			
			function fireUpStations(stations) {
				var count = 0;
				$.each(stations.results.bindings, function(i, station) {
						
						var hasLift = {value: false, text: 'Not available'};
						
						
						if(station.hasLift.value === "true") {
							hasLift = {value:true,text:'Lift available'};
							image = '/public/img/pins/wheelchair-ok.png';
						} else {
							image = '/public/img/pins/wheelchair-not-ok.png';
						}
						
						//console.log("hasLift",station.hasLift.value);
						markers.push(new google.maps.Marker({
							position: new google.maps.LatLng(station.lat.value, station.long.value),
							map: map,
							title: station.name.value.replace(" tube station", ""),
							icon: image,
							draggable: false,
							lift: hasLift,
							visible: true
						}));
						
						count++;
						// Marker display box
						google.maps.event.addListener(markers[markers.length - 1], 'click', function(){
							$("#station-name").html(this.title);
							$("#station-lift").html(this.lift.text);
							$("#station").show();
						});
					});
					$("#alert").html(
						'<div class="alert alert-success">Showing '+count+' stations.</div>'
					).show();
			}

			function initialize() {
				
				// Image for each pin
				var image = '/public/img/theme/wheelchair-not-ok.png';
				
				// Fire up map
				var mapDiv = document.getElementById('map-canvas');
				map = new google.maps.Map(mapDiv, {
					center: new google.maps.LatLng(51.508129, -0.128005),
					zoom: 10,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				});
				
				// Add "Loading" text here.
				
				//console.log(stations);
				
				
				//if(typeof sessionStorage.tube == "undefined") {
					$.getJSON("/public/ajax/tube.php", function(stations) {
						fireUpStations(stations);
						//sessionStorage.tube = stations;
					});
				//} else {
				//	fireUpStations(sessionStorage.tube);
				//}
				
				// Clear "Loading" text here
			}
			
			function showStationsCount()
			{
				if(stationsDisplayed.length ==0)
				{
					$("#alert").html('<div class="alert alert-warning">'+'No stations have been found.</div>');
				}
				else
				{
					$("#alert").html(
						'<div class="alert alert-success">Showing '+stationsDisplayed.length+' of '+markers.length+' stations matching your search.</div>'
					);
				}
				$("#alert").show();
			}
			
			function checkDistance(station)
			{
					//console.log("currentMarker:"+currentMarker);
					station.setVisible(true);
					if(currentMarker != null)
					{
						//console.log("here");
						var distance =-1;
						var lat1 = currentMarker.getPosition().lat();
						var lat2 = station.getPosition().lat();
						var lon1 = currentMarker.getPosition().lng();
						var lon2 = station.getPosition().lng();
						//console.log("here2");
						var R = 6371; // Radius of the earth in km
						var dLat = (lat2-lat1).toRad();
						var dLon = (lon2-lon1).toRad(); 
						var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
        					Math.cos(lat1.toRad()) * Math.cos(lat2.toRad()) * 
        					Math.sin(dLon/2) * Math.sin(dLon/2); 
						var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
						//console.log("here3");
						//console.log(station.getPosition().toString()+"###"+currentMarker.getPosition().toString());
						//console.log("distance:"+distance);
						//console.log("radius:"+radius);
						distance = R * c *1000; // Distance in Meters
						//console.log("distance:"+distance);
         				if(distance <= radius)
         				{
         				 	//console.log("within distance:"+station.title);
         				 	checkAccessibility(station);
         				}
         				else
         				{
         				    station.setVisible(false);			
         				}
					}
			}
			
			function checkAccessibility(station)
			{
				
				var filterLift = $("#filter-lift").is(':checked');
				
				station.setVisible(true);
					
				if(filterLift== true && station.lift.value == false) {
					station.setVisible(false);
				}
				
				if(station.getVisible() === true)
				{
					stationsDisplayed.push(station);
				} 			
			}
			
			// terrible code-duplicated function to run location lookups
			
			function stationsNear(location) {
					var address = location;
					$("#address").val(address);
					$("#radius>option:eq(2)").attr('selected', true);
					$("#filter-lift").prop("checked", true);
					
					radius = parseFloat(1)*1600;
					console.log("radius:"+radius);
					stationsDisplayed = new Array();
					if(address !== undefined && $.trim(address).length >0)
					{
						var geocoder = new google.maps.Geocoder();
        				geocoder.geocode( 
        					{'address': address },
            				function(data, status) 
            				{ 
            					var lat = data[0].geometry.location.Xa;
								var lng = data[0].geometry.location.Ya;
								var latlng = new google.maps.LatLng(lat,lng,true);
								//console.log(latlng.toString());
								//draw a circle
								if (circle != null) {
									//console.log("setvisible");
								    circle.setVisible(false);
        							circle.setMap(null);
    							}
    							
    							if(currentMarker != null) currentMarker.setMap(null);
    							
    							currentMarker = new google.maps.Marker({
   										map: map,
   										position: latlng,
    									draggable: false
  								});
  								
								circle = new google.maps.Circle({
									radius:radius,
									strokeColor: "#FF0000",
        							strokeOpacity: 0.8,
        							strokeWeight: 2,
        							fillColor: "#FF0000",
       								fillOpacity: 0.35,
       								map: map
								});
								
								circle.bindTo('center', currentMarker, 'position');
																
								map.setCenter(currentMarker.getPosition());
								$.each(markers, function(i, station) {
									checkDistance(station);
								});
								//set focus if stations are available
								//console.log("displayed "+stationsDisplayed.length);
								if(stationsDisplayed.length >0)
								{
									/*
									var latlngbounds = new google.maps.LatLngBounds( );
									for ( var i = 0; i < stationsDisplayed.length; i++ ) {
  										latlngbounds.extend( stationsDisplayed[ i ].getPosition() );
									}
									//map.setCenter(currentMarker.getPosition());
									map.fitBounds( latlngbounds );*/
									
								}
								map.fitBounds( circle.getBounds() );
								
								showStationsCount();
							});
					}
					else
					{
						alert("Please input a postcode");
					}
					return false;
			}
			
			// When ready, fire up the google map. RDF loads when the map is ready.
			$(function() {
				google.maps.event.addDomListener(window, 'load', initialize);
				
				$("#clear_btn").click(function() {
					if(circle != null) circle.setMap(null);
					if(currentMarker != null) currentMarker.setMap(null);
					
					map.setCenter(initialLatLong);
					map.setZoom(10);
					
					$.each(markers, function(i, marker) {
						marker.setVisible(true);
					});
					return false;
				});
				
				$(".locationlookups a").click(function() {
					return false;
				});
				
				$("input[type='checkbox']").change(function() {
					stationsDisplayed = new Array();
					$.each(markers, function(i, station) {
						if(currentMarker != null)
						{
							checkDistance(station);
						}
						else
						{					
							//console.log("check acc");
							checkAccessibility(station);
						}
					});
					
					showStationsCount();
				});
				
				//search nearby
				$("#search_btn").click(function(){
					
					var address = $("#address").val();
					radius = parseFloat($("#radius").val())*1600;
					console.log("radius:"+radius);
					stationsDisplayed = new Array();
					if(address !== undefined && $.trim(address).length >0)
					{
						var geocoder = new google.maps.Geocoder();
        				geocoder.geocode( 
        					{'address': address },
            				function(data, status) 
            				{ 
            					var lat = data[0].geometry.location.Xa;
								var lng = data[0].geometry.location.Ya;
								var latlng = new google.maps.LatLng(lat,lng,true);
								//console.log(latlng.toString());
								//draw a circle
								if (circle != null) {
									//console.log("setvisible");
								    circle.setVisible(false);
        							circle.setMap(null);
    							}
    							
    							if(currentMarker != null) currentMarker.setMap(null);
    							
    							currentMarker = new google.maps.Marker({
   										map: map,
   										position: latlng,
    									draggable: false
  								});
  								
								circle = new google.maps.Circle({
									radius:radius,
									strokeColor: "#FF0000",
        							strokeOpacity: 0.8,
        							strokeWeight: 2,
        							fillColor: "#FF0000",
       								fillOpacity: 0.35,
       								map: map
								});
								
								circle.bindTo('center', currentMarker, 'position');
								
								map.setCenter(currentMarker.getPosition());
								$.each(markers, function(i, station) {
									checkDistance(station);
								});
								//set focus if stations are available
								//console.log("displayed "+stationsDisplayed.length);
								if(stationsDisplayed.length >0)
								{
									/*
									var latlngbounds = new google.maps.LatLngBounds( );
									for ( var i = 0; i < stationsDisplayed.length; i++ ) {
  										latlngbounds.extend( stationsDisplayed[ i ].getPosition() );
									}
									//map.setCenter(currentMarker.getPosition());
									map.fitBounds( latlngbounds );*/
									
								}
								map.fitBounds( circle.getBounds() );
								
								showStationsCount();
							});
					}
					else
					{
						alert("Please input a postcode");
					}
					return false;
				});
			});
			
			var markers = new Array();
		</script>
		
	</head>
	
	
	
	<body>
	
		<? require_once($path.'/includes/menu.php'); ?>
		
		<div class="container" id="container">
			<div class="page-header">
					<h1><img src='/public/img/theme/tube-logo.png' alt="London Underground logo"/> London Underground <small>Accessible London Underground Map</small></h1>
				</div>
			<div class="row-fluid">
				<div class="span8">
					<div id="map-canvas" <?=($detect->isMobile()) ? 'style="width: 220; height: 250"' : 'style="width: 700px; height: 700px"'; ?>></div>
				</div>
				<div class="span4">
					<div id="alert"><div class="alert alert-info">Loading&hellip;</div></div>
					<div>
						<form class="form-search">
							<div class="control-group">
								<label for="address" class="control-label"><b>Location</b></label>
								<div class="controls">
    								<input type="text" name="address" id="address" class="input-long" placeholder="Postcode"/>
    							</div>
    							<label for="radius" class="control-label"><b>Search Radius</b></label>
    							<div class="controls">
    								<select id="radius" name="radius">
    									<option value="0.25">0.25 mile</option>
										<option value="0.5" selected="selected">0.5 miles</option>
										<option value="1.0">1 miles</option>
										<option value="2.0">2 miles</option>
										<option value="5.0">5 miles</option>
										<option value="10.0">10 miles</option>
    								</select>
    							</div>
    							<br/>
								<div class="controls">
									<label><input type="checkbox" name="station" id="filter-lift" value="lift" /> Lift available <img src="/public/img/fugue/ticket-1.png" alt="ticket office" /></label><br />
								</div>
							</div>
						</form>
						<button class="btn btn-primary" id="search_btn" type="submit">Search</button>
						<a class="btn pull-right" id="clear_btn" href="#" onclick="return false">Clear Results</a>
						
						<br /><br />
						<div id="station" style="display:none">
							<h4 id="station-name"></h4>
							<div id="station-innerticket">
								<p><b>Lift:</b> <span id="station-lift"></span></p>
							</div>
							<div id="station-footer"><img src='/public/img/theme/ticket-logo.png' alt='National Rail' /></div>
						</div>
						
						<div class="locationlookups">
							<h4>Accessible Stations Near&hellip;</h4>
							<p><a href='#' onclick='stationsNear("SE16 3TP")'>City Mobility</a></p>
							<p><a href='#' onclick='stationsNear("SW1W 0DH")'>Dyslexia Action</a></p>
							<p><a href='#' onclick='stationsNear("WC1N 3JH")'>Great Ormond Street Hospital for Children</a></p>
							<p><a href='#' onclick='stationsNear("SW7 5BD")'>Natural History Museum</a></p>
							<p><a href='#' onclick='stationsNear("WC1H 9NE")'>RNIB</a></p>
							<p><a href='#' onclick='stationsNear("WC2R 0EU")'>The Savoy</a></p>
							<p><a href='#' onclick='stationsNear("SW7 2DD")'>Science Museum</a></p>
							<p><a href='#' onclick='stationsNear("N7 9PW")'>Scope</a></p>
							<p><a href='#' onclick='stationsNear("SE1 7EH")'>St. Thomas' Hospital</a></p>
							<p><a href='#' onclick='stationsNear("EC1Y 8SL")'>Action on Hearing Loss</a></p>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<? require_once($path.'/includes/footer.php'); ?>
		
	</body>
</html>
