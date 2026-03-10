<?php 
namespace Core\Users\Services\Phone;
class Phone{
    public $phone;
    public $code;

    function __construct($phone, $code) {
        $this->phone = $phone;
        $this->code = $code;
    }

    function getNationalNumber() {
        return $this->phone;
    }

    function getCountryCode() {
        return is_numeric($this->code) ? $this->code : 20;
    }

};
