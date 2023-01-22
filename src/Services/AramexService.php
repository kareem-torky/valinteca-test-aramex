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

    public function createShipment($order) {
        // try {
            $response = $this->client->createShipment($this->getShipmentParams($order));
            // $processedShipment = optional($response->Shipments)->ProcessedShipment;
            // if (! $processedShipment) {
            //     throw ValidationException::withMessages([
            //         'error' =>  __('error.something_went_wrong'),
            //     ]);
            // }

            // if (isset($response->HasErrors) && $response->HasErrors){
            //     if (isset($response->Notifications->Notification->Message)) {
            //         $message = $response->Notifications->Notification->Message;

            //         if ($response->Notifications->Notification->Code == 'ERR03') {
            //             $message = 'هذا الحساب محظور. الرجاء التواصل مع الإدارة';
            //         }

            //         throw ValidationException::withMessages([
            //             'carrier_id' =>  $message,
            //         ]);
            //     } 
            // }

            // if (isset($processedShipment->HasErrors) && $processedShipment->HasErrors && isset($processedShipment->Notifications->Notification->Message)){
            //     throw ValidationException::withMessages([
            //         'carrier_id' =>  $processedShipment->Notifications->Notification->Message,
            //     ]);
            // }

            return $response;
            // return $processedShipment;
            // return [
            //     'tracking_number' => $processedShipment->ID,
            //     'label_url' => $processedShipment->ShipmentLabel->LabelURL,
            // ];
        // } 
        // catch (SoapFault $fault) {
        //     die('Error : ' . $fault->faultstring);
        // }
    }

    public function fetchCountries()
    {
        $params = [
            'ClientInfo'  => $this->getClientInfo(),
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
            'ClientInfo'  => $this->getClientInfo(),
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


    private function getShipmentParams($order)
    {
        $services = isset($order['services']) ? $order['services'] : config('aramex.services');

        return [
            'Shipments' => [
                'Shipment' => [
                    'Shipper'       => $this->formatShipperData($order['shipper']),
                    'Consignee'     => $this->formatConsigneeData($order['consignee']),
                    'TransportType'          => 0,
                    'ShippingDateTime'       => $order['delivery']['shipping_date_time'],
                    'DueDate'                => $order['delivery']['due_date'],
                    'PickupLocation'         => $order['delivery']['pickup_location'],
                    // 'PickupGUID'             => '',
                    // 'AccountingInstrcutions' => '',
                    // 'OperationsInstructions' => '',

                    'Details' => [
                        'Dimensions' => [
                            'Length' => $order['specifications']['length'],
                            'Width'  => $order['specifications']['width'],
                            'Height' => $order['specifications']['height'],
                            'Unit'   => $order['specifications']['length_unit'],
                        ],
                        'ActualWeight' => [
                            'Value' => $order['specifications']['weight'],
                            'Unit'  => $order['specifications']['weight_unit'],
                        ],
                        'ProductGroup'       => isset($order['product_group']) ? $order['product_group'] : config('aramex.product_group'),
                        'ProductType'        => isset($order['product_type']) ? $order['product_type'] : config('aramex.product_type'),
                        'PaymentType'        => isset($order['payment_type']) ? $order['payment_type'] : config('aramex.payment_type'),
                        'PaymentOptions'     => isset($order['payment_options']) ? $order['payment_options'] : config('aramex.payment_options'),
                        'Services'           => $services,
                        'NumberOfPieces'     => $order['specifications']['number_of_pieces'],
                        'DescriptionOfGoods' => '',
                        'GoodsOriginCountry' => '',
                        'CashOnDeliveryAmount' => [
                            'Value'        =>  $order['payment']['cash_on_delivery_amount'],
                            'CurrencyCode' => $order['payment']['currency_code'],
                        ],
                        'CollectAmount' => [
                            'Value'        =>  $order['payment']['collect_amount'],
                            'CurrencyCode' => $order['payment']['currency_code'],
                        ],
                        'CashAdditionalAmountDescription' => '',
                    ],

                    'Comments'     => $order['comments'],
                ],
            ],
        
            'ClientInfo' => $this->getClientInfo(),
            'LabelInfo'   => [
                'ReportID'   =>  $services == 'CODS' ? 9729 : 9201,
                'ReportType' => 'URL',
            ],
        ];
    }

    private function formatShipperData($shipper)
    {
        return [
            'AccountNumber' => config('aramex.account_number'),
            'PartyAddress'  => [
                'Line1'               => $shipper['line1'],
                'Line2'               => $shipper['line2'] ?? '',
                'Line3'               => $shipper['line3'] ?? '',
                'City'                => $shipper['city'],
                'PostCode'            => $shipper['zip_code'],
                'CountryCode'         => $shipper['country_code'],
            ],
            'Contact'       => [
                'PersonName'      => $shipper['name'],
                'CompanyName'     => $shipper['company_name'],
                'PhoneNumber1'    => $shipper['phone'],
                'CellPhone'       => $shipper['cell_phone'],
                'EmailAddress'    => $shipper['email'],
            ],
            'Reference1'    => $shipper['reference'], 
        ];
    }

    private function formatConsigneeData($consignee)
    {
        return [
            'AccountNumber' => config('aramex.account_number'),
            'PartyAddress'  => [
                'Line1'               => $consignee['line1'],
                'Line2'               => $consignee['line2'] ?? '',
                'Line3'               => $consignee['line3'] ?? '',
                'City'                => $consignee['city'],
                'PostCode'            => $consignee['zip_code'],
                'CountryCode'         => $consignee['country_code'],
            ],
            'Contact'       => [
                'PersonName'      => $consignee['name'],
                'CompanyName'     => $consignee['company_name'],
                'PhoneNumber1'    => $consignee['phone'],
                'CellPhone'       => $consignee['cell_phone'],
                'EmailAddress'    => $consignee['email'],
            ],
            'Reference1'    => $consignee['reference'], 
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

    private function getClientInfo()
    {
        return [
            'AccountCountryCode' => config('aramex.account_country_code'),
            'AccountEntity'      => config('aramex.account_entity'),
            'AccountNumber'      => config('aramex.account_number'),
            'AccountPin'         => config('aramex.account_pin'),
            'UserName'           => config('aramex.username'),
            'Password'           => config('aramex.password'),
            'Version'            => config('aramex.version'),
        ];
    }
}