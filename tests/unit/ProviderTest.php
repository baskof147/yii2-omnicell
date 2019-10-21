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
                            'getContent' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><status id="6614704225266" date="Mon, 21 Oct 2019 12:53:27 +0300"><state>Accepted</state></status>'
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
                            'getContent' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><status id="6614704225266" date="Mon, 21 Oct 2019 12:53:27 +0300"><state>Rejected</state></status>'
                        ])
                    ];
                }
            ])
        ]);
        $this->assertFalse($provider->compose('Hi!!!')->setTo('+380345345345')->send());
    }
}
