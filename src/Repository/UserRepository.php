<?php declare(strict_types = 1);


namespace App\Repository;


use App\Entity\User;
use App\Validation\UserValidation;
use App\Validation\ValidationException;

class UserRepository
{
    /**
     * @param $array
     * @return User
     * @throws ValidationException
     */
    public static function generateUserFromArray($array): User
    {
        $user = new User();
        $user->setId(UserValidation::validateId($array[0]));
        $user->setFio(UserValidation::validateFio($array[1]));
        $user->setEmail(UserValidation::validateEmail($array[2]));
        $user->setCurrency(UserValidation::validateCurrency($array[3]));
        $user->setSum(UserValidation::validateSum($array[4]));

        return $user;
    }
}