<?php

/**
 * Config Array
 * Static and General configuration for the integration
 * Constant Parameters.
 */

return [
    'ENV'                   => env('ARAMEX_ENV', 'TEST'),

    'account_country_code'  => env('ARAMEX_COUNTRY_CODE', 'GB'),
    'account_entity'        => env('ARAMEX_ACCOUNT_ENTITY', 'LON'),
    'account_number'        => env('ARAMEX_ACCOUNT_NUMBER', '102331'),
    'account_pin'           => env('ARAMEX_ACCOUNT_PIN', '321321'),
    'username'              => env('ARAMEX_USERNAME', 'testingapi@aramex.com'),
    'password'              => env('ARAMEX_PASSWORD', 'R123456789$r'),
    'version'               => env('ARAMEX_VERSION', 'v1'),
    'company_name'          => env('ARAMEX_COMPANY_NAME', 'SA'),

    'CompanyName'  => 'Ecom ',

    /**                        Business Attributes
     *  Usually there are attributes that never change in the projects (specially
     *  for external integration) depending on business models.
     *  for example: i dont allow COD (Cash on Delivery) on my e-commerce website
     *  or my products are made from glass so they require special shipping terms.
     */


    /**
     * Product Group
     *    Avaiable Values:
     *    EXP = Express
     *    DOM = Domestic
     */
    'product_group' => env('ARAMEX_PRODUCT_GROUP', 'DOM'),

    /**
     * Product Type
     * Available Values:
     *        OND = only for Product Group DOM
     *        PDX = Priority Document Express
     *        PPX = Priority Parcel Express
     *        PLX = Priority Letter Express
     *        DDX = Deferred Document Express
     *        DPX = Deferred Parcel Express
     *        GDX = Ground Document Express
     *        GPX = Ground Parcel Express
     *        GPX = Ground Parcel Express
     *        EPX = Economy Parcel Express
     *    For more information naviagte to Appendix-A (Page: 51) in
     *  https://www.aramex.com/docs/default-source/resourses/resourcesdata/shipping-services-api-manual.pdf
     */
    'product_type'  => env('ARAMEX_PRODUCT_GROUP', 'ONP'),

    /**
     * Payment Method
     * Available Values:
     *        P = Prepaid
     *        C = Collect
     *        3 = Third Party
     *    For more information naviagte to Appendix-B (Page: 52) in
     *  https://www.aramex.com/docs/default-source/resourses/resourcesdata/shipping-services-api-manual.pdf
     */
    'payment_type'       => env('ARAMEX_PAYMENT_TYPE', 'P'),
    'RefundPaymentType' => env('ARAMEX_REFUND_PAYMENT_TYPE', '3'),

    /**
     * Payment Options
     * Available Values:
     *        For PaymentType = C
     *            ASCC = Needs Shipper Account Number to be filled.
     *            ARCC = Needs Consignee Account Number to be filled.
     *        For PaymentType = P (it's nullable here)
     *            CASH
     *            ACCT (Stands for Account)
     *            PPST (Stands for Prepaid Stock)
     *            CRDT (Stands for Credit)
     *
     *  Please note that no one on earth know any details about the above
     *  Even though for more information navigate to 4.7 Shipment Details (Page: 42) in
     *  https://www.aramex.com/docs/default-source/resourses/resourcesdata/shipping-services-api-manual.pdf
     */
    'payment_options' => env('ARAMEX_PAYMENT_OPTIONS', ''),

    /**
     *  Service Code (Additional Services for the shipment)
     *  Separate by comma when selecting multiple services
     *  Available Values: (nullable)
     *        CODS = Cash on Delivery
     *        FIRST = First Delivery
     *        FRDM = Free Domicile
     *        HFPU = Hold for pick up
     *        NOON = Noon Delivery
     *        SIG = Signature Required
     *  For more information navigate to Appendix-C (Page: 52) in
     *  https://www.aramex.com/docs/default-source/resourses/resourcesdata/shipping-services-api-manual.pdf
     */
    'Services' => env('ARAMEX_SERVICES', ''),

    /**
     *    Default Currency Code
     *    if your project supports more than currency code, so you should send CurrencyCode parameter when shipment
     *    Creation (if needed) but you can set the default currency code so you can just not pass it when you only
     *    support one currency or other purposes.
     */
    'CurrencyCode' => env('ARAMEX_CURRENCY_CODE', 'SAR'),

    /**
     *     Label Information
     *     Available Values:
     *      ReportID   => 9201, 9729 (9729 use it when COD to extract readable reports, 9201 with COD will not be accepted)
     *      ReportType => “URL” to get report hosted on URL as PDF, “RPT” to get a streamed file
     */
    'LabelInfo'      => [
        'ReportID'   => env('ARAMEX_REPORT_ID', 9729),
        'ReportType' => env('ARAMEX_REPORT_TYPE', 'URL'),
    ],

    'tracking_url' => 'https://www.aramex.com/ae/en/track/results?ShipmentNumber=',
];
