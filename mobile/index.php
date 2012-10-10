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
				<h1>RailGB Tube Map</h1>
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
							  <input type="button" value="Use Current Location" data-theme="e" id="current_location_btn"/>
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
			<div data-role="footer" data-theme="d" data-position="fixed" class="nav-glyphish">
				<div data-role="navbar" >
					<ul>
						<li><a class="ui-btn-active ui-state-persist" href="#tubemap_div" data-role="button" data-theme="d" id="tubemap_btn" data-mini="true" data-icon="custom">Map</a></li>
						<li><a href="#filter_div" data-role="button" data-theme="d" id="filter_btn" data-mini="true" data-icon="custom">Filter</a></li>
						<li><a href="#places_div" data-role="button" data-theme="d" id="places_btn" data-mini="true" data-icon="custom">Places</a></li>
						<li><a href="#help_div" data-role="button" data-theme="d" id="help_btn" data-mini="true" data-icon="custom">Help</a></li>
						<li><a href="#about_div" data-role="button" data-theme="d" id="about_btn" data-mini="true" data-icon="custom">About</a></li>
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
					   <label for="blind">Blind or Partially sighted</label>
					   
					   <input type="checkbox" name="hearing" id="hearing" class="custom" />
					   <label for="hearing">Hearing Loss</label>

					   <input type="checkbox" name="mobility" id="mobility" class="custom" />
					   <label for="mobility">Mobility Difficulties</label>
				    </fieldset>
				</div>
				<div data-role="fieldcontain">
				 	<fieldset data-role="controlgroup">
				 		<legend>Accessibility:</legend>
				 		<label><input type="checkbox" name="facility[]" id="filter-sf" value="sf" /> Step-free </label>
						<label><input type="checkbox" name="facility[]" id="filter-lifts" value="lifts" /> Lifts </label>
						<label><input type="checkbox" name="facility[]" id="filter-th" value="th" /> Tickets Hall </label>
						<label><input type="checkbox" name="facility[]" id="filter-es" value="es" /> Escalators </label>
						<label><input type="checkbox" name="facility[]" id="filter-to" value="to" /> Toilets </label>
						<label><input type="checkbox" name="facility[]" id="filter-cp" value="cp" /> Car Park </label>
						<label><input type="checkbox" name="facility[]" id="filter-hpth" value="hpth" /> Help Points in ticket halls</label>
						<label><input type="checkbox" name="facility[]" id="filter-hppf" value="hppf" /> Help Points on platforms</label>
						<label><input type="checkbox" name="facility[]" id="filter-wr" value="wr" /> Waiting Room </label>
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
			
			<div data-role="footer" data-theme="d" data-position="fixed" class="nav-glyphish">
				<div data-role="navbar" >
					<ul>
						<li><a href="#tubemap_div" data-role="button" data-theme="d" id="tubemap_btn" data-mini="true" data-icon="custom">Map</a></li>
						<li><a class="ui-btn-active ui-state-persist" href="#filter_div" data-role="button" data-theme="d" id="filter_btn" data-mini="true" data-icon="custom">Filter</a></li>
						<li><a href="#places_div" data-role="button" data-theme="d" id="places_btn" data-mini="true" data-icon="custom">Places</a></li>
						<li><a href="#help_div" data-role="button" data-theme="d" id="help_btn" data-mini="true" data-icon="custom">Help</a></li>
						<li><a href="#about_div" data-role="button" data-theme="d" id="about_btn" data-mini="true" data-icon="custom">About</a></li>
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
			<div data-role="footer" data-theme="d" data-position="fixed" class="nav-glyphish">
				<div data-role="navbar" >
					<ul>
						<li><a class="ui-btn-active ui-state-persist" href="#tubemap_div" data-role="button" data-theme="d" id="tubemap_btn" data-mini="true" data-icon="custom">Map</a></li>
						<li><a href="#filter_div" data-role="button" data-theme="d" id="filter_btn" data-mini="true" data-icon="custom">Filter</a></li>
						<li><a href="#places_div" data-role="button" data-theme="d" id="places_btn" data-mini="true" data-icon="custom">Places</a></li>
						<li><a href="#help_div" data-role="button" data-theme="d" id="help_btn" data-mini="true" data-icon="custom">Help</a></li>
						<li><a href="#about_div" data-role="button" data-theme="d" id="about_btn" data-mini="true" data-icon="custom">About</a></li>
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
		
			<div data-role="content">
				<h2 id="station_h2"></h2>
				<div data-role="collapsible-set" data-theme="b" data-content-theme="d">
					<div data-role="collapsible" data-collapsed="false" id="station_info_div">
						<h3>Station Info</h3>
						<div id="station_info_content">
							<ul data-role="listview" data-theme="d" data-divider-theme="d" id="station_info_ul_content">
								<li>
									<h4 id="station_h4"></h4>
									<p id="address_p"></p>
									<p id="phone_p"></p>
									<p id="zone_p"></p>
									<p class="ui-li-aside" id="sf_p"></p>
									<img src="/public/img/no-image.jpeg" id="station_thumbnail" style="margin-top:10px;" alt=""/>
								</li>
							</ul>
						</div>
						<div data-role="popup" id="station_depiction_popup" data-overlay-theme="a" data-theme="d" data-corners="false">
							<a href="#" data-rel="back" data-role="button" data-theme="a" data-icon="delete" data-iconpos="notext" class="ui-btn-right">Close</a><img id="station_depiction" class="popphoto" src="" alt=""/>
						</div>
					</div>
						
					<div data-role="collapsible" id="station_facility_div_yes">
						<h3>Facilities Available</h3>
						<div id="station_facility_content_yes">
							<ul id="station_facility_content_ul_yes" data-role="listview"></ul>
						</div>
					</div>
					<div data-role="collapsible" id="station_facility_div_yes">
						<h3>Facilities Not Available</h3>
						<div id="station_facility_content_no">
							<ul id="station_facility_content_ul_no" data-role="listview"></ul>
						</div>
					</div>
					<div data-role="collapsible" id="station_more_div">
						<h3>More Info</h3>
						<div id="station_more_content">
							
						</div>
					</div>
				</div>
			</div>
		
			<div data-role="footer" data-theme="d" data-position="fixed" class="nav-glyphish">
				<div data-role="navbar" >
					<ul>
						<li><a href="#tubemap_div" data-role="button" data-theme="d" id="tubemap_btn" data-mini="true" data-icon="custom">Map</a></li>
						<li><a href="#filter_div" data-role="button" data-theme="d" id="filter_btn" data-mini="true" data-icon="custom">Filter</a></li>
						<li><a href="#places_div" data-role="button" data-theme="d" id="places_btn" data-mini="true" data-icon="custom">Places</a></li>
						<li><a class="ui-btn-active ui-state-persist" href="#help_div" data-role="button" data-theme="d" id="help_btn" data-mini="true" data-icon="custom">Help</a></li>
						<li><a href="#about_div" data-role="button" data-theme="d" id="about_btn" data-mini="true" data-icon="custom">About</a></li>
					</ul>
				</div>
			</div>	
		</div>
		<!-- /Filter page -->
		
		<!-- Places page -->
		<div data-role="page" id="places_div" data-title="Quick Places">
			<div data-role="header" data-theme="a">
				<h1>Quick Places</h1>
				<a href="#tubemap_div" data-theme="d" data-rel="back" data-direction="reverse" class="ui-btn-left" data-icon="arrow-l">Back</a>			
			</div>
			<div data-role="content">
				<ul id="placelist_ul" data-autodividers="true" data-role="listview" data-filter="true" data-filter-placeholder="Filter Places..."></ul>
			</div>
			<div data-role="footer" data-theme="d" data-position="fixed" class="nav-glyphish">
				<div data-role="navbar" >
					<ul>
						<li><a href="#tubemap_div" data-role="button" data-theme="d" id="tubemap_btn" data-mini="true" data-icon="custom">Map</a></li>
						<li><a href="#filter_div" data-role="button" data-theme="d" id="filter_btn" data-mini="true" data-icon="custom">Filter</a></li>
						<li><a class="ui-btn-active ui-state-persist" href="#places_div" data-role="button" data-theme="d" id="places_btn" data-mini="true" data-icon="custom">Places</a></li>
						<li><a href="#help_div" data-role="button" data-theme="d" id="help_btn" data-mini="true" data-icon="custom">Help</a></li>
						<li><a href="#about_div" data-role="button" data-theme="d" id="about_btn" data-mini="true" data-icon="custom">About</a></li>
					</ul>
				</div>
			</div>	
		</div>
		<!-- /Places page -->
		
		<!-- Help page -->
		<div data-role="page" id="help_div" data-title="Help">
			<div data-role="header" data-theme="a">
				<h1>Help</h1>
				<a href="#tubemap_div" data-theme="d" data-rel="back" data-direction="reverse" class="ui-btn-left" data-icon="arrow-l">Back</a>			
			</div>
			<div data-role="content">
				<h2>Help</h2>
			</div>
			<div data-role="footer" data-theme="d" data-position="fixed" class="nav-glyphish">
				<div data-role="navbar" >
					<ul>
						<li><a href="#tubemap_div" data-role="button" data-theme="d" id="tubemap_btn" data-mini="true" data-icon="custom">Map</a></li>
						<li><a href="#filter_div" data-role="button" data-theme="d" id="filter_btn" data-mini="true" data-icon="custom">Filter</a></li>
						<li><a href="#places_div" data-role="button" data-theme="d" id="places_btn" data-mini="true" data-icon="custom">Places</a></li>
						<li><a class="ui-btn-active ui-state-persist" href="#help_div" data-role="button" data-theme="d" id="help_btn" data-mini="true" data-icon="custom">Help</a></li>
						<li><a href="#about_div" data-role="button" data-theme="d" id="about_btn" data-mini="true" data-icon="custom">About</a></li>
					</ul>
				</div>
			</div>	
		</div>
		<!-- /Help page -->
		
		<!-- About page : about this application-->
		<div data-role="page" id="about_div" data-title="About">
			<div data-role="header" data-theme="a">
				<h1>About</h1>
				<a href="#tubemap_div" data-theme="d" data-rel="back" data-direction="reverse" class="ui-btn-left"  data-icon="arrow-l">Back</a>
			</div>
		
			<div data-role="content">
				<h2>About RailGB</h2>
				<p> RailGB as a project came out of the annual WAIS Fest 2012. The aim of the project is to identify accessible public transport systems, primarily the UK rail services and London Underground Network. </p>
				<p>Special thanks to Hugh Glaser and Ian Millard for providing the triple store in <a href="http://oad.rkbexplorer.com/" target="_blank">RKBExplorer</a>.</p>
				<p>
The development team are members of the Web and Internet Science Faculty at the University of Southampton, with considerable experience in the field of digital technologies in relation to personalisation, disability, assistive technology, e-learning, mobile and Semantic Web services.</p>
			<p> &copy; 2012 ECS, University of Southampton</p>
			</div>
		
			<div data-role="footer" data-theme="d" data-position="fixed" class="nav-glyphish">
				<div data-role="navbar" >
					<ul>
						<li><a href="#tubemap_div" data-role="button" data-theme="d" id="tubemap_btn" data-mini="true" data-icon="custom">Map</a></li>
						<li><a href="#filter_div" data-role="button" data-theme="d" id="filter_btn" data-mini="true" data-icon="custom">Filter</a></li>
						<li><a href="#places_div" data-role="button" data-theme="d" id="places_btn" data-mini="true" data-icon="custom">Places</a></li>
						<li><a href="#help_div" data-role="button" data-theme="d" id="help_btn" data-mini="true" data-icon="custom">Help</a></li>
						<li><a class="ui-btn-active ui-state-persist" href="#about_div" data-role="button" data-theme="d" id="about_btn" data-mini="true" data-icon="custom">About</a></li>
					</ul>
				</div>
			</div>	
		
		</div>
		<!-- /About page -->
	</body>
</html>