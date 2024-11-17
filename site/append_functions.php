<?php

if (!defined('CATALOG_INCLUDED')) {
	die();
}

if (class_exists('CBXShortUri')) {
	CBXShortUri::CheckUri();
}
