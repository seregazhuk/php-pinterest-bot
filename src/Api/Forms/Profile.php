<?php

namespace seregazhuk\PinterestBot\Api\Forms;

class Profile extends Form
{
    /**
     * @var string
     */
    protected $lastName;

    /**
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $userName;

    /**
     * @var string
     */
    protected $about;

    /**
     * @var string
     */
    protected $location;

    /**
     * @var string
     */
    protected $websiteUrl;

    /**
     * @var string
     */
    protected $image;

    /**
     * @var string
     */
    protected $country;

    /**
     * @var bool
     */
    protected $excludeFromSearch = false;

    /**
     * @var string
     */
    protected $locale = '';

    /**
     * @var string
     */
    protected $accountType = 'other';

    /**
     * @param mixed $lastName
     * @return Profile
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @param mixed $firstName
     * @return Profile
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @param mixed $userName
     * @return Profile
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;
        return $this;
    }

    /**
     * @param mixed $about
     * @return Profile
     */
    public function setAbout($about)
    {
        $this->about = $about;
        return $this;
    }

    /**
     * @param mixed $location
     * @return Profile
     */
    public function setLocation($location)
    {
        $this->location = $location;
        return $this;
    }

    /**
     * @param mixed $websiteUrl
     * @return Profile
     */
    public function setWebsiteUrl($websiteUrl)
    {
        $this->websiteUrl = $websiteUrl;
        return $this;
    }

    /**
     * @param mixed $image
     * @return Profile
     */
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @param string $country
     * @return Profile
     */
    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @param bool $excludeFromSearch
     * @return $this
     */
    public function setExcludeFromSearch($excludeFromSearch)
    {
        $this->excludeFromSearch = $excludeFromSearch;

        return $this;
    }

    /**
     * @param string $locale
     * @return Profile
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * @param string $accountType
     * @return Profile
     */
    public function setAccountType($accountType)
    {
        $this->accountType = $accountType;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'last_name'           => $this->lastName,
            'first_name'          => $this->firstName,
            'username'            => $this->userName,
            'about'               => $this->about,
            'location'            => $this->location,
            'website_url'         => $this->websiteUrl,
            'profile_image'       => $this->image,
            'locale'              => $this->locale,
            'account_type'        => $this->accountType,
            'exclude_from_search' => $this->excludeFromSearch,
        ];
    }
}