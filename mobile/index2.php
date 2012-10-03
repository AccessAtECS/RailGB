<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Tube London - Accessible London Underground Map</title>
		<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
		<script type="text/javascript" src="http://google-maps-utility-library-v3.googlecode.com/svn/trunk/infobox/src/infobox.js"></script>
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
				<a href="#tubelist_div" data-icon="grid" data-theme="d" data-direction="reverse" class="ui-btn-right"  id="list_a">List</a>
			</div>
			<div data-theme="d" style="width:100%;height:100%;padding:0">
				<div id="map-canvas" style="width:100%;height:100%;"></div>
			</div>
			<!-- Search Form -->
			<div data-role="popup" id="search_div">	
				<div data-role="content" data-theme="d">
					 <div style="padding:10px 20px;">
						  <h3>Search</h3>
						  <form action="/public/ajax/tube.php" method="get" data-ajax="false" id="search_form">
							  <input type="search" name="address" id="address" value="" data-mini=“true” placeholder="Postcode or Address"/>
					          <select id="radius" name="radius" data-mini=“true”>
											<option value="0.25">0.25 miles radius</option>
											<option value="0.5" selected="selected">0.5 miles radius</option>
											<option value="1.0">1 miles radius</option>
											<option value="2.0">2 miles radius</option>
											<option value="5.0">5 miles radius</option>
											<option value="10.0">10 miles radius</option>
							  </select>
							  <input type="submit" value="Submit" data-theme="b"/>
						  </form>			
					 </div>
			    </div>
			</div>
			<!-- /Search Form-->
			<!-- Search result info form -->
			<div data-role="popup" id="search_info_div" class="ui-content" data-theme="e" style="max-width:200px;">
				<p id="search_info_p"></p>
	        </div>
			<!-- /Search result info form -->
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
				<!-- TODO: Add a reset Button -->
				<!-- TODO: use cookie to remember user's choices on this phone -->
			</div>
			<div data-role="content">
				<div data-role="fieldcontain">
				    <fieldset data-role="controlgroup">
				       <legend>You are:</legend>
					   <input type="checkbox" name="wheelchair" id="wheelchair" class="custom" />
					   <label for="wheelchair">Wheelchair user</label>
					   
					   <input type="checkbox" name="blind" id="blind" class="custom" />
					   <label for="blind">Visual impair (blind)</label>

				    </fieldset>
				</div>
				<div data-role="fieldcontain">
				 	<fieldset data-role="controlgroup">
				 		<legend>Accessibility:</legend>
						<label><input type="checkbox" name="facility[]" id="filter-lifts" value="lifts" /> Lifts </label>
						<label><input type="checkbox" name="facility[]" id="filter-th" value="th" /> Tickets Hall </label>
						<label><input type="checkbox" name="facility[]" id="filter-es" value="es" /> Escalators </label>
						<label><input type="checkbox" name="facility[]" id="filter-to" value="to" /> Toilets </label>
						<label><input type="checkbox" name="facility[]" id="filter-cp" value="cp" /> Car Park </label>
						<label><input type="checkbox" name="facility[]" id="filter-hpth" value="hpth" /> Help Points in ticket halls</label>
						<label><input type="checkbox" name="facility[]" id="filter-hppf" value="hppf" /> Help Points on platforms</label>
						<label><input type="checkbox" name="facility[]" id="filter-wr" value="wr" /> Waiting Room </label>
						<label><input type="checkbox" name="facility[]" id="filter-wifi" value="wifi" /> WiFi available </label>
						<label><input type="checkbox" name="facility[]" id="filter-sf" value="sf" /> Step-free </label>
				 	</fieldset>
				</div>
				<div data-role="collapsible" data-theme="e" data-content-theme="d">
				   <h3>Other Facilities</h3>
				   <p>
				   <div data-role="fieldcontain">
					 	<fieldset data-role="controlgroup">
							<label><input type="checkbox" name="facility[]" id="filter-gates" value="gates" /> Gates </label>
							<label><input type="checkbox" name="facility[]" id="filter-pb" value="pb" /> Photo Booths </label>
							<label><input type="checkbox" name="facility[]" id="filter-cm" value="cm" /> Cash Machine </label>
							<label><input type="checkbox" name="facility[]" id="filter-pp" value="pp" /> Payphones </label>
							<label><input type="checkbox" name="facility[]" id="filter-vm" value="vm" /> Vending Machines </label>
							<label><input type="checkbox" name="facility[]" id="filter-br" value="br" /> Bridge </label>
					 	</fieldset>
					</div>
				   </p>	
				</div>
							
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
		
		<!-- tubelist page -->
		<div data-role="page" id="tubelist_div" data-title="Tube List">
			<div data-role="header" data-theme="a">
				<h1>List of Tube Stations</h1>
				<a href="#tubemap_div" data-theme="d" data-rel="back" data-direction="reverse" class="ui-btn-left" data-icon="arrow-l">Back</a>			
			</div>
			<div data-role="content">
				<ul id="tubelist_ul" data-role="listview" data-filter="true" data-filter-placeholder="Filter stations...">
				</ul>
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
		<!-- /tubelist page -->
		
		<!-- detail page : display the detailed information of a tube station-->
		<div data-role="page" id="detail_div" data-theme="d" data-title="Station Detail">
			<div data-role="header" data-theme="a">
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