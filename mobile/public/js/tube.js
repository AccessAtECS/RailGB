console.log("load tube.js");

var map = null;
var circle = null;
var currentMarker = null; //the center of current marker
var markers = new Array(); //all the markers of tube stations
var stationsDisplayed = new Array();
var radius = 1600.0; //1 miles

var rs = null;
var initialLatLong = new google.maps.LatLng(51.508129, -0.128005);

if (typeof(Number.prototype.toRad) === "undefined") {
		Number.prototype.toRad = function() {
		return this * Math.PI / 180;
	}
}

function clearOverylays()
{
	if(markers && markers.length > 0)
	{
		for(i in markers)
		{
			markers[i].setMap(null);
		}
	}
	markers = new Array();
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
				 	//checkAccessibility(station);
				 	return true;
				}
				else
				{
				    station.setVisible(false);	
				    return false;		
				}
		}
}
			
function fireUpStations(stations) {
	console.log("new markers");
	clearOverylays();
	stationsDisplayed = new Array();
	var count = 0;
	//console.log(stations.results.bindings.length);
	$.each(stations.results.bindings, function(i, station) {
			//console.log(station);
			//Yunjia: use default marker image first
			//var hasLift = {value: false, text: 'Not available'};
			//if(station.hasLift.value === "true") {
			//	hasLift = {value:true,text:'Lift available'};
			//	image = '/public/img/pins/wheelchair-ok.png';
			//} else {
			//	image = '/public/img/pins/wheelchair-not-ok.png';
			//}
			
			//console.log("hasLift",station.hasLift.value);
			
			//Yunjia: change the image later
			//var image = '/public/img/pins/beachflag.png';
			var image = "http://code.google.com/apis/maps/documentation/javascript/examples/images/beachflag.png";
			
			//Yunjia Li: This is deliberate! There is something wrong with the dataset
			var lng = parseFloat(station.lat.value);
			var lat = parseFloat(station.lng.value)
			var marker = new google.maps.Marker({
				position: new google.maps.LatLng(lat,lng,true),
				map: map,
				icon:image,
				uri: station.station.value,
				title: station.name.value,
				draggable: false,
				visible: true
			});
			
			if(checkDistance(marker))
			{
				stationsDisplayed.push(marker);
			}
			markers.push(marker);
			count++;
			// Marker display box
			google.maps.event.addListener(marker, 'click', function(){
				$.ajax({
					dataType:"json",
					url: "/public/ajax/detail.php",
					data:{stationURI:marker.uri},
					success:function(data)
					{
						//console.log("successful");
						$("#station-name").html(marker.title);
						//Yunjia: render has or doesn't have div
						$("#station").show();
						$("#map-canvas").hide();
					}
				});
				
			});
		});
		$("#alert").html(
			'<div class="alert alert-success">Showing '+count+' stations.</div>'
		).show();
}

function initialize() {
	
	// Image for each pin
	//var image = '/public/img/theme/wheelchair-not-ok.png';
	
	// Fire up map
	var mapDiv = document.getElementById('map-canvas');
	console.log(mapDiv);
	map = new google.maps.Map(mapDiv, {
		center: new google.maps.LatLng(51.508129, -0.128005),
		zoom: 12,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	});
	
	// Add "Loading" text here.
	
	//console.log(stations);
	
	//Commented by Yunjia Li for test
	//if(typeof sessionStorage.tube == "undefined") {
		displayStations("Lodon Bridge",function(err,data){
			sessionStorage.tube = data;
		});
		
	//} 
	//else {
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
			'<div class="alert alert-success">Showing '+stationsDisplayed.length+' stations matching your search.</div>'
		);
	}
	$("#alert").show();
}

//callback: the callback function if necessary
function displayStations(address, callback)
{				
	radius = parseFloat($("#radius").val())*1600;
	console.log("radius:"+radius);
	console.log("address:"+address);
	stationsDisplayed = new Array();
	if(address !== undefined && $.trim(address).length >0)
	{
		var selected = new Array()
		$("#form-search :checkbox:checked").each(function(){
			selected.push($(this).val());
		});
		var geocoder = new google.maps.Geocoder();
		geocoder.geocode( 
			{'address': address },
			function(data, status) 
			{ 
				var lat = data[0].geometry.location.Xa;
				var lng = data[0].geometry.location.Ya;
				var latlng = new google.maps.LatLng(lat,lng,true);
				console.log(latlng.toString());
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
				//map.fitBounds( circle.getBounds() );
				
				//console.log("call");
				$.ajax({
					dataType:"json",
					url: "/public/ajax/tube.php",
					data:{facility:selected},
					success:function(data)
					{
						//console.log("successful");
						fireUpStations(data);
						
						if(stationsDisplayed.length >0)
						{
							
							var latlngbounds = new google.maps.LatLngBounds( );
							for ( var i = 0; i < stationsDisplayed.length; i++ ) {
									latlngbounds.extend( stationsDisplayed[ i ].getPosition() );
							}
							map.setCenter(currentMarker.getPosition());
							map.fitBounds( latlngbounds );
						}
						
						showStationsCount();
						callback(null, data);
						return;
					}
				});
				//Build query sting and make sparql query
											//$.each(markers, function(i, station) {
				//	checkDistance(station);
				//});
				//set focus if stations are available
				//console.log("displayed "+stationsDisplayed.length);
			}
		);
		
	}
	else
	{
		alert("Please input an address or postcode");
	}
	return false;
}

//####################JQuery Mobile Methods##########################
$( document ).bind( 'mobileinit', function(){
	$.mobile.loader.prototype.options.text = "loading";
	$.mobile.loader.prototype.options.textVisible = false;
	$.mobile.loader.prototype.options.theme = "d";
	$.mobile.loader.prototype.options.html = "";
	
	$.mobile.page.prototype.options.backBtnTheme    = "d";
});
					
$('#tubemap_div').live('pageshow', function(event) {
	console.log("tubemap init");
	
	google.maps.event.addDomListener(window, 'load', initialize);
				
	/*$("#clear_btn").click(function() {
		if(circle != null) circle.setMap(null);
		if(currentMarker != null) currentMarker.setMap(null);
		
		map.setCenter(initialLatLong);
		map.setZoom(10);
		
		//Yunjia Li:
		//$.each(markers, function(i, marker) {
		//	marker.setVisible(true);
		//});
		return false;
	});
	
	$(".locationlookups a").click(function() {
		return false;
	});
	
	//search nearby
	$("#search_btn").click(function(){
		var address = $("#address").val();
		displayStations(address,function(){
			//Do nothing
		});
	});*/
});
