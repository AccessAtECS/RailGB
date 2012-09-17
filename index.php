<?
$query = "PREFIX id:   <http://oad.rkbexplorer.com/id/>
PREFIX rdf:  <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
PREFIX owl:  <http://www.w3.org/2002/07/owl#>
PREFIX foaf: <http://xmlns.com/foaf/0.1/>
PREFIX geo: <http://www.w3.org/2003/01/geo/wgs84_pos#>
PREFiX rf: <http://ontologi.es/rail/vocab#facilities/>
SELECT Distinct ?station ?name ?long ?lat ?ramp ?ticket ?staffing
WHERE 
{ 
  ?station foaf:name ?name.
  ?station geo:lat ?lat.
  ?station geo:long ?long.
  OPTIONAL 
  {
    ?station ?p1 ?t.
    ?t rf:availability ?ticket.
    ?t rdfs:label \"Ticket Office\"@en.
  }
  OPTIONAL
  {
    ?station ?p2 ?s.
    ?s rf:availability ?staffing.
    ?s rdfs:label \"Staffing\"@en.
  }
  OPTIONAL
  {
    ?station ?p3 ?ramp.
    ?ramp rdfs:label \"Ramp for Train Access\"@en.
  }
} LIMIT 100";
//echo "http://oad.rkbexplorer.com/sparql/?format=json&query=".urlencode(str_replace("\n", " ", $query));
$contents = file_get_contents("http://oad.rkbexplorer.com/sparql/?format=json&query=".urlencode(str_replace("\n", " ", $query)));
$contents = json_decode($contents);

?><!DOCTYPE html>
<html lang="en">
	<head>
		<link href="/railgb/css/bootstrap.css" rel="stylesheet">
		<link href="/railgb/css/bootstrap-responsive.css" rel="stylesheet">
		<link href="/railgb/css/railgb.css" rel="stylesheet">
		<script type="text/javascript" src="/railgb/js/jquery-1.8.1.min.js"></script>
		<script type="text/javascript" src="/railgb/js/bootstrap.min.js"></script>
		<title>RailGB - Accessible Rail Network Map</title>
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
		<script type="text/javascript">
			function initialize() {
				// Fire up map
				var mapDiv = document.getElementById('map-canvas');
				var map = new google.maps.Map(mapDiv, {
					center: new google.maps.LatLng(52.84923, -2.032471),
					zoom: 7,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				});
				
				// Add "Loading" text here.
				
				$.each(stations.results.bindings, function(i, station) {
					//console.log(station.name.value);
					markers.push(new google.maps.Marker({
				    	position: new google.maps.LatLng(station.lat.value, station.long.value),
				    	map: map,
				    	title: station.name.value
				    }));
				});
								
				// Clear "Loading" text here
			}
			
			// When ready, fire up the google map. RDF loads when the map is ready.
			$(function() {
				google.maps.event.addDomListener(window, 'load', initialize);
				//console.log(localData);
				
			});
			
			var stations = <?=json_encode($contents)?>;
			var markers = new Array();
		</script>
	</head>
	<body>
		<div class="container" id="container">
			<div class="page-header">
					<h1>RailGB <small>Accessible Rail Network Map</small></h1>
				</div>
			<div class="row-fluid">
				
				<div class="span3">
					<h4>Select stations to show with:</h4>
					<form>
						<input type="checkbox" name="station" value="ramp" /> Ramp<br />
						<input type="checkbox" name="station" value="staff" /> Staffed<br />
						<input type="checkbox" name="station" value="ticketoffice" /> Ticket Office<br />
					</form>
				</div>
				<div class="span8 offset1">
					<div id="map-canvas" style="width: 700px; height: 900px"></div>
				</div>
				</div>
		</div>	
		<div class="container">
			<footer>
				<p class="pull-right muted"><a href="/railgb/about.php">About</a></p>
				<p class="pull-left"><img src="/railgb/img/theme/uos.png" alt="University of Southampton"></p>
			</footer>
		</div>
	</body>
</html>