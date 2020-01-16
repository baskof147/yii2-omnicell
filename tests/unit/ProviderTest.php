<?php

namespace tests\unit;

use baskof147\omnicell\Provider;
use yii\httpclient\Client;
use yii\httpclient\Response;

/**
 * Class ProviderTest
 * @package tests\unit
 */
class ProviderTest extends \Codeception\Test\Unit
{
    /**
     * @test
     */
    public function testAcceptedStatus()
    {
        $provider = $this->make(Provider::class, [
            'getClient' => $this->make(Client::class, [
                'batchSend' => function () {
                    return [
                        $this->make(Response::class, [
                            'getContent' => '<?xml version="1.0" encoding="utf-8"?>
<message>
<state code="ACCEPT" date="22.05.2009 15:33:21">Message accepted for delivery</state>
<reference>33DF12</reference>
</message>'
                        ])
                    ];
                }
            ])
        ]);
        $this->assertTrue($provider->compose('Hi!!!')->setTo('+380345345345')->send());
    }

    /**
     * @test
     */
    public function testRejectedStatus()
    {
        $provider = $this->make(Provider::class, [
            'getClient' => $this->make(Client::class, [
                'batchSend' => function () {
                    return [
                        $this->make(Response::class, [
                            'getContent' => '<?xml version="1.0" encoding="utf-8"?>
<message>
<state code="INVREQ" date="22.05.2009 15:33:21">Message accepted for delivery</state>
<reference>33DF12</reference>
</message>'
                        ])
                    ];
                }
            ])
        ]);
        $this->assertFalse($provider->compose('Hi!!!')->setTo('+380345345345')->send());
    }
}
