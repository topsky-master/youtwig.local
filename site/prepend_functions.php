<?

if (!class_exists('cartFix')) {

        class CartFix
        {
            public static function checkFix()
            {
                if (isset($_GET['sessid'])
                    && preg_match('~^[a-z0-9]+$~isu', $_GET['sessid'])
                ) {
                    $sessid = trim($_GET['sessid']);
                    $sFile = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/tmp/' . $sessid . '.txt';
                    if (file_exists($sFile)) {
                        $sSerial = file_get_contents($sFile);
                        unlink($sFile);
                        $aSerial = unserialize($sSerial);
                        unset($aSerial['recovery']);
                        $aSerial['tryrecovery'] = true;
                        if (isset($aSerial['sessid']) && $aSerial['sessid'] == $sessid) {
                            $_POST = $_REQUEST = $aSerial;
                            $_SERVER['REQUEST_METHOD'] = 'POST';
                        }
                    }
                }
            }
        }

    }

if (!class_exists('SeoPaginationFix')) {
    class SeoPaginationFix
    {

        private static $removeParams = 'clid; utm_source; utm_medium; utm_campaign; utm_content; utm_term; yclid; gclid; _openstat; referrer1; referrer2; referrer3';

        private static function isPathTraversalUri($uri)
        {
            if (($pos = mb_strpos($uri, "?")) !== false)
                $uri = mb_substr($uri, 0, $pos);

            $uri = trim($uri);
            return preg_match("#(?:/|2f|^|\\\\|5c)(?:(?:%0*(25)*2e)|\\.){2,}(?:/|%0*(25)*2f|\\\\|%0*(25)*5c|$)#i", $uri) ? true : false;
        }

        private static function removeParams($param)
        {

            if (!is_array(static::$removeParams)) {
                static::$removeParams = explode(';', static::$removeParams);
                static::$removeParams = array_map('trim', static::$removeParams);
            }

            return in_array($param, static::$removeParams);

        }

        public static function rebuildPagination()
        {

            $_SERVER['ORIG_REQUEST_URI'] = $_SERVER['REQUEST_URI'];
            $newUri = preg_replace('#(/pages([\d]*?)-([\d]+))#is', '', $_SERVER['REQUEST_URI']);
            $newUri = preg_replace('#/reviews/#is', '/', $newUri);

            $newUri = preg_replace('#&*?PAGEN_[0-9]+?=[^&]+#is', '', $newUri);
            $newUri = (mb_stripos($newUri, '?') !== false) ? ($newUri) : ($newUri . '?');

            $testUri = $_SERVER['REQUEST_URI'];

            if (preg_match('#(/pages([\d]*?)-1/)#is', $testUri) && !headers_sent()) {
                $redirectUri = preg_replace('#(/pages([\d]*?)-1/)#is', '/', $testUri);
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: " . $redirectUri);
                die();
            }

            $page = 0;

            if (isset($testUri)
                && !empty($testUri)
                && !static::isPathTraversalUri($testUri)) {

                if (preg_match('#^/brand/.*?/filter/(.+)(\\?(.*))?#is', $testUri)
                    && !preg_match('#^/brand/.*?/filter/clear/#is', $testUri)) {

                    preg_match_all('#^/brand/.*?/filter/([^\?]+)(\\?(.*))?#is', $testUri, $matches);

                    $sSmartFilter = trim($matches[1][0]);
                    $sSmartFilter = preg_replace('~/pages-.*~', '/', $sSmartFilter);
                    $_GET["BRAND_SMART_FILTER_PATH"] = '/filter/' . $sSmartFilter;
                    $newUri = str_ireplace('/filter/' . $sSmartFilter, '/', $newUri);

                }

                if (preg_match('#/pages([\d]*?)-([\d]+)#is', $testUri)) {

                    $matches = array();
                    preg_match_all('#/pages([\d]*?)-([\d]+)#is', $testUri, $matches);

                    $cnum = (int)trim($matches[1][0]);
                    $numbers = empty($cnum) ? array(1) : array($cnum);

                    $page = (int)trim($matches[2][0]);

                    if (!empty($page)
                        && !empty($numbers)) {

                        foreach ($numbers as $num) {

                            $newUri .= '&PAGEN_' . $num . '=' . $page;

                            if (!in_array($num, array(1))) {
                                $stUri = $newUri;
                                $stUri = preg_replace('~\?(.*)$~', '', $stUri);
                                $stUri .= '/pages' . $num . '/';
                                //file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/test.txt', $stUri . "\n", FILE_APPEND);
                            }

                            $_GET["PAGEN_" . $num] = $page;
                            $_REQUEST["PAGEN_" . $num] = $page;

                            if (!defined("PAGEN_" . $num))
                                define("PAGEN_" . $num, $page);


                        }

                    }

                }

                if (preg_match('#/reviews/#is', $testUri)) {

                    $_GET["previews"] = true;
                    $_REQUEST["previews"] = true;

                }

            }

            list($pagePath, $queryString) = explode('?', $newUri, 2);

            if (!empty($pagePath)) {
                $exludePath = urlencode($pagePath);
                $newUri = preg_replace('~&' . preg_quote($exludePath, '~') . '\=[^&]*~isu', '', $newUri);
                $newUri = preg_replace('~\?' . preg_quote($exludePath, '~') . '\=[^&]*~isu', '?', $newUri);
            }

            mb_parse_str($queryString, $parameters);

            static::cleanRequest($parameters);
            static::cleanRequest($_GET);
            static::cleanRequest($_REQUEST);

            $queryString = http_build_query($parameters, '', '&');

            $newUri = $pagePath . (!empty($queryString) ? ('?' . $queryString) : '');

            $_SERVER["QUERY_STRING"] = $queryString;
            $_SERVER['REQUEST_URI'] = $newUri;

        }

        private static function cleanRequest(&$parameters)
        {
            foreach ($parameters as $key => $value) {
                if ((mb_stripos(urldecode($key), '/') !== false)
                    || ($removeKey = static::removeParams($key))) {

                    unset($parameters[$key]);

                    if(!defined('NEED_CANONICAL')) {
                        define('NEED_CANONICAL', true);
                    }
                }
            }
        }
    }

}

SeoPaginationFix::rebuildPagination();
