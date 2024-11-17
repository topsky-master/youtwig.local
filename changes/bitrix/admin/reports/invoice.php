<?php die(); 
/*

Скопируйте этот файл в папку /bitrix/admin/reports и измените по своему усмотрению



$ORDER_ID - ID текущего заказа



$arOrder - массив атрибутов заказа (ID, доставка, стоимость, дата создания и т.д.)

Следующий PHP код:

print_r($arOrder);

выведет на экран содержимое массива $arOrder.



$arOrderProps - массив свойств заказа (вводятся покупателями при оформлении заказа) следующей структуры:

array(

    "мнемонический код (или ID если мнемонический код пуст) свойства" => "значение свойства"

    )



$arParams - массив из настроек Печатных форм



$arUser - массив из настроек пользователя, совершившего заказ

*/

?><!DOCTYPE>

<html>

<head>

    <meta http-equiv=Content-Type content="text/html; charset=<?=LANG_CHARSET?>" />

    <title langs="ru">

        Счет TWiG

    </title>

    <style type="text/css">

        @import url(http://fonts.googleapis.com/css?family=PT+Serif&subset=cyrillic-ext,latin);

        html, body, div, span, applet, object, iframe,

        h1, h2, h3, h4, h5, h6, p, blockquote, pre,

        a, abbr, acronym, address, big, cite, code,

        del, dfn, em, img, ins, kbd, q, s, samp,

        small, strike, strong, sub, sup, tt, var,

        b, u, i, center,

        dl, dt, dd, ol, ul, li,

        fieldset, form, label, legend,

        table, caption, tbody, tfoot, thead, tr, th, td,

        article, aside, canvas, details, embed,

        figure, figcaption, footer, header, hgroup,

        menu, nav, output, ruby, section, summary,

        time, mark, audio, video {

        	margin: 0;

        	padding: 0;

        	border: 0;

        	font-size: 100%;

        	font: inherit;

        	vertical-align: baseline;

        }

        /* HTML5 display-role reset for older browsers */

        article, aside, details, figcaption, figure,

        footer, header, hgroup, menu, nav, section {

        	display: block;

        }

        body {

        	line-height: 1;

        }

        ol, ul {

        	list-style: none;

        }

        blockquote, q {

        	quotes: none;

        }

        blockquote:before, blockquote:after,

        q:before, q:after {

        	content: '';

        	content: none;

        }

        table, table td, table tr {

        	border-collapse: collapse;

        	border-spacing: 0;

        	padding: 0;

        	margin: 0;

        }

        body{

        	font: normal normal 14px/1.35 Arial, sans-serif;

            text-align: center;

            background: #fff;

        }

 		#page-area{

            padding: 40px 0;

 			margin: 0 auto;

 			width: 800px;

 			text-align: left;

    	}

    	.pull-left{

    		float: left;

        }

    	.pull-right{

            float: right;

    	}

        .padding{

        	padding: 0 40px;

 			float: none;

 			clear: both;

    	}

    	.top{

            font-size: 27px;

    		font-weight: bold;

    		margin-bottom: 35px;

    	}

    	table.logo-area{

            width: 100%;

    		margin-bottom: 35px;

    	}

    	table.logo-area tr,

    	table.logo-area td{

            padding: 0;

    		margin: 0;

    		vertical-align: middle;

    		overflow: hidden;

    		background-color: #d0d0d0;

    	}

    	table.logo-area td.padding-left{

            width: 40px;

    		background-color: #d0d0d0!important;

        }

        table.logo-area tr td.center{

            padding: 0 45px;

            overflow: hidden;

            position: relative;

            width: 154px;

        }

        table.logo-area td.center:before,

        table.logo-area td.center:after{

            position: absolute;

            left: 0;

            top: 0;

            content: " ";

            display: block;

            visibility: visible;

            height: 60px;

            width: 60px;

            margin-left: -30px;

            background-color: #d0d0d0!important;

            border-radius: 60px;

            -moz-border-radius: 60px;

            -webkit-border-radius: 60px;

            border: none;

            outline: 0;

        }

        table.logo-area td.center:after{

            margin-right: -30px;

            right: 0;

            left: auto;

            margin-left: 0;

        }

        table.logo-area,

        table.logo-area td,

        table.logo-area tr{

            border: none!important;

            outline: 0;

            padding: 0;

            margin: 0;

            height: 60px;

            overflow: hidden;

        }

        table.logo-area tr td.center{

            background-color: #ffffff!important;

        }

        table.logo-area td.center img{

            height: 60px;

            width: auto;

            background-color: #ffffff!important;

        }

        table.logo-area td.padding-right{

            background-color: #d0d0d0!important;

            font-size: 16px;

            font-style: italic;

            white-space: nowrap;

            vertical-align: middle;

        }

        table.logo-area td.padding-right span{

            position: relative;

            white-space: nowrap;

            line-height: 1!important;

        }

        table.logo-area td.padding-right strong{

            font-weight: bold;

        }

    	.buyer-title{

            font-weight: bold;

    		margin-bottom: 30px;

    	}

    	.order-number-area{

            margin: 30px 0 25px 0;

    	}

    	.order-number-area strong{

            font-weight: bold;

    	}

    	.product-table{

            width: 100%;

    		margin-bottom: 30px;

    	}

    	.product-table,

    	.product-table td{ border: 1px solid #000;}

    	.product-table td{

            padding: 5px 8px;

    	}

    	.product-table .all-cost-label{

            text-align: right;

    		font-weight: bold;

    	}

    	.product-table .all-cost-value{

            font-weight: bold;

    	}

    	.product-table td.number{

            text-align: center;

    	}

    	.all-cost-area strong{

            font-weight: bold;

    	}

    	.all-cost-area{

            margin-bottom: 40px;

    	}

    	.signs-area span{

            padding-right: 25px;

    	}

    	.signs-area div{

            margin-bottom: 40px;

    	}

    	.comment2-area{

            margin-bottom: 25px;

    	}

    	.bottom-area div{

            margin-top: 80px;

    		margin-bottom: 10px;

    	}

    	.bottom-area .comment4-area{

            text-align: center;

    	}

    	.bottom-area .comment4-area strong{

            font-weight: bold;

    	}

    	.bottom-area .comment4-area span{

            line-height: 1.5;

    		background: #d0d0d0;

    		padding: 0 15px;

    	}

    	.phone-url-area .site-url,

    	.phone-url-area .bottom-phone{

            line-height: 30px;

    		height: 30px;

    		font-weight: bold;

    		font-size: 16px;

    	}



    	.phone-url-area .site-url img,

    	.phone-url-area .bottom-phone img{

            padding: 0;

    		margin-right: 10px;

    		display: block;

    		float: left;

    	}



        .phone-url-area .site-url{



    	}

    	.buyer-area{

            margin-top: 35px;

    	}

    	#logo-area{

            height: 60px!important;

    		overflow: hidden!important;

    	}



        .comment2-area,

        .comment3-area{

            font-size: 11px;

        }



        @media screen and (-ms-high-contrast: active), (-ms-high-contrast: none) {

           table.logo-area tr td.center{

                overflow: visible!important;

            }

            table.logo-area td.padding-right span{

                z-index: 2!important;

                position: relative;

            }

        }



    </style>

    <!--[if IE]>

    <style type="text/css">

    table.logo-area tr td.center{

        overflow: visible!important;

    }

    table.logo-area td.padding-right span{

        z-index: 2!important;

        position: relative;

    }

    </style>

    <![endif]-->



</head>

<body>

    <div id="page-area">

        <div class="top padding">

            <?php echo $arParams["~USER_TITLE_AT_TOP"]; ?>

            <div class="pull-right phone">

                <?php echo $arParams["~PHONE"]; ?>

            </div>

        </div>

        <div id="logo-area">

            <table class="logo-area gray">

                <tr>

                    <td class="padding-left">

                    </td>

                    <td class="center">

                        <img src="http://youtwig.ru/upload/img/yotwig-logo-print.png" />

                    </td>

                    <td class="padding-right store-address">

                        <span>

                            <?php echo $arParams["~USER_WHERE_THE_STORE_IS"]; ?>

                        </span>

                    </td>

                </tr>

            </table>

        </div>

        <div class="padding buyer-area">

            <p class="buyer-title">ПОКУПАТЕЛЬ:</p>

            <p class="buyer-name"><?php echo $arParams["BUYER_LAST_NAME"]; ?> <?php echo $arParams["BUYER_FIRST_NAME"]?>  <?php echo $arParams["BUYER_SECOND_NAME"]; ?></p>

            <p class="buyer-address">Адрес: <?php echo $arParams["BUYER_ADDRESS"]; ?></p>

            <p class="buyer-phone">Телефон: <?php echo $arOrderProps["PHONE"];?></p>

            <?php if(isset($arOrder['USER_DESCRIPTION']) && !empty($arOrder['USER_DESCRIPTION'])): ?>

            <p class="buyer-comments"><?php echo $arOrder['USER_DESCRIPTION']; ?></p>

            <?php endif; ?>

        </div>

        <div class="padding order-number-area">

            <p class="order-number">

                <strong>ЗАКАЗ №:</strong>

                <?echo $ORDER_ID?> от <?echo $arOrder["DATE_INSERT_FORMAT"]?>

            </p>

        </div>

        <?

            $priceTotal 		= 0;

            $bUseVat 			= false;

            $arBasketOrder 		= array();

            for ($i = 0; $i < count($arBasketIDs); $i++)

            {

                $arBasketTmp 	= CSaleBasket::GetByID($arBasketIDs[$i]);



                $priceTotal += $arBasketTmp["PRICE"]*$arBasketTmp["QUANTITY"];



                $arBasketTmp["PROPS"] = array();

                if (isset($_GET["PROPS_ENABLE"]) && $_GET["PROPS_ENABLE"] == "Y")

                {

                    $dbBasketProps = CSaleBasket::GetPropsList(

                            array("SORT" => "ASC", "NAME" => "ASC"),

                            array("BASKET_ID" => $arBasketTmp["ID"]),

                            false,

                            false,

                            array("ID", "BASKET_ID", "NAME", "VALUE", "CODE", "SORT")

                        );

                    while ($arBasketProps = $dbBasketProps->GetNext())

                        $arBasketTmp["PROPS"][$arBasketProps["ID"]] = $arBasketProps;

                }



                $arBasketOrder[] = $arBasketTmp;

            }



            //разбрасываем скидку на заказ по товарам

            if (floatval($arOrder["DISCOUNT_VALUE"]) > 0)

            {

                $arBasketOrder = GetUniformDestribution($arBasketOrder, $arOrder["DISCOUNT_VALUE"], $priceTotal);

            }



            //состав заказа

            ClearVars("b_");

            //$db_basket = CSaleBasket::GetList(($b="NAME"), ($o="ASC"), array("ORDER_ID"=>$ORDER_ID));

            //if ($db_basket->ExtractFields("b_")):

        ?>

        <div class="product-table-area padding">

            <table class="product-table">

                <tr>

                    <td class="number">№</td>

                    <td class="product-name">Наименование товара</td>

                    <td class="product-number">Кол-во</td>

                    <td class="product-number">Цена (руб.)</td>

                    <td class="product-cost">Сумма (руб.)</td>

                </tr>

                <?

                $n 							= 1;

                $sum 						= 0.00;

                $arTax 						= array("VAT_RATE" => 0, "TAX_RATE" => 0);

                $mi 						= 0;

                $total_sum 					= 0;



                foreach ($arBasketOrder as $arBasket)

                {

                    $nds_val 				= 0;

                    $taxRate 				= 0;



                    if (floatval($arQuantities[$mi]) <= 0)

                        $arQuantities[$mi] 	= DoubleVal($arBasket["QUANTITY"]);



                    $b_AMOUNT 				= DoubleVal($arBasket["PRICE"]);

                    $item_price 			= $b_AMOUNT;



                    ?>

                    <tr valign="top">

                        <td class="number">

                            <?echo $n++ ?>

                        </td>

                        <td class="product-name">

                            <?echo $arBasket["NAME"]; ?>

                        </td>

                        <td class="product-quantity">

                            <?echo $arQuantities[$mi]; ?>

                        </td>

                        <td class="product-price">

                            <?echo number_format($arBasket["PRICE"], 2, ',', ' ') ?>

                        </td>

                        <td class="product-cost">

                            <?echo number_format(($arBasket["PRICE"])*$arQuantities[$mi], 2, ',', ' ') ?>

                        </td>

                    </tr>

                    <?

                    $total_sum += $arBasket["PRICE"]*$arQuantities[$mi];

                    $mi++;

                }//endforeach

                ?>

                <?if (DoubleVal($arOrder["PRICE_DELIVERY"])>0):?>

                    <tr>

                        <td class="delivery number">

                            <?echo $n++?>

                        </td>

                        <td class="delivery product-quantity">

                            Доставка <?

                            $arDelivery_tmp 			= CSaleDelivery::GetByID($arOrder["DELIVERY_ID"]);

                            if (strlen($arDelivery_tmp["NAME"]) > 0)

                            {

                                echo "(".$arDelivery_tmp["NAME"].")";

                            }



                            $basket_tax 				= CSaleOrderTax::CountTaxes(DoubleVal($arOrder["PRICE_DELIVERY"]), $arTaxList, $arOrder["CURRENCY"]);

                            $nds_val 					= 0;

                            $item_price 				= DoubleVal($arOrder["PRICE_DELIVERY"]);

                            $total_sum 					+= $item_price

                            ?>

                        </td>

                        <td class="delivery product-number">

                            1

                        </td>

                        <td class="delivery product-price">

                            <?echo number_format($arOrder["PRICE_DELIVERY"], 2, ',', ' ') ?>

                        </td>

                        <td class="delivery product-cost">

                            <?echo number_format($arOrder["PRICE_DELIVERY"], 2, ',', ' ') ?>

                        </td>

                    </tr>

                <?endif?>

                <tr>

                    <td class="all-cost-label" colspan="4">Итого:</td>

                    <td class="all-cost-value"><?echo number_format($total_sum, 2, ',', ' ') ?></td>

                </tr>

            </table>

        </div>

    <?//endif?>

        <div class="all-cost-area padding">

            <p><strong>Итого к оплате:</strong> <? if ($arOrder["CURRENCY"]=="RUR" || $arOrder["CURRENCY"]=="RUB") { echo Number2Word_Rus($arOrder["PRICE"]);}

            else { echo SaleFormatCurrency($arOrder["PRICE"], $arOrder["CURRENCY"]); } ?>.</p>

        </div>

        <div class="signs-area padding">

            <div class="half pull-left seller-sign">

                <span>Продавец:</span>

                _____________________________

            </div>

            <div class="half pull-right buyer-sign">

                <span>Покупатель:</span>

                _____________________________

            </div>

        </div>



        <?php if(isset($arParams["~USER_COMMENT_1"]) && !empty($arParams["~USER_COMMENT_1"])): ?>

        <div class="comment2-area padding">

            <?php echo $arParams["~USER_COMMENT_1"]; ?>

        </div>

        <?php endif; ?>



        <?php if(isset($arParams["~USER_COMMENT_2"]) && !empty($arParams["~USER_COMMENT_2"])): ?>

        <div class="comment3-area padding">

            <?php echo $arParams["~USER_COMMENT_2"]; ?>

        </div>

        <?php endif; ?>



        <div class="bottom-area padding">

            <?php if(isset($arParams["~USER_COMMENT_3"]) && !empty($arParams["~USER_COMMENT_3"])): ?>

            <div class="comment4-area pull-left">

                <?php echo $arParams["~USER_COMMENT_3"]; ?>

            </div>

            <?php endif; ?>

            <div class="phone-url-area pull-right">

                <p class="bottom-phone">

                    <img src="http://youtwig.ru/upload/img/yotwig-bottom-phone.png" />

                    <?php echo $arParams["~PHONE"]; ?>

                </p>

                <p class="site-url">

                    <img src="http://youtwig.ru/upload/img/youtwig-phone-bottom.png" />

                    <?php echo $_SERVER['HTTP_HOST'];?>

                </p>

            </div>

        </div>

    </div>

</body>

</html>