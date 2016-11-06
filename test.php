<?php 
define('AGEND_UNICA_URL','https://docs.google.com/spreadsheets/d/1bzVASM5_JjCgvNp3Vs0GJ4vDgYsKo_ig5NHU1QI5USc/export?format=tsv&exportFormat=tsv&ndplr=1');

require('AgendaSheetParser.php');
require('DefaultEventParser.php');
$p = new AgendaSheetParser(AGEND_UNICA_URL, new DefaultEventParser());
foreach ($p as $e)
{
	$auth=$e->distributionAuthorization ? 'yes' : 'no';
	if ($e->creationTime!=null)
		echo $e->name." Creation=".$e->creationTime->format('d/m/Y H.i.s')." auth=".$auth."\n";
	else
		echo $e->name." auth=".$auth."\n";
}


echo "LOCATIONS\n\n";
$locations=$p->getAllParsedLocations();
foreach ($locations as $n => $l)
{
	echo "$n >> ".$l->city." ".$l->houseNumber." ".$l->address."\n";
}
?>