<?php declare(strict_types=1);


namespace App\Builder;


use App\Entity\User;
use App\Validation\UserValidation;

class UserBuilder
{
    /**
     * @var UserValidation
     */
    private UserValidation $validator;

    /**
     * UserBuilder constructor.
     * @param UserValidation $validator
     */
    public function __construct(UserValidation $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param $array
     * @return User
     */
    public function generateUserFromArray($array): User
    {
        $user = new User();
        $user->setId($this->validator->validateId($array[0]));
        $user->setFio($this->validator->validateFio($array[1]));
        $user->setEmail($this->validator->validateEmail($array[2]));
        $user->setCurrency($this->validator->validateCurrency($array[3]));
        $user->setSum($this->validator->validateSum($array[4]));

        return $user;
    }
}