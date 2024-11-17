<?php

namespace Api;


class Seo {

    /**
     * Проверяем ссылку на наличие в ee конце постраничной навигаци, если структура не правильная, формируем ее в нужном порядке и делаем редирект
     */
    public static function redirectToEndPageNav()
    {
        $page_path = $_SERVER['DOCUMENT_URI'];
        $res_nav = strripos($page_path, "/pages-");

        if($res_nav !== false)
        {
            $path_arr = explode("/", $page_path);
            $path_arr = array_diff($path_arr, array(''));
            $item_end = end($path_arr);

            $res_nav_end = strripos($item_end, "pages-");
            if($res_nav_end === false)
            {
                $page = "";
                foreach ($path_arr as $key => $item)
                {
                    $res_nav_item = strripos($item, "pages-");
                    if($res_nav_item !== false)
                    {
                        $page = $item;
                        unset($path_arr[$key]);
                        break;
                    }
                }

                $url_redirect = "";
                foreach ($path_arr as $item)
                {
                    $url_redirect .= "/".$item;
                }
                $url_redirect .= "/".$page."/";

                LocalRedirect($url_redirect, false, '301 Moved permanently');
            }
        }
    }

}