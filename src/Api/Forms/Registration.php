<?php

namespace seregazhuk\PinterestBot\Api\Forms;

class Registration extends Form
{
    protected $email;
    protected $password;
    protected $name;
    protected $country = 'GB';
    protected $age = 18;
    protected $gender = 'male';
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
     * @param mixed $email
     * @return Registration
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @param mixed $password
     * @return Registration
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @param mixed $name
     * @return Registration
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param mixed $country
     * @return Registration
     */
    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @param mixed $age
     * @return Registration
     */
    public function setAge($age)
    {
        $this->age = $age;
        return $this;
    }

    /**
     * @param mixed $gender
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
    public function setMaleGender()
    {
        return $this->setGender('male');
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
    public function toArray()
    {

        return [
            'first_name'    => $this->name,
            'email'         => $this->email,
            'password'      => $this->password,
            'age'           => (string)$this->age,
            'gender'        => $this->gender,
            'country'       => $this->country,
            'site'          => $this->site,
            'container'     => 'home_page',
            'visited_pages' => [],
        ];
    }

    /**
     * @param mixed $site
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
    public function getSite()
    {
        return $this->site;
    }
}