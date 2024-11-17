<?php

if(!defined('CATALOG_INCLUDED')) die();

AddEventHandler("sender", "OnPresetMailingList", array("twigSenderEventBuyOrdersHandler", "onPresetMailingList"));

if(!class_exists('TwigCheckPostingByUser')){

    class TwigCheckPostingByUser{

        private static $amIds = array();
        private static $apIds = array();

        public static function getPostIds($sTriggerCode){

            if(empty(static::$amIds)){
                static::getMailingIds($sTriggerCode);
            }

            if(!empty(static::$amIds)
                && empty(static::$apIds)){
                static::getPostsIds();
            }

        }

        private static function checkPayordersTwigTable($iUserId,$orderId){

            global $DB;
            $sSql = 'SELECT COUNT(`ID`) AS `count` FROM `b_twig_payorders_check` WHERE `USER_ID`='.(int)$iUserId.' AND `ORDER_ID`='.(int)$orderId;

            $rDb = $DB->Query($sSql);
            $iCount = 0;

            if($rDb
                && $aCheck = $rDb->Fetch()){

                $iCount = isset($aCheck['count'])
                && !empty($aCheck['count'])
                    ? trim($aCheck['count'])
                    : 0;

            }

            return (int)$iCount ? false : true;

        }

        private static function updatePayordersTwigTable($iUserId,$orderId){

            global $DB;

            $sSql = 'INSERT INTO `b_twig_payorders_check`(`ID`,`USER_ID`,`ORDER_ID`) VALUES(\'NULL\','.(int)$iUserId.','.(int)$orderId.')';
            $DB->Query($sSql);


        }

        public static function checkUserAndField($iUserId,$acFields){

            $canRun = true;
            $orderId = 0;

            if(!empty(static::$apIds)){

                $rdRecipient = \Bitrix\Sender\PostingRecipientTable::getList(
                    array(
                        'select' => array('FIELDS','USER_ID'),
                        'filter' => array(
                            'POSTING_ID' => static::$apIds,
                            'USER_ID' => $iUserId,
                            '!FIELDS' => ''
                        )));

                if($rdRecipient)
                    while($aRecipient = $rdRecipient->fetch()) {

                        if(isset($aRecipient['FIELDS'])){

                            $arFields = $aRecipient['FIELDS'];

                            foreach($acFields as $sfCode => $sfValue){

                                if(isset($arFields[$sfCode]) && $arFields[$sfCode] == $sfValue){
                                    $canRun = false;
                                    break;
                                }
                            }

                        }

                        if(!$canRun)
                            break;

                    }

            }

            if($canRun){

                $canRun = static::checkPayordersTwigTable($iUserId,current($acFields));

                if($canRun){

                    static::updatePayordersTwigTable($iUserId,current($acFields));

                }


            }

            return $canRun;
        }


        private static function getPostsIds(){

            foreach(static::$amIds as $amId){

                $dPosting = \Bitrix\Sender\PostingTable::getList(
                    array(
                        'select' => array('ID'),
                        'filter' => array('MAILING_ID' => $amId)
                    )
                );

                if($dPosting)
                    while($aPosting = $dPosting->fetch()){
                        static::$apIds[$aPosting['ID']] = $aPosting['ID'];
                    };

            }

        }

        private static function getMailingIds($sTriggerCode){

            $dMailing = \Bitrix\Sender\MailingTable::getList(
                array(
                    'select' => array('ID','TRIGGER_FIELDS'),
                    'filter' => array('!TRIGGER_FIELDS' => '')
                )
            );

            if($dMailing)
                while ($aMailing = $dMailing->fetch()) {

                    $aTrigger = $aMailing['TRIGGER_FIELDS'];

                    if(isset($aTrigger['START'])
                        && isset($aTrigger['START']['CODE'])
                        && $aTrigger['START']['CODE'] == $sTriggerCode
                    ) {

                        static::$amIds[$aMailing['ID']] = $aMailing['ID'];

                    }

                }

        }

    }

}


if(!class_exists('twigSenderEventBuyOrdersHandler')){
    class twigSenderEventBuyOrdersHandler
    {
        public static function onPresetMailingList()
        {
            $result = array();

            $result[] = array(
                'TYPE' => 'Мои рассылки',
                'CODE' => 'twig_payorders',
                'NAME' => 'Уведомление по завершенному заказу',
                'DESC_USER' => 'Это автоматическая рассылка, предлагающая оценить уже купленные товары для завершенных заказов.',
                'DESC' => 'Рассылка запускаемая через месяц для тех, кто оплатил заказ.',
                'TRIGGER' => array(
                    'START' => array(
                        'ENDPOINT' => array(
                            'MODULE_ID' => '',
                            'CODE' => 'twig_payorders',
                            'FIELDS' => array('DAYS_FOR_SEND' => 30)
                        )
                    ),
                    'END' => array(
                        'ENDPOINT' => array(
                            'MODULE_ID' => 'sender',
                            'CODE' => 'user_auth',
                            'FIELDS' => array('DAYS_FOR_SEND' => 30)
                        )
                    ),
                ),
                'CHAIN' => array(
                    array(
                        'TIME_SHIFT' => 0,
                        'SUBJECT' => 'Оцените купленный товар',
                        'MESSAGE' => 'Здравствуйте, #NAME#<br><br> Оцените уже купленный товар в заказе (#ORDER_ID#).',
                    )
                )
            );


            return $result;
        }
    }

}

if(!class_exists('SenderTriggerBuyOrders')) {

    class SenderTriggerBuyOrders extends \Bitrix\Sender\TriggerConnectorClosed
    {

        private static $arFields = array();
        private static $ADMIN_EMAIL = '~(@i\.su)|(@twig\.su)|(@youtwig\.ru)|(@impel\.pro)|(@i\.ru)~isu';
        private const STATUS_ID = 'FF';
        private const GROUPS = array(
            0 => '3',
            1 => '4',
            2 => '5',
            3 => '2',
        );
        private const DAYS = 30;

        /**
         * @return string
         * Название триггера
         */
        public function getName()
        {
            return 'Уведомление по завершенному заказу';
        }

        /**
         * @return string
         * Уникальный код триггера
         */
        public function getCode()
        {
            return "twig_payorders";
        }

        public static function canBeTarget()
        {
            return true;
        }

        /**
         * @return bool
         * Может ли триггер обрабатывать старые данные
         */
        public static function canRunForOldData()
        {
            return false;
        }

        public function getForm()
        {
            return '
             <table>
                <tr>
                   <td>Сколько дней назад:</td>
                   <td>
                      <input size=3 type="text" name="' . $this->getFieldName('DAYS_FOR_SEND') . '" value="' . htmlspecialcharsbx($this->getFieldValue('DAYS_FOR_SEND', 30)) . '">
                   </td>
                </tr>
             </table>
          ';
        }


        public function filter()
        {
            TwigCheckPostingByUser::getPostIds(static::getCode());
            \Bitrix\Main\Loader::includeModule('sale');
            \Bitrix\Main\Loader::includeModule('catalog');
            $idForSend = (int)trim($this->getFieldValue('DAYS_FOR_SEND'));
            $idForSend = empty($idForSend) ? static::DAYS : $idForSend;

            $aOrders = static::getArchiveOrdersList($idForSend);
            $aOrders = array_merge((array)$aOrders, (array)static::getOrdersList($idForSend));
            $this->setOrderRecipient($aOrders);

            return sizeof($this->recipient) && is_array($this->recipient) ? true : false;

        }

        private static function checkSendToUser($iUserId)
        {

            $aUgroups = CUser::GetUserGroup($iUserId);
            $aUdiff = array_diff($aUgroups, static::GROUPS);

            return sizeof($aUdiff) == 0 ? true : false;

        }

        private static function getUserName($iUserId)
        {
            $rUser = CUser::getById($iUserId);
            $sUserName = false;

            if ($rUser
                && $aUser = $rUser->getNext()) {
                $sUserName = CUser::FormatName('#LAST_NAME# #NAME# #SECOND_NAME#', $aUser);
            }

            return $sUserName;
        }

        private static function getProductDetailPage($sName)
        {
            $rDB = CIBlockElement::GetList(
                array(),
                array(
                    'NAME' => $sName,
                    'IBLOCK_ID' => 11
                ),
                false,
                false,
                array('DETAIL_PAGE_URL')
            );
            $sdPage = '';

            if ($rDB
                && $aDb = $rDB->GetNext()) {

                $sdPage = isset($aDb['DETAIL_PAGE_URL'])
                && !empty($aDb['DETAIL_PAGE_URL'])
                    ? trim($aDb['DETAIL_PAGE_URL'])
                    : '';
            }

            return $sdPage;

        }

        private function setOrderRecipient($aOrders)
        {

            $aRecipients = array();

            foreach ($aOrders as $iOrderId => $aOrder) {

                if (isset($aOrder['basket'])
                    && !empty($aOrder['basket'])
                    && isset($aOrder['USER_EMAIL'])
                    && !empty($aOrder['USER_EMAIL'])
                    && isset($aOrder['USER_ID'])
                    && !empty($aOrder['USER_ID'])
                    && isset($aOrder['ORDER_ID'])
                    && !empty($aOrder['ORDER_ID'])
                    && !isset($aRecipients[$aOrder['ORDER_ID']])
                    && (TwigCheckPostingByUser::checkUserAndField(
                        $aOrder['USER_ID'],
                        array(
                            'ORDER_ID' => $aOrder['ORDER_ID']
                        )
                    ))
                ) {

                    $sUserName = static::getUserName($aOrder['USER_ID']);

                    if ($sUserName !== false) {

                        $aRecipients[$aOrder['ORDER_ID']] = array(
                            'NAME' => $sUserName ? $sUserName : $aOrder['USER_EMAIL'],
                            'USER_NAME' => $sUserName ? $sUserName : $aOrder['USER_EMAIL'],
                            'EMAIL' => $aOrder['USER_EMAIL'],
                            'DATE_INSERT' => FormatDate('Y.m.d H:i:s', MakeTimeStamp($aOrder['DATE_INSERT'])),
                            //'EMAIL' => 's@impel.pro',
                            'USER_ID' => $aOrder['USER_ID'],
                            'ORDER_ID' => $aOrder['ORDER_ID'],

                        );

                        $aRecipients[$aOrder['ORDER_ID']]['FIELDS'] = array(
                            'ORDER_ID' => $aOrder['ORDER_ID'],
                            'USER_ID' => $aOrder['USER_ID'],
                            'USER_NAME' => $sUserName ? $sUserName : $aOrder['USER_EMAIL'],
                            'DATE_INSERT' => FormatDate('Y.m.d H:i:s', MakeTimeStamp($aOrder['DATE_INSERT'])),
                        );

                    }

                }

            }

            if (sizeof($aRecipients) > 0) {
                $aRecipients = array_values($aRecipients);
                //$aRecipients = array(current($aRecipients));
            }

            if (!empty($aRecipients)) {
                $this->recipient = $aRecipients;
            }

            return true;

        }

        private static function makeAuthHash($iUserId, $iOrderId)
        {
            $asHashes = array(':+', '.+', '-+', '!+', '++', '*+', '~+', '=+');
            $srSalt = md5($asHashes[mt_rand(0, sizeof($asHashes) - 1)] . $iUserId);
            $sSalt = '?&amp;order_id=' . $iOrderId . '&amp;check_hash=' . md5($iUserId . '-' . $iOrderId) . ':' . $srSalt;
            return $sSalt;
        }

        private static function getOrdersList($idForSend)
        {

            $dateFrom = new \Bitrix\Main\Type\DateTime;
            $dateFrom = $dateFrom->add('-' . $idForSend . ' days');

            $select = array(
                'ID',
                'PAYED',
                'CANCELED',
                'STATUS_ID',
                "USER_ID",
                "USER_EMAIL" => "USER.EMAIL",
                'DATE_INSERT'
            );

            $arFilter = array();
            $arFilter['STATUS_ID'] = static::STATUS_ID;
            $arFilter['PAYED'] = 'Y';

            $arFilter['>DATE_INSERT'] = $dateFrom->format(\Bitrix\Main\UserFieldTable::MULTIPLE_DATETIME_FORMAT);

            $getListParams = array(
                'filter' => $arFilter,
                'select' => $select,
                'runtime' => array()
            );

            $getListParams['order'] = array('DATE_INSERT' => 'DESC');

            $usePageNavigation = true;

            $orderClassName = '\Bitrix\Sale\Order';

            $dbOrders = new \CDBResult($orderClassName::getList($getListParams));

            if ($dbOrders)
                while ($arOrder = $dbOrders->GetNext()) {

                    if ($arOrder['CANCELED'] == 'Y'
                        || preg_match(static::$ADMIN_EMAIL,$arOrder['USER_EMAIL'])
                        || !static::checkSendToUser($arOrder['USER_ID']))
                        continue;

                    $arOrder['ORDER_ID'] = $arOrder['ID'];
                    $listOrders[$arOrder['ID']] = $arOrder;

                }

            if (!empty($listOrders)) {

                $orderIds = array_keys($listOrders);

                $basketClassName = '\Bitrix\Sale\Basket';
                /** @var Main\DB\Result $listBaskets */
                $listBaskets = $basketClassName::getList(array(
                    'select' => array("*"),
                    'filter' => array("=ORDER_ID" => $orderIds),
                    'order' => array('ID' => 'ASC')
                ));

                while ($basket = $listBaskets->fetch()) {

                    if (CSaleBasketHelper::isSetItem($basket))
                        continue;

                    $listOrders[$basket['ORDER_ID']]['basket'][] = $basket;
                }

            }

        }

        private static function getArchiveOrdersList($idForSend)
        {

            $archToOrder = array();
            $archOrderIdList = array();
            $listOrders = array();

            $dateFrom = new \Bitrix\Main\Type\DateTime;
            $dateFrom = $dateFrom->add('-' . $idForSend . ' days');

            $select = array(
                'ID',
                'ORDER_ID',
                'PAYED',
                'CANCELED',
                'STATUS_ID',
                "USER_ID",
                "USER_EMAIL" => "USER.EMAIL",
                'DATE_INSERT'
            );

            $arFilter = array();
            $arFilter['STATUS_ID'] = static::STATUS_ID;
            $arFilter['PAYED'] = 'Y';

            $arFilter['>DATE_INSERT'] = $dateFrom->format(\Bitrix\Main\UserFieldTable::MULTIPLE_DATETIME_FORMAT);

            $getListParams = array(
                'filter' => $arFilter,
                'select' => $select,
                'runtime' => array()
            );

            $getListParams['order'] = array('DATE_INSERT' => 'DESC');

            $usePageNavigation = true;

            $orderClassName = '\Bitrix\Sale\Internals\OrderArchiveTable';

            $dbOrders = new \CDBResult($orderClassName::getList($getListParams));

            if ($dbOrders)
                while ($arOrder = $dbOrders->GetNext()) {

                    if ($arOrder['CANCELED'] == 'Y'
                        || preg_match(static::$ADMIN_EMAIL,$arOrder['USER_EMAIL'])
                        || !static::checkSendToUser($arOrder['USER_ID']))
                        continue;

                    $listOrders[$arOrder['ORDER_ID']] = $arOrder;
                    $archToOrder[$arOrder['ID']] = $arOrder['ORDER_ID'];
                    $archOrderIdList[] = $arOrder['ID'];

                }

            if (!empty($archOrderIdList)) {

                $basketClassName = '\Bitrix\Sale\Internals\BasketArchiveTable';
                /** @var Main\DB\Result $listBaskets */
                $listBaskets = $basketClassName::getList(array(
                    'select' => array("*"),
                    'filter' => array("=ARCHIVE_ID" => $archOrderIdList),
                    'order' => array('ID' => 'ASC')
                ));

                while ($basket = $listBaskets->fetch()) {

                    if (CSaleBasketHelper::isSetItem($basket))
                        continue;

                    $basket['ORDER_ID'] = $archToOrder[$basket['ARCHIVE_ID']];
                    $listOrders[$basket['ORDER_ID']]['basket'][] = $basket;
                }

            }

            return $listOrders;
        }


        /*
        * @return array|\Bitrix\Main\DB\Result|\CDBResult
        *
        * Функция, которая из данных события
        * вернет данные о получателе рассылки
        */
        public function getRecipient()
        {
            // возвращаем сохраненные адресаты
            return $this->recipient;
        }

        public static function getPersonalizeList()
        {
            return array(
                array(
                    'CODE' => 'USER_NAME',
                    'NAME' => 'Имя заказчика',
                    'DESC' => 'Имя заказчика'
                ),
                array(
                    'CODE' => 'ORDER_ID',
                    'NAME' => 'ID заказа',
                    'DESC' => 'ID заказа'
                ),
                array(
                    'CODE' => 'DATE_INSERT',
                    'NAME' => 'Время создания заказа',
                    'DESC' => 'Время создания заказа'
                ),
            );
        }

    }

}

