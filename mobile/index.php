<!DOCTYPE html>
<html lang="en">
	<head>
		<?
			$path = $_SERVER['DOCUMENT_ROOT'];
			require_once($path.'/includes/header.php');
		?>
		
		<title>Tube London - Accessible London Underground Map</title>
		<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
		
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
		<script type="text/javascript">
			var map = null;
			var circle = null;
			var currentMarker = null;
			var stationsDisplayed = new Array();
			var radius = 1600.0; //1 miles
			var initialLatLong = new google.maps.LatLng(51.508129, -0.128005);
			
			if (typeof(Number.prototype.toRad) === "undefined"){
  				Number.prototype.toRad = function(){
					return this * Math.PI / 180;
  				}
			}
			
			function fireUpStations(stations){
				var count = 0;
				$.each(stations.results.bindings, function(i, station){
						
					var hasLift = {value: false, text: 'Not available'};
					
					if(station.hasLift.value === "true"){
						hasLift = {value:true,text:'Lift available'};
						image = 'http://wwww.railgb.org.uk/public/img/pins/wheelchair-ok.png';
					} else {
						image = 'http://wwww.railgb.org.uk/public/img/pins/wheelchair-not-ok.png';
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
				$("#alert").hide();
			}

			function initialize() {
				
				// Image for each pin
				var image = 'http://wwww.railgb.org.uk/public/img/theme/wheelchair-not-ok.png';
				
				// Fire up map
				var mapDiv = document.getElementById('map-canvas');
				map = new google.maps.Map(mapDiv, {
					center: new google.maps.LatLng(51.508129, -0.128005),
					zoom: 10,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				});

				$.getJSON("/public/ajax/tube.php", function(stations) {
					fireUpStations(stations);
				});
			}
			
			function showStationsCount(){
				if(stationsDisplayed.length ==0) $("#alert").html('<div class="alert alert-warning">'+'No stations have been found.</div>');
				else $("#alert").html('<div class="alert alert-success">Showing '+stationsDisplayed.length+' of '+markers.length+' stations matching your search.</div>');
				$("#alert").show();
			}
			
			function checkDistance(station){
				station.setVisible(true);
				if(currentMarker != null){
					var distance =-1;
					var lat1 = currentMarker.getPosition().lat();
					var lat2 = station.getPosition().lat();
					var lon1 = currentMarker.getPosition().lng();
					var lon2 = station.getPosition().lng();

					var R = 6371; // Radius of the earth in km
					var dLat = (lat2-lat1).toRad();
					var dLon = (lon2-lon1).toRad(); 
					var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
						Math.cos(lat1.toRad()) * Math.cos(lat2.toRad()) * 
						Math.sin(dLon/2) * Math.sin(dLon/2); 
					var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 

					distance = R * c *1000; // Distance in Meters
					
	 				if(distance <= radius) checkAccessibility(station);
	 				else station.setVisible(false);	
				}
			}
			
			function checkAccessibility(station){
				var filterLift = $("#filter-lift").is(':checked');
				
				station.setVisible(true);
					
				if(filterLift== true && station.lift.value == false) station.setVisible(false);
				if(station.getVisible() === true) stationsDisplayed.push(station);		
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
					if(address !== undefined && $.trim(address).length >0){
						var geocoder = new google.maps.Geocoder();
						geocoder.geocode( 
							{'address': address },
							function(data, status){ 
								var lat = data[0].geometry.location.Xa;
								var lng = data[0].geometry.location.Ya;
								var latlng = new google.maps.LatLng(lat,lng,true);
								
								//draw a circle
								if (circle != null) {
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
								map.fitBounds( circle.getBounds() );
								
								showStationsCount();
							});
					}
					else alert("Postcode or Area");
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
					
					$.each(markers, function(i, marker){
						marker.setVisible(true);
					});
					return false;
				});
				
				$(".locationlookups a").click(function(){
					return false;
				});
				
				$("input[type='checkbox']").change(function(){
					stationsDisplayed = new Array();
					$.each(markers, function(i, station){
						if(currentMarker != null) checkDistance(station);
						else checkAccessibility(station);
					});
					
					showStationsCount();
				});
				
				//search nearby
				$("#search_btn").click(function(){
					
					var address = $("#address").val();
					radius = parseFloat($("#radius").val())*1600;
					console.log("radius:"+radius);
					stationsDisplayed = new Array();
					if(address !== undefined && $.trim(address).length >0){
						var geocoder = new google.maps.Geocoder();
						geocoder.geocode( 
							{'address': address },
							function(data, status) { 
								var lat = data[0].geometry.location.Xa;
								var lng = data[0].geometry.location.Ya;
								var latlng = new google.maps.LatLng(lat,lng,true);
								//draw a circle
								if (circle != null){
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
								$.each(markers, function(i, station){
									checkDistance(station);
								});
								map.fitBounds( circle.getBounds() );
								
								showStationsCount();
							});
					}
					else alert("Please input a postcode");
					return false;
				});
			});
			
			var markers = new Array();
		</script>
		
	</head>
	
	<body>
	
		<? include_once($path.'/includes/menu.php'); ?>
		<div class="map-area">
			<div class="row-fluid">
				<div id="map-canvas" style="width: 200px; height: 200px"></div>
				<br />
				<div id="alert"><div class="alert alert-info">Loading&hellip; <img src="http://www.railgb.org.uk/public/img/loading.gif" style="height: 20px;"/></div></div>
				
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
								<label><input type="checkbox" name="station" id="filter-lift" value="lift" /> Lift available <img src="http://www.railgb.org.uk/public/img/fugue/ticket-1.png" alt="ticket office" /></label><br />
							</div>
						</div>
					</form>
					<button class="btn btn-primary" id="search_btn" type="submit">Search</button>
					<a class="btn pull-right" id="clear_btn" href="#" onclick="return false">Clear Results</a>
				</div>
			</div>
		</div>
		
		<div class="station-area" style="display:none">
			<div id="station">
				<h4 id="station-name"></h4>
				<div id="station-innerticket">
					<p><b>Lift:</b> <span id="station-lift"></span></p>
				</div>
				<div id="station-footer"><img src="http://www.railgb.org.uk/public/img/theme/ticket-logo.png" alt='National Rail' /></div>
			</div>
		</div>
		
		
		<script>
			$("#map-canvas").css("width", "100%");
		</script>
	</body>
</html>