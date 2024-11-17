<?
use Bitrix\Main\Localization\Loc;
use Bitrix\Sale\Payment;
use Bitrix\Sale\PriceMaths;

Loc::loadMessages(__FILE__);

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$ORDER_ID = (int)($_REQUEST["ORDER_ID"]);

if(!empty($ORDER_ID) && $ORDER_ID > 0){

    CModule::IncludeModule("sale");

    global $APPLICATION;

    if (array_key_exists('PAYMENT_SHOULD_PAY', $params))
        $params['PAYMENT_SHOULD_PAY'] = PriceMaths::roundByFormatCurrency($params['PAYMENT_SHOULD_PAY'], $params['PAYMENT_CURRENCY']);

    $atOrderPage = $APPLICATION->GetCurPage(false) == '/personal/order/payment/' ? true : false;

    error_reporting(0);
    ini_set('display_errors',0);

    if ($_REQUEST["DOWNLOAD"] == "Y" || $atOrderPage)
        $APPLICATION->RestartBuffer();

    $arOrder = CSaleOrder::GetByID($ORDER_ID);
    $dbOrder = CSaleOrder::GetList(
        array("DATE_UPDATE" => "DESC"),
        array(
            "LID" => SITE_ID,
            "ID" => $ORDER_ID
        )
    );
    $arOrder = $dbOrder->GetNext();

    CSalePaySystemAction::InitParamArrays($arOrder);

//проверяем наличие класса
    if (!CSalePdf::isPdfAvailable())
        die();


    $pdf = new CSalePdf("P", "pt", "A4");
    $pdf->AddFont("Font", "", "pt_sans-regular.ttf", true);
    $pdf->AddFont("Font", "B", "pt_sans-bold.ttf", true);
    $pdf->AddFont("Font", "I", "pt_sans-italic.ttf", true);
    $fontFamily = "Font";
    $fontSize   = 9;

    $pdf->SetDisplayMode(100, 'continuous');


    $pdf->AddPage();

    $lh15 = 15;
    $lh13 = 13;
    $lh10 = 10;

    $mcol_1 =160;
    $mcol_2 =340;
    $mcol_2_1 =365;

    $mcol_sep =20;
    $mcol_sep2 =40;
    $mcol_4 = 120;
    $mcol_5 = 180;
    $mcol_6 = 210;
    $mcol_7 = 110;
    $mcol_8 = 160;
    $mcol_9 = 90;
    $mcol_10 = 250;


    $pdf->Line(30,30,560,30);   //top line
    $pdf->Line(30,30,30,525);   //left line
    $pdf->Line(200,30,200,525); //left second line
    $pdf->Line(30,268,560,268); //top second line
    $pdf->Line(560,30,560,525); //right line
    $pdf->Line(30,525,560,525); //bottom line

    for($z0;$z<2;++$z)
    {
        //line 1 ******************
        $pdf->SetFont($fontFamily, "B", $fontSize-1);
        $pdf->Cell($mcol_1,$lh15,CSalePdf::prepareToPdf(Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_SBERBANK_TITLE')), 0, 0, "C");

        $pdf->SetFont($fontFamily, "I", $fontSize-1);
        $pdf->Cell($mcol_2_1,$lh15,CSalePdf::prepareToPdf(Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_SBERBANK_FORM_TITLE')), 0, 0, "R");
        $pdf->Ln();

        //line 2 ******************
        $pdf->SetFont($fontFamily,"",$fontSize);
        $pdf->Cell($mcol_1,$lh13,"",0,0,"C");

        $pdf->SetFont($fontFamily, "", $fontSize-1);
        $pdf->Cell($mcol_sep,$lh13,"", 0, 0, "L");
        $pdf->Cell($mcol_2,13,CSalePdf::prepareToPdf($params["SELLER_COMPANY_NAME"]), 0, 0, "L");
        $pdf->Ln();

        $gX=$pdf->GetX();
        $gY=$pdf->GetY();
        $pdf->Line($gX+$mcol_sep+$mcol_1 + 3,$gY,$gX+$mcol_1+$mcol_sep+$mcol_2,$gY);

        //new line *****************
        $pdf->SetFont($fontFamily, "", $fontSize-3);
        $pdf->Cell($mcol_1,$lh10,"",0,0,"C");
        $pdf->Cell($mcol_2,$lh10,CSalePdf::prepareToPdf(Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_SBERBANK_COMPANY_NAME')), 0, 0, "C");;
        $pdf->Ln();

        //new line *****************
        $pdf->SetFont($fontFamily, "", $fontSize);
        $pdf->Cell($mcol_1,$lh13,"",0,0,"");
        $pdf->Cell($mcol_sep,$lh13,"",0,0,"");

        $pdf->Cell($mcol_4,$lh13,CSalePdf::prepareToPdf(($params["SELLER_COMPANY_INN"])."/".($params["SELLER_COMPANY_KPP"])),0,0,"L");
        $pdf->Cell($mcol_sep2,$lh13,"",0,0,"");
        $pdf->Cell($mcol_5,$lh13,CSalePdf::prepareToPdf($params["SELLER_COMPANY_BANK_ACCOUNT"]),0,0,"");
        $pdf->Ln();
        $gX=$pdf->GetX();
        $gY=$pdf->GetY();
        $pdf->Line($gX+$mcol_sep+$mcol_1 + 3,$gY,$gX+$mcol_sep+$mcol_1+$mcol_4,$gY);
        $pdf->Line($gX+$mcol_sep+$mcol_1+$mcol_4+$mcol_sep2 + 3,$gY,$gX+$mcol_sep+$mcol_1+$mcol_4+$mcol_sep2+$mcol_5,$gY);


        //new line *****************
        $pdf->SetFont($fontFamily, "", $fontSize-3);
        $pdf->Cell($mcol_1,$lh10,"",0,0,"");
        $pdf->Cell($mcol_sep,$lh10,"",0,0,"");
        $pdf->Cell($mcol_4,$lh10,CSalePdf::prepareToPdf(Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_SBERBANK_INN')),0,0,"C");
        $pdf->Cell($mcol_sep2,10,"",0,0,"");
        $pdf->Cell($mcol_5,$lh10,CSalePdf::prepareToPdf(Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_SBERBANK_SETTLEMENT_ACC')),0,0,"C");
        $pdf->Ln();

        //new line *****************
        $pdf->SetFont($fontFamily, "", $fontSize);
        $pdf->Cell($mcol_1,$lh13,"",0,0,"");
        $pdf->Cell($mcol_sep,$lh13,"",0,0,"");
        $pdf->Cell($mcol_6,$lh13,CSalePdf::prepareToPdf(Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_SBERBANK_IN')." ".$params["SELLER_COMPANY_BANK_NAME"]),0,0,"L");
        $pdf->Cell($mcol_sep,$lh13,"",0,0,"");
        $pdf->Cell($mcol_7,$lh13,CSalePdf::prepareToPdf(Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_SBERBANK_BANK_BIC')." ".$params["SELLER_COMPANY_BANK_BIC"]),0,0,"");
        $pdf->Ln();
        $gX=$pdf->GetX();
        $gY=$pdf->GetY();
        $pdf->Line($gX+$mcol_sep+$mcol_1 + 10,$gY,$gX+$mcol_sep+$mcol_1+$mcol_6,$gY);
        $pdf->Line($gX+$mcol_sep+$mcol_1+$mcol_6+$mcol_sep + 23,$gY,$gX+$mcol_sep+$mcol_1+$mcol_6+$mcol_sep+$mcol_7,$gY);

        //new line *****************
        $pdf->SetFont($fontFamily, "", $fontSize-3);
        $pdf->Cell($mcol_1,$lh10,"",0,0,"");
        $pdf->Cell($mcol_sep,$lh10,"",0,0,"");
        $pdf->Cell($mcol_6,$lh10,CSalePdf::prepareToPdf(Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_SBERBANK_BANK_NAME')),0,0,"C");
        $pdf->Cell($mcol_sep2,10,"",0,0,"");
        $pdf->Cell($mcol_5,$lh10,"",0,0,"C");
        $pdf->Ln();

        //new line *****************
        $pdf->SetFont($fontFamily, "", $fontSize);
        $pdf->Cell($mcol_1,$lh15,"",0,0,"");
        $pdf->Cell($mcol_sep,$lh15,"",0,0,"");
        $pdf->Cell($mcol_2,$lh15,CSalePdf::prepareToPdf(Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_SBERBANK_BANK_COR_ACC')."   ".$params["SELLER_COMPANY_BANK_ACCOUNT_CORR"]),0,0,"L");
        $pdf->Ln();
        $gX=$pdf->GetX();
        $gY=$pdf->GetY();
        $pdf->Line($gX+$mcol_sep+$mcol_1+$mcol_5 - 5,$gY,$gX+$mcol_sep+$mcol_1+$mcol_6+$mcol_sep+$mcol_7,$gY);

        //new line *****************
        $pdf->SetFont($fontFamily, "", $fontSize);
        $pdf->Cell($mcol_1,$lh13,"",0,0,"");
        $pdf->Cell($mcol_sep,$lh13,"",0,0,"");
        $pdf->Cell($mcol_8,$lh13,CSalePdf::prepareToPdf(Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_SBERBANK_ORDER_ID')." ".$ORDER_ID." ".Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_SBERBANK_ORDER_FROM')." ".($params["PAYMENT_DATE_INSERT"] ? $params["PAYMENT_DATE_INSERT"] : (isset($arOrder["DATE_INSERT"]) && !empty($arOrder["DATE_INSERT"]) ? ConvertDateTime($arOrder["DATE_INSERT"], "DD.MM.YYYY") : ''))),0,0,"L");
        $pdf->Ln();
        $gX=$pdf->GetX();
        $gY=$pdf->GetY();
        $pdf->Line($gX+$mcol_sep+$mcol_1 + 3,$gY,$gX+$mcol_sep+$mcol_1+$mcol_8,$gY);
        $pdf->Line($gX+$mcol_sep+$mcol_1+$mcol_8+$mcol_sep - 5,$gY,$gX+$mcol_sep+$mcol_1+$mcol_8+$mcol_sep+$mcol_8,$gY);

        //new line *****************
        $pdf->SetFont($fontFamily, "", $fontSize-3);
        $pdf->Cell($mcol_1,$lh10,"",0,0,"");
        $pdf->Cell($mcol_sep,$lh10,"",0,0,"");

        $pdf->Cell($mcol_8,$lh10,CSalePdf::prepareToPdf(Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_SBERBANK_PAYMENT_NAME')),0,0,"C");
        $pdf->Cell($mcol_sep,10,"",0,0,"");
        $pdf->Cell($mcol_8,$lh10,CSalePdf::prepareToPdf(Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_SBERBANK_PAYMENT_ACC')),0,0,"C");
        $pdf->Ln();


        //new line *****************
        $pdf->SetFont($fontFamily, "", $fontSize);
        $pdf->Cell($mcol_1,$lh15,"",0,0,"");
        $pdf->Cell($mcol_sep,$lh15,"",0,0,"");
        $pdf->Cell($mcol_2,$lh15,CSalePdf::prepareToPdf(Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_SBERBANK_PAYER_FIO')."   ".$params["BUYER_PERSON_FIO"]),0,0,"L");
        $pdf->Ln();
        $gX=$pdf->GetX();
        $gY=$pdf->GetY();
        $pdf->Line($gX+$mcol_sep+$mcol_1+$mcol_7 - 20,$gY,$gX+$mcol_sep+$mcol_1+$mcol_6+$mcol_sep+$mcol_7,$gY);
        $pdf->Ln(10);

        //new line *****************
        $pdf->SetFont($fontFamily, "", $fontSize);
        $pdf->Cell($mcol_1,$lh13,"",0,0,"");
        $pdf->Cell($mcol_sep,$lh13,"",0,0,"");
        $pdf->Cell($mcol_9,$lh10,CSalePdf::prepareToPdf(Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_SBERBANK_PAYER_ADDRESS')),0,0,"L");
        /***************/
        $sAddrFact = "";
        ($params["BUYER_PERSON_ZIP"]);
        if(mb_strlen($params["BUYER_PERSON_ZIP"])>0)
            $sAddrFact .= ($sAddrFact<>""? ", ":"").($params["BUYER_PERSON_ZIP"]);
        if(mb_strlen($params["BUYER_PERSON_COUNTRY"])>0)
            $sAddrFact .= ($sAddrFact<>""? ", ":"").($params["BUYER_PERSON_COUNTRY"]);
        if(mb_strlen($params["BUYER_PERSON_REGION"])>0)
            $sAddrFact .= ($sAddrFact<>""? ", ":"").($params["BUYER_PERSON_REGION"]);
        if(mb_strlen($params["BUYER_PERSON_CITY"])>0)
        {
            $g = mb_substr($params["BUYER_PERSON_CITY"], 0, 2);
            $sAddrFact .= ($sAddrFact<>""? ", ":"").($g<>"г." && $g<>"Г."? Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_SBERBANK_SHORT_YEAR')." ":"").($params["BUYER_PERSON_CITY"]);
        }
        if(mb_strlen($params["BUYER_PERSON_ADDRESS_FACT"])>0)
            $sAddrFact .= ($sAddrFact<>""? ", ":"").($params["BUYER_PERSON_ADDRESS_FACT"]);
        /**************/
        $pdf->MultiCell($mcol_10,$lh10,"",0,"L");
        $gY=$pdf->GetY();
        $gX=$pdf->GetX();
        $pdf->Ln();
        $pdf->Line($gX+$mcol_sep+$mcol_1+$mcol_9 - 5,$gY,$gX+$mcol_sep+$mcol_1+$mcol_4+$mcol_sep2+$mcol_5,$gY);


        //new line *****************
        $pdf->SetFont($fontFamily, "", $fontSize);
        $pdf->Cell($mcol_1,$lh13,"",0,0,"");
        $pdf->Cell($mcol_sep,$lh13,"",0,0,"");
        /****************/
        if(mb_strpos($params["PAYMENT_SHOULD_PAY"], ".")!==false)
            $a = explode(".", ($params["PAYMENT_SHOULD_PAY"]));
        else
            $a = explode(",", ($params["PAYMENT_SHOULD_PAY"]));

        if ($a[1] <= 9 && $a[1] > 0)
            $a[1] = $a[1]."0";
        elseif ($a[1] == 0)
            $a[1] = "00";
        /****************/

        if(mb_strlen($a[0]) < 5){
            $a[0] = str_pad($a[0], 5);
        }

        $pdf->Cell($mcol_8,$lh13,CSalePdf::prepareToPdf(Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_SBERBANK_SHOULD_PAY')."  ".$a[0]."  ".Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_SBERBANK_SHORT_RUB')."  ".$a[1]."  ".Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_SBERBANK_SHORT_COP')."                  ".Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_SBERBANK_ADD_SUM')."        ".Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_SBERBANK_SHORT_RUB')."      ".Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_SBERBANK_SHORT_COP').""),0,0,"L");
        $pdf->Ln();
        $gX=$pdf->GetX();
        $gY=$pdf->GetY();
        $pdf->Line($gX+$mcol_sep+$mcol_1+65,$gY,$gX+$mcol_sep+$mcol_1+90,$gY);
        $pdf->Line($gX+$mcol_sep+$mcol_1+112,$gY,$gX+$mcol_sep+$mcol_1+128,$gY);
        $pdf->Line($gX+$mcol_sep+$mcol_1+278,$gY,$gX+$mcol_sep+$mcol_1+296,$gY);
        $pdf->Line($gX+$mcol_sep+$mcol_1+313,$gY,$gX+$mcol_sep+$mcol_1+327,$gY);
        $pdf->Ln(5);

        //new line *****************
        $pdf->SetFont($fontFamily, "B", $fontSize);
        $pdf->Cell($mcol_1,$lh13,CSalePdf::prepareToPdf("Кассир"),0,0,"C");
        $pdf->SetFont($fontFamily, "", $fontSize);
        $pdf->Cell($mcol_sep,$lh13,"",0,0,"");
        $pdf->Cell($mcol_8,$lh13,CSalePdf::prepareToPdf(Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_SBERBANK_FINAL_SUM')."             ".Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_SBERBANK_SHORT_RUB')."        ".Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_SBERBANK_SHORT_COP').""),0,0,"L");
        $pdf->Cell($mcol_5+5,$lh13,CSalePdf::prepareToPdf(Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_SBERBANK_DATE')."                           ".Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_SBERBANK_YEAR')."     ".Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_SBERBANK_SHORT_YEAR').""),0,0,"R");
        $pdf->Ln();
        $gX=$pdf->GetX();
        $gY=$pdf->GetY();
        $pdf->Line($gX+$mcol_sep+$mcol_1+26,$gY,$gX+$mcol_sep+$mcol_1+55,$gY);
        $pdf->Line($gX+$mcol_sep+$mcol_1+72,$gY,$gX+$mcol_sep+$mcol_1+90,$gY);
        $pdf->Line($gX+$mcol_sep+$mcol_1+222,$gY,$gX+$mcol_sep+$mcol_1+245,$gY);
        $pdf->Line($gX+$mcol_sep+$mcol_1+255,$gY,$gX+$mcol_sep+$mcol_1+310,$gY);
        $pdf->Line($gX+$mcol_sep+$mcol_1+325,$gY,$gX+$mcol_sep+$mcol_1+336,$gY);
        $pdf->Ln(5);

        //new line *****************
        $pdf->SetFont($fontFamily, "", $fontSize-2);
        $pdf->Cell($mcol_1,$lh13,"",0,0,"");
        $pdf->Cell($mcol_sep,$lh13,"",0,0,"");
        $pdf->MultiCell($mcol_2,$lh10,CSalePdf::prepareToPdf(Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_SBERBANK_CONFIRM')),0,"L");


        //new line *****************
        $pdf->SetFont($fontFamily, "B", $fontSize);
        $pdf->Cell($mcol_1,$lh13,"",0,0,"");
        $pdf->Cell($mcol_5,$lh13,"",0,0,"");
        $pdf->Cell($mcol_2,$lh10,CSalePdf::prepareToPdf(Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_SBERBANK_SIGNATURE')),0,0,"L");
        $pdf->Ln();
        $gX=$pdf->GetX();
        $gY=$pdf->GetY();
        $pdf->Line($gX+$mcol_sep+$mcol_1+255,$gY,$gX+$mcol_sep+$mcol_1+340,$gY);

        $pdf->Ln(5);
    }

    $pdf->Ln(30);
    $pdf->SetFont($fontFamily, "B", $fontSize+3);
    $pdf->Cell(0,20,CSalePdf::prepareToPdf(Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_SBERBANK_WARNING')),0,0,"L");
    $pdf->Ln(25);

    $pdf->Cell(0,20,CSalePdf::prepareToPdf(Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_SBERBANK_PAYMENT_METHOD')),0,0,"L");
    $pdf->Ln(20);
    $pdf->SetFont($fontFamily,"",$fontSize);
    $pdf->MultiCell(0,12,CSalePdf::prepareToPdf(Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_SBERBANK_PAYMENT_STRING_1')),0,"L");
    $pdf->MultiCell(0,12,CSalePdf::prepareToPdf(Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_SBERBANK_PAYMENT_STRING_2')),0,"L");
    $pdf->MultiCell(0,12,CSalePdf::prepareToPdf(Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_SBERBANK_PAYMENT_STRING_3')),0,"L");
    $pdf->MultiCell(0,12,CSalePdf::prepareToPdf(Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_SBERBANK_PAYMENT_STRING_4')),0,"L");
    $pdf->MultiCell(0,12,CSalePdf::prepareToPdf(Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_SBERBANK_PAYMENT_STRING_5')),0,"L");

    $pdf->SetFont($fontFamily, "B", $fontSize+3);
    $pdf->Cell(0,16,CSalePdf::prepareToPdf(Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_SBERBANK_CONDITIONS_OF_DELIVERY')),0,0,"L");
    $pdf->Ln(20);
    $pdf->SetFont($fontFamily,"",$fontSize);
    $pdf->MultiCell(0,12,CSalePdf::prepareToPdf(Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_SBERBANK_DELIVERY_STRING_1')),0,"L");
    $pdf->MultiCell(0,12,CSalePdf::prepareToPdf(Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_SBERBANK_DELIVERY_STRING_2')),0,"L");
    $pdf->Ln();
    $pdf->SetFont($fontFamily,"B",$fontSize);
    $pdf->Cell(0,12,CSalePdf::prepareToPdf(Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_SBERBANK_NOTE')),0,0,"L");
    $pdf->Ln();
    $pdf->SetFont($fontFamily,"",$fontSize);
    $pdf->MultiCell(0,12,CSalePdf::prepareToPdf($params["SELLER_COMPANY_NAME"]." ".Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_SBERBANK_NOTE_DESCRIPTION')),0,"L");


    $dest = "I";

    $filename = "sberbank_".$ORDER_ID.".pdf";

    if ($_REQUEST["GET_CONTENT"] == "Y") {
        $dest = "S";
    } else if ($_REQUEST["DOWNLOAD"] == "Y") {
        $dest = "D";
    } else if ($_REQUEST["SAVE"] == "Y") {
        $dest = "F";
        $filename = $_SERVER['DOCUMENT_ROOT']."/upload/sale/paysystem/".$filename;
    }

    if(file_exists($_SERVER['DOCUMENT_ROOT']."/upload/sale/paysystem/".$filename)
        && is_readable($_SERVER['DOCUMENT_ROOT']."/upload/sale/paysystem/".$filename)
        && $dest == "I"
        && $atOrderPage){

        echo '<html><body style="margin:0px;padding:0px;overflow:hidden"><iframe  src="/upload/sale/paysystem/'.$filename.'" frameborder="0" style="overflow:hidden;overflow-x:hidden;overflow-y:hidden;height:100%;width:100%;position:absolute;top:0px;left:0px;right:0px;bottom:0px" height="100%" width="100%"></iframe></body></html>';
        die();

    } else {


        $pdf->Output(
            CSalePdf::prepareToPdf(
                $filename
            ),
            $dest
        );

    }


    if ($_REQUEST["DOWNLOAD"] == "Y"  || $atOrderPage || $dest == "I")
        die();

};