<?php
include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/wsrubi.smtp/classes/general/wsrubismtp.php");

CModule::AddAutoloadClasses(
    '',
    array(
        '\CurrencyRates\HelperTask' => '/local/interface/include/classes/currencyRates/helpertask.php'
    )
);

use Bitrix\Sale;

AddEventHandler("sale", "OnOrderNewSendEmail", "ModifySaleMails");

AddEventHandler("sale", "OnSaleStatusOrder", "DeletePersonData");

function ModifySaleMails($orderID, &$eventName, &$arFields)
{
    $delivery = "";

    $arOrder = Sale\Order::load($orderID);

    $collection = $arOrder->getShipmentCollection()->getNotSystemItems();

    foreach($collection as $shipment) {

        if($shipment->getDeliveryId() == 2) {

            $store_id = $shipment->getStoreId();

            $store = CCatalogStore::GetList(
                array(),
                array("ID" => $store_id),
                false,
                false,
                array("TITLE", "ADDRESS")
            );
            while ($row = $store->GetNext()) {
                $delivery .= $row["TITLE"] . ", " . $row["ADDRESS"];
            }
        }
    }

    $arFields["DELIVERY_DESCRIPTION"] = $delivery;
}

function DeletePersonData($orderID, $val) {
    $flag = false;
    if($val == "F") {

        $arOrder = Sale\Order::load($orderID);

        $collection = $arOrder->getShipmentCollection()->getNotSystemItems();

        foreach ($collection as $shipment) {
            if ($shipment->getDeliveryId() == 2) {
                $flag = true;
                break;
            }
        }
        if ($flag) {
            $arOrder = Sale\Order::load($orderID);
            $idUser = $arOrder->getUserId();
            $ff = new CUser;
            $ff->Update($idUser, array(
                "PERSONAL_COUNTRY" => "",
                "PERSONAL_STATE" => "",
                "PERSONAL_CITY" => "",
                "PERSONAL_ZIP" => "",
                "PERSONAL_STREET" => "",
                "PERSONAL_MAILBOX" => "",
                "PERSONAL_NOTES" => ""
            ));
        }
    }
}