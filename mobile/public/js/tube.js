//console.log("load tube.js");

var map = null;
var newSearch = false;
var circle = null;
var currentMarker = null; //the center of current marker
var cachedData = null;
var markers = new Array(); //all the markers of tube stations
var stationsDisplayed = new Array();
var filterArray = new Array(); //The array to remember the filter selections
var radius = 1600.0; //1 miles
var initialLatLong = new google.maps.LatLng(51.5077475, -0.08776190000003226);

var currentStationURI = null;

var rs = null;

if (typeof(Number.prototype.toRad) === "undefined") {
		Number.prototype.toRad = function() {
		return this * Math.PI / 180;
	}
}

function sortStationsDisplay(prop, asc) {
    stationsDisplayed = stationsDisplayed.sort(function(a, b) {
        if (asc) return (a[prop] > b[prop]);
        else return (b[prop] > a[prop]);
    });
}

//This function clears the marker array
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

//station here is the marker
function getDistance(station)
{
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

	distance = R * c *1000; // Distance in Meters
	return distance;

}

function initialize() {
	//console.log("initialize");
	var mapDiv = document.getElementById('map-canvas');
	map = new google.maps.Map(mapDiv, {
		center: initialLatLong,
		zoom:14,
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		mapTypeControl: true,
	    mapTypeControlOptions: {
	      style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
	    },
	    navigationControl: true,
	    navigationControlOptions: {
	      style: google.maps.NavigationControlStyle.SMALL
	    },
	    zoomControl:true,
	    zoomControlOptions:{
		    style: google.maps.ZoomControlStyle.SMALL,
		    position: google.maps.ControlPosition.TOP_LEFT
	    },
	    streetViewControl: false
	});
}
		
function fireUpStations(stations) {
	clearOverylays();
	stationsDisplayed = new Array();
	var count = 0;
	
	$.each(stations.results.bindings, function(i, station) {

			var image = new google.maps.MarkerImage("/public/img/station.png", null, null, null, new google.maps.Size(82,49));

			var lng = parseFloat(station.lat.value);
			var lat = parseFloat(station.lng.value);
			var marker;
			
			marker = new google.maps.Marker({
					position: new google.maps.LatLng(lat,lng,true),
					map: map,
					icon:image,
					uri: station.station.value,
					fareZone:station.zone.value,
					title: station.name.value,
					draggable: false,
					visible: true,
					optimized: false
			});
			
			if(currentMarker != null)
			{
				var distance = getDistance(marker);
				marker.distance = distance;
				if(distance <= radius)
				{
				 	marker.setVisible(true);
				 	stationsDisplayed.push(marker);
				
					google.maps.event.addListener(marker, 'click', function(){
						 
						 var boxText = $("<div/>").addClass("infobox ui-corner-all").text(marker.title);
						 var detail_a = $("<a/>").attr("href","#detail_div").attr("data-mini","true").attr("data-role","button").attr("data-icon","arrow-r").attr("data-iconpos","right").attr("data-theme","e").text("details").button();
						 detail_a.bind('click',{uri:marker.uri},function(event){
							 currentStationURI = event.data.uri;
						 });
						 boxText.append(detail_a);
						 var ibOptions = {
							content: boxText.get(0),
			                disableAutoPan: false,
			                maxWidth: 280,
			                pixelOffset: new google.maps.Size(-140, 0),
			                zIndex: null,
			                boxStyle:{
				                background: "url('http://google-maps-utility-library-v3.googlecode.com/svn/trunk/infobox/examples/tipbox.gif') no-repeat",
				                opacity: 0.75,
				                width: "280px"
			                },
			                closeBoxMargin: "10px 20px 20px 20px",
			                closeBoxURL: "http://www.google.com/intl/en_us/mapfiles/close.gif",
			                infoBoxClearance: new google.maps.Size(1, 1),
			                isHidden: false,
			             	enableEventPropagation: false
						}
			
						var ib= new InfoBox(ibOptions);
						ib.open(map, marker);
					});
				}
				else
				{
				    marker.setVisible(false);	
				}
			}
			markers.push(marker);
			count++;
		});
		
		//console.log("marker size:"+markers.length);
		//sort the stations by distance
		if(stationsDisplayed.length >0)
		{
			sortStationsDisplay('distance',true);
		}
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
function displayStations(address, addName, callback)
{				
	radius = parseFloat($("#radius").val())*1600;
	if(address.match(/-?\d+\.\d+/g) == null && address.toLowerCase().indexOf("london") == -1)
	{
		address += ", London";
	}
	
	// Image for each pin
	var image = new google.maps.MarkerImage("/public/img/poi-blue.png", null, null, null, new google.maps.Size(82,49));
	
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
				//draw a circle
				if (circle != null) {
				    circle.setVisible(false);
					circle.setMap(null);
				}
				
				if(currentMarker != null) currentMarker.setMap(null);
				
				currentMarker = new google.maps.Marker({
						map: map,
						position: latlng,
						draggable: false,
						icon: image,
						optimized: false
					});
				if(addName !="")
				{
					var infowindow = new google.maps.InfoWindow({
						content: addName
					});
					
					google.maps.event.addListener(currentMarker, 'click', function() {
						infowindow.open(map, currentMarker);
					});
					
					infowindow.open(map,currentMarker);
				}
				circle = new google.maps.Circle({
					radius:radius,
					strokeColor: "#FF0000",
					strokeOpacity: 0.8,
					strokeWeight: 2,
					fillColor: "#FF0000",
					fillOpacity: 0.35,
					map: map,
				});
				
				circle.bindTo('center', currentMarker, 'position');
				
				map.setCenter(currentMarker.getPosition());
				
				var selected = new Array()
				$("#filter_div :checkbox:checked").each(function(){
					selected.push($(this).val());
				});
				if(($(selected).not(filterArray).length == 0 && $(filterArray).not(selected).length == 0) && cachedData != null)
				{
					//use old data
					fireUpStations(cachedData);
					
					if(stationsDisplayed.length >0)
					{
						
						var latlngbounds = new google.maps.LatLngBounds( );
						latlngbounds.extend(currentMarker.getPosition());
						for ( var i = 0; i < stationsDisplayed.length; i++ ) {
								latlngbounds.extend( stationsDisplayed[ i ].getPosition() );
						}
						//map.setCenter(currentMarker.getPosition());
						map.fitBounds( latlngbounds );
						//var zoomLevel = getPropertyInfo(radius);
						//map.setZoom(zoomLevel);
					}
					
					showStationsCount();
					callback(null, cachedData);
					return;
				}
				else
				{
					//send ajax again
					filterArray = selected;
					$.ajax({
						dataType:"json",
						url: "/public/ajax/tube.php",
						data:{facility:selected},
						beforeSend:function(jqXHR, settings){
							$.mobile.loading( 'show', {
								text: 'Loading...',
								textVisible: true,
								theme: 'd',
								html: ""
							});
						},
						success:function(data)
						{
							
							cachedData = data;
							fireUpStations(data);
							
							if(stationsDisplayed.length >0)
							{
								
								var latlngbounds = new google.maps.LatLngBounds( );
								latlngbounds.extend(currentMarker.getPosition());
								for ( var i = 0; i < stationsDisplayed.length; i++ ) {
										latlngbounds.extend( stationsDisplayed[ i ].getPosition() );
								}
								//map.setCenter(currentMarker.getPosition());
								map.fitBounds( latlngbounds );
								//var zoomLevel = getPropertyInfo(radius);
								//map.setZoom(zoomLevel);
							}
							
							showStationsCount();
							callback(null, data);
							return;
						},
						complete:function(jqXHR, textStatus)
						{
							$.mobile.loading( 'hide', {
								text: 'Loading...',
								textVisible: true,
								theme: 'd',
								html: ""
							});
						}
					});
				}
			}
		);
		
	}
	else
	{
		alert("Please input an address or postcode");
	}
	return false;
}

function getFaclityAndQuantity(item)
{
	var p = item.p.value;
	var name = null;
	var quantity = 0;
	if(p.indexOf("hasLifts") != -1)
	{
		name = "Lifts";
		quantity = parseInt(item.q.value);
	}
	else if(p.indexOf("hasWaitingRoom") != -1)
	{
		name = "Waiting Room";
		quantity = parseInt(item.q.value);
	}
	else if(p.indexOf("hasTicketHalls") != -1)
	{
		name = "Ticket Halls";
		quantity = parseInt(item.q.value);
	}
	else if(p.indexOf("hasEscalators") != -1)
	{
		name = "Escalators";
		quantity = parseInt(item.q.value);
	}
	else if(p.indexOf("hasGates") != -1)
	{
		name = "Gates";
		quantity = parseInt(item.q.value);
	}
	else if(p.indexOf("hasToilets") != -1)
	{
		name = "Toilets";
		quantity = parseInt(item.q.value);
	}
	else if(p.indexOf("hasPhotoBooths") != -1)
	{
		name = "Photo Booths";
		quantity = parseInt(item.q.value);
	}
	else if(p.indexOf("hasCashMachines") != -1)
	{
		name = "Cash Machines";
		quantity = parseInt(item.q.value);
	}
	else if(p.indexOf("hasPayphones") != -1)
	{
		name = "Payphones";
		quantity = parseInt(item.q.value);
	}
	else if(p.indexOf("hasCarPark") != -1)
	{
		name = "Car Park";
		quantity = parseInt(item.q.value);
	}
	else if(p.indexOf("hasVendingMachines") != -1)
	{
		name = "Vending Machines";
		quantity = parseInt(item.q.value);
	}
	else if(p.indexOf("hasHelpPointsInTicketHalls") != -1)
	{
		name = "Help Points in Ticket Halls";
		quantity = parseInt(item.q.value);
	}
	else if(p.indexOf("hasHelpPointsOnPlatforms") != -1)
	{
		name = "Help Points on Platforms";
		quantity = parseInt(item.q.value);
	}
	else if(p.indexOf("hasBridge") != -1)
	{
		name = "Bridge";
		quantity = parseInt(item.q.value);
	}
	//else if(p.indexOf("hasWiFi") != -1)
	//{
	//	name = "WiFi";
	//	quantity = parseInt(item.q.value);
	//}
	
	if(name != null)
	{
		var result = new Object();
		result.name = name;
		result.quantity = quantity;
		return result;
	}
	else
		return null;
}

function getPropertyInfo(item)
{
	if(item.p == undefined)
		return;
	
	var p = item.p.value;
	var name = null;
	var o = "";
	if(p.indexOf("label") != -1)
	{
		$("#station_h2").text(item.o.value);
		$("#station_h4").text(item.o.value);
		$("#station_thumbnail").attr("alt",item.o.value+" thumbnail");
		$("#station_depiction").attr("alt",item.o.value+" depiction");
	}
	else if(p.indexOf("hasAddress") != -1)
	{
		$("#address_p").html("<strong>Address:</strong>"+item.o.value);
	}
	else if(p.indexOf("hasPhone") != -1)
	{
		$("#phone_p").html("<strong>Phone:</strong>"+item.o.value);
	}
	else if(p.indexOf("fareZone") != -1)
	{
		$("#zone_p").html("<strong>Zone "+item.o.value+"</strong>");
	}
	else if(p.indexOf("isStepFreeStation") != -1)
	{
		if(item.o.value == "false")
		{
			$("#sf_p").text("Not step-free");
		}
		else
		{
			$("#sf_p").text("Step-free");
		}
	}
}

function afterSearch(err,data)
{
	var resultStr = "";
	var radiusText = $("#search_form option:selected").text();
	var checkedFacilitiesCount = $(".facilities :checked").length;
	resultStr += "<p>"+radiusText+", "+checkedFacilitiesCount+" facilities selected.</p>"
	if(stationsDisplayed.length >0)
	{
		resultStr += "<p><strong>"+stationsDisplayed.length+" stations found. </strong.</p>";
	}
	else
	{
		resultStr += "<p>No station found. </p>";
	}
	$("#search_info_p").html(resultStr);
	
	//TODO: let it position to tubelist_div, it doens't work
	window.setTimeout(function(){
		$("#search_info_div").popup("open",{
			positionTo:"#tubelist_div",
			transition:'fade'
		});
		window.setTimeout(function(){
			$("#search_info_div").popup("close");
		}, 2000)
	}, 1000);
	
	newSearch = false;
}

function handleNoGeolocation()
{
	$("#search_info_p").html("Error: The geo-location service failed.");
	
	//TODO: let it position to tubelist_div, it doens't work
	window.setTimeout(function(){
		$("#search_info_div").popup("open",{
			positionTo:"#tubelist_div",
			transition:'fade'
		});
		window.setTimeout(function(){
			$("#search_info_div").popup("close");
		}, 2000)
	}, 1000);
}
//####################Define some extra functions####################
jQuery.fn.sortElements = (function(){
 
    var sort = [].sort;
 
    return function(comparator, getSortable) {
 
        getSortable = getSortable || function(){return this;};
 
        var placements = this.map(function(){
 
            var sortElement = getSortable.call(this),
                parentNode = sortElement.parentNode,
 
                // Since the element itself will change position, we have
                // to have some way of storing its original position in
                // the DOM. The easiest way is to have a 'flag' node:
                nextSibling = parentNode.insertBefore(
                    document.createTextNode(''),
                    sortElement.nextSibling
                );
 
            return function() {
 
                if (parentNode === this) {
                    throw new Error(
                        "You can’t sort elements if any one is a descendant of another."
                    );
                }
 
                // Insert before flag:
                parentNode.insertBefore(this, nextSibling);
                // Remove flag:
                parentNode.removeChild(nextSibling);
 
            };
 
        });
 
        return sort.call(this, comparator).each(function(i){
            placements[i].call(getSortable.call(this));
        });
 
    };
 
})();
//####################JQuery Mobile Methods##########################
$('#search_div').live('pageinit',function(event){
	$("#current_location_btn").click(function(){
		
      // Try HTML5 geolocation
      if(navigator.geolocation) 
      {
          navigator.geolocation.getCurrentPosition(function(position) {
          	$("#address").val(position.coords.latitude+","+position.coords.longitude);
          }, 
          function() {
           	handleNoGeolocation(); 
          });
        } 
        else 
        {
          // Browser doesn't support Geolocation
          handleNoGeolocation();
        }
    });
    
    $("#search_a").click(function(){
	    newSearch = true;
    })
});

$('#search_div').live('pageshow',function(event){
	var checkedFacilities = $(".facilities :checked");
	var search_filter_div =$("#search_filter_div");
	var search_filter_ul = $("#search_filter_ul");

	if(checkedFacilities.length >0)
	{
		search_filter_ul.empty();
		$.each(checkedFacilities,function(i,facility){
			var facility_text = $(facility).prop("title");
			var facility_li = $("<li/>");
			facility_li.text(facility_text);
			facility_li.appendTo(search_filter_ul);
		});
		search_filter_ul.listview('refresh');
		search_filter_ul.show();
		search_filter_div.hide();
	}
	else
	{
		search_filter_ul.hide();
		search_filter_div.show();
	}
});

$("#search_form").live('submit',function(e){
	//cache the form element for use in this function
	//console.log("submit");
    var $this = $(this);

    //prevent the default submission of the form
    e.preventDefault();

    //run an AJAX post request to your server-side script, $this.serialize() is the data from your form being added to the request
    var address = $("#address").val();
    var addName = $("#addName").val();
    displayStations(address,addName,afterSearch);
});

//$('#tubemap_div').live('pageinit',function(event){
	
	//console.log("tubemap init");
	//google.maps.event.addDomListener(window, 'load', initialize);
	//initialize();
//});
					
$('#tubemap_div').live('pageshow', function(event) {
	if(map == null)
	{
		//console.log("map null");
		initialize();
		
		//if($("#address").val() != "")
		//{
		//	displayStations($("#address").val(),$("#addName").val(), afterSearch);
		//}	
	}
	
	if(newSearch == true)
	{
		$("#search_form").submit();
	}
});

$('#filter_div').live('pageinit',function(event){
	$('#filter_div #wheelchair').change(function() {
		//console.log("change");
        if($(this).is(":checked")) {
            $("#filter-sf").prop("checked",true).checkboxradio('refresh');
        }
        else
        {
	        //console.log("change2");
	        $("#filter-sf").prop("checked",false).checkboxradio('refresh');
        }      
    });	
    
    $('#filter_div #blind').change(function() {
        if($(this).is(":checked")) {
            $("#filter-hpth").prop("checked",true).checkboxradio('refresh');
            //$("#filter-hppf").prop("checked",true).checkboxradio('refresh');
        }
        else
        {
	        $("#filter-hpth").prop("checked",false).checkboxradio('refresh');
            //$("#filter-hppf").prop("checked",false).checkboxradio('refresh');
        }       
    });
    
    $('#filter_div #hearing').change(function() {
        if($(this).is(":checked")) {
            $("#filter-hpth").prop("checked",true).checkboxradio('refresh');
        }
        else
        {
	        $("#filter-hpth").prop("checked",false).checkboxradio('refresh');
        }       
    });
    
    $('#filter_div #mobility').change(function() {
        if($(this).is(":checked")) {
            $("#filter-sf").prop("checked",true).checkboxradio('refresh');
        }
        else
        {
	        $("#filter-sf").prop("checked",false).checkboxradio('refresh');
        }       
    });
    
    $('#reset_btn').click(function(){
		$("#filter_form :checked").prop("checked",false).checkboxradio('refresh');
	});
});

$('#tubelist_div').live('pageshow', function(event) {
	var tubelist_ul = $("#tubelist_ul");
	tubelist_ul.empty();
	if(stationsDisplayed.length >0)
	{
		var search_count_li = $("<li/>").attr("data-role","list-divider");
		search_count_li.html('<span id="search_count_span">'+stationsDisplayed.length+'</span><span> stations have been found.</span>')
		tubelist_ul.append(search_count_li);
		$.each(stationsDisplayed, function(i, station) {
			var distance = station.distance;
			var station_li = $("<li/>").appendTo(tubelist_ul);
			var station_a = $("<a/>",{
				href:"#detail_div",
				html:"<h3>"+station.title+"</h3>"+
					"<p><strong>Zone "+station.fareZone+"</strong></p>"+
					"<p class='ui-li-aside'><strong>"+Math.floor(distance)+" m</strong></p>"
			}).appendTo(station_li);
			station_a.bind('click',{uri:station.uri},function(event){
				currentStationURI = event.data.uri;
			});
		});
		
		tubelist_ul.listview('refresh');
	}
	else
	{
		tubelist_ul.html("<b>No station is found</b>");
	}				
});

$('#places_div').live('pageinit',function(event){
    //console.log("places page init......");
    $("#placelist_ul").listview({
		autodividers:true,
		autodividersSelector:function(li){
			var out = li.find('a').text().substring(0,1);
			//console.log("out:"+out);
			return out;
		}
	});
	$.ajax({
		dataType:"json",
		url: "/public/ajax/osseme4.php",
		beforeSend:function(jqXHR, settings){
			$.mobile.loading( 'show', {
				text: 'Loading...',
				textVisible: true,
				theme: 'd',
				html: ""
			});

		},
		success:function(data)
		{
			if(data != null && data.results.bindings.length > 0)
			{
				//List the places on the page
				//<li><a href='#tubemap_div' onclick='$("#address").val("SW1W 0DH");$("#search_form").submit();'>Dyslexia Action</a></li>
				var places_ul = $("#placelist_ul");
				$.each(data.results.bindings, function(i,place){
					var place_li = $("<li/>");
					place_li.appendTo(places_ul);
					var place_a=$("<a/>").attr("href","#search_div").text(place.label.value);
					place_a.bind('click',{address:place.pclabel.value},function(event){
						$("#address").val(event.data.address);
						//$("#search_form").submit();
					});
					place_a.appendTo(place_li);
				});
				
				$('#placelist_ul li').sortElements(function(a, b){
				    return $(a).find('a').text() > $(b).find('a').text() ? 1 : -1;
				});
				
				$("#placelist_ul").listview('refresh');
			}
			else
			{
				//Connection failed
				$("#placelist_ul").html("<b>Connection failed. Cannot find any places</b>");
			}
		},
		complete:function(jqXHR, textStatus)
		{
			$.mobile.loading( 'hide', {
				text: 'Loading...',
				textVisible: true,
				theme: 'd',
				html: ""
			});
		}
	});	
});

$('#detail_div').live('pageshow',function(event){
	if(currentStationURI !=null)
	{
		$.ajax({
			dataType:"json",
			url: "/public/ajax/detail.php",
			data:{stationURI:currentStationURI},
			beforeSend:function(jqXHR, settings){
				$.mobile.loading( 'show', {
					text: 'Loading...',
					textVisible: true,
					theme: 'd',
					html: ""
				});
    
			},
			success:function(data)
			{
				if(data.results.bindings.length > 0)
				{
					var facilityHasStr = "";
					var facilityHasnotStr = "";
					var stationInfoStr = "";
					$.each(data.results.bindings,function(i,item){
						var result = getFaclityAndQuantity(item);
						if(result != null)
						{
							var name = result.name;
							var quantity = result.quantity;
							//console.log("name:"+name);
							//console.log("quantity:"+quantity);
							if(quantity > 0)
							{
								facilityHasStr +="<li>"+quantity+" "+ name+"</li>";
							}
							else
							{
								facilityHasnotStr +="<li>"+name+"</li>";
							}
						}
						else if(item.p.value.indexOf("sameAs") != -1)//sameAS
						{
							var dbpediaURI = item.o.value;
							//console.log(dbpediaURI);
							//query dbpedia to get the thumbnail picture
							$.ajax({
								dataType:"json",
								url: "/public/ajax/dbpedia.php",
								data:{dbpediaURI:dbpediaURI},
								success:function(data){
								
									if(data.results.bindings[0] != undefined)
									{
										var dbresult = data.results.bindings[0];
										var moreStr = ""
										moreStr += "<p>"+dbresult.abstract.value+"</p>";
																				
										if(dbresult.thumbnail.value != undefined)
										{
											$("#station_thumbnail").prop("src",data.results.bindings[0].thumbnail.value);
											$("#station_thumbnail").click(function(){
												$("#station_depiction_popup").popup("open");
											});
											
											$("#station_depiction").prop("src",data.results.bindings[0].depiction.value);
										}
										
										$("#station_more_content").html(moreStr);
										var wiki_ext_a = $("<a/>",{
											href: dbresult.pt.value,
											target:"_blank",
											text: "More info from Wikipedia"
										});
										wiki_ext_a.prop("data-role","button");
										wiki_ext_a.attr("data-icon","info");
										wiki_ext_a.button();
										wiki_ext_a.appendTo($("#station_more_content"));
									}
								}
							});
						}						
						else
						{
							getPropertyInfo(item);
						}
					});
					
					$("#station_facility_content_ul_yes").html(facilityHasStr);
					$("#station_facility_content_ul_yes").listview('refresh');
					$("#station_facility_content_ul_no").html(facilityHasnotStr);
					$("#station_facility_content_ul_no").listview('refresh');
				}
				else
				{
					$("#station_info_content").text("No information is found");
					$("#station_facility_content").html("No information is found");
					$("#station_more_content").text("No information is found");
				}
			},
			complete:function(jqXHR, textStatus)
			{
				$.mobile.loading( 'hide', {
					text: 'Loading...',
					textVisible: true,
					theme: 'd',
					html: ""
				});
			}
		});
	}
});

$('#event_div').live('pageinit',function(event){
	
	$.ajax({
		dataType:"json",
		url: "/public/ajax/eventmedia.php",
		beforeSend:function(jqXHR, settings){
			$.mobile.loading( 'show', {
				text: 'Loading...',
				textVisible: true,
				theme: 'd',
				html: ""
			});
		},
		success:function(data)
		{
			if(data != null && data.results.bindings.length > 0)
			{
				var event_ul = $("#eventlist_ul");
				$.each(data.results.bindings, function(i,ev){
					var event_li = $("<li/>");
					event_li.appendTo(event_ul);
					var event_a=$("<a/>").attr("href","#search_div").text(ev.title.value);
					event_a.bind('click',{lat:ev.lat.value,lng:ev.lng.value,placeName:ev.placeName.value, title:ev.title.value},function(event){
						$("#address").val(event.data.placeName+ ", London, UK");//+event.data.lat+","+event.data.lng);
						$("#addName").val("Event "+event.data.title+" at: "+event.data.placeName);
						//$("#search_form").submit();
					});
					event_a.appendTo(event_li);
				});
				$("#eventlist_ul").listview('refresh');
			}
			else
			{
				//Connection failed
				$("#eventlist_ul").html("<b>Connection failed. Cannot find any events</b>");
			}
		},
		complete:function(jqXHR, textStatus)
		{
			$.mobile.loading( 'hide', {
					text: 'Loading...',
					textVisible: true,
					theme: 'd',
					html: ""
				});
		}
	});	
});

