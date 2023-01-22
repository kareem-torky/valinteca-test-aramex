<?php

namespace Valinteca\Aramex\Tests\Unit;

use Orchestra\Testbench\TestCase;
use Valinteca\Aramex\Aramex;

class AramexTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'aramex.account_country_code'  => env('ARAMEX_COUNTRY_CODE', 'GB'),
            'aramex.account_entity'        => env('ARAMEX_ACCOUNT_ENTITY', 'LON'),
            'aramex.account_number'        => env('ARAMEX_ACCOUNT_NUMBER', '102331'),
            'aramex.account_pin'           => env('ARAMEX_ACCOUNT_PIN', '321321'),
            'aramex.username'              => env('ARAMEX_USERNAME', 'testingapi@aramex.com'),
            'aramex.password'              => env('ARAMEX_PASSWORD', 'R123456789$r'),
            'aramex.version'               => env('ARAMEX_VERSION', 'v1'),
            'aramex.company_name'          => env('ARAMEX_COMPANY_NAME', 'SA'),
        ]);
    }

    /** @test */
    // public function test_that_create_shipment_returns_processed_shipment_id()
    // {
    //     $shipper = [
    //         'line1' => 'مكتب شركة النهضة',
    //         'line2' => '',
    //         'line3' => '',
    //         'city' => 'Jeddah',
    //         'zip_code' => '22412',
    //         'country_code' => 'SA',
    //         'name' => 'أحمد محمد',
    //         'company_name' => 'شركة النهضة',
    //         'email' => '',
    //         'phone' => '0532476784',
    //         'cell_phone' => '0532476784',
    //         'reference' => '',
    //     ];
    //     $consignee = [
    //         'line1' => 'شارع وسط المدينة',
    //         'line2' => '',
    //         'line3' => '',
    //         'city' => 'Jeddah',
    //         'zip_code' => '22412',
    //         'country_code' => 'SA',
    //         'name' => 'حامد الفواز',
    //         'company_name' => '',
    //         'email' => '',
    //         'phone' => '0530820588',
    //         'cell_phone' => '0530820588',
    //         'reference' => '',
    //     ];
    //     $payment = [
    //         'customs_value_amount' => 100,
    //         'cash_on_delivery_amount' => 100,
    //         'cash_additional_amount' => 100,
    //         'collect_amount' => 100,
    //         'currency_code' => 'SAR',
    //     ];
    //     $specifications = [
    //         'number_of_pieces' => 3,
    //         'length' => 10,
    //         'width' => 20,
    //         'height' => 30,
    //         'length_unit' => 'cm',
    //         'weight' => 1,
    //         'weight_unit' => 'kg',
    //     ];
    //     $delivery = [
    //         'shipping_date_time' => time(),
    //         'due_date' => time(),
    //         'pickup_location' => 'Receiption',
    //     ];

    //     $order = [
    //         'shipper' => $shipper,
    //         'consignee' => $consignee,
    //         'payment' => $payment,
    //         'specifications' => $specifications,
    //         'delivery' => $delivery,
    //         'comments' => 'this is test order',
    //     ];

    //     $shipment = Aramex::createShipment($order);
    //     $this->assertObjectHasAttribute('ID', $shipment);
    // }
}