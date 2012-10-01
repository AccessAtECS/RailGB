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
		
		<title>RailGB - Accessible Rail Network Map</title>
		
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
		<script type="text/javascript">
			var map = null;
			var circle = null;
			var currentMarker = null;
			var stationsDisplayed = new Array();
			var radius = 16000; //10 miles
			var initialLatLong = new google.maps.LatLng(53.000000, -2.000000);
			
			if (typeof(Number.prototype.toRad) === "undefined") {
  				Number.prototype.toRad = function() {
    				return this * Math.PI / 180;
  				}
			}

			function initialize() {
				
				// Image for each pin
				var image = '/public/img/pins/rail.png';
				
				// Fire up map
				var mapDiv = document.getElementById('map-canvas');
				map = new google.maps.Map(mapDiv, {
					center: initialLatLong,
					zoom: 7,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				});
				
				// Add "Loading" text here.
				var count = 0;
				
				$.getJSON("/public/ajax/rail.php", function(stations) {
					$.each(stations.results.bindings, function(i, station) {
					
						var ticketoffice = {value: false, text: 'Not available'};
						var staffing = {value: false, text: 'No staff available'};
						var ramp = {value: false, text: 'No'};
						
						station.staffing.value = $.trim(station.staffing.value);
						
						if(typeof station.ticket != 'undefined' && station.ticket.value != "No") ticketoffice = {value: true, text: station.ticket.value};
						
						if(typeof station.staffing != 'undefined' && station.staffing.value.length > 0) staffing = {value: true, text: station.staffing.value};
						
						if(typeof station.ramp != 'undefined' && typeof station.ramp.value != 'undefined') ramp = {value: true, text: 'Yes'};
						
						markers.push(new google.maps.Marker({
							position: new google.maps.LatLng(station.lat.value, station.long.value),
							map: map,
							title: station.name.value,
							icon: image,
							draggable: false,
							railgb_ticketoffice: ticketoffice,
							railgb_staffing: staffing,
							railgb_ramp: ramp,
							visible: true
						}));
						
						count++;
						// Marker display box
						google.maps.event.addListener(markers[markers.length - 1], 'click', function(){
							$("#station-name").html(this.title);
							$("#station-ticketoffice").html(this.railgb_ticketoffice.text);
							$("#station-staffing").html(this.railgb_staffing.text);
							$("#station-ramp").html(this.railgb_ramp.text);
							$("#station").show();
						});
						
						$("#alert").html(
							'<div class="alert alert-success">Showing '+count+' stations.</div>'
						).show();
					});
				})
				
				
				
						
				// Clear "Loading" text here
			}
			
			function showStationsCount()
			{
				if(stationsDisplayed.length ==0)
				{
					$("#alert").html('<div class="alert alert-warning">'+
						'No stations have been found. Try a different search.</div>');
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
					station.setVisible(true);
					if(currentMarker != null)
					{
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
						distance = R * c*1000; // Distance in Meters
						//console.log(station.getPosition().toString()+"###"+currentMarker.getPosition().toString());
						//console.log("distance:"+distance);
						//console.log("radius:"+radius);
         				if(distance <= radius)
         				{
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
				
				var filterTicketOffices = $("#filter-ticketoffice").is(':checked');
				var filterStaffing = $("#filter-staff").is(':checked');
				var filterRamp = $("#filter-ramp").is(':checked');
				
				station.setVisible(true);
					
				if(filterTicketOffices == true && station.railgb_ticketoffice.value == false) {
					station.setVisible(false);
				}
					
				if(filterStaffing == true && station.railgb_staffing.value == false) {
					station.setVisible(false);
				} 
				if(filterRamp == true && station.railgb_ramp.value == false) {
					station.setVisible(false);
					//console.log("Hiding "+station.title+" because it has no ranmp!");
				}
				
				if(station.getVisible() === true)
				{
					stationsDisplayed.push(station);
				} 			
			}
			
			
			// When ready, fire up the google map. RDF loads when the map is ready.
			$(function() {
				google.maps.event.addDomListener(window, 'load', initialize);
				
				$("input[type='checkbox']").change(function() {
					stationsDisplayed = new Array();
					$.each(markers, function(i, station) {
						if(currentMarker != null)
						{
							checkDistance(station);
						}
						else
						{					
							checkAccessibility(station);
						}
					});
					
					showStationsCount();
				});
				
				$("#clear_btn").click(function() {
					if(circle != null) circle.setMap(null);
					if(currentMarker != null) currentMarker.setMap(null);
					
					map.setCenter(initialLatLong);
					map.setZoom(7);
					
					$.each(markers, function(i, marker) {
						marker.setVisible(true);
					});
					
				});
				
				//search nearby
				$("#search_btn").click(function(){
					var address = $("#address").val();
					radius = parseInt($("#radius").val())*1600;
					stationsDisplayed = new Array();
					if(address !== undefined && $.trim(address).length >0)
					{
						var geocoder = new google.maps.Geocoder();
        				geocoder.geocode( 
        					{'address': address },
            				function(data, status) 
            				{ 
            					console.log("status coming up!");
            					console.log(status);
            					var lat = data[0].geometry.location.Xa;
								var lng = data[0].geometry.location.Ya;
								var latlng = new google.maps.LatLng(lat,lng,true);
								//console.log(latlng.toString());
								//draw a circle
								if (circle != null) {
									console.log("setvisible");
								    circle.setVisible(false);
        							circle.setMap(null);
    							}
    							
    							// Remove old marker
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
								//set focus
								/*
								var latlngbounds = new google.maps.LatLngBounds( );
								for ( var i = 0; i < stationsDisplayed.length; i++ ) {
  									latlngbounds.extend( stationsDisplayed[ i ].getPosition() );
								}
								*/
								map.fitBounds( circle.getBounds() );
								showStationsCount();
							});
					}
					else
					{
						alert("Please input a town or postcode");
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
				<h1><img src="/public/img/pins/rail.png" alt="national rail logo" /> RailGB <small>Accessible Rail Network Map</small></h1>
			</div>
			
			<div class="row-fluid">
				<div class="span8">
					<div id="map-canvas" style="width: 700px; height: 700px"></div>
				</div>
				<div class="span4">
					<div id="alert"><div class="alert alert-info">Loading&hellip;</div></div>
					<div>
						<form class="form-search" onsubmit="return false">
							<div class="control-group">
								<label for="address" class="control-label"><b>Location</b></label>
								<div class="controls">
    								<input type="text" name="address" id="address" class="input-long" placeholder="Town or Postcode"/>
    							</div>
    							<label for="radius" class="control-label"><b>Search Radius</b></label>
    							<div class="controls">
    								<select id="radius" name="radius">
    									<option value="1">1 mile</option>
										<option value="5" selected="selected">5 miles</option>
										<option value="10">10 miles</option>
										<option value="20">20 miles</option>
										<option value="50">50 miles</option>
										<option value="100">100 miles</option>
    								</select>
    							</div>
    							
    							<label>Select stations to show with:</label>
								<div class="controls">
									<label><input type="checkbox" name="station" id="filter-ticketoffice" value="ticketoffice" /> Ticket Office <img src="/public/img/fugue/ticket-1.png" alt="ticket office" /></label><br />
									<label><input type="checkbox" name="station" id="filter-staff" value="staff" /> Staffed <img src="/public/img/fugue/user.png" alt="staffed" /></label><br />
									<label><input type="checkbox" name="station" id="filter-ramp" value="ramp" /> Ramp <img src="/public/img/fugue/road.png" alt="ramp" /></label><br />
								</div>
							</div>
						</form>
						<button class="btn btn-primary" id="search_btn" type="submit">Search</button>
						<button class="btn pull-right" id="clear_btn">Clear Results</button>
					</div>
					<br/>
					<br/>
					<div id="station" style="display:none">
						<h4 id="station-name"></h4>
						<div id="station-innerticket">
							<p><b>Ticket Office:</b> <span id="station-ticketoffice"></span></p>
							<p><b>Staffing:</b> <span id="station-staffing"></span></p>
							<p><b>Ramp:</b> <span id="station-ramp"></span></p>
						</div>
						<div id="station-footer"><img src='/public/img/theme/ticket-logo.png' alt='National Rail' /></div>
					</div>
				</div>
			</div>
		</div>

		<? require_once($path.'/includes/footer.php'); ?>
		
	</body>
</html>