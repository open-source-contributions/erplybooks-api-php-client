<?php
/**
 * Erply Books API PHP client
 *
 * @author Rene Korss <rene@koren.ee>
 * @copyright Copyright (c) 2020 Rene Korss (https://koren.ee)
 * @license MIT
 */

namespace Koren\ErplyBooks\Tests;

use Http\Client\HttpClient;
use GuzzleHttp\Psr7\Request;
use Koren\ErplyBooks\Client;
use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Koren\ErplyBooks\Resource\BaseResource;
use Http\Adapter\Guzzle6\Client as GuzzleClient;

final class ClientTest extends BaseTest
{
    protected function setUp() : void
    {
        parent::setUp();
    }

    public function testCanInitClient()
    {
        // Defaults to Guzzle
        $this->assertInstanceOf(
            GuzzleClient::class,
            (new Client('api-key'))->getHttpClient()
        );

        $this->assertInstanceOf(
            Client::class,
            $this->client
        );

        $this->assertInstanceOf(
            HttpClient::class,
            $this->client->getHttpClient()
        );
    }

    /**
      * Test can get client from resource
      *
      * @return void
      */
    public function testCanGetClientFromResource()
    {
        $invoicesResource = $this->client->Invoices();

        $this->assertInstanceOf(
            Client::class,
            $invoicesResource->getClient()
        );
    }

    /**
     * Test that Client sets correct authentication header
     *
     * @return void
     */
    public function testSetsCorrectAuthenticationHeader()
    {
        /** @var \Koren\ErplyBooks\Resource\Invoices $invoicesResource */
        $invoicesResource = $this->client->Invoices();

        $request = new Request('GET', $invoicesResource->getEndpointUrl());

        $request = $this->client->authenticate($request);
        $this->assertInstanceOf(
            RequestInterface::class,
            $request
        );

        $this->assertTrue(
            $request->hasHeader('Accept')
        );

        $this->assertTrue(
            $request->hasHeader('Content-Type')
        );

        $this->assertEquals(
            $request->getHeader('Accept')[0],
            'application/json'
        );

        $this->assertEquals(
            $request->getHeader('Content-Type')[0],
            'application/json'
        );
    }

    /**
     * Test that Client can get existing resource
     *
     * @return void
     */
    public function testGetExistingResource()
    {
        $resource = $this->client->Invoices();
        $this->assertInstanceOf(
            BaseResource::class,
            $resource
        );
    }

    /**
     * Test that Client trows if non-existing resource is requested
     *
     * @return void
     */
    public function testGetNotExistingResource()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->client->nonExistingResource();
    }
}
