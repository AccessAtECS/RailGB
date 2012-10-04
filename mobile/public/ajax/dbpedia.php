<?php

if(!isset($_GET['dbpediaURI']) || empty($_GET['dbpediaURI']))
{
	exit;
}

$dbpediaURI = '<'.$_GET['dbpediaURI'].'>';

$query= "PREFIX rdf:  <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
	PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
	PREFIX xsd: <http://www.w3.org/2001/XMLSchema#> 
	PREFIX owl:  <http://www.w3.org/2002/07/owl#>        
	PREFIX foaf:<http://xmlns.com/foaf/0.1/>        
	PREFIX geo:<http://www.w3.org/2003/01/geo/wgs84_pos#>
	PREFIX dbpedia-owl: <http://dbpedia.org/ontology/>
	PREFIX dbpprop:<http://dbpedia.org/property/>           
	select distinct ?abstract ?thumbnail ?depiction ?pt
	where {
          $dbpediaURI foaf:isPrimaryTopicOf ?pt.
          { 
	        $dbpediaURI dbpedia-owl:abstract ?abstract. 
            optional{    
               $dbpediaURI dbpedia-owl:thumbnail ?thumbnail .
               $dbpediaURI foaf:depiction ?depiction .
            }
          }
          UNION
          {
                $dbpediaURI dbpedia-owl:wikiPageRedirects ?station2 .
                ?station2 dbpedia-owl:abstract ?abstract.
                optional{
                	?station2 dbpedia-owl:thumbnail ?thumbnail.
                	?station2 foaf:depiction ?depiction.
                }
          }
          FILTER(langMatches(lang(?abstract), 'EN'))

	}";
$contents = file_get_contents("http://dbpedia.org/sparql?format=json&query=".urlencode(str_replace("\n", " ", $query)));
$contents = json_decode($contents);
echo json_encode($contents);
?>