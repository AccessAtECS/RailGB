<?
$query = "PREFIX id:   <http://oad.rkbexplorer.com/id/>
PREFIX rdf:	 <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
PREFIX owl:	 <http://www.w3.org/2002/07/owl#>
PREFIX foaf: <http://xmlns.com/foaf/0.1/>
PREFIX geo: <http://www.w3.org/2003/01/geo/wgs84_pos#>
PREFIX rf: <http://ontologi.es/rail/vocab#facilities/>
PREFIX ontologi: <http://ontologi.es/rail/vocab#>
SELECT Distinct ?station ?name ?long ?lat ?ramp ?ticket ?staffing ?code
WHERE 
{ 
  ?station foaf:name ?name.
  ?station geo:lat ?lat.
  ?station geo:long ?long.
  ?station ontologi:crs ?code.
  OPTIONAL 
  {
	?station ?p1 ?t.
	?t rf:availability ?ticket.
	?t rdfs:label \"Ticket Office\"@en.
  }
  OPTIONAL
  {
	?station ?p2 ?s.
	?s rf:availability ?staffing.
	?s rdfs:label \"Staffing\"@en.
  }
  OPTIONAL
  {
	?station ?p3 ?ramp.
	?ramp rdfs:label \"Ramp for Train Access\"@en.
  }
}";

$contents = file_get_contents("http://oad.rkbexplorer.com/sparql/?format=json&query=".urlencode(str_replace("\n", " ", $query)));
$contents = json_decode($contents);
echo json_encode($contents);