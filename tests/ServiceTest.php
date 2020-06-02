<?php


namespace OneSite\VNPT\EPay;


use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

require_once "helpers.php";

/**
 * Class ServiceTest
 * @package OneSite\VNPT\EPay
 */
class ServiceTest extends TestCase
{

    /**
     * @var Service
     */
    private $service;

    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->service = new Service();
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
     * PHPUnit test: vendor/bin/phpunit --filter testQueryBalance tests/ServiceTest.php
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
     * PHPUnit test: vendor/bin/phpunit --filter testTopup tests/ServiceTest.php
     */
    public function testTopup()
    {
        $response = $this->service->topup([
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
     * PHPUnit test: vendor/bin/phpunit --filter testDownloadSoftpin tests/ServiceTest.php
     */
    public function testDownloadSoftpin()
    {
        $response = $this->service->downloadSoftpin([
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
     * PHPUnit test: vendor/bin/phpunit --filter testReDownloadSoftpin tests/ServiceTest.php
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
     * PHPUnit test: vendor/bin/phpunit --filter testCheckTransSuccess tests/ServiceTest.php
     */
    public function testCheckTransSuccess()
    {
        $response = $this->service->checkTrans('partnerTest_PHP_1591088750221', Service::TYPE_TOPUP);

        echo "\n" . json_encode($response);

        if (isset($response['error'])) {
            return $this->assertTrue(false);
        }

        return $this->assertTrue(true);
    }

    /**
     * PHPUnit test: vendor/bin/phpunit --filter testCheckTransFail tests/ServiceTest.php
     */
    public function testCheckTransFail()
    {
        $response = $this->service->checkTrans('partnerTest_PHP_191293241189', Service::TYPE_TOPUP);

        echo "\n" . json_encode($response);

        if (isset($response['error'])) {
            return $this->assertTrue(true);
        }

        return $this->assertTrue(false);
    }
}
