<?php declare(strict_types=1);


namespace App\Builder;


use App\Entity\User;
use App\Exception\UserFieldSetException;
use App\Validation\UserValidation;

class UserBuilder implements EntityBuilderInterface
{
    const EXPECTED_FIELD_COUNT = 5;
    const USER_FIELD_LIST = [
        'id', 'fio', 'email', 'currency', 'sum'
    ];

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
     * @todo magic
     * @param $array
     * @return User
     * @throws UserFieldSetException
     */
    public function generateEntityFromSimpleArray($array): User
    {
        $user = new User();
        foreach (self::USER_FIELD_LIST as $fieldId => $field) {
            $setMethod = 'set' . ucfirst($field);
            $validateMethod = 'validate' . ucfirst($field);
            $value = $this->validator->{$validateMethod}($array[$fieldId]);
            if ($value === false) {
                throw new UserFieldSetException("Bad $field");
            }
            $user->{$setMethod}($value);
        }
        return $user;
    }

    public function getExpectedFieldCount(): int
    {
        return self::EXPECTED_FIELD_COUNT;
    }
}