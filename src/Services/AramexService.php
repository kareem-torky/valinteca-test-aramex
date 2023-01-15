<?php

namespace Valinteca\Aramex\Services;

use SoapFault;
use Illuminate\Validation\ValidationException;
use Valinteca\Aramex\Clients\AramexClient;

class AramexService
{    
    private $client;

    public function __construct()
    {
        $this->client = new AramexClient;
    }

    public function createShipment($shipper,$order){
        $params = $this->getShipmentParams($shipper, $order);

        try {
            $response = $this->client->createShipment($params);
            $processedShipment = optional($response->Shipments)->ProcessedShipment;
            if (! $processedShipment) {
                throw ValidationException::withMessages([
                    'error' =>  __('error.something_went_wrong'),
                ]);
            }

            if (isset($response->HasErrors) && $response->HasErrors){
                if (isset($response->Notifications->Notification->Message)) {
                    $message = $response->Notifications->Notification->Message;

                    if ($response->Notifications->Notification->Code == 'ERR03') {
                        $message = 'هذا الحساب محظور. الرجاء التواصل مع الإدارة';
                    }

                    throw ValidationException::withMessages([
                        'carrier_id' =>  $message,
                    ]);
                } 
            }

            if (isset($processedShipment->HasErrors) && $processedShipment->HasErrors && isset($processedShipment->Notifications->Notification->Message)){
                throw ValidationException::withMessages([
                    'carrier_id' =>  $processedShipment->Notifications->Notification->Message,
                ]);
            }

            return [
                'tracking_number' => $processedShipment->ID,
                'label_url' => $processedShipment->ShipmentLabel->LabelURL,
            ];
        }catch (SoapFault $fault) {
            die('Error : ' . $fault->faultstring);
        }

    }

    public function fetchCountries()
    {
        $params = [
            'ClientInfo'  => [
                'AccountCountryCode' => 'JO',
                'AccountEntity'      => 'AMM',
                'AccountNumber'      => '20016',
                'AccountPin'         => '331421',
                'UserName'           => 'testingapi@aramex.com',
                'Password'           => 'R123456789$r',
                'Version'            => 'v1.0',
            ],
        ];
        
        try {
            $response = $this->client->fetchCountries($params);
            return $response;
        } catch (SoapFault $fault) {
            die('Error : ' . $fault->faultstring);
        }
        
    }

    public function fetchCities($country_code)
    {
        $params = [
            'ClientInfo'  => [
                'AccountCountryCode' => 'JO',
                'AccountEntity'      => 'AMM',
                'AccountNumber'      => '20016',
                'AccountPin'         => '331421',
                'UserName'           => 'testingapi@aramex.com',
                'Password'           => 'R123456789$r',
                'Version'            => 'v1.0',
            ],
            'CountryCode' => $country_code,
        ];
        
        try {
            $response = $this->client->fetchCities($params);

            // if (isset($response->HasErrors) && $response->HasErrors){
            //     if (isset($response->Notifications->Notification->Message)) {
            //         $message = $response->Notifications->Notification->Message;

            //         if ($response->Notifications->Notification->Code == 'ERR03') {
            //             $message = __('messages.aramex.account_is_blocked');
            //         }

            //         throw ValidationException::withMessages([
            //             'error' =>  $message,
            //         ]);
            //     } 
            // }
            
            return $response;
        } catch (SoapFault $fault) {
            die('Error : ' . $fault->faultstring);
        }
    }


    private function getShipmentParams($shipper, $order)
    {
        $grandTotal = $order['grandTotal'];
        $isCOD = $order['isCOD'];

        $items = [];
        foreach ($order['items'] as $item) {
            $itemId = $item['inventory_id'];
            $items[] = $itemId;
        }

        $params = [
            'Shipments' => [
                'Shipment' => [
                    'Shipper'       => array_merge($this->getShipmentShipperInfo($shipper), [
                        'Reference1' => "{$order['order_id']}-{$order['id']}",
                    ]),
                    'Consignee'     => array_merge($this->getShipmentConsigneeInfo($order['order']), [
                        'Reference1' => "Customer #{$order['order']['customer_id']}",
                    ]),
                    'TransportType'          => 0,
                    'ShippingDateTime'       => time(),
                    'DueDate'                => time(),
                    'PickupLocation'         => 'Reception',
                    'PickupGUID'             => '',
                    'AccountingInstrcutions' => '',
                    'OperationsInstructions' => '',
        
                    'Details' => [
                        'Dimensions' => [
                            'Length' => 10,
                            'Width'  => 10,
                            'Height' => 10,
                            'Unit'   => 'cm',
                        ],
                        'ActualWeight' => [
                            'Value' => 1,
                            'Unit'  => 'Kg',
                        ],
                        'ProductGroup'       => config('valinteca-aramex.ProductGroup'),
                        'ProductType'        => config('valinteca-aramex.ProductType'),
                        'PaymentType'        => config('valinteca-aramex.PaymentType'),
                        'PaymentOptions'     => config('valinteca-aramex.PaymentOptions'),
                        'Services'           => $isCOD ? 'CODS' : config('valinteca-aramex.services'),
                        'NumberOfPieces'     => $order['quantity_after_refund'],
                        'DescriptionOfGoods' => '',
                        'GoodsOriginCountry' => '',
                        'CashOnDeliveryAmount' => [
                            'Value'        =>  $grandTotal,
                            'CurrencyCode' => 'SAR',
                        ],
                        'CollectAmount' => [
                            'Value'        =>   $grandTotal,
                            'CurrencyCode' => 'SAR',
                        ],
                        'CashAdditionalAmountDescription' => '',
                    ],

                    'Comments'     => implode(",", $items),
                ],
            ],
        
            'ClientInfo' => $this->getClientInfo(),
            'LabelInfo'   => [
                'ReportID'   =>  $isCOD ? 9729 : 9201,
                'ReportType' => 'URL',
            ],
        ];

        return $params;
    }

    private function getShipmentShipperInfo($shipper)
    {
        
        return [
            'AccountNumber' => config('valinteca-aramex.AccountNumber'),
            'PartyAddress'  => [
                'Line1'               => $shipper['address']['line_1'],
                'Line2'               => $shipper['address']['line_2'] ?? '',
                'Line3'               => '',
                'City'                => "Jeddah",
                'StateOrProvinceCode' => '',
                'PostCode'            => $shipper['address']['zip_code'],
                'CountryCode'         => 'SA',
            ],
            'Contact'       => [
                'Department'      => '',
                'PersonName'      => $shipper['address']['name'],
                'Title'           => '',
                'CompanyName'     => config('valinteca-aramex.company_name'),
                'PhoneNumber1'    => $shipper['address']['phone'],
                'PhoneNumber1Ext' => '',
                'PhoneNumber2'    => '',
                'PhoneNumber2Ext' => '',
                'FaxNumber'       => '',
                'CellPhone'       => $shipper['address']['phone'],
                'EmailAddress'    => $shipper['email'],
                'Type'            => '',
            ],
        ];
    }

    public function track($trackingNumber)
    {
        $params = [
            'ClientInfo' => $this->getClientInfo(),
            'Shipments'  => [$trackingNumber]
        ];
        
        try {
            $response = $this->client->track($params);

            if (isset($response->HasErrors) && $response->HasErrors){
                if (isset($response->Notifications->Notification->Message)) {
                    $message = $response->Notifications->Notification->Message;

                    if ($response->Notifications->Notification->Code == 'ERR03') {
                        $message = __('messages.aramex.account_is_blocked');
                    }

                    throw ValidationException::withMessages([
                        'error' =>  $message,
                    ]);
                } 
            }

            if (! isset($response->TrackingResults)) {
                throw ValidationException::withMessages([
                    'error' =>  "No tracking results",
                ]);
            }

            $trackingResults = [];

            foreach (array_values(collect($response->TrackingResults)->toArray()) as $t) {
                if (is_object(optional($t->Value)->TrackingResult)) {
                    $trackingResults[] = (array) optional($t->Value)->TrackingResult;
                } elseif (is_array(optional($t->Value)->TrackingResult)) {
                    foreach ($t->Value->TrackingResult as $tt) {
                        $trackingResults[] = (array) $tt;
                    }
                }
            }
            
            return $trackingResults;
        } catch (SoapFault $fault) {
            die('Error : ' . $fault->faultstring);
        }
    }



    private function getShipmentConsigneeInfo($order)
    {
        $address = $order['address'] ?? $order['customer']['address'];
        $consignee = $order['customer'];
        
        return [
            'AccountNumber' => config('valinteca-aramex.AccountNumber'),
            'PartyAddress'  => [
                'Line1'               => $address['line_1'],
                'Line2'               => $address['line_2'] ?? '',
                'Line3'               => '',
                'City'                => $address['state']['aramex_name'] ?? $address['state']['name'],
                'StateOrProvinceCode' => '',
                'PostCode'            => $address['zip_code'],
                'CountryCode'         => 'SA',
            ],

            'Contact' => [
                'Department'      => '',
                'PersonName'      => $address['name'],
                'Title'           => '',
                'CompanyName'     => $address['name'],
                'PhoneNumber1'    => $address['phone'],
                'PhoneNumber1Ext' => '',
                'PhoneNumber2'    => '',
                'PhoneNumber2Ext' => '',
                'FaxNumber'       => '',
                'CellPhone'       => $address['phone'],
                'EmailAddress'    => $consignee['email'],
                'Type'            => '',
            ],
        ];
    }

    private function getClientInfo()
    {
        return [
            'AccountCountryCode' => config('valinteca-aramex.AccountCountryCode'),
            'AccountEntity'      => config('valinteca-aramex.AccountEntity'),
            'AccountNumber'      => config('valinteca-aramex.AccountNumber'),
            'AccountPin'         => config('valinteca-aramex.AccountPin'),
            'UserName'           => config('valinteca-aramex.UserName'),
            'Password'           => config('valinteca-aramex.Password'),
            'Version'            => config('valinteca-aramex.Version'),
        ];
    }
}