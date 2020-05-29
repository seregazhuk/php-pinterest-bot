<?php

namespace seregazhuk\PinterestBot\Api\Forms;

class Registration extends Form
{
    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $country = 'GB';

    /**
     * @var string
     */
    protected $age = '18';

    /**
     * @var string
     */
    protected $gender = 'male';

    /**
     * @var string
     */
    protected $site;

    /**
     * @param string $email
     * @param string $password
     * @param string $name
     */
    public function __construct($email, $password, $name)
    {
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
    }

    /**
     * @param string $country
     * @return Registration
     */
    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @param int $age
     * @return Registration
     */
    public function setAge($age)
    {
        // Pinterest requires age to be a string
        $this->age = (string)$age;
        return $this;
    }

    /**
     * @return Registration
     */
    public function setMaleGender()
    {
        return $this->setGender('male');
    }

    /**
     * @param string $gender
     * @return Registration
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
        return $this;
    }

    /**
     * @return Registration
     */
    public function setFemaleGender()
    {
        return $this->setGender('female');
    }

    /**
     * @return array
     */
    public function getData()
    {
        return [
            'first_name'         => $this->name,
            'email'              => $this->email,
            'password'           => $this->password,
            'age'                => $this->age,
            'gender'             => $this->gender,
            'country'            => $this->country,
            'site'               => $this->site,
            'container'          => 'home_page',
            'signupSource'       => 'home_page',
            'hybridTier'         => 'open',
            'page'               => 'home',
            'recaptchaV3Token'   => '',
            'user_behavior_data' => "{}",
        ];
    }

    /**
     * @return string
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param string $site
     * @return Registration
     */
    public function setSite($site)
    {
        $this->site = $site;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Registration
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     * @return Registration
     */
    public function setEmail($email)
    {
        $this->email = $email;
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
     * @return Registration
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }


}
