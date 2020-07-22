<?php


namespace App\Validation;


class UserValidation
{
    public static function validateId($id)
    {
        return filter_var($id, FILTER_VALIDATE_INT);
    }

    public static function validateFio($fio)
    {
        return filter_var($fio, FILTER_SANITIZE_STRING);
    }

    public static function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function validateCurrency($currency)
    {
        return is_string($currency) && mb_strlen($currency) == 3 ? $currency : false;
    }

    public static function validateSum($sum)
    {
        if (preg_match('/^\d+(.\d{0,2})?$/', $sum)) {
            return filter_var($sum, FILTER_VALIDATE_FLOAT, ['options' => array('decimal' => '.')]);
        }
        return false;
    }
}