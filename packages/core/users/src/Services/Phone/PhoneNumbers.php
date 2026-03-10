<?php

namespace Core\Users\Services\Phone;

use Core\Settings\Helpers\PhoneNumberSettings;
use Core\Logs\Helpers\LogHelper;

class PhoneNumbers
{

    private static $packageInstance = [];
    public static function getCountryCode($code)
    {

        if ($code) {

            if (is_array($code) && array_key_exists("value", $code)) {
                $code = $code["value"];
            }

            $characters = str_split($code);

            foreach ($characters as $i => $character) {
                if (!in_array($character, ["0", "+"])) {
                    $code = substr($code, $i, strlen($code));
                    break;
                }
            }

            return $code;
        }

        return null;
    } // end of countryCodeCheck
    public static function getAlphaFromCode($code)
    {
        $alphaCodes = PhoneNumberSettings::getAlphaCodes();
        if (array_key_exists($code, $alphaCodes)) {
            return $alphaCodes[$code];
        }
        return null;
    } // end of countryCode
    public static function getCountry($code)
    {
        if ($code) {
            return  collect(PhoneNumberSettings::getCountries())->filter(function($item) use($code){return $item['code'] == $code;})->first() ?? null;
        }
        return null;
    } // end of countryCodeCheck
    /*
    
    */
    public static function getPackageInstance(string $package, string $phoneNumber, $countryCode = null)
    {
        if (!isset(self::$packageInstance[$package]) && $package == 'libPhoneNumber') {
            self::$packageInstance['libPhoneNumber']  = \libphonenumber\PhoneNumberUtil::getInstance();
        }
        return self::$packageInstance[$package];
    }
    public static function libPhoneNumber(string $phoneNumber, $countryCode = null)
    {
        $libPhoneNumber = self::getPackageInstance('libPhoneNumber', $phoneNumber, $countryCode);
        if(is_array($countryCode)){
            if(isset($countryCode['value'])){
                $countryCode = $countryCode['value'];
            }else{
                dd($countryCode);

            }
            
        }
        $countryCode    = strtoupper($countryCode);
        $packageResult  = $libPhoneNumber->parse($phoneNumber, $countryCode);
        return ['status' => $libPhoneNumber->isValidNumber($packageResult), 'packageResult'  =>    $packageResult];
    }
    public static function getPackageResult(string $package, string $phoneNumber, $countryCode = null)
    {
        if ($package == 'libPhoneNumber') {
            return self::libPhoneNumber($phoneNumber, $countryCode);
        }
    }
    public static function checkPhonNumber(string $phoneNumber, $countryCode = null)
    {
        try {

            if (!is_numeric(str_replace('+', '', $phoneNumber))) {
                return ['status' => false, "message" => "Phone number must be numeric"];
            }

            if (str_starts_with($phoneNumber, '00')) {
                $phoneNumber = '+' . substr($phoneNumber, 2);
            }
            if (isset($countryCode) && is_numeric(str_replace('+', '', $countryCode))) {
                $countryCode = self::getCountryCode($countryCode);
                if (!$countryCode) {
                    return ['status' => false, "message" => trans("Invalid country code")];
                }
                $countryCode = self::getAlphaFromCode($countryCode);
            }

            //if no code
            $OriginalPhone     = new Phone($phoneNumber, $countryCode);
            $isValidPhone      = false;
            $phoneCountry      = [];

            if(isset($countryCode) & !empty($countryCode)){
                $result         = self::getPackageResult('libPhoneNumber', $phoneNumber, $countryCode);
                $packagePhone   = $result['packageResult'];
                if ($result['status']) {
                    $isValidPhone = true;
                    $phoneCountry = self::getCountry($packagePhone->getCountryCode());
                }

            }else{
                $iso            =  ["EG", "SA", "AE", "QA", "LY", "KW", "BH", "OM", "CA", "US", "UK", "CL"]; //TODO: laod them form database
                foreach ($iso as $isoItem) {

                    $result         = self::getPackageResult('libPhoneNumber', $phoneNumber, $isoItem);
                    $packagePhone   = $result['packageResult'];
                    if ($result['status']) {
                        $isValidPhone = true;
                        $phoneCountry = self::getCountry($packagePhone->getCountryCode());
                        break;
                    }
                }
            }

            if ($isValidPhone) {
                return ['status' => true, 'phone_country' => '+'.$packagePhone->getCountryCode().$packagePhone->getNationalNumber(), 'phone' => $packagePhone->getNationalNumber(), "code" => $packagePhone->getCountryCode(),"country" =>$phoneCountry ,"obj" => $packagePhone];
            } else {
                return ((config("backend-settings.general.ignore_phone_validation"))) ?
                    ['status'   =>  true,  'phone' => $OriginalPhone->getNationalNumber(), "code" => $OriginalPhone->getCountryCode(), "obj" => $OriginalPhone] :
                    ['status'   =>  false, 'phone' => $OriginalPhone->getNationalNumber(), "code" => $OriginalPhone->getCountryCode(), "obj" => $OriginalPhone ,'message' => trans('Invalid phone number'),];
            }
        } catch (\libphonenumber\NumberParseException $e) {
            //add logs here
            LogHelper::register('path','package can\'t handel this number',['status' => false, 'phone' => $OriginalPhone->getNationalNumber(), "code" => $OriginalPhone->getCountryCode(), "obj" => $OriginalPhone],'phone-checker');
            return ['status' => false, 'phone' => $OriginalPhone->getNationalNumber(), "code" => $OriginalPhone->getCountryCode(), "obj" => $OriginalPhone];
        }
    }
}
