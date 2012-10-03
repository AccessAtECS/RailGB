<?php

/*
	* Abbrivations:
	    a: availability
	    q: quantity
		th: TicketHalls
		es: escalators
		gates: gates
		to: toilets
		pb: photo booths
		cm: cash machines
		pp: payphones
		cp: car parks
		vm: vending machines
		hpth: help points in ticket halls
		hppf: help points in platforms
		br: bridge
		wr: waiting room
		sf: step free
		lifts: lifts
		wifi: wifi	
	*/
$thFilter = ' ?station tf:hasTicketHalls ?th. ?th tf:facilityAvailable ?tha. FILTER(?tha = "true"^^xsd:boolean) ';
$esFilter = ' ?station tf:hasEscalators ?es. ?es tf:facilityAvailable ?esa FILTER(?esa = "true"^^xsd:boolean) ';
$gatesFilter = ' ?station tf:hasGates ?gates. ?gates tf:facilityAvailable ?gatesa. FILTER(?gatesa = "true"^^xsd:boolean) ';
$toFilter = ' ?station tf:hasToilets ?to. ?to tf:facilityAvailable ?toa. FILTER(?toa = "true"^^xsd:boolean) ';
$pbFilter = ' ?station tf:hasPhotoBooths ?pb. ?pb tf:facilityAvailable ?pba. FILTER(?pba = "true"^^xsd:boolean) ';
$cmFilter = ' ?station tf:hasCashMachines ?cm. ?cm tf:facilityAvailable ?cma. FILTER(?cma = "true"^^xsd:boolean) ';
$ppFilter = ' ?station tf:hasPayphones ?pp. ?pp tf:facilityAvailable ?ppa. FILTER(?ppa = "true"^^xsd:boolean) ';
$cpFilter = ' ?station tf:hasCarPark ?cp. ?cp tf:facilityAvailable ?cpa. FILTER(?cpa = "true"^^xsd:boolean) ';
$vmFilter = ' ?station tf:hasVendingMachines ?vm. ?vm tf:facilityAvailable ?vma. FILTER(?vma = "true"^^xsd:boolean)';
$hpthFilter = ' ?station tf:hasHelpPointsInTicketHalls ?hpth. ?hpth tf:facilityAvailable ?hptha. FILTER(?hptha = "true"^^xsd:boolean) ';
$hppfFilter = ' ?station tf:hasHelpPointsOnPlatforms ?hppf. ?hppf tf:facilityAvailable ?hppfa. FILTER(?hppfa = "true"^^xsd:boolean) ';
$brFilter = ' ?station tf:hasBridge ?br. ?br tf:facilityAvailable ?bra. FILTER(?bra = "true"^^xsd:boolean) ';
$wrFilter = ' ?station tf:hasWaitingRoom ?wr. ?wr tf:facilityAvailable ?wra. FILTER(?wra = "true"^^xsd:boolean) ';
$liftsFilter = ' ?station tf:hasLifts ?lifts. ?lifts tf:facilityAvailable ?liftsa. FILTER(?liftsa = "true"^^xsd:boolean) ';
$wifiFilter = ' ?station tf:hasWiFi ?wifi. ?wifi tf:facilityAvailable ?wifia. FILTER(?wifia = "true"^^xsd:boolean) ';
$sfFilter = ' FILTER(?sf = "true"^^xsd:boolean)';

$filter = '';

if(isset($_GET['facility']))
{
	$facility = $_GET['facility'];
	//Fb::log('facility'.$facility);
	
	if(!empty($facility))
	{
		if(in_array("lifts",$facility) )
		{
			$filter .= $liftsFilter;
		}
		
		if(in_array("th",$facility) )
		{
			$filter .=  $thFilter;
		}
		
		if(in_array("es",$facility) )
		{
			$filter .=  $esFilter;
		}
		
		if(in_array("gates",$facility) )
		{
			$filter .=  $gatesFilter;
		}
		
		if(in_array("to",$facility) )
		{
			$filter .=  $toFilter;
		}
		
		if(in_array("pb",$facility) )
		{
			$filter .=  $pbFilter;
		}
		
		if(in_array("cm",$facility) )
		{
			$filter .=  $cmFilter;
		}
		
		if(in_array("pp",$facility) )
		{
			$filter .=  $ppFilter;
		}
		
		if(in_array("cp",$facility) )
		{
			$filter .=  $cpFilter;
		}
		
		if(in_array("vm",$facility) )
		{
			$filter .=  $vmFilter;
		}
		
		if(in_array("hpth",$facility) )
		{
			$filter .=  $hpthFilter;
		}
		
		if(in_array("hppf",$facility) )
		{
			$filter .=  $hppfFilter;
		}
		
		if(in_array("br",$facility) )
		{
			$filter .=  $brFilter;
		}
		
		if(in_array("wr",$facility) )
		{
			$filter .=  $wrFilter;
		}
		
		if(in_array("wifi",$facility) )
		{
			$filter .=  $wifiFilter;
		}
		
		if(in_array("sf",$facility) )
		{
			$filter .=  $sfFilter;
		}
	}
}

$queryList = "PREFIX rdf:  <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
			PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#> 
			PREFIX xsd: <http://www.w3.org/2001/XMLSchema#> 
			PREFIX owl:  <http://www.w3.org/2002/07/owl#> 
			PREFIX foaf:<http://xmlns.com/foaf/0.1/> 
			PREFIX geo:<http://www.w3.org/2003/01/geo/wgs84_pos#> 
			PREFIX dbpprop:<http://dbpedia.org/property/> 
			PREFIX tf: <http://www.railgb.org.uk/ns/2012/9/tubefacility.owl#> 
			
			select distinct ?station ?name ?address ?phone ?lat ?lng ?zone ?sf
			where{
				?station rdf:type tf:TubeStation. 
				?station rdfs:label ?name. 
				?station tf:hasAddress ?address. 
				?station tf:hasPhone ?phone. 
				?station geo:lat ?lat. 
				?station geo:long ?lng. 
				?station dbpprop:fareZone ?zone. 
				?station tf:isStepFreeStation ?sf.
				
				$filter
			}";
$contents = file_get_contents("http://oad.rkbexplorer.com/sparql/?format=json&query=".urlencode(str_replace("\n", " ", $queryList)));
$contents = json_decode($contents);
echo json_encode($contents);
?>