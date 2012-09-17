<!DOCTYPE html>
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
				var mapDiv = document.getElementById('map-canvas');
				var map = new google.maps.Map(mapDiv, {
					center: new google.maps.LatLng(52.84923, -2.032471),
					zoom: 7,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				});
				
				
				$.getJSON("http://oad.rkbexplorer.com/sparql/?callback=?",
					{
						format: "json",
						query: "PREFIX id: <http://oad.rkbexplorer.com/id/>	PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#> PREFIX owl: <http://www.w3.org/2002/07/owl#> PREFIX foaf: <http://xmlns.com/foaf/0.1/> PREFIX geo: <http://www.w3.org/2003/01/geo/wgs84_pos#> PREFIX rf: <http://ontologi.es/rail/vocab#facilities/> SELECT ?station ?name ?long ?lat ?staffing ?ramp ?ticket WHERE { ?station foaf:name ?name. ?station geo:lat ?lat. ?station geo:long ?long. }"},
					function(data) {
						console.log(data);
						alert("got datas!");
					});
			}
			
			$(function() {	
				google.maps.event.addDomListener(window, 'load', initialize);
			});
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
				<div class="span8">
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