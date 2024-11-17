<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php

$hasTest = isset($_REQUEST['test']);

if ($hasTest) {
	echo 'here';
}

twigSeoSections::printSeoAndSetTitlesSection(0,$arParams);