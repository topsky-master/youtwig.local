<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?>
<?php
global $arrFilter;

if(isset($_REQUEST["q"]) && !empty($_REQUEST["q"])){
	$query 							  = trim(urldecode($_REQUEST["q"]));
	$query							  = html_entity_decode($query,ENT_QUOTES,'UTF-8');
    $query                            = strip_tags($query);

	$_REQUEST["q"]						  = $query;

	if(!is_array($arrFilter)){
		$arrFilter					  = array();
	}

	/* $arrFilter[]			  	= array(
		"LOGIC"				=> "OR",
		array("PROPERTY_ARTNUMBER"      => $query),
		array("NAME"			=> $query)
	); */

	$arrFilter["IBLOCK_ID"]			= 11;

	 $arrFilter[]			  	= array(
		"LOGIC"				=> "OR",
		array("PROPERTY_ARTNUMBER"      => "%".$query."%"),
		array("NAME"			=> "%".$query."%"),
		array("PROPERTY_ORIGINALS_CODES" => "%".$query."%"),

	);

}


