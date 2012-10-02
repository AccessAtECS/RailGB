<?php
$query = 'PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#> 
PREFIX xsd: <http://www.w3.org/2001/XMLSchema#> 
PREFIX owl: <http://www.w3.org/2002/07/owl#> 
PREFIX foaf:<http://xmlns.com/foaf/0.1/> 
PREFIX geo:<http://www.w3.org/2003/01/geo/wgs84_pos#> 
PREFIX dbpprop:<http://dbpedia.org/property/> 
PREFIX tf: <http://www.railgb.org.uk/ns/2012/9/tubefacility.owl#> 

SELECT distinct ?name ?address ?lat ?lng ?zone ?sf
WHERE{
?station rdf:type
	tf:TubeStation. 
?station rdfs:label
	?name. 
?station tf:hasAddress
	?address. 
?station geo:lat ?lat. 
?station geo:long ?lng. 
?station dbpprop:fareZone
	?zone. 
?station
	tf:isStepFreeStation ?sf.

?station tf:hasEscalators
	?es. ?es tf:facilityAvailable ?esa FILTER(?esa =
	"true"^^xsd:boolean) FILTER(?sf = "true"^^xsd:boolean)
}';

$contents = file_get_contents("http://oad.rkbexplorer.com/sparql/?format=json&query=".urlencode(str_replace("\n", " ", $query)));
$contents = json_decode($contents);
echo json_encode($contents);