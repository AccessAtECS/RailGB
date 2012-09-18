<?php
$query = "PREFIX id:   <http://oad.rkbexplorer.com/id/>
PREFIX rdf:  <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
PREFIX owl:  <http://www.w3.org/2002/07/owl#>
PREFIX foaf: <http://xmlns.com/foaf/0.1/>
PREFIX geo: <http://www.w3.org/2003/01/geo/wgs84_pos#>
PREFIX dbpedia-owl: <http://dbpedia.org/ontology/>
PREFIX dbpprop: <http://dbpedia.org/property/>
PREFIX dbpedia: <http://dbpedia.org/resource/>
PREFIX fb: <http://rdf.freebase.com/ns/>

SELECT Distinct ?station ?name ?long ?lat ?hasLift
{
    ?station rdf:type dbpedia-owl:Station.
    ?station dbpprop:manager dbpedia:London_Underground.
    ?station owl:sameAs ?freebase.
    ?station rdfs:label ?name.
    ?station <http://rdf.freebase.com/ns/user.sterops.accessibility.wheelchair_accessible_location.elevator> ?hasLift.
    ?freebase fb:location.location.geolocation ?location.
    ?location fb:location.geocode.longitude ?long.
    ?location fb:location.geocode.latitude ?lat.
    FILTER ( lang(?name) = 'en' )
}";

$contents = file_get_contents("http://oad.rkbexplorer.com/sparql/?format=json&query=".urlencode(str_replace("\n", " ", $query)));
$contents = json_decode($contents);
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<link rel="icon" href="img/theme/favicon.png" type="image/x-icon">
		<link href="css/bootstrap.css" rel="stylesheet">
		<link href="css/bootstrap-responsive.css" rel="stylesheet">
		<link href="css/railgb.css" rel="stylesheet">
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
		<script type="text/javascript" src="js/bootstrap.min.js"></script>
		
		<title>Tube London - Accessible London Tube Ma</title>
		
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
		<script type="text/javascript">
			var map = null;
			var circle = null;
			var currentMarker = null;
			var stationsDisplayed = new Array();
			var radius = 1600.0; //1 miles
			
			if (typeof(Number.prototype.toRad) === "undefined") {
  				Number.prototype.toRad = function() {
    				return this * Math.PI / 180;
  				}
			}

			function initialize() {
				
				// Image for each pin
				var image = 'img/pins/rail-red.png';
				
				// Fire up map
				var mapDiv = document.getElementById('map-canvas');
				map = new google.maps.Map(mapDiv, {
					center: new google.maps.LatLng(51.508129, -0.128005),
					zoom: 10,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				});
				
				// Add "Loading" text here.
				var count = 0;
				//console.log(stations);
				$.each(stations.results.bindings, function(i, station) {
					
					var hasLift = {value: false, text: 'Not available'};
					
					if(station.hasLift.value === "true")
					{
						hasLift = {value:true,text:'Lift available'}
					};
					
					//console.log("hasLift",station.hasLift.value);
					markers.push(new google.maps.Marker({
						position: new google.maps.LatLng(station.lat.value, station.long.value),
						map: map,
						title: station.name.value,
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
				
				$("#total_span").text(count+" stations in total.");			
				// Clear "Loading" text here
			}
			
			function showStationsCount()
			{
				if(stationsDisplayed.length ==0)
				{
					$("#alert").html('<div class="alert alert-warning"><button type="button" class="close" data-dismiss="alert">x</button>'+
						'No station has been found.</div>');
				}
				else
				{
					$("#alert").html(
						'<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">x</button>'+
						stationsDisplayed.length+' stations have been found.</div>'
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
			
			
			// When ready, fire up the google map. RDF loads when the map is ready.
			$(function() {
				google.maps.event.addDomListener(window, 'load', initialize);
				
				$("input[type='checkbox']").click(function() {
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
    							
    							var marker = new google.maps.Marker({
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
								
								circle.bindTo('center', marker, 'position');
								currentMarker = marker;
								
								map.setCenter(currentMarker.getPosition());
								$.each(markers, function(i, station) {
									checkDistance(station);
								});
								//set focus if stations are available
								//console.log("displayed "+stationsDisplayed.length);
								if(stationsDisplayed.length >0)
								{
									var latlngbounds = new google.maps.LatLngBounds( );
									for ( var i = 0; i < stationsDisplayed.length; i++ ) {
  										latlngbounds.extend( stationsDisplayed[ i ].getPosition() );
									}
									//map.setCenter(currentMarker.getPosition());
									map.fitBounds( latlngbounds );
								}
								
								showStationsCount();
							});
					}
					else
					{
						alert("Please input the Postcode");
					}
					return false;
				});
			});
			
			var stations = <?php echo json_encode($contents); ?>;
			var markers = new Array();
		</script>
		
	</head>
	
	
	
	<body>
		<div class="container" id="container">
			<div class="page-header">
					<h1>Tube London <small>Accessible London Tube Map</small></h1>
				</div>
			<div class="row-fluid">
				<div class="span8">
					<div id="map-canvas" style="width: 700px; height: 900px"></div>
				</div>
				<div class="span4">
					<div class="pull-right"><span class="badge badge-success" id="total_span"></span></div>
					<br/>
					<br/>
					<div id="alert" style="display:none"></div>
					<div>
						<form class="form-search">
							<div class="control-group">
								<label for="address" class="control-label"><b>Location</b></label>
								<div class="controls">
    								<input type="text" name="address" id="address" class="input-long" placeholder="Please Input the Postcode"/>
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
									<label><input type="checkbox" name="station" id="filter-lift" value="lift" /> Lift available <img src="img/fugue/ticket-1.png" alt="ticket office" /></label><br />
								</div>
						</form>
						<br/>
						<button class="btn btn-primary" id="search_btn">Search</button>
					</div>
					<div id="station" style="display:none">
						<h4 id="station-name"></h4>
						<div id="station-innerticket">
							<p><b>Lift:</b> <span id="station-lift"></span></p>
						</div>
						<div id="station-footer"><img src='img/theme/ticket-logo.png' alt='National Rail' /></div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="container">
			<footer>
				<p class="pull-right muted"><a href="about.php">About</a></p>
				<p class="pull-left"><img src="img/theme/uos.png" alt="University of Southampton"></p>
			</footer>
		</div>
		
		
	</body>
</html>
