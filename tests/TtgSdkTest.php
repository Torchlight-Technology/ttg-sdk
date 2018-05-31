<?php
require 'vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use torchlighttechnology\TtgSDK;

class TtgSdkTest extends TestCase
{

    public function testBasic()
    {
        $url = 'http://delayed-event.local/delayed-events/';
        $ttgSdk = new TtgSDK($url);

        $response = $ttgSdk->fireEvents(
            '{}',
            'POST'
        );

        $this->assertNotEmpty($response);

        if(!empty($response->status)) {
            $this->assertNotEquals($response->status, 'error');
        }
    }

    public function testBasicWithHeaders()
    {
        $url = 'https://api.torchte.ch/delayedevents/staging/delayed-events/';
        $ttgSdk = new TtgSDK($url);

        $ttgSdk->setHeaders(
            ['x-api-key: '] // fill it in to get test to pass
        );

        $args = [
            'callback_uri' => 'http://bar.com',
            'fire_date' => '2018-04-23 12:00:00',
            'parameters' => ['foo' => 'barbasol']
        ];

        $response = $ttgSdk->create(
            json_encode($args),
            'POST'
        );


        $this->assertNotEmpty($response);

        if(!empty($response->status)) {
            $this->assertNotEquals($response->status, 'error');
        }
    }


    public function testCamelCase()
    {
        $url = 'https://api.torchte.ch/delayedevents/staging/delayed-events/';
        $ttgSdk = new TtgSDK($url);

        $ttgSdk->setHeaders(
            ['x-api-key: '] // fill it in to get test to pass
        );

        // Fire that event so re-tests don't dupe out
        $response = $ttgSdk->fireEvents('{}', 'GET');

        $this->assertNotEmpty($response);
        if(!empty($response->status)) {
            //print_r($response);
            $this->assertNotEquals($response->status, 'error');
        }
    }
}