<?
$query = "PREFIX id:   <http://oad.rkbexplorer.com/id/>
PREFIX rdf:	 <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
PREFIX owl:	 <http://www.w3.org/2002/07/owl#>
PREFIX foaf: <http://xmlns.com/foaf/0.1/>
PREFIX geo: <http://www.w3.org/2003/01/geo/wgs84_pos#>
PREFIX rf: <http://ontologi.es/rail/vocab#facilities/>
PREFIX ontologi: <http://ontologi.es/rail/vocab#>
SELECT Distinct ?station ?name ?long ?lat ?ramp ?ticket ?staffing ?code
WHERE 
{ 
  ?station foaf:name ?name.
  ?station geo:lat ?lat.
  ?station geo:long ?long.
  ?station ontologi:crs ?code.
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
}";

$contents = file_get_contents("http://oad.rkbexplorer.com/sparql/?format=json&query=".urlencode(str_replace("\n", " ", $query)));
$contents = json_decode($contents);
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<link rel="icon" href="/railgb/img/theme/favicon.png" type="image/x-icon">
		<link href="/railgb/css/bootstrap.css" rel="stylesheet">
		<link href="/railgb/css/bootstrap-responsive.css" rel="stylesheet">
		<link href="/railgb/css/railgb.css" rel="stylesheet">
		<script type="text/javascript" src="/railgb/js/jquery-1.8.1.min.js"></script>
		<script type="text/javascript" src="/railgb/js/bootstrap.min.js"></script>
		
		<title>RailGB - Accessible Rail Network Map</title>
		
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
		<script type="text/javascript">
			function initialize() {
				
				// Image for each pin
				var image = '/railgb/img/pins/rail-red.png';
				
				// Fire up map
				var mapDiv = document.getElementById('map-canvas');
				var map = new google.maps.Map(mapDiv, {
					center: new google.maps.LatLng(52.84923, -2.032471),
					zoom: 7,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				});
				
				// Add "Loading" text here.
				
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
					
					// Marker display box
					google.maps.event.addListener(markers[markers.length - 1], 'click', function(){
						$("#station-name").html(this.title);
						$("#station-ticketoffice").html(this.railgb_ticketoffice.text);
						$("#station-staffing").html(this.railgb_staffing.text);
						$("#station-ramp").html(this.railgb_ramp.text);
						$("#station").show();
					});
				});
								
				// Clear "Loading" text here
			}
			
			// When ready, fire up the google map. RDF loads when the map is ready.
			$(function() {
				google.maps.event.addDomListener(window, 'load', initialize);
				
				
				
				$("input[type='checkbox']").click(function() {
					var filterTicketOffices = $("#filter-ticketoffice").is(':checked');
					var filterStaffing = $("#filter-staff").is(':checked');
					var filterRamp = $("#filter-ramp").is(':checked');
					
					$.each(markers, function(i, station) {
						station.setVisible(true);
						
						if(filterTicketOffices == true && station.railgb_ticketoffice.value == false) station.setVisible(false);
						if(filterStaffing == true && station.railgb_staffing.value == false) {
							station.setVisible(false);
							
						} 
						if(filterRamp == true && station.railgb_ramp.value == false) {
							station.setVisible(false);
							console.log("Hiding "+station.title+" because it has no ranmp!");
						} 
					});
					
				});
				
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
				
				<div class="span4">
					<h4>Select stations to show with:</h4>
					<form>
						<label><input type="checkbox" name="station" id="filter-ticketoffice" value="ticketoffice" /> Ticket Office <img src="/railgb/img/fugue/ticket-1.png" alt="ticket office" /></label><br />
						<label><input type="checkbox" name="station" id="filter-staff" value="staff" /> Staffed <img src="/railgb/img/fugue/user.png" alt="staffed" /></label><br />
						<label><input type="checkbox" name="station" id="filter-ramp" value="ramp" /> Ramp <img src="/railgb/img/fugue/road.png" alt="ramp" /></label><br />
					</form>
					
					<div id="station" style="display:none">
						<h4 id="station-name"></h4>
						<div id="station-innerticket">
							<p><b>Ticket Office:</b> <span id="station-ticketoffice"></span></p>
							<p><b>Staffing:</b> <span id="station-staffing"></span></p>
							<p><b>Ramp:</b> <span id="station-ramp"></span></p>
						</div>
						<div id="station-footer"><img src='/railgb/img/theme/ticket-logo.png' alt='National Rail' /></div>
					</div>
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