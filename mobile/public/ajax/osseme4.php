<?php

if(!function_exists('curl_init'))
{
	die('cURL php is not installed');
}

$folder = "cache";
$filename = "cache/osseme4.json";

// Check if the file is older than a day
if(time() - filemtime($filename) > 86400) unlink($filename);


if (file_exists($filename)){
	$contents = file_get_contents($filename);
	echo $contents;
}
else{
	$query= "PREFIX rdf:  <http://www.w3.org/1999/02/22-rdf-syntax-ns#> 
	PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#> 
	PREFIX xsd: <http://www.w3.org/2001/XMLSchema#> 
	PREFIX geo: <http://www.w3.org/2003/01/geo/wgs84_pos#> 
	PREFIX gaz: <http://data.ordnancesurvey.co.uk/ontology/50kGazetteer/> 
	PREFIX pc: <http://data.ordnancesurvey.co.uk/ontology/postcode/> 
	PREFIX os: <http://data.ordnancesurvey.co.uk/ontology/spatialrelations/>
	
	SELECT distinct ?place ?label ?pclabel WHERE {
	
	  ?place rdf:type gaz:NamedPlace ;
	             rdfs:label ?label ;
	             gaz:nearest-postcode ?postcode .
	  ?postcode  rdfs:label ?pclabel.
	  ?postcode  pc:district ?district .
	  ?district  os:within <http://data.ordnancesurvey.co.uk/id/7000000000041428>
	
	}";
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://ordnancesurvey.data.seme4.com/sparql/endpoint/?query=".urlencode(str_replace("\n", " ", $query)));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch, CURLOPT_HTTPHEADER,array("Accept:application/sparql-results+json"));
	
	$resp = curl_exec($ch);
	curl_close($ch);
	$contents = json_decode($resp);

	// Write to file	
	file_put_contents($filename, json_encode($contents));

	echo json_encode($contents);
}