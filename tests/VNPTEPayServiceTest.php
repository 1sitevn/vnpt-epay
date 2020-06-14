<?php


namespace OneSite\VNPT\EPay;

use PHPUnit\Framework\TestCase;


/**
 * Class ServiceTest
 * @package OneSite\VNPT\EPay
 */
class VNPTEPayServiceTest extends TestCase
{

    /**
     * @var VNPTEPayService
     */
    private $service;

    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = new VNPTEPayService();
    }

    /**
     *
     */
    public function tearDown(): void
    {
        $this->service = null;

        parent::tearDown();
    }

    /**
     * PHPUnit test: vendor/bin/phpunit --filter testQueryBalance tests/VNPTEPayServiceTest.php
     */
    public function testQueryBalance()
    {
        $response = $this->service->queryBalance();

        echo "\n" . json_encode($response);

        if (isset($response['error'])) {
            return $this->assertTrue(false);
        }

        return $this->assertTrue(true);
    }

    /**
     * PHPUnit test: vendor/bin/phpunit --filter testTopup tests/VNPTEPayServiceTest.php
     */
    public function testTopup()
    {
        $response = $this->service->topup([
            'request_id' => uniqid('9PAY_TEST'),
            'provider' => Provider::TYPE_VIETTEL,
            'account' => '01676696055',
            'amount' => 100000
        ]);

        echo "\n" . json_encode($response);

        if (isset($response['error'])) {
            return $this->assertTrue(false);
        }

        return $this->assertTrue(true);
    }

    /**
     * PHPUnit test: vendor/bin/phpunit --filter testDownloadSoftpin tests/VNPTEPayServiceTest.php
     */
    public function testDownloadSoftpin()
    {
        $response = $this->service->downloadSoftpin([
            'request_id' => uniqid('9PAY_TEST'),
            'provider' => Provider::TYPE_VIETTEL,
            'amount' => 10000,
            'quantity' => 2
        ]);

        echo "\n" . json_encode($response);

        if (isset($response['error'])) {
            return $this->assertTrue(false);
        }

        return $this->assertTrue(true);
    }

    /**
     * PHPUnit test: vendor/bin/phpunit --filter testReDownloadSoftpin tests/VNPTEPayServiceTest.php
     */
    public function testReDownloadSoftpin()
    {
        $response = $this->service->reDownloadSoftpin('partnerTest_PHP_1591109500887');

        echo "\n" . json_encode($response);

        if (isset($response['error'])) {
            return $this->assertTrue(true);
        }

        return $this->assertTrue(false);
    }

    /**
     * PHPUnit test: vendor/bin/phpunit --filter testCheckTransSuccess tests/VNPTEPayServiceTest.php
     */
    public function testCheckTransSuccess()
    {
        $response = $this->service->checkTrans('9PAY_TEST5ee59b21ef15e', VNPTEPayService::TYPE_TOPUP);

        echo "\n" . json_encode($response);

        if (isset($response['error'])) {
            return $this->assertTrue(false);
        }

        return $this->assertTrue(true);
    }

    /**
     * PHPUnit test: vendor/bin/phpunit --filter testCheckTransFail tests/VNPTEPayServiceTest.php
     */
    public function testCheckTransFail()
    {
        $response = $this->service->checkTrans('partnerTest_PHP_191293241189', VNPTEPayService::TYPE_TOPUP);

        echo "\n" . json_encode($response);

        if (isset($response['error'])) {
            return $this->assertTrue(true);
        }

        return $this->assertTrue(false);
    }
}
