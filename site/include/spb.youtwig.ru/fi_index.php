<?if(!defined('CATALOG_INCLUDED')) die(); ?>
<?

unset($arrFilter);

global $arrTFilter;
$arrTFilter['ID'] = twigElement::getBigData();

global $arrNFilter;
$arrNFilter = array(
    "!PROPERTY_NEWPRODUCT" => false
);

?>