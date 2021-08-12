<?php

namespace OneSite\VNPT\EPay;

use Carbon\Carbon;
use OneSite\Core\Security\RSA;


/**
 * Class VNPTEPayService
 * @package OneSite\VNPT\EPay
 */
class VNPTEPayService
{
    /**
     *
     */
    const TYPE_TOPUP = 1;
    /**
     *
     */
    const TYPE_DOWNLOAD_SOFTPIN = 2;

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
     * Service constructor.
     * @throws \SoapFault
     */
    public function __construct()
    {
        $this->wsUrl = config('vnpt.epay.ws_url');
        $this->partnerUserName = config('vnpt.epay.partner_username');
        $this->partnerPassword = config('vnpt.epay.partner_password');
        $this->keySofpin = config('vnpt.epay.key_sofpin');
        $this->privateKeyPath = config('vnpt.epay.private_key_path');
        $this->publicKeyPath = config('vnpt.epay.public_key_path');
    }

    /**
     * @return mixed|null
     */
    public function queryBalance()
    {
        try {
            $service = new \SoapClient($this->wsUrl);

            $response = $service->__soapCall("queryBalance", [
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
            $requestId = !empty($params['request_id']) ? $params['request_id'] : null;
            $requestId = $this->rebuildRequestId($requestId);

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

            $service = new \SoapClient($this->wsUrl);

            $response = $service->__soapCall("topup", $data);

            if ($response->errorCode != 0) {
                return $this->getResponseServiceError($response);
            }

            return [
                'data' => [
                    'request_id' => $this->getRequestIdFromResponse($requestId),
                    'partner_name' => $this->partnerUserName,
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
     * @param array $params
     * @return array
     */
    public function downloadSoftpin(array $params = [])
    {
        try {
            $requestId = !empty($params['request_id']) ? $params['request_id'] : null;
            $requestId = $this->rebuildRequestId($requestId);

            $data = [
                'requestId' => $requestId,
                'partnerName' => $this->partnerUserName,
                'provider' => $params['provider'],
                'amount' => $params['amount'],
                'quantity' => $params['quantity'],
                'sign' => $this->sign($requestId
                    . $this->partnerUserName
                    . $params['provider']
                    . $params['amount']
                    . $params['quantity']
                )
            ];

            $service = new \SoapClient($this->wsUrl);

            $response = $service->__soapCall("downloadSoftpin", $data);

            if ($response->errorCode != 0) {
                return $this->getResponseServiceError($response);
            }

            $listCards = $this->decrypt($response->listCards);
            $listCards = json_decode($listCards);
            $listCards = !empty($listCards->listCards) ? $listCards->listCards : [];
            $listCards = $this->getCardInfo($listCards);

            return [
                'data' => [
                    'request_id' => $this->getRequestIdFromResponse($requestId),
                    'partnerName' => $this->partnerUserName,
                    'provider' => $params['provider'],
                    'amount' => $params['amount'],
                    'quantity' => $params['quantity'],
                    'cards' => $listCards,
                    //'response' => $response
                ]
            ];
        } catch (\Exception $ex) {
            return $this->getResponseServerError($ex->getMessage());
        }
    }

    /**
     * @param $requestId
     * @return array
     */
    public function reDownloadSoftpin($requestId)
    {
        try {
            $data = [
                'requestId' => $requestId,
                'partnerName' => $this->partnerUserName,
                'sign' => $this->sign($this->partnerUserName)
            ];

            $service = new \SoapClient($this->wsUrl);

            $response = $service->__soapCall("reDownloadSoftpin", $data);

            if ($response->errorCode != 0) {
                return $this->getResponseServiceError($response);
            }

            $listCards = $this->decrypt($response->listCards);
            $listCards = json_decode($listCards);
            $listCards = !empty($listCards->listCards) ? $listCards->listCards : [];
            $listCards = $this->getCardInfo($listCards);

            return [
                'data' => [
                    'request_id' => $this->getRequestIdFromResponse($requestId),
                    'partnerName' => $this->partnerUserName,
                    'cards' => $listCards
                ]
            ];
        } catch (\Exception $ex) {
            return $this->getResponseServerError($ex->getMessage());
        }
    }

    /**
     * @param $data
     * @return false|string
     * @throws \Exception
     */
    private function decrypt($data)
    {
        try {
            return openssl_decrypt(base64_decode($data),
                'DES-EDE3',
                substr(md5($this->keySofpin), 0, 24),
                OPENSSL_RAW_DATA
            );
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
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
            $requestId = $this->partnerUserName . '_' . $requestId;

            $data = [
                'requestId' => $requestId,
                'partnerName' => $this->partnerUserName,
                'type' => $type,
                'sign' => $this->sign($requestId . $this->partnerUserName . $type)
            ];

            $service = new \SoapClient($this->wsUrl);

            $response = $service->__soapCall("checkTrans", $data);

            if ($response->errorCode != 0) {
                return $this->getResponseServiceError($response);
            }

            return [
                'data' => $response
            ];
        } catch (\Exception $ex) {
            return $this->getResponseServerError($ex->getMessage());
        }
    }

    /**
     * @param array $listCards
     * @return array
     */
    private function getCardInfo(array $listCards = [])
    {
        if (empty($listCards)) {
            return [];
        }

        $data = [];

        foreach ($listCards as $card) {
            list($provider, $amount, $serial, $pin, $expiredAt) = explode('|', $card);

            $data[] = [
                'provider' => $provider,
                'amount' => $amount,
                'serial' => $serial,
                'pin' => $pin,
                'expired_at' => Carbon::createFromFormat('H:i:s d/m/Y', $expiredAt)->format('Y-m-d H:i:s'),
            ];
        }

        return $data;
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

    /**
     * @param null $requestId
     * @return string
     */
    private function rebuildRequestId($requestId = null)
    {
        if (!empty($requestId)) {
            return $this->partnerUserName . '_' . $requestId;
        }

        return $this->partnerUserName . '_' . uniqid();
    }

    /**
     * @param $requestId
     * @return string|string[]
     */
    private function getRequestIdFromResponse($requestId)
    {
        return str_replace($this->partnerUserName . '_', '', $requestId);
    }
}
