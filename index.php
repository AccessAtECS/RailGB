<!DOCTYPE html>
<html lang="en">
	<head>
		<link href="/railgb/css/bootstrap.css" rel="stylesheet">
		<link href="/railgb/css/bootstrap-responsive.css" rel="stylesheet">
		<script type="text/javascript" src="/railgb/js/jquery-1.8.1.min.js"></script>
		<script type="text/javascript" src="/railgb/js/bootstrap.min.js"></script>
		<title>Google Maps V3 API Sample</title>
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
		<script type="text/javascript">
			function initialize() {
				var mapDiv = document.getElementById('map-canvas');
				var map = new google.maps.Map(mapDiv, {
					center: new google.maps.LatLng(52.84923, -2.032471),
					zoom: 7,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				});
			}
			
			google.maps.event.addDomListener(window, 'load', initialize);
		</script>
	</head>
	<body>
		<div class="container">
			<div class="page-header">
					<h1>RailGB <small>Accessible Rail</small></h1>
				</div>
			<div class="row-fluid">
				<div class="span3">
					Sidebar!
				</div>
				<div class="span8">
					<div id="map-canvas" style="width: 700px; height: 900px"></div>
				</div>
				</div>
			
		</div>
	</body>
</html>
​