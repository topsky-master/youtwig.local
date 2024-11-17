<?php

namespace Api;

class Url {

    /**
     * Выбираем текущую ссылку страницы
     */
    public static function getUrlPage()
    {
        return ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }


    /**
     * Выбираем домен с протоколом
     */
    public function getDomain()
    {
        return ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
    }


    /**
     * Выбираем текущий путь страницы
     */
    public function getPathPage()
    {
        $url_page = self::getUrlPage();
        $url_arr = parse_url($url_page);
        return $url_arr['path'];
    }


}