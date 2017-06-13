<?php 
/**
 * This script converts a backug generated by the sparql query SELECT ?x ?y ?z WHERE {?x ?y ?z}
 * into NTriples. Read the backup from standard input and output the NTriples file in std out 
 * 
 * Copyright 2017 Cristiano Longo
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Generate and output an ntriples file by reading the csv $src
 * @param unknown $src url of the source csv
 */
function publishNTriples($src){
	$f = fopen( $src, 'r' );
	//skip the first line
	fgetcsv($f);

	// output
	header ( 'Content-type: application/n-triples; charset=UTF-8' );
	header ('Access-Control-Allow-Origin: *');
	//output other lines
	while (($row = fgetcsv($f)) !== false) {
		if (count($row)==3){
			$x=convertToNtriples($row[0]);
			$y="<$row[1]>";
			$z=convertToNtriples($row[2]);
			echo "$x $y $z .\n";
		}
	}
	fclose($f);
}

/**
 * Enclose the string into < > if it starts with http, enclose literals in ""
 * and leave blank nodes unchanged
 * @param unknown $string
 */
function convertToNtriples($string){
	if (preg_match('#^http#', $string))
		return "<$string>";
	if (preg_match('#^_:#', $string))
		return $string;
	$string=str_replace("\n",'',
			str_replace("\r",'', 
			str_replace('"',"\\\"", $string)));
	return "\"$string\"";
}

?>