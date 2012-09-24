<?php
$query = "PREFIX id:   <http://oad.rkbexplorer.com/id/>
PREFIX rdf:  <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
PREFIX owl:  <http://www.w3.org/2002/07/owl#>
PREFIX foaf: <http://xmlns.com/foaf/0.1/>
PREFIX geo: <http://www.w3.org/2003/01/geo/wgs84_pos#>
PREFIX dbpedia-owl: <http://dbpedia.org/ontology/>
PREFIX dbpprop: <http://dbpedia.org/property/>
PREFIX dbpedia: <http://dbpedia.org/resource/>
PREFIX fb: <http://rdf.freebase.com/ns/>

SELECT Distinct ?station ?name ?long ?lat ?hasLift
{
    ?station rdf:type dbpedia-owl:Station.
    ?station dbpprop:manager dbpedia:London_Underground.
    ?station owl:sameAs ?freebase.
    ?station rdfs:label ?name.
    ?station <http://rdf.freebase.com/ns/user.sterops.accessibility.wheelchair_accessible_location.elevator> ?hasLift.
    ?freebase fb:location.location.geolocation ?location.
    ?location fb:location.geocode.longitude ?long.
    ?location fb:location.geocode.latitude ?lat.
    FILTER ( lang(?name) = 'en' )
}";

$contents = file_get_contents("http://oad.rkbexplorer.com/sparql/?format=json&query=".urlencode(str_replace("\n", " ", $query)));
$contents = json_decode($contents);
echo json_encode($contents);