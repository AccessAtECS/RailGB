<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Tube London - Accessible London Underground Map</title>
		<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
		<?php
			$path = $_SERVER['DOCUMENT_ROOT'];
			require_once($path.'/includes/header.php');
		?>
	</head>
	<body>
		<div data-role="page" id="tubemap_div" data-theme="d" data-title="Tube Map" style="width:100%;height:100%;">
			<div data-role="header" data-theme="a" data-position="fixed">
				<h1>Tube Map</h1>
				<a href="#search_div" data-icon="search" data-theme="d" data-direction="reverse" data-rel="popup" data-transition="slidedown" class="ui-btn-left">Search</a>
			</div>
			<div data-theme="d" style="width:100%;height:100%;padding:0">
				<div id="map-canvas" style="width:100%;height:100%;"></div>
			</div>
			<!-- Search Form -->
			<div data-role="popup" id="search_div">	
				<div data-role="content" data-theme="d">
					 <div style="padding:10px 20px;">
					  <h3>Please sign in</h3>
			          <select id="radius" name="radius">
									<option value="0.25">0.25 mile</option>
									<option value="0.5" selected="selected">0.5 miles</option>
									<option value="1.0">1 miles</option>
									<option value="2.0">2 miles</option>
									<option value="5.0">5 miles</option>
									<option value="10.0">10 miles</option>
					  </select>			
					 </div>
			    </div>
			</div>
			<!-- /Search Form-->
			<div data-role="footer" data-theme="d" data-position="fixed" >
				<div data-role="navbar" >
					<ul>
						<li><a class="ui-btn-active ui-state-persist" href="#tubemap_div" data-role="button" data-theme="d" id="tubemap_btn">Map</a></li>
						<li><a href="#filter_div" data-role="button" data-theme="d" id="filter_btn">Filter</a></li>
						<li><a href="#about_div" data-role="button" data-theme="d" id="about_btn">About</a></li>
					</ul>
				</div>
			</div>	
		</div>
		<!-- /map page -->
		
		<!-- filter page -->
		<div data-role="page" id="filter_div" data-title="Fitler">
			<div data-role="header" data-theme="a">
				<h1>Filter</h1>
				<a href="#tubemap_div" data-theme="d" data-rel="back" data-direction="reverse" class="ui-btn-left" data-icon="arrow-l">Back</a>			
			</div>
			<div data-role="content">
				Filter page
			</div>
			<div data-role="footer" data-theme="d" data-position="fixed" >
				<div data-role="navbar" >
					<ul>
						<li><a href="#tubemap_div" data-role="button" data-theme="d" id="tubemap_btn">Map</a></li>
						<li><a class="ui-btn-active ui-state-persist" href="#filter_div" data-role="button" data-theme="d" id="filter_btn">Filter</a></li>
						<li><a href="#about_div" data-role="button" data-theme="d" id="about_btn">About</a></li>
					</ul>
				</div>
			</div>	
		</div>
		<!-- /filter page -->
		
		<!-- detail page : display the detailed information of a tube station-->
		<div data-role="page" id="detail_div" data-theme="d" data-title="Station Detail">
			<div data-role="header" data-theme="a">>
				<h1>Station Detail</h1>
				<a href="#tubemap_div" data-theme="d" data-rel="back" data-direction="reverse" class="ui-btn-left" data-icon="arrow-l">Back</a>
			</div>
			</div>
		
			<div data-role="content">
				
			</div>
		
			<div data-role="footer" data-theme="d" data-position="fixed" >
				<div data-role="navbar" >
					<ul>
						<li><a class="ui-btn-active ui-state-persist" href="#tubemap_div" data-role="button" data-theme="d" id="tubemap_btn">Map</a></li>
						<li><a href="#filter_div" data-role="button" data-theme="d" id="filter_btn">Filter</a></li>
						<li><a href="#about_div" data-role="button" data-theme="d" id="about_btn">About</a></li>
					</ul>
				</div>
			</div>	
		</div>
		<!-- /Filter page -->
		
		<!-- About page : about this application-->
		<div data-role="page" id="about_div" data-title="About">
			<div data-role="header" data-theme="a">
				<h1>About</h1>
				<a href="#tubemap_div" data-theme="d" data-rel="back" data-direction="reverse" class="ui-btn-left"  data-icon="arrow-l">Back</a>
			</div>
		
			<div data-role="content">
				
			</div>
		
			<div data-role="footer" data-theme="d" data-position="fixed" >
				<div data-role="navbar" >
					<ul>
						<li><a href="#tubemap_div" data-role="button" data-theme="d" id="tubemap_btn">Map</a></li>
						<li><a href="#filter_div" data-role="button" data-theme="d" id="filter_btn">Filter</a></li>
						<li><a class="ui-btn-active ui-state-persist" href="#about_div" data-role="button" data-theme="d" id="about_btn">About</a></li>
					</ul>
				</div>
			</div>	
		
		</div>
		<!-- /About page -->
	</body>
</html>