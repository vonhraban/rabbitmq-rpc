<?php
namespace Datix\User;


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
     * @param string $firstName Optional first name
     * @param string $lastName Optional last name
     * @param string $email Optional email
     * @param string $gender Optional gender
     * @param string $ipAddress Optional ip address
     */
    protected function __construct(string $firstName = null, string $lastName = null, string $email = null,
                                   string $gender = null, string $ipAddress = null)
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