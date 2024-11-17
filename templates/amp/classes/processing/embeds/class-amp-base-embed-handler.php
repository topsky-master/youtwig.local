<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

// Used by some children
require_once( AMP__DIR__ . '/utils/class-amp-html-utils.php' );

abstract class AMP_Base_Embed_Handler {
	protected $DEFAULT_WIDTH = 600;
	protected $DEFAULT_HEIGHT = 480;

	protected $args = array();
	protected $did_convert_elements = false;

	abstract function register_embed();
	abstract function unregister_embed();

	function __construct( $args = array() ) {
		$this->args = array_merge(array(
			'width' => $this->DEFAULT_WIDTH,
			'height' => $this->DEFAULT_HEIGHT,
		), (array)$args );
	}

	public function get_scripts() {
		return array();
	}
}
