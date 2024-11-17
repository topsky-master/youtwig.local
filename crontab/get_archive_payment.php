<?php

$_SERVER["DOCUMENT_ROOT"] = "/var/www/twig/data/www/youtwig.ru/";

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS",true);
define("BX_CAT_CRON", true);
define('NO_AGENT_CHECK', true);
set_time_limit (0);
define("LANG","s1");
define('SITE_ID', 's1');

if ($argc > 0 && $argv[0]) {
    $_REQUEST['intestwetrust'] = 1;
}

if(!isset($_REQUEST['intestwetrust'])) die();

require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

class impelGetArchivePaymentInfo{

    private static $countStrings = 200;
    private static $iblockId = 0;

    public static function getList($paymentId){

        $paymentId = trim($paymentId);

        $connection = Bitrix\Main\Application::getConnection();
        $sqlHelper = $connection->getSqlHelper();

        $sql = "SELECT ORDER_ARCHIVE_ID,ORDER_DATA
                  FROM b_sale_order_archive_packed
                  WHERE ORDER_DATA LIKE '%\"".$sqlHelper->forSql($paymentId)."\"%'";

        $recordset = $connection->query($sql);

        if($recordset)
            while ($record = $recordset->fetch()) {

                $sOrdData = $record['ORDER_DATA'];
                $oOrdData = unserialize($sOrdData);

                if(isset($oOrdData['PAYMENT'])
                    && isset($oOrdData['PAYMENT'][$paymentId])
                    && isset($oOrdData['ORDER'])
                    && isset($oOrdData['ORDER']['ID'])
                ){

                    echo 'ID заказа: '. $oOrdData['ORDER']['ID']."<br />";

                    if(isset($oOrdData['PAYMENT'][$paymentId]['PS_STATUS_DESCRIPTION']))
                    echo 'Информация об оплате: '. $oOrdData['PAYMENT'][$paymentId]['PS_STATUS_DESCRIPTION']."<br />";


                }




            }

    }


}

if(CModule::IncludeModule("iblock")) {

    impelGetArchivePaymentInfo::getList($_REQUEST['payment_id']);
    //impelFixText::getList(16);

}