<?php



/**
*  Define the output file
*
*  @author Michele Maresca
*/

if(basename(__FILE__) == basename($argv[0]))
	define('XML_FILE', 'demo.xml');



/**
*  Define rdf:label
*
*  @author Michele Maresca
*/

define('LABEL', 'rdf:label');

/**
*  Define RDF attribute rdf:About
*
*  @author Michele Maresca
*/

define('RDF_ATTRIBUTE', 'rdf:about');

/**
*  Define adminUnitL1 value
*
*  @author Michele Maresca
*/

define('ADMIN_UNIT', 'IT');

/**
 * Basic representation of a Location
 *
 * @author Michele Maresca
 */

class Location
{
	public $name;
	public $city;
	public $address;
	public $houseNumber;
	public $lat;
	public $long;

	/**
	 * @param String $name not null, used to identify the location
	 * @param String $city
	 * @param String $address
	 * @param String $houseNumber
	 * @param double $lat
	 * @param double $long
	 */
	public function __construct($name, $city, $address, $houseNumber, $lat, $long)
	{
		$this->name=$name;
		$this->city = $city;
		$this->address = $address;
		$this->houseNumber = $houseNumber;
		$this->lat = $lat;
		$this->long = $long;
	}
}

/**
 * Create a Locn RDFnode
 *  
 * @author Michele Maresca
 */

class RDFLocnGenerator
{
	private $location;
	private $uri;
	
	public function __construct($location)
	{
		$this->location = $location;
		$this->uri = $uri;
	}

	public function generateLocation($xml, $rdfParent, $uri)
	{
//Create RDF Node for location
		$locnRDF = $xml->createElement("locn:Location");
		$locnRDFAttribute = $xml->createAttribute(RDF_ATTRIBUTE);
		$locnRDFAttribute->value = $this->uri."location/";
		$locnRDF->appendChild($locnRDFAttribute);

//Create NODE locn:address
		$parentLocnAddress = $xml->createElement("locn:address");
//Create NODE locn:Address		
		$locnAddress = $xml->createElement("locn:Address");
		$locnAddressAttribute = $xml->createAttribute(RDF_ATTRIBUTE);
		$locnAddressAttribute->value = $uri;
		$locnAddress->appendChild($locnAddressAttribute);

//Create node rdf:label
		$rdfLabel = $xml->createElement(LABEL);		
		$rdfLabel->appendChild($xml->createTextNode($this->formatLabel()));
//Create node locn:fullAddress
		$fullAddress = $xml->createElement("locn:FullAddress");
		$fullAddress->appendChild($xml->createTextNode($this->formatLabel()));

//Create node locn:throughfare
		$locnThroughfare = $xml->createElement("locn:throughfare");
		$locnThroughfare->appendChild($xml->createTextNode($this->location->address));

//Create node locn:locatorDesignator
		$locnDesignator = $xml->createElement("locn:locatorDesignator");
		$locnDesignator->appendChild($xml->createTextNode($this->location->houseNumber));

//Create node locn:locatorPostname
		$locnPostName = $xml->createElement("locn:postName");
		$locnPostName->appendChild($xml->createTextNode($this->location->city));

//Create node locn:adminUnitL1
		$locnAdminUnitL1 = $xml->createElement("locn:adminUnitL1");
		$locnAdminUnitL1->appendChild($xml->createTextNode(ADMIN_UNIT));

//Insert node in locn:Address
		$locnAddress->appendChild($rdfLabel);
		$locnAddress->appendChild($fullAddress);
		$locnAddress->appendChild($locnThroughfare);
		$locnAddress->appendChild($locnDesignator);
		$locnAddress->appendChild($locnPostName );
		$locnAddress->appendChild($locnAdminUnitL1);

		$parentLocnAddress->appendChild($locnAddress);

//Create locn:geometry
		$parentGeometry= $xml->createElement("locn:geometry");
		
//Create NODE locn:Geometry		
		$geoLocnGeometry = $xml->createElement("locn:Geometry");
		$geoAttribute = $xml->createAttribute(RDF_ATTRIBUTE);
		$geoAttribute->value = $uri."location/geometry";
		$geoLocnGeometry->appendChild($geoAttribute);

//Create node geo:lat
		$geoLat = $xml->createElement("geo:lat");
		$geoLat->appendChild($xml->createTextNode($this->location->lat));

//Create node geo:long
		$geoLong = $xml->createElement("geo:long");
		$geoLong->appendChild($xml->createTextNode($this->location->long));

		$geoLocnGeometry->appendChild($geoLat);
		$geoLocnGeometry->appendChild($geoLong);
		$parentGeometry->appendChild($geoLocnGeometry);

		$locnRDF->appendChild($parentLocnAddress);
		$locnRDF->appendChild($parentGeometry);
		
		$rdfParent->appendChild($locnRDF);
	}

	public static function getLocationURI($prefix, $name)
	{
		return (string) $prefix.urlencode($name);
	}

	private function formatLabel()
	{
		return (string)($this->location->houseNumber.", ".$this->location->address.", ".$this->location->city.", Italy");
	}
}

//Insert a URI

//Create a XML file
// $xml = new DOMDocument();

// //Create a RDF parent node
// $rdfParent = $xml->createElement("rdf:RDF");

// $xml->preserveWhiteSpace = false;
// $xml->formatOutput = true;


// $l = new Location("Hackspace catania", Catania", "Via Grotte Bianche", "112", "37.513287", "15.088008");
// $rdfl = new RDFLocnGenerator($l);
// $rdfl->generateLocation($xml, $rdfParent);

// $xml->appendChild($rdfParent);

// echo "File \"".XML_FILE."\" creato!";
// $xml->save(XML_FILE);