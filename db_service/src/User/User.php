<?php
namespace Datix\Server\User;


class User
{
    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $gender;

    /**
     * @var string
     */
    private $ipAddress;

    /**
     * User constructor.
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string $gender
     * @param string $ipAddress
     */
    protected function __construct($firstName, $lastName, $email, $gender, $ipAddress)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->gender = $gender;
        $this->ipAddress = $ipAddress;
    }

    /**
     * Named constructor to create itself from an array consisting of:
     * first_name, last_name, email, gender, ip_address
     * All keys are optional
     *
     * @param array $data Data to build from
     *
     * @return User
     */
    static function fromArray(array $data): self {
        return new static(
            $data['first_name'] ?? null,
            $data['last_name'] ?? null,
            $data['email'] ?? null,
            $data['gender'] ?? null,
            $data['ip_address'] ?? null
        );
    }

    /**
     * Return itself translated into array
     *
     * @return array of first_name, last_name, email, gender, ip_address
     */
    public function toArray(): array {
        return [
            "first_name" => $this->firstName,
            "last_name" => $this->lastName,
            "email" => $this->email,
            "gender" => $this->gender,
            "ip_address" => $this->ipAddress
        ];
    }
}