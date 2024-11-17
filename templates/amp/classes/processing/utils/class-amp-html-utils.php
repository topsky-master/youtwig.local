<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

class AMP_HTML_Utils {
	public static function build_tag( $tag_name, $attributes = array(), $content = '' ) {
		$attr_string = self::build_attributes_string( $attributes );
		return sprintf( '<%1$s %2$s>%3$s</%1$s>', preg_replace( '/[^a-z0-9_\-]/', '', mb_strtolower($tag_name)), $attr_string, ($content));
	}

	public static function build_attributes_string( $attributes ) {
		$string = array();
		foreach ( $attributes as $name => $value ) {
			if ( '' === $value ) {
				$string[] = sprintf( '%s', preg_replace( '/[^a-z0-9_\-]/', '', mb_strtolower($name)));
			} else {
				$string[] = sprintf( '%s="%s"', preg_replace( '/[^a-z0-9_\-]/', '', mb_strtolower($name)), htmlspecialcharsbx($value));
			}
		}
		return implode( ' ', $string );
	}
}
