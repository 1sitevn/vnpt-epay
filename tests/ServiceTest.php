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

        var_dump(1, $response);
        exit;

        $this->assertTrue(true);
    }
}
