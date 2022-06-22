<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Porabote\FullRestApi\Server\ApiTrait;
use App\Exceptions\ApiException;
use App\Http\Components\Thyssen\CredInform\CredInform;

class ContractorsController extends Controller
{
    use ApiTrait;

    static $authAllows;

    function __construct()
    {
        self::$authAllows = [
            'getFileByCredInform',
        ];
    }

    function getFileByCredInform($request)
    {
        try {
            if (!$request->query('taxNumber') || !$request->query('statisticalNumber')) {
                throw new ApiException('TaxNumber or statisticalNumber is empty');
            }

            $credinform = new CredInform();
            $info = $credinform->getCompanyInfo($request->query('taxNumber'), $request->query('statisticalNumber'));

            $shema = '{
                "language": "Russian",
                "companyId": "'.$info['companyId'].'",
                "sectionKeyList": [
                "ContactData", 
                "RegData",
                "FirstPageFinData",
                "Rating",
                "CompanyName",
                "UserVerification",
                "Bankruptcy",
                "LeadingFNS",	        	        	         
                "ShareFNS",	
                "Subs_short",	
                "SRO",
                "Pledges",	
                "ArbitrageInNewFormat",
                "EnforcementProceeding"
              ]
            }';
            $blobData = $credinform->getBlobData($shema);

            $shema = '{
              "period": {            
                "from": "2010-01-01T00:00:00"            
              },           
              "companyId": "948d514c-b993-40cf-b7d4-f1e7ad9c473c",            
              "language": "Russian"          
            }';
//
//           $blobData = $credinform->getBlobData($shema, '/api/CompanyInformation/FinancialEconomicIndicators?apiVersion=1.5');
//debug($blobData);
            $this->exportToExcel($blobData, $info);

        } catch (ApiException $e) {
            $e->jsonApiError();
        }
    }

    function exportToExcel($blobData, $info)
    {
        $fileName = \Porabote\Stringer\Stringer::transcript($info['captionName']);
//debug($blobData);
        $fileData = base64_decode($blobData->file->fileContents);

        $temp = tmpfile();
        fwrite($temp, $fileData);
        fseek($temp, 0);

        header("Content-type:application/pdf");
        header("Content-Disposition:attachment;filename=$fileName.pdf");

        echo readfile(stream_get_meta_data($temp)['uri']);
        fclose($temp);
    }
}