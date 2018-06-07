<?php

namespace WAM\Paylands\Tests\Unit;

use Http\Message\ResponseFactory;
use Http\Mock\Client as HttpClient;
use WAM\Paylands\Client;
use WAM\Paylands\RequestFactory;

/**
 * Class ClientTest.
 *
 * @author Santi Garcia <sgarcia@wearemarketing.com>, <sangarbe@gmail.com>
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function tryIsModeSandboxEnabled()
    {
        $httpClient = new HttpClient($this->prophesize(ResponseFactory::class)->reveal());

        $apiRequestFactoryMock = $this->prophesize(RequestFactory::class);

        $apiClient = new Client($httpClient, $apiRequestFactoryMock->reveal(), true);

        $this->assertTrue($apiClient->isModeSandboxEnabled());

        $apiClient = new Client($httpClient, $apiRequestFactoryMock->reveal(), false);

        $this->assertFalse($apiClient->isModeSandboxEnabled());
    }
}
