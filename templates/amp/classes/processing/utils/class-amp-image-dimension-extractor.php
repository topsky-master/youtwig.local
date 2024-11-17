<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

class AMP_Image_Dimension_Extractor {
    static $callbacks_registered = false;

    public static function extract( $url ) {
        if ( ! self::$callbacks_registered ) {
            self::register_callbacks();
        }

        /* $url = self::normalize_url( $url );
        if ( false === $url ) {
            return false;
        } */

        $dimensions = array();
        $dimensions = self::extract_local_image(false , $url);

        return $dimensions;
    }

    public static function normalize_url( $url ) {
        if ( empty( $url ) ) {
            return false;
        }

        if ( 0 === mb_strpos( $url, 'data:' ) ) {
            return false;
        }

        if ( 0 === mb_strpos( $url, '//' ) ) {
            return set_url_scheme( $url, 'http' );
        }

        $parsed = parse_url( $url );
        if ( ! isset( $parsed['host'] ) ) {
            $path = '';
            if ( isset( $parsed['path'] ) ) {
                $path .= $parsed['path'];
            }
            if ( isset( $parsed['query'] ) ) {
                $path .= '?' . $parsed['query'];
            }

            $path = ltrim($path,'/');
            $url = (CMain::IsHTTPS() ? 'https' : 'http') . '://' . preg_replace('~\:.*?$~is','',$_SERVER['SERVER_NAME']) .'/' . ( $path );
        }

        return $url;
    }

    private static function register_callbacks() {
        self::$callbacks_registered = true;
    }

    public static function extract_from_attachment_metadata( $dimensions, $url ) {
        if ( is_array( $dimensions ) ) {
            return $dimensions;
        }
    }

    public static function extract_local_image( $dimensions, $url ) {

        if ( is_array( $dimensions ) ) {
            return $dimensions;
        }


        if(mb_stripos($url,'//') === false
            && file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$url)){

            $dimensions = getimagesize($_SERVER['DOCUMENT_ROOT'].'/'.$url);

        }

        if ( ! is_array( $dimensions ) ) {
            return false;
        }

        return $dimensions;
    }
}
