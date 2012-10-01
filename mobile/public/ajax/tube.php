<?php
$query = "
PREFIX id:   <http://oad.rkbexplorer.com/id/>
PREFIX rdf:  <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
prefix xsd: <http://www.w3.org/2001/XMLSchema#> 
PREFIX owl:  <http://www.w3.org/2002/07/owl#>
prefix foaf:<http://xmlns.com/foaf/0.1/> 
prefix geo:<http://www.w3.org/2003/01/geo/wgs84_pos#> 
prefix dbpprop:<http://dbpedia.org/property/> 
prefix tf: <http://www.railgb.org.uk/ns/2012/9/tubefacility.owl#> 

select distinct ?name ?address ?phone ?lat ?lng ?zone ?stepFree ?toilets
where
{
  ?station rdf:type tf:TubeStation.
  ?station rdfs:label ?name.
  ?station tf:hasAddress ?address.
  ?station tf:hasPhone ?phone.
  ?station geo:lat ?lat.
  ?station geo:long ?lng.
  ?station dbpprop:fareZone ?zone.
  
  ?station tf:isStepFreeStation ?stepFree.
  Filter(?stepFree = "false"^^xsd:boolean)
  
  ?station tf:hasToilets ?toilets.
  ?toilets tf:facilityAvailable ?toiletsBool.
  Filter(?toiletsBool = "false"^^xsd:boolean)
  
}";

$contents = file_get_contents("http://oad.rkbexplorer.com/sparql/?format=json&query=".urlencode(str_replace("\n", " ", $query)));
$contents = json_decode($contents);
echo json_encode($contents);