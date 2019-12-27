<?php


namespace App\Forms;

use Symfony\Component\Validator\Constraints as Assert;


class UserLogin
{
    /**
     * @Assert\NotBlank(message="Login must not be empty")
     * @var string
     */
    private $login;

    /**
     * @Assert\NotBlank(message="Password must not be empty")
     * @var string
     */
    private $password;

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @param string $login
     * @return UserLogin
     */
    public function setLogin($login): self
    {
        $this->login = $login;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return UserLogin
     */
    public function setPassword($password): self
    {
        $this->password = $password;

        return $this;
    }
}