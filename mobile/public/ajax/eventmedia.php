<?php
$query = "select distinct ?_about ?title ?description ?placeName ?lat ?lng ?datetime ?numOfAttendees

      WHERE {
        {
          SELECT DISTINCT ?_about ?title ?description ?placeName ?lat ?lng (count(distinct ?attendees) as ?numOfAttendees)  where {

            ?_about a lode:Event;
                    dc:title ?title;
                    dc:description ?description;
                    lode:atPlace ?place;
                    lode:inSpace ?space;
                    lode:involved ?attendees;
                    lode:atTime ?time .
            
            ?place rdfs:label ?placeName.

            ?space geo:lat ?lat.
            filter(xsd:double(?lat) > 51.38494009999999 && xsd:double(?lat) < 51.6723432).
            ?space geo:long ?lng.
            filter(xsd:double(?lng) > -0.351468299999965 && xsd:double(?lng) < 0.14787969999997586).

            ?time time:inXSDDateTime ?datetime .
            filter(?datetime > '07/15/2012'^^xsd:dateTime)
            filter(?datetime < '01/15/2013'^^xsd:dateTime)
          }

          group by  ?_about ?title ?placeName ?lat ?lng ?description
          offset 0 limit 1000
        }

       
        optional {
          ?_about lode:atTime ?time .
          {
            ?time time:inXSDDateTime ?datetime.
          }
          union
          {
            ?time time:hasBeginning ?node .
            ?node  time:inXSDDateTime ?datetime
          }
        }
      }
      order by desc(?datetime)";

$contents = file_get_contents("http://eventmedia.eurecom.fr/sparql?format=json&query=".urlencode(str_replace("\n", " ", $query)));
$contents = json_decode($contents);
echo json_encode($contents);