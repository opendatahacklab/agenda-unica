<?php

//Include this for get event

include("./AgendaSheetParser.php");

/**
*  Define the output file
*
*  @author Michele Maresca
*/
define('XML_FILE', 'events.xml');


define('DATATYPE_ATTRIBUTE', 'rdf:datatype');

/**
*  Define RDF attribute rdf:About
*
*  @author Michele Maresca
*/

define('RDF_ATTRIBUTE', 'rdf:about');

/**
*  Define RDF DATATYPE
*
*  @author Michele Maresca
*/
define('RDF_DATATYPE', "http://www.w3.org/2001/XMLSchema#dateTime");

/**
*  Define Ontology attribute
*
*  @author Michele Maresca
*/
define('ONTOLOGY_VALUE', 'http://www.dmi.unict.it/~longo/opendatatour');

/**
 * Define RDF  Attributes value
 *
 * @author Michele Maresca
 */

define('BASE_VALUE', 'http://example.org');
define('DC_VALUE', 'http://purl.org/dc/elements/1.1/');
define('GEO_VALUE', 'http://www.w3.org/2003/01/geo/wgs84_pos#');
define('FOAF_VALUE', 'http://xmlns.com/foaf/0.1/');
define('ORG_VALUE', 'ttp://www.w3.org/ns/org#');
define('LOCN_VALUE', 'http://www.w3.org/ns/locn#/');
define('DCTERMS_VALUE', 'http://purl.org/dc/terms/');
define('RDFS_VALUE', 'http://www.w3.org/2000/01/rdf-schema#');
define('OWL_VALUE', 'http://www.w3.org/2002/07/owl#');
define('XSD_VALUE', 'http://www.w3.org/2001/XMLSchema#');
define('EVENT_VALUE', 'http://purl.org/NET/c4dm/event.owl#');
define('RDF_VALUE', 'http://purl.org/NET/c4dm/event.owl#');
define('TIME_VALUE', 'http://www.w3.org/2006/time#');
define('SIOC_VALUE', '&sioc;');


/**
 * Create a RDFnode to insert in the XML file
 *
 * @author Michele Maresca
 */
class RDFEventsGenerator
{
	private $event;
	private $xml;
	private static $stage = 0;


	public function __construct($event)
	{
		$this->event = $event;
	}


	public function generateEvent($xml, $rdfParent)
	{
		$formatInstant = [];

		$event = $xml->createElement("event:Event");
		$eventAttribute = $xml->createAttribute(RDF_ATTRIBUTE);
		$event->appendChild($eventAttribute);


		$name = $xml->createElement("rdfs:Label");
		$eventTime = $xml->createElement("event:time");

		$timeInterval = $xml->createElement("time:Interval");
		$rdfTimeAttribute = $xml->createAttribute(RDF_ATTRIBUTE);
		$timeInterval->appendChild($rdfTimeAttribute);
		$timeHasBeginning = $xml->createElement("time:hasBeginning");

		$timeInstant = $xml->createElement("time:Instant");
		$rdfInstantAttribute = $xml->createAttribute(RDF_ATTRIBUTE);

		$timeXSDDataTime = $xml->createElement("time:inXSDDateTime");

		$name->appendChild($xml->createTextNode($this->event->name));

		if($this->event->start)
			$date = $xml->createTextNode(date("c", $this->event->start->getTimestamp()));
		else
			$date = $xml->createComment("Data non valida!");

		$formatInstant = $this->setDateAttribute($date);
		$rdfInstantAttribute ->value = "time".$formatInstant[0].$formatInstant[1];
		$timeInstant->appendChild($rdfInstantAttribute);


		$timeXSDDataTime->appendChild($date);
		$rdfXSDAttribute = $xml->createAttribute(DATATYPE_ATTRIBUTE);
		$rdfXSDAttribute->value = RDF_DATATYPE;
		$timeXSDDataTime->appendChild($rdfXSDAttribute);

		$timeInstant->appendChild($timeXSDDataTime);
		$timeHasBeginning->appendChild($timeInstant);
		$timeInterval->appendChild($timeHasBeginning);
		$eventTime->appendChild($timeInterval);

		$event->appendChild($eventTime);
		$event->appendChild($name);

		$rdfParent->appendChild($event);

	}

	private function setDateAttribute($date)
	{
		$text = $tmp = [];
		$str1 = $str2 = "";
		$text = split("T",$date->wholeText);
		$str1 = join(split("-", $text[0]));
		$tmp = split("\+", $text[1]);
		$tmp = join(split(":", $tmp[0]));
		$str2 = substr($tmp, 0, 4);

		return [$str1, $str2];
	}

}



//Create a XML file
$xml = new DOMDocument();

//Create a RDF parent node
$rdfParent = $xml->createElement("rdf:RDF");

$xml->preserveWhiteSpace = false;
$xml->formatOutput = true;

ob_start();

?>

<!DOCTYPE rdf:RDF[
	<!ENTITY org "http://www.w3.org/ns/org#" >
	<!ENTITY dcterms "http://purl.org/dc/terms/" >
    <!ENTITY locn "http://www.w3.org/ns/locn#" >
    <!ENTITY foaf "http://xmlns.com/foaf/0.1/" >
	<!ENTITY owl "http://www.w3.org/2002/07/owl#" >
	<!ENTITY xsd "http://www.w3.org/2001/XMLSchema#" >
	<!ENTITY rdfs "http://www.w3.org/2000/01/rdf-schema#" >
	<!ENTITY geo "http://www.w3.org/2003/01/geo/wgs84_pos#" >
	<!ENTITY rdf "http://www.w3.org/1999/02/22-rdf-syntax-ns#" >
	<!ENTITY odt "http://www.dmi.unict.it/~longo/opendatatour/" >
	<!ENTITY event "http://purl.org/NET/c4dm/event.owl#" >
	<!ENTITY time "http://www.w3.org/2006/time#" >
	<!ENTITY cct "http://www.comune.catania.it/comunect.owl/" >
	<!ENTITY sioc "http://rdfs.org/sioc/ns#" >
]>

<?php
$doctype = ob_get_contents();
ob_flush();

$xml->loadXML($doctype);


//The parse is set in AgendaSheetParser

foreach ($p as $e) 
{
	$r = new RDFEventsGenerator($e);
	$r->generateEvent($xml, $rdfParent);
}

$xml->appendChild($rdfParent);

echo "File \"events.xml\" creato!";
$xml->save(XML_FILE);