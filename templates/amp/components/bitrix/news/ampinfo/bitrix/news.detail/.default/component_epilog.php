<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php

global $APPLICATION;

$CANONICAL_PROPERTY = "";

$canonical_url = $canonical_path = $filterPath = '';

$APPLICATION->AddViewContent('AMP_SCRIPTS','<script async custom-element="amp-iframe" data-skip-moving="true" src="https://cdn.ampproject.org/v0/amp-iframe-0.1.js"></script>
    <script async custom-element="amp-youtube" data-skip-moving="true" src="https://cdn.ampproject.org/v0/amp-youtube-0.1.js"></script>
    <script async custom-element="amp-vimeo" data-skip-moving="true" src="https://cdn.ampproject.org/v0/amp-vimeo-0.1.js"></script>
    <script async custom-element="amp-video" data-skip-moving="true" src="https://cdn.ampproject.org/v0/amp-video-0.1.js"></script>
    <script async custom-element="amp-audio" data-skip-moving="true" src="https://cdn.ampproject.org/v0/amp-audio-0.1.js"></script>
');

if(isset($arResult['CANONICAL_URL'])){

    $canonical_url = $arResult['CANONICAL_URL'];

    if(isset($canonical_url) && !empty($canonical_url)){

        $canonical_path = $canonical_url;

        $canonical_url = (preg_match('~http(s*?)://~',$canonical_url) == 0 ? (IMPEL_PROTOCOL.IMPEL_SERVER_NAME.$canonical_url) : $canonical_url);
        $canonical_url = preg_replace('~\:\/\/(www\.)*m\.~','://',$canonical_url);

        if($filter_set){

            $filterPath = preg_replace('~^.*?/filter/~','filter/',$APPLICATION->GetCurPage());

        }

        $SERVER_PAGE_URL = IMPEL_PROTOCOL.IMPEL_SERVER_NAME.$_SERVER['REQUEST_URI'];
        $SERVER_PAGE_URL = preg_replace('~\?.*?$~isu','',$SERVER_PAGE_URL);
        $DETAIL_PAGE_URL = preg_replace('~\?.*?$~isu','',$canonical_url);

        if($DETAIL_PAGE_URL != $SERVER_PAGE_URL){

            $CANONICAL_PROPERTY .= '<link href="'.$canonical_url.$filterPath.'" rel="canonical" />'.PHP_EOL;

        };

    };

};

if(file_exists(__DIR__.'/amp_style.css')){

    $amp_style = file_get_contents(__DIR__.'/amp_style.css');
    if(get_class($this->__template)!=="CBitrixComponentTemplate")
        $this->InitComponentTemplate();

    $this->__template->SetViewTarget("AMP_STYLE");
    echo $amp_style;
    $this->__template->EndViewTarget();

}

if(!empty($CANONICAL_PROPERTY)){

    if(get_class($this->__template)!=="CBitrixComponentTemplate")
        $this->InitComponentTemplate();


    $this->__template->SetViewTarget("CANONICAL_PROPERTY");

    echo $CANONICAL_PROPERTY;

    $this->__template->EndViewTarget();

}