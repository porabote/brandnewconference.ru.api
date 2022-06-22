<?php
namespace App\Http\Components\Thyssen\CredInform;


class CredInform {

    private $url = 'https://restapi.credinform.ru';
    private $login = 'credlogin@ro.ru';
    private $psw = '123456';
    private $accessKey;

    function __construct()
    {
        $this->accessKey = $this->setAccessKey();
    }

    function setAccessKey()
    {
        $data = [
            'url' => $this->url,
            'uri' => '/api/Authorization/GetAccessKey',
            'response' => [
                'username' => $this->login,
                'password' => $this->psw
            ]
        ];

        $ch = curl_init($data['url'] . $data['uri']);
        curl_setopt_array($ch, [
            CURLOPT_URL            => $data['url'] . $data['uri'],
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HEADER         => 0, // allow return headers
            CURLOPT_HTTPHEADER     => ['accept: text/plain'],
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_COOKIESESSION  => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     =>  json_encode($data['response'])
        ]);

        $output = curl_exec($ch);
        curl_close($ch);

        return json_decode($output, true)['accessKey'];
    }

    function getCompanyInfo($taxNumber, $statisticalNumber)
    {
        $data = '{
          "language": "Russian",
          "searchCompanyParameters": {
	          statisticalNumber: "' . $statisticalNumber . '",
	          companyName : "",	
              taxNumber: "' . $taxNumber . '",
              includeBranch: "true"
          }
        }';

        $ch = curl_init($this->url . '/api/Search/SearchCompany');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HEADER         => 0, // allow return headers
            CURLOPT_HTTPHEADER     => ['accept: text/plain', 'accessKey: ' . $this->accessKey, 'Content-Type: application/json-patch+json'],
            CURLOPT_COOKIESESSION  => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $data
        ]);

        $output = curl_exec($ch);//debug($output);
        curl_close($ch);

        return json_decode($output, true)['companyDataList'][0];
    }

    function getBlobData($shema, $section = '/api/Report/GetFile')
    {
        $ch = curl_init($this->url . $section);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HEADER         => 0, // allow return headers
            CURLOPT_HTTPHEADER     => ['accept: text/plain', 'accessKey: ' . $this->accessKey, 'Content-Type: application/json-patch+json'],
            CURLOPT_COOKIESESSION  => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $shema
        ]);

        $output = curl_exec($ch);
        curl_close($ch);

        return json_decode($output);
    }

}