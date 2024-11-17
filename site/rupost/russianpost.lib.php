<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/**
 * Russian Post tracking API PHP library
 * @author InJapan Corp. <max@injapan.ru>
 *
 ************************************************************************
 * You MUST request usage access for this API through request mailed to *
 * fc@russianpost.ru                                                    *
 ************************************************************************
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

$russianpostRequiredExtensions = array('SimpleXML', 'curl', 'pcre');
foreach($russianpostRequiredExtensions as $russianpostExt) {
    if (!extension_loaded($russianpostExt)) {
        throw new RussianPostSystemException('Required extension ' . $russianpostExt . ' is missing');
    }
}

class RussianPostAPI {
    /**
     * SOAP service URL
     */
    const SOAPEndpoint = 'https://tracking.russianpost.ru/rtm34?wsdl';

    protected $proxyHost;
    protected $proxyPort;
    protected $proxyAuthUser;
    protected $proxyAuthPassword;
    protected $barcode;

    /**
     * Constructor. Pass proxy config here.
     * @param string $proxyHost
     * @param string $proxyPort
     * @param string $proxyAuthUser
     * @param string $proxyAuthPassword
     */
    public function __construct($proxyHost = "", $proxyPort = "", $proxyAuthUser = "", $proxyAuthPassword = "") {
        $this->proxyHost         = $proxyHost;
        $this->proxyPort         = $proxyPort;
        $this->proxyAuthUser     = $proxyAuthUser;
        $this->proxyAuthPassword = $proxyAuthPassword;
    }

    /**
     * Returns tracking data
     * @param string $trackingNumber tracking number
     * @return array of RussianPostTrackingRecord
     */
    public function getOperationHistory($trackingNumber) {
        $trackingNumber = trim($trackingNumber);
        if (!preg_match('/^[0-9]{14}|[A-Z]{2}[0-9]{9}[A-Z]{2}$/', $trackingNumber)) {
            //throw new RussianPostArgumentException('Incorrect format of tracking number: ' . $trackingNumber);
            return false;
        }

        $this->barcode = $trackingNumber;

        $data = $this->makeRequest($trackingNumber);
        $data = $this->parseResponse($data);

        return $data;
    }

    protected function parseResponse($raw) {

        usleep(50);

        $records                                    = array();

        if(         is_object($raw)
            &&  isset($raw->data)

        ){
            $records                                = $raw->data;
        };

        if (empty($records)){

            return false;
        };

        if(is_array($records->events)
            && sizeof($records->events)){

            $subRecords                            = $records->events;
            $out 								   = array();

            foreach($subRecords                    as$record){

                $outRecord 			               = new RussianPostTrackingRecord();

                $outRecord->operationRecord	       = new stdClass();

                $outRecord->operationRecord->operationType = (string) $record->operationType;
                $outRecord->operationType = (string) $record->operationType;

                //$outRecord->operationRecord->operationTypeId = (int) $record->id;

                $outRecord->operationRecord->operationAttribute = (string) $record->operationAttribute;
                $outRecord->operationAttribute = (string) $record->operationAttribute;

                //$outRecord->operationRecord->operationAttributeId = (int) $record->id;

                //$outRecord->operationPlacePostalCode = (string) $record->operationPlacePostalCode;
                //$outRecord->operationPlaceName       = (string) $record->operationPlaceName;

                //$outRecord->destinationPostalCode    = (string) $records->destinationPostalCode;
                //$outRecord->destinationAddress       = (string) $records->destinationAddress;

                //$outRecord->operationDate            = (string) $record->operationDateTime;

                //$outRecord->itemWeight               = round(floatval($records->itemWeight) / 1000, 3);
                //$outRecord->declaredValue            = round(floatval($records->declaredValue) / 100, 2);
                //$outRecord->collectOnDeliveryPrice   = round(floatval($records->collectOnDeliveryPrice) / 100, 2);

                $outRecord->operationRecord->ItemParameters           = new stdClass();
                $outRecord->operationRecord->UserParameters           = new stdClass();
                $outRecord->operationRecord->OperationParameters      = new stdClass();
                $outRecord->operationRecord->OperationParameters->OperType = new stdClass();

                $outRecord->operationRecord->AddressParameters        = new stdClass();

                $outRecord->operationRecord->AddressParameters->OperationAddress = new stdClass();
                $outRecord->operationRecord->AddressParameters->DestinationAddress = new stdClass();

                $outRecord->operationRecord->FinanceParameters        = new stdClass();

                $outRecord->operationRecord->ItemParameters->Barcode  = $this->barcode;
                $outRecord->operationRecord->UserParameters->Rcpn     = '';

                if(!empty($records->destinationPostalAddress)){
                    $outRecord->operationRecord->UserParameters->Rcpn  .= $records->destinationPostalAddress;
                }

                if(!empty($records->destinationPostalPhones)){
                    $outRecord->operationRecord->UserParameters->Rcpn  .= $records->destinationPostalPhones;
                }

                if(!empty($records->destinationPostalWorkTime)){
                    $outRecord->operationRecord->UserParameters->Rcpn  .= $records->destinationPostalWorkTime;
                }

                $outRecord->operationRecord->OperationParameters->OperType->Name = $record->operationType;
                $outRecord->operationRecord->AddressParameters->OperationAddress->Index = $record->operationPlacePostalCode;
                $outRecord->operationRecord->AddressParameters->OperationAddress->Description = $record->operationPlaceName;

                $outRecord->operationRecord->ItemParameters->Mass = $record->itemWeight;
                $outRecord->operationRecord->FinanceParameters->Value = $records->declaredValue;
                $outRecord->operationRecord->FinanceParameters->Payment = $records->declaredValue;

                $outRecord->operationRecord->AddressParameters->DestinationAddress->Index = $records->destinationPostalCode;
                $outRecord->operationRecord->AddressParameters->DestinationAddress->Description = $records->destinationPostalAddress;

                //$records->destinationPostalAddress.' '.$records->destinationPostalPhones.' '.$records->destinationPostalWorkTime;

                //first_record->ItemParameters->Barcode
                //first_record->ItemParameters->ComplexItemName -
                //first_record->ItemParameters->MailRank->Name -
                //first_record->ItemParameters->PostMark->Name -
                //first_record->UserParameters->Sndr -

                //first_record->UserParameters->Rcpn - destinationPostalAddress + destinationPostalPhones + destinationPostalWorkTime
                //first_record->OperationParameters->OperType->Name events->operationType
                //first_record->AddressParameters->OperationAddress->Index events->operationPlacePostalCode
                //first_record->AddressParameters->OperationAddress->Description events->operationPlaceName
                //first_record->ItemParameters->Mass events->itemWeight
                //first_record->FinanceParameters->Value declaredValue
                //first_record->FinanceParameters->Payment declaredValue
                //first_record->AddressParameters->DestinationAddress->Index destinationPostalCode
                //first_record->AddressParameters->DestinationAddress->Description destinationPostalAddress
                //last->operationAttribute
                //last->operationType

                $out[]                               = $outRecord;

            }

        }

        return $out;
    }

    protected function makeRequest($trackingNumber) {

        $url                                = 'https://api.track24.ru/tracking.json.php?apiKey=0ac8897e2055582be4cb5955883825de&domain='.$_SERVER['HTTP_HOST'].'&code='.$trackingNumber;

        usleep(50);

        if(function_exists('curl_init')){

            $tuCurl     					= curl_init();
            $tuData							= '';

            if($tuCurl && is_resource($tuCurl)){



                $opts   				    = array(CURLOPT_URL     =>  $url,
                    CURLOPT_HTTPGET   =>  1,
                    CURLOPT_HEADER  =>  0,
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_FOLLOWLOCATION => 1,
                    CURLOPT_BINARYTRANSFER => 1,
                    CURLOPT_AUTOREFERER => 1,
                    CURLOPT_SSL_VERIFYPEER => 0,
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_HTTPHEADER => array('Expect:')
                );


                foreach($opts as $key=>$value){
                    curl_setopt($tuCurl,$key,$value);
                }

                $tuData     					= curl_exec($tuCurl);
                curl_close($tuCurl);

            }

        } else {


            $tuData                     = file_get_contents($url);
        }

        if(!empty($tuData)){
            $tuData                     = json_decode($tuData);
        }

        return $tuData;
    }



}

/**
 * One record in tracking history
 */
class RussianPostTrackingRecord {

    public $operationRecord;
    /**
     * Operation type, e.g. Р�РјРїРѕСЂС‚, Р­РєСЃРїРѕСЂС‚ and so on
     * @var string
     */
    public $operationType;

    /**
     * Operation type ID
     * @var int
     */
    public $operationTypeId;

    /**
     * Operation attribute, e.g. Р’С‹РїСѓС‰РµРЅРѕ С‚Р°РјРѕР¶РЅРµР№
     * @var string
     */
    public $operationAttribute;

    /**
     * Operation attribute ID
     * @var int
     */
    public $operationAttributeId;

    /**
     * ZIP code of the postal office where operation took place
     * @var string
     */
    public $operationPlacePostalCode;

    /**
     * Name of the postal office where operation took place
     * @var [type]
     */
    public $operationPlaceName;

    /**
     * Operation date in ISO 8601 format
     * @var string
     */
    public $operationDate;

    /**
     * Item wight (kg)
     * @var float
     */
    public $itemWeight;

    /**
     * Declared value of the item in rubles
     * @var float
     */
    public $declaredValue;

    /**
     * COD price of the item in rubles
     * @var float
     */
    public $collectOnDeliveryPrice;

    /**
     * Postal code of the place item addressed to
     * @var string
     */
    public $destinationPostalCode;

    /**
     * Destination address of the place item addressed to
     * @var string
     */
    public $destinationAddress;
}

class RussianPostException         extends Exception { }
class RussianPostArgumentException extends RussianPostException { }
class RussianPostSystemException   extends RussianPostException { }
class RussianPostChannelException  extends RussianPostException { }
class RussianPostDataException     extends RussianPostException { }