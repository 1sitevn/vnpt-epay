<?php

namespace OneSite\VNPT\EPay;


use GuzzleHttp\Client;


/**
 * Class Service
 * @package OneSite\VNPT\EPay
 */
class Service
{
    const TYPE_TOPUP = 1;
    const TYPE_DOWNLOAD_SOFTPIN = 2;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var array|mixed|null
     */
    private $wsUrl;
    /**
     * @var array|mixed|null
     */
    private $partnerUserName;
    /**
     * @var array|mixed|null
     */
    private $partnerPassword;
    /**
     * @var array|mixed|null
     */
    private $keySofpin;
    /**
     * @var array|mixed|null
     */
    private $privateKeyPath;
    /**
     * @var array|mixed|null
     */
    private $publicKeyPath;
    /**
     * @var int
     */
    private $timeOut = 150;

    /**
     * @var \SoapClient
     */
    private $service;

    /**
     * Service constructor.
     * @throws \SoapFault
     */
    public function __construct()
    {
        $this->client = new Client();

        $this->wsUrl = Config::get('vnpt.epay.ws_url');
        $this->partnerUserName = Config::get('vnpt.epay.partner_username');
        $this->partnerPassword = Config::get('vnpt.epay.partner_password');
        $this->keySofpin = Config::get('vnpt.epay.key_sofpin');
        $this->privateKeyPath = Config::get('vnpt.epay.private_key_path');
        $this->publicKeyPath = Config::get('vnpt.epay.public_key_path');

        $this->service = new \SoapClient($this->wsUrl);
    }

    /**
     * @return mixed|null
     */
    public function queryBalance()
    {
        try {
            $response = $this->service->__soapCall("queryBalance", [
                'partnerName' => $this->partnerUserName,
                'sign' => $this->sign($this->partnerUserName)
            ]);

            if ($response->errorCode != 0) {
                return $this->getResponseServiceError($response);
            }

            return [
                'data' => [
                    'balance_avaiable' => $response->balance_avaiable,
                    'balance_bonus' => $response->balance_bonus,
                    'balance_debit' => $response->balance_debit,
                    'balance_money' => $response->balance_money
                ]
            ];
        } catch (\Exception $ex) {
            return $this->getResponseServerError($ex->getMessage());
        }
    }

    /**
     * @param array $params
     * @return array
     */
    public function topup(array $params = [])
    {
        try {
            $requestId = $this->partnerUserName . '_' . time() . rand(000, 999);

            $data = [
                'requestId' => $requestId,
                'partnerName' => $this->partnerUserName,
                'provider' => $params['provider'],
                'account' => $params['account'],
                'amount' => $params['amount'],
                'sign' => $this->sign($requestId
                    . $this->partnerUserName
                    . $params['provider']
                    . $params['account']
                    . $params['amount']
                )
            ];

            $response = $this->service->__soapCall("topup", $data);

            if ($response->errorCode != 0) {
                return $this->getResponseServiceError($response);
            }

            return [
                'data' => [
                    'requestId' => $requestId,
                    'partnerName' => $this->partnerUserName,
                    'provider' => $params['provider'],
                    'account' => $params['account'],
                    'amount' => $params['amount']
                ]
            ];
        } catch (\Exception $ex) {
            return $this->getResponseServerError($ex->getMessage());
        }
    }


    /**
     * @param $requestId
     * @param int $type
     * @return array
     */
    public function checkTrans($requestId, $type = 1)
    {
        try {
            $data = [
                'requestId' => $requestId,
                'partnerName' => $this->partnerUserName,
                'type' => $type,
                'sign' => $this->sign($requestId . $this->partnerUserName . $type)
            ];

            $response = $this->service->__soapCall("checkTrans", $data);

            if ($response->errorCode != 0) {
                return $this->getResponseServiceError($response);
            }

            return [
                'data' => []
            ];
        } catch (\Exception $ex) {
            return $this->getResponseServerError($ex->getMessage());
        }
    }

    /**
     * @param $response
     * @return array
     */
    private function getResponseServiceError($response)
    {
        return [
            'error' => [
                'message' => $response->message,
                'code' => $response->errorCode
            ]
        ];
    }

    /**
     * @param $message
     * @return array
     */
    private function getResponseServerError($message)
    {
        return [
            'error' => [
                'message' => $message,
                'code' => 110
            ]
        ];
    }

    /**
     * @param $data
     * @return string
     */
    private function sign($data)
    {
        $privateKey = file_get_contents($this->privateKeyPath);

        openssl_sign($data, $binarySignature, $privateKey, OPENSSL_ALGO_SHA1);

        return base64_encode($binarySignature);
    }

}
