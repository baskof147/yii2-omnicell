<?php

namespace baskof147\omnicell;

use mikk150\sms\BaseProvider;
use Yii;
use yii\httpclient\Client;
use yii\httpclient\XmlFormatter;

/**
 * Class Provider
 * @package baskof147\omnicell
 */
class Provider extends BaseProvider
{
    /**
     * @var string
     */
    public $apiUrl = 'https://api.omnicell.com.ua/ip2sms/';

    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $password ;

    /**
     * @inheritDoc
     */
    public $messageClass = Message::class;

    /**
     * @var Client
     */
    private $_client;

    /**
     * @return Client
     */
    public function getClient()
    {
        if (!$this->_client) {
            $this->_client = new Client([
                'baseUrl' => $this->apiUrl,
                'formatters' => [
                    Client::FORMAT_XML => [
                        'class' => XmlFormatter::class,
                        'encoding' => 'UTF-8',
                        'contentType' => 'application/vnd.alt+xml',
                        'rootTag' => 'message'
                    ],
                ],
            ]);
        }
        return $this->_client;
    }

    /**
     * @inheritDoc
     */
    protected function sendMessage($message)
    {
        $requests = [];
        foreach ((array) $message->getTo() as $recipient) {
            $requests[] = $this->getClient()->post('', [
                'da' => $recipient,
                'text' => $message->getBody(),
                'oa' => $message->getFrom()
            ], [
                'Authorization' => 'Basic ' . base64_encode($this->username . ':' . $this->password)
            ])
                ->setFormat(Client::FORMAT_XML);
        }
        $responses = $this->getClient()->batchSend($requests);
        $messagesSent = true;
        foreach ($responses as $response) {
            if (strpos($response->content, 'Accepted') === false) {
                Yii::error('Error response: "' . $response->content . '". Message: ' . $message->toString(), 'number');
                $messagesSent = false;
            }
        }
        return count($responses) ? $messagesSent : false;
    }
}
