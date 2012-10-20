<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Tube London - Accessible London Underground Map</title>
		<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
		<script type="text/javascript" src="http://google-maps-utility-library-v3.googlecode.com/svn/trunk/infobox/src/infobox.js"></script>
		<?php
			$path = $_SERVER['DOCUMENT_ROOT'];
			require_once($path.'/includes/header.php');
		?>
	</head>
	<body>
		<!-- Search page -->
		<div data-role="page" id="search_div" data-theme="d" data-title="RailGB">
			<div data-role="header" data-theme="a" data-position="fixed">
				<a href="#tubemap_div" data-theme="b" data-icon="arrow-r" class="ui-btn-left" data-iconpos="right" id="nav_map_a">Map</a>
				<h1></h1>
				<a href="#help_div" data-icon="info" data-theme="d" data-direction="reverse" class="ui-btn-right">Help</a>
			</div>
			<div data-role="content" id="search_form_div">
				<div style="text-align:center">
					<img src="/public/img/railgb_36.png"/><h2 style="display:inline;font-size:2.4em">RailGB</h2>
				</div>
				<h3>Search Accessible Tube Stations</h3>
						  <form action="/public/ajax/tube.php" method="get" data-ajax="false" id="search_form">
							  <input type="search" data-mini="true" name="address" id="address" value="" data-mini=“true” placeholder="Postcode or Address in London"/>
							  <input type="hidden" name="addName" id="addName" value=""/>
							  <div class="ui-grid-a">
							  		<div class="ui-block-a">
								  		<input type="button" data-mini="true" value="Current Location" data-theme="e" id="current_location_btn"/></div>
								  	<div class="ui-block-b"><a href="#places_div" data-mini="true" data-icon="arrow-r" data-iconpos="right" data-theme="e" id="quick_places_a" data-role="button" data-transition="slideup">Quick Places</a></div>
							  </div>
					          <select data-mini="true" id="radius" name="radius" data-mini=“true” id="search_radius_select">
											<option value="0.25">0.25 miles radius</option>
											<option value="0.5">0.5 miles radius</option>
											<option value="1.0" selected="selected">1 mile radius</option>
											<option value="2.0">2 miles radius</option>
											<option value="5.0">5 miles radius</option>
											<option value="10.0">10 miles radius</option>
							  </select>
							  <div data-role="collapsible" data-content-theme="d" data-collapsed="false">
							  		<h4>Selected Facilities</h4>
							  		<div id="search_filter_div">
							  			<p>No facility is selected</p>
							  		</div>
							  		<ul id="search_filter_ul" data-role="listview" style="display:none">
							  		</ul>
							  		<br/>
							  		<div id="filter_config_div">
								  		<a data-role="button" data-transition="slideup" href="#filter_div" data-theme="b" data-icon="gear" data-mini="true" data-iconpos="right">Config Filter</a>
							  		</div>
							  		
							  </div>
							  <a href="#tubemap_div" data-role="button" data-mini="true" data-theme="b" id="search_a">Search</a>
						  </form>
			</div>
		</div>
		<!-- /Search page -->
		
		<!-- map page-->
		<div data-role="page" id="tubemap_div" data-theme="d" data-title="Tube Map" style="width:100%;height:100%;">
			<div data-role="header" data-theme="a" data-position="fixed">
				<a href="#search_div" data-icon="search" data-theme="b" data-direction="reverse" class="ui-btn-left">Search</a>
				<h1>Map View</h1>
				<a href="#help_div" data-icon="info" data-theme="d" data-direction="reverse" class="ui-btn-right">Help</a>
			</div>
			<div data-role="content" data-theme="d" style="width:100%;height:100%;padding:0">
				<div id="map-canvas" style="width:100%;height:100%;"></div>
			</div>
			<!-- Search result info form -->
			<div data-role="popup" id="search_info_div" class="ui-content" data-theme="e" style="max-width:200px;">
				<p id="search_info_p"></p>
	        </div>
			<!-- /Search result info form -->
			<div data-role="footer" data-theme="d" data-tap-toggle="false" data-position="fixed" id="map_footer_div">
				<div data-role="navbar">
					<ul>
						<li><a class="ui-btn-active ui-state-persist" data-transition="flip" href="#tubemap_div" data-role="button" data-theme="d" id="tubemap_btn" data-mini="true">Map View</a></li>
					<li><a href="#tubelist_div" data-role="button" data-transition="flip" data-theme="d" id="list_btn" data-mini="true">List View</a></li>
					</ul>
				</div>
			</div>	
		</div>
		<!-- /map page -->
		
		<!-- filter page -->
		<div data-role="page" id="filter_div" data-title="Fitler">
			<div data-role="header" data-theme="a">
				<input type="reset" id="reset_btn" data-theme="d" class="ui-btn-left" data-icon="refresh" value="Reset"/>
				<h1>Filter</h1>
				<a data-theme="b" data-rel="back" data-direction="reverse" class="ui-btn-right" data-icon="check">Save</a>
				<!-- TODO: use cookie to remember user's choices on this phone -->
			</div>
			<div data-role="content">
			    <form id="filter_form">
				<div data-role="fieldcontain" class="disabilities">
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
				<div data-role="fieldcontain" class="facilities">
				 	<fieldset data-role="controlgroup">
				 		<legend>Accessibility:</legend>
				 		<label><input type="checkbox" name="facility[]" id="filter-sf" value="sf" title="Step-free"/> Step-free </label>
						<label><input type="checkbox" name="facility[]" id="filter-lifts" value="lifts" title="Lifts"/> Lifts </label>
						<label><input type="checkbox" name="facility[]" id="filter-th" value="th" title="Ticket Hall"/> Tickets Hall </label>
						<label><input type="checkbox" name="facility[]" id="filter-es" value="es" title="Escalators"/> Escalators </label>
						<label><input type="checkbox" name="facility[]" id="filter-to" value="to" title="Toilets"/> Toilets </label>
						<label><input type="checkbox" name="facility[]" id="filter-cp" value="cp" title="Car Park"/> Car Park </label>
						<label><input type="checkbox" name="facility[]" id="filter-hpth" value="hpth" title="Help Points in Ticket Halls"/> Help Points in Ticket Halls</label>
						<label><input type="checkbox" name="facility[]" id="filter-hppf" value="hppf" title="Help Points on Platforms"/> Help Points on platForms</label>
						<label><input type="checkbox" name="facility[]" id="filter-wr" value="wr" title="Waiting Room"/> Waiting Room </label>
				 	</fieldset>
				</div>
				<div data-role="collapsible" data-theme="e" data-content-theme="d">
				   <h3>Other Facilities</h3>
				   <p>
				   <div data-role="fieldcontain" class="facilities">
					 	<fieldset data-role="controlgroup">
							<label><input type="checkbox" name="facility[]" id="filter-gates" value="gates" title="Gates"/> Gates </label>
							<label><input type="checkbox" name="facility[]" id="filter-pb" value="pb" title="Photo Booths"/> Photo Booths </label>
							<label><input type="checkbox" name="facility[]" id="filter-cm" value="cm" title="Cash Machine"/> Cash Machine </label>
							<label><input type="checkbox" name="facility[]" id="filter-pp" value="pp" title="Payphones"/> Payphones </label>
							<label><input type="checkbox" name="facility[]" id="filter-vm" value="vm" title="Vending"/> Vending Machines </label>
							<label><input type="checkbox" name="facility[]" id="filter-br" value="br" title="Bridge"/> Bridge </label>
					 	</fieldset>
					</div>
				   </p>	
				</div>
			    </form>	
			</div>
		</div>
		<!-- /filter page -->
		
		<!-- tubelist page -->
		<div data-role="page" id="tubelist_div" data-title="Tube List">
			<div data-role="header" data-theme="a" data-position="fixed">
				<a href="#search_div" data-icon="search" data-theme="b" data-direction="reverse" class="ui-btn-left">Search</a>
				<h1>List View</h1>
				<a href="#help_div" data-icon="info" data-theme="d" data-direction="reverse" class="ui-btn-right">Help</a>
			</div>
			<div data-role="content">
				<ul id="tubelist_ul" data-role="listview" data-filter="true" data-filter-placeholder="Filter stations...">
				</ul>
			</div>
			<div id="tubelist_footer_div" data-role="footer" data-theme="d" data-position="fixed" data-tap-toggle="false">			
				<div data-role="navbar">
					<ul>
						<li><a data-transition="flip" href="#tubemap_div" data-role="button" data-theme="d" id="tubemap_btn" data-mini="true">Map View</a></li>
					<li><a class="ui-btn-active ui-state-persist" href="#tubelist_div" data-role="button" data-transition="flip" data-theme="d" id="list_btn" data-mini="true">List View</a></li>
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
		</div>
		<!-- /detail page -->
		
		<!-- Places page -->
		<div data-role="page" id="places_div" data-title="Quick Places">
			<div data-role="header" data-theme="a">
				<h1>Quick Places</h1>
				<a href="#tubemap_div" data-theme="d" data-rel="back" data-direction="reverse" class="ui-btn-left" data-icon="arrow-l">Back</a>			
			</div>
			<div data-role="content">
				<ul id="placelist_ul" data-autodividers="true" data-role="listview" data-filter="true" data-filter-placeholder="Filter Places..."></ul>
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
				<div data-role="controlgroup" data-type="horizontal" data-mini="true" class="help-nav">
					<a href="#help_div" data-role="button" data-transition="fade" class="ui-btn-active">Help</a>
					<a href="#about_div" data-role="button" data-transition="fade">About</a>
				</div>
				<h2>Help</h2>
				<ul id="ul_help_list">
					<li><a href="#help_search" data-ajax="false">Search</a></li>
					<li><a href="#help_detail" data-ajax="false">View Station Detail</a></li>
					<li><a href="#help_filter" data-ajax="false">Filter</a></li>
					<li><a href="#help_places" data-ajax="false">Quick Places</a></li>
				</ul>
				<div>
					<div id="help_search">
						<h3>Search</h3>
						<p>The front-page of RailGB is the search page. In the search box, you can fill the place's name, postcode or use your current location. You can also change the search radius. <a href="#help_places" data-ajax="false">Quick Places</a> can help you to quickly use places of interest and <a href="#help_filter" data-ajax="false">Config Filter</a> will let you select the preferences of facilities.</p>
						<p><a href="#ul_help_list" data-ajax="false">Back to Top</a></p>
						<p>After search, if there are any stations which meet the search requirements, the stations will be marked on the map. You can click on the marker to see the station's name. Or, you can view the results in a list by clicking the "List View" button.</p>
						<p><a href="#ul_help_list" data-ajax="false">Back to Top</a></p>
					</div>
					<div id="help_detail">
						<h3>View Station Detail</h3>
						<p>You can view the detail of a station by clicking the "Detail" button in the information box of a station on the map. Or in the stations list page, click on the station in the list.</p>
						<p><a href="#ul_help_list" data-ajax="false">Back to Top</a></p>
						<p>On the station detail page, you can check the name, address and phone of the station. RailGB also indicates if it is a "step-free" station. You can also check the "Facilities Available" and "Facilities Not Available". The "More Info" tab provides more information about this station.</p>
						<p><a href="#ul_help_list" data-ajax="false">Back to Top</a></p>
					</div>
					<div id="help_filter">
						<h3>Filter</h3>
						<p>On the filter page, you can either choose the disability, or check the facilities that you requirement in the search. If you choose the disability, we will automatically set the facility preferences for this disability. These facilities include lifts, escalators, toilets, car parks, cash machines, vending machines, etc. After selecting the preferences, you can go back to Tube Map page and search for the stations which meet your preferences. </p>
						<p><a href="#ul_help_list" data-ajax="false">Back to Top</a></p>
					</div>
					<div id="help_places">
						<h3>Quick Places</h3>
						<p>RailGB provides a list of quick places on the Places page. You can filter the places by characters and select a place, where you want to search the nearby stations.</p>
						<p><a href="#ul_help_list" data-ajax="false">Back to Top</a></p>
					</div>
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
				<div data-role="controlgroup" data-type="horizontal" data-mini="true" class="help-nav">
					<a href="#help_div" data-role="button" data-transition="fade">Help</a>
					<a href="#about_div" data-role="button" data-transition="fade" class="ui-btn-active">About</a>
				</div>
				<h2>About RailGB</h2>
				<p> RailGB as a project came out of the annual WAIS Fest 2012. The aim of the project is to identify accessible public transport systems, primarily the London Underground Network, as well as UK railway networks. </p>
				<p>Special thanks to Hugh Glaser and Ian Millard for providing the triple store in <a data-ajax="false" href="http://oad.rkbexplorer.com/" target="_blank">RKBExplorer</a>.</p>
				<p>
The development team are members of the Web and Internet Science Faculty at the University of Southampton, with considerable experience in the field of digital technologies in relation to personalisation, disability, assistive technology, e-learning, mobile and Semantic Web services.</p>
				<p>This application uses data from <a data-ajax="false" href="http://www.tfl.gov.uk/" target="_blank">Transport for London</a>. But this application is not affiliated, associated, endorsed, or in any other way connected officially with Transport for London site. </p>
			<p> &copy; 2012 ECS, University of Southampton</p>
			</div>	
		</div>
		<!-- /About page -->
	</body>
</html>