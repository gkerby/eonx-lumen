<?php

declare(strict_types=1);

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Kerby\EonxTestTask\Contracts\Entity\CustomerDTOInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="customers",
 *     uniqueConstraints={
 *        @UniqueConstraint(name="email",
 *            columns={"email"}
 *        )
 *    }
 * )
 */
class Customer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private string $firstName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private string $lastName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private string $email;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private string $country;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private string $gender;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private string $city;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private string $phone;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private string $username;

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getFullName(): string
    {
        return trim($this->getFirstName() . ' ' . $this->getLastName());
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setFirstName(string $value): self
    {
        $this->firstName = $value;

        return $this;
    }

    public function setLastName(string $value): self
    {
        $this->lastName = $value;

        return $this;
    }

    public function setEmail(string $value): self
    {
        $this->email = $value;

        return $this;
    }

    public function setCountry(string $value): self
    {
        $this->country = $value;

        return $this;
    }

    public function setGender(string $value): self
    {
        $this->gender = $value;

        return $this;
    }

    public function setCity(string $value): self
    {
        $this->city = $value;

        return $this;
    }

    public function setPhone(string $value): self
    {
        $this->phone = $value;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $value): self
    {
        $this->username = $value;

        return $this;
    }

    public function fillFromCustomerAdapter(CustomerDTOInterface $customer): void
    {
        $this->setFirstName($customer->getFirstName());
        $this->setLastName($customer->getLastName());
        $this->setGender($customer->getGender());
        $this->setCity($customer->getCity());
        $this->setCountry($customer->getCountry());
        $this->setPhone($customer->getPhone());
        $this->setEmail($customer->getEmail());
        $this->setUsername($customer->getUsername());
        $this->setFirstName($customer->getFirstName());
    }
}
