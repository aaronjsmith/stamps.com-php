<?php

class stamps_com
{
    private $Authenticator;

    //API LOGIN
    private $IntegrationID = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";
    private $Username = "XXXXXXXXXXXXXXX";
    private $Password = "XXXXXXXXXXXXXXX";
    private $wsdl = "https://swsim.testing.stamps.com/swsim/swsimv38.asmx?wsdl";

    public $client;

    public $ServiceType = array(
        "US-FC" =>  "USPS First-Class Mail",
        "US-MM" =>  "USPS Media Mail",
        "US-PP" =>  "USPS Parcel Post ",
        "US-PM" =>  "USPS Priority Mail",
        "US-XM" =>  "USPS Priority Mail Express",
        "US-EMI" =>  "USPS Priority Mail Express International",
        "US-PMI" =>  "USPS Priority Mail International",
        "US-FCI" =>  "USPS First Class Mail International",
        "US-CM" =>  "USPS Critical Mail",
        "US-PS" =>  "USPS Parcel Select",
        "US-LM" =>  "USPS Library Mail"
    );

    function __construct()
    {
        $this->connect();
        $this->GetRates("90210","90210",null,"10",6,6,6,"Package","2014-10-28",'100',null);
    }

    function connect(){
        $authData = array(
            "Credentials"       => array(
                "IntegrationID"     => $this->IntegrationID,
                "Username"          => $this->Username,
                "Password"          => $this->Password
        ));

        $this->client = new SoapClient('https://swsim.testing.stamps.com/swsim/swsimv38.asmx?wsdl');
        $auth = $this->client->AuthenticateUser($authData);
        $this->Authenticator = $auth->Authenticator;
    }

    function GetRates($FromZIPCode,$ToZIPCode = null,$ToCountry = null,$WeightLb,$Length,$Width,$Height,$PackageType,$ShipDate,$InsuredValue,$ToState = null){
        $data = array(
                "Authenticator"     => $this->Authenticator,
                "Rate" => array(
                    "FromZIPCode" => $FromZIPCode,
                    "WeightLb" => $WeightLb,
                    "Length" => $Length,
                    "Width" => $Width,
                    "Height" => $Height,
                    "PackageType" => $PackageType,
                    "ShipDate" => $ShipDate,
                    "InsuredValue" => $InsuredValue
                )
        );

        if($ToZIPCode == null && $ToCountry != null){
            $data["Rate"]['ToCountry'] = $ToCountry;
        }else{
            $data["Rate"]['ToZIPCode'] = $ToZIPCode;
        }

        if($ToState != null){
            $data["Rate"]['ToState'] = $ToState;
        }

        $r = $this->client->GetRates($data);
        $r = $r->Rates->Rate;

        echo "<pre>";

        foreach ($r as $k => $v) {
            foreach ($data['Rate'] as $kk => $vv) {
                $result[$k][$kk] = $v->$kk;
            }

             $result[$k] =  $result[$k] + array(
                "ServiceType" => $this->ServiceType[$v->ServiceType],
                "Amount" => $v->Amount,
                "PackageType" => $v->PackageType,
                "WeightLb" => $v->WeightLb,
                "Length" => $v->Length,
                "Width" => $v->Width,
                "Height" => $v->Height,
                "ShipDate" => $v->ShipDate,
                "DeliveryDate" => $v->DeliveryDate,
                "RateCategory" => $v->RateCategory,
                "ToState" => $v->ToState,
            );
        }

        print_r($result);
    }
}

$stamps_com = new stamps_com;
