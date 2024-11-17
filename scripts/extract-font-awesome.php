<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Title");
?><?php

//extract font-awesome icons for the font https://www.fontsquirrel.com/tools/webfont-generator

$str = '
.fa-angle-down:before {
	content: "\f107"
}

.fa-angle-right:before {
	content: "\f105"
}

.fa-angle-up:before {
	content: "\f106"
}

.fa-chevron-right:before {
	content: "\f054"
}

.fa-plus:before {
	content: "\f067"
}

.fa-minus:before {
	content: "\f068"
}

.fa-check:before {
	content: "\f00c"
}

.fa-check-square-o:before {
	content: "\f046"
}

.fa-check-square:before {
	content: "\f14a"
}

.fa-square-o:before {
	content: "\f096"
}

.fa-square:before {
	content: "\f0c8"
}

.fa-youtube:before {
	content: "\f167"
}

.fa-instagram:before {
	content: "\f16d"
}

.fa-facebook-f:before {
	content: "\f09a"
}

.fa-facebook:before {
	content: "\f09a"
}

.fa-twitter:before {
	content: "\f099"
}

.fa-shopping-cart:before {
	content: "\f07a"
}

.fa-sliders:before {
	content: "\f1de"
}

.fa-question-circle:before {
	content: "\f059"
}

.fa-question:before {
	content: "\f128"
}

.fa-star:before {
	content: "\f005"
}

.fa-star-o:before {
	content: "\f006"
}

.fa-star-half:before {
	content: "\f089"
}

.fa-star-half-empty:before {
	content: "\f123"
}

.fa-star-half-full:before {
	content: "\f123"
}

.fa-star-half-o:before {
	content: "\f123"
}

.fa-user:before {
	content: "\f007"
}

.fa-navicon:before {
	content: "\f0c9"
}

.fa-reorder:before {
	content: "\f0c9"
}

.fa-bars:before {
	content: "\f0c9"
}

.fa-clock-o:before {
	content: "\f017"
}

.fa-remove:before {
	content: "\f00d"
}

.fa-close:before {
	content: "\f00d"
}

.fa-times:before {
	content: "\f00d"
}

.fa-vk:before {
	content: "\f189"
}

.fa-long-arrow-left:before {
	content: "\f177"
}

.fa-search:before {
	content: "\f002"
}

.fa-mobile-phone:before {
	content: "\f10b"
}

.fa-mobile:before {
	content: "\f10b"
}

.fa-opencart:before {
	content: "\f23d"
}

.fa-angle-left:before {
	content: "\f104"
}

.fa-th-list:before {
	content: "\f00b"
}

.fa-th:before {
	content: "\f00a"
}

.fa-chevron-left:before {
	content: "\f053"
}

.fa-list:before {
	content: "\f03a"
}

.fa-envelope-o:before {
	content: "\f003"
}

.fa-toggle-off:before {
	content: "\f204"
}

.fa-toggle-on:before {
	content: "\f205"
}

.fa-tasks:before {
	content: "\f0ae"
}

.fa-whatsapp:before {
	content: "\f232"
}

.fa-telegram:before {
	content: "\f2c6"
}

.fa-heart-o:before {
	content: "\f08a"
}

.fa-heart:before {
	content: "\f004"
}

';

preg_match_all('~content\:\s+"\\\\([^"]+?)"~isu',$str,$astr);

$str = '';

foreach ($astr[1] as $char) {
  $str .= (!empty($str) ? ',' : '') .$char;  	
}

print_r($str);<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>