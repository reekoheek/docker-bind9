<?php

namespace Bapi\Test;

use PHPUnit_Framework_TestCase;
use GuzzleHttp\Client;

class ZoneTest extends PHPUnit_Framework_TestCase
{
    public function testSearch()
    {
        $client = new Client();
        $res = $client->request('GET', 'http://bind9_bind9_1:8080/zone');
        $bodyString = (string)$res->getBody();
        $body = json_decode($bodyString, true);
        $this->assertEquals(200, $res->getStatusCode());
        $this->assertEquals('application/json', $res->getHeaderLine('content-type'));
        $this->assertEquals(2, count($body));
        $this->assertEquals('sagara.id', $body[0]['domain']);
    }
}
