<?php

error_reporting(E_ERROR);

define('__BASE_DOMAIN__','https://market.yandex.ru');

$isContinue = isset($_REQUEST['nextURI'])
            &&!empty($_REQUEST['nextURI'])
            && preg_match('~page=([0-9]+)~isu',$_REQUEST['nextURI'])
            ? true
            : false;

$nextURI = isset($_REQUEST['nextURI'])
        && !empty($_REQUEST['nextURI'])
        ? trim(urldecode($_REQUEST['nextURI']))
        : (__BASE_DOMAIN__.'/shop/228852/reviews?shopId=228852&lr=143');

$csv_file_name = 'results-'.date('Y-m-d').'.csv';

define('CSV_FILE_NAME',$csv_file_name);

if(!$isContinue){

    file_put_contents(__DIR__.'/'.$csv_file_name,'');
    $pageCounter = 1;

} else {

    $matches = array();
    preg_match('~page=([0-9]+)~isu',$nextURI,$matches);

    if(isset($matches[1])){
        $pageCounter = (int)$matches[1];
    }

}

define('__BASE__URI__',$nextURI);

function clearYaHtml($rText){
    $rText = preg_replace('~\s+?class="[^"]+?"~','',$rText);
    $rText = preg_replace('~\s+?style="[^"]+?"~','',$rText);
    $rText = preg_replace('~[\n\r]~isu',' ',$rText);
    $rText = preg_replace('~[\s]+?[\s]+~isu',' ',$rText);
    return $rText;
}

function getYaCaptcha($rep,$key,$retpath){

    $key = urlencode(urldecode($key));
    $rep = urlencode(urldecode($rep));
    $retpath = urlencode(htmlspecialchars_decode(urldecode($retpath)));

    unset($_REQUEST['rep'],$_REQUEST['key'],$_REQUEST['retpath']);
    $url = __BASE_DOMAIN__.'/checkcaptcha?key='.($key).'&rep='.($rep).'&retpath='.($retpath);
    getURIContent($url,__BASE_DOMAIN__.'/showcaptcha?retpath='.($retpath).'&status=failed&t=');

}

function checkYaCaptcha($body,$retpath){

    $hasCaptcha = false;
    $key = '';

    $htmlDom = new DOMDocument();
    $htmlDom->loadHTML($body);
    $xPath = new DOMXPath($htmlDom);

    $cNodes = $xPath->query('//form[@class="form__inner"]');
    $captchaForm = "";

    if($cNodes->length){

        foreach($cNodes as $cForm){
            $action = $cForm->getAttribute('action');

            if(stripos($action,'/checkcaptcha') !== false){
                //image form__captcha

                $iNodes = $xPath->query('//img[@class="image form__captcha"]');

                if($iNodes->length){

                    $captchaForm = trim($htmlDom->saveHTML($iNodes->item(0)));

                }

                $hasCaptcha = true;
            }
        }

        if($hasCaptcha){
            $kNodes = $xPath->query('//input[@name="key"]');

            if($kNodes->length){
                $key = $kNodes->item(0)->getAttribute('value');
            }

            $rNodes = $xPath->query('//input[@name="retpath"]');

            if($rNodes->length){
                $retpath = $rNodes->item(0)->getAttribute('value');
            }
        }

        if(empty($key)){
            $hasCaptcha = false;
        }

    }

    if($hasCaptcha){

        if($key){

            $template = '<html><head></head><body>'.$captchaForm.'<p>'.__BASE__URI__.'</p><form method="post"><input type="hidden" name="nextURI" value="'.htmlspecialchars(__BASE__URI__).'" /><input type="hidden" name="retpath" value="'.htmlspecialchars($retpath).'" /><input type="hidden" name="key" value="'.htmlspecialchars($key).'" /><input type="text" name="rep" value="" placeholder="Captcha" /><input type="submit" value="Send" /></form></body></html>';
            echo $template;
        }

        die();

    }


}

function getURIContent($url, $referer = "", $method = "get", $data="", $cookie = ""){

    static $storedReferer;

    $rep = isset($_REQUEST['rep'])
            &&!empty($_REQUEST['rep'])
            ? trim($_REQUEST['rep']) : '';

    $key = isset($_REQUEST['key'])
    &&!empty($_REQUEST['key'])
        ? trim($_REQUEST['key']) : '';

    $retpath = isset($_REQUEST['retpath'])
    &&!empty($_REQUEST['retpath'])
        ? trim($_REQUEST['retpath']) : '';


    if(!empty($rep)
        && !empty($key)
        && !empty($retpath)){

        getYaCaptcha($rep,$key,$retpath);

    }

    if(empty($referer)
        && !empty($storedReferer)){

        $referer = $storedReferer;

    }

    $tuCurl = curl_init();

    if($tuCurl && is_resource($tuCurl)){

        switch ($method){

            case 'post':

                $opts = array(
                    CURLOPT_POST => 1,
                    CURLOPT_POSTFIELDS => $data,
                    CURLOPT_CONNECTTIMEOUT => 12,
                );

                break;
            case 'file':

                $opts = array(
                    CURLOPT_HTTPGET => 1,
                    CURLOPT_BINARYTRANSFER => 1,
                    CURLOPT_CONNECTTIMEOUT => 60,
                    CURLOPT_FILE => $data,
                );

                break;
            case 'get':
            default:

                $opts = array(
                    CURLOPT_HTTPGET => 1,
                    CURLOPT_CONNECTTIMEOUT => 12,
                );

                break;


        }


    };

    $opts[CURLOPT_URL] = $url;
    $opts[CURLOPT_RETURNTRANSFER] = 1;
    $opts[CURLOPT_FOLLOWLOCATION] = 1;
    $opts[CURLOPT_AUTOREFERER] = 1;
    $opts[CURLOPT_HEADER] = 0;
    $opts[CURLOPT_COOKIESESSION] = 1;

    $ckfile = __DIR__.'/cookies.txt';

    $opts[CURLOPT_COOKIEJAR] = $ckfile;
    $opts[CURLOPT_COOKIEFILE] = $ckfile;

    $opts[CURLOPT_SSL_VERIFYHOST] = 0;
    $opts[CURLOPT_SSL_VERIFYPEER] = 0;

    if(!empty($cookie)){
        $opts[CURLOPT_COOKIE] = $cookie;
    }

    $opts[CURLOPT_USERAGENT] = "Opera/9.80 (X11; Linux i686; Ubuntu/14.10) Presto/2.12.388 Version/12.16";

    if(!empty($referer)){
        $opts[CURLOPT_REFERER] = $referer;
    }

    foreach($opts as $key=>$value){
        curl_setopt($tuCurl,$key,$value);
    }

    $tuData = curl_exec($tuCurl);
    curl_close($tuCurl);

    if($data && is_resource($data)){
        fclose($data);
    }

    $storedReferer = $url;

    checkYaCaptcha($tuData,$url);

    return $tuData;

}

function parseYaComments($body){

    $htmlDom = new DOMDocument();

    $body = mb_convert_encoding($body, 'HTML-ENTITIES', "UTF-8");

    $htmlDom->loadHTML($body);
    $xPath = new DOMXPath($htmlDom);
    $rNodes = $xPath->query('//div[contains(@class,"n-product-review-item i-bem")]');

    $reviews = array();

    if($rNodes->length){

        foreach($rNodes as $number => $rnode){

            $reviews[$number] = array();

            $iNodes = $xPath->query('.//img[@class="n-product-review-user__avatar"]',$rnode);

            $imgSrc = '';
            $imgSrcS = '';

            if($iNodes->length){

                $imgSrc = $iNodes->item(0)->getAttribute('src');

                if(!empty($imgSrc)){

                    $imgSrc = stripos($imgSrc,'http') === false
                            &&stripos($imgSrc,'//') === false
                            ?
                            '//'.$imgSrc
                            : $imgSrc;

                    $imgSrc = !(stripos($imgSrc,'http') === 0)
                            ? 'http:'.$imgSrc
                            : $imgSrc;

                    $imgSrc = preg_replace('~/islands\-[^/]+~is','/islands-200',$imgSrc);

                }

            }

            if(!empty($imgSrc)){

                $sizes = getimagesize($imgSrc);

                if(isset($sizes['mime'])
                    && !empty($sizes['mime'])
                    && isset($sizes[0])
                    && !empty($sizes[0])
                    && isset($sizes[1])
                    && !empty($sizes[1])
                ){

                    $imageType = $sizes['mime'];

                    $imageExtension = "";

                    switch($imageType){
                        case 'image/gif':
                            $imageExtension = '.gif';
                            break;
                        case 'image/png':
                            $imageExtension = '.png';
                            break;
                        case 'image/jpeg':
                            $imageExtension = '.jpg';
                            break;
                    }

                    $imgContent =  file_get_contents($imgSrc);

                    if(!empty($imgContent)
                        && !empty($imageExtension)){

                        $fileName = md5($imgSrc).$imageExtension;

                        if(!file_exists(__DIR__.'/images/'.$fileName,$imgContent)){
                            file_put_contents(__DIR__.'/images/'.$fileName,$imgContent);
                        }

                        if(file_exists(__DIR__.'/images/'.$fileName)){
                            $imgSrcS = '/images/'.$fileName;
                        }

                    }

                }

            }

            $reviews[$number]['authorAvatar'] = $imgSrcS;

            $anNodes = $xPath->query('.//*[@itemprop="author"]',$rnode);

            $authorName = '';

            if($anNodes->length){

                $authorName = trim($anNodes->item(0)->getAttribute('content'));

            }

            $reviews[$number]['authorName'] = $authorName;

            $delNodes = $xPath->query('.//span[@class="n-product-review-item__delivery"]',$rnode);

            $deliveryName = '';

            if($delNodes->length){

                $deliveryName = trim($delNodes->item(0)->nodeValue);

            }

            $reviews[$number]['deliveryName'] = $deliveryName;

            $ratNodes = $xPath->query('.//meta[@itemprop="ratingValue"]',$rnode);

            $ratingValue = '';

            if($ratNodes->length){

                $ratingValue = trim($ratNodes->item(0)->getAttribute('content'));

            }

            $reviews[$number]['ratingValue'] = $ratingValue;

            $dNodes = $xPath->query('.//span[@class="n-product-review-item__date-region"]',$rnode);

            $rDave = '';

            if($dNodes->length){

                $rDave = trim($dNodes->item(0)->nodeValue);

            }

            $reviews[$number]['reviewDate'] = $rDave;

            $dlNodes = $xPath->query('.//dl[contains(@class,"n-product-review-item__stat")]',$rnode);

            $rText = '';

            if($dlNodes->length){

                foreach($dlNodes as $dlnode){
                    $rText .= trim($htmlDom->saveHTML($dlnode));
                }

            }

            if(!empty($rText)){
                $rText= clearYaHtml($rText);
            }

            $reviews[$number]['reviewText'] = $rText;

            $reviews[$number]['comments'] = array();

            $cNodes = $xPath->query('.//div[@class="n-shop-review-comment"]',$rnode);

            $reviews[$number]['comments'] = '';

            if($cNodes->length){

                foreach($cNodes as $cnode){

                    $cTitlte = '';
                    $ctNodes = $xPath->query('.//div[@class="n-shop-review-comment__title"]',$cnode);

                    if($ctNodes->length){
                        $cTitlte = trim($htmlDom->saveHTML($ctNodes->item(0)));
                    }

                    if(!empty($cTitlte)){
                        $cTitlte = clearYaHtml($cTitlte);
                    }

                    $cText = '';
                    $ctNodes = $xPath->query('.//div[@class="n-shop-review-comment__body"]',$cnode);

                    if($ctNodes->length){
                        $cText = trim($htmlDom->saveHTML($ctNodes->item(0)));
                    }

                    if(!empty($cText)) {
                        $cText = clearYaHtml($cText);
                    }

                    $reviews[$number]['comments'] .= '[title:]'.$cTitlte.'[text:]'.$cText;

                }

            }

        }

        if(!empty($reviews)){

            $cf = fopen(__DIR__.'/'.CSV_FILE_NAME,'ab+');

            foreach($reviews as $cReview){

                fputcsv($cf,$cReview,";");

            }

            fclose($fp);

        }

    }

}


$pages = array();
$body = getURIContent($nextURI);

echo $body;
die();

parseYaComments($body);

$pages[] = $body;

$htmlDom = new DOMDocument();
$htmlDom->loadHTML($body);
$xPath = new DOMXPath($htmlDom);

$pnNodes = $xPath->query('//div[@class="n-pager i-bem"]/a');

$links = array();

if($pnNodes->length){

    if(!$isContinue){

        $linksSimiliar = '';

        foreach ($pnNodes as $node) {

            $href = $node->getAttribute('href');
            $href = !preg_match('~http(s*?)://~',$href)
                    ? (__BASE_DOMAIN__.$href)
                    : $href;

            if(stripos($href,'page=0') === false
                && stripos($href,'page=') !== false){

                $linksSimiliar = $href;
                break;

            }

        }

    } else {

        ++$pageCounter;
        $linksSimiliar = preg_replace('~page=([0-9]+)~isu','page='.$pageCounter,$nextURI);
    }

    if(!empty($linksSimiliar)){

        $request_uri = $_SERVER['REQUEST_URI'];
        $request_uri = preg_replace('~\?.*?$~isu','',$request_uri);
        header("Location: ".$request_uri."?nextURI=".urlencode($linksSimiliar));

        exit();

    }

} else {

    echo sprintf('Парсинг окончен. Последняя ссылка %s',$nextURI);

}

?>