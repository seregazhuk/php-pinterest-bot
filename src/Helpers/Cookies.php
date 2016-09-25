<?php

namespace seregazhuk\PinterestBot\Helpers;

class Cookies
{
    const TOKEN_NAME = 'csrftoken';
    const DEFAULT_TOKEN = '1234';

    /**
     * @var array
     */
    protected $cookies = [];

    /**
     * @param $name
     * @return mixed
     */
    public function get($name)
    {
        if(array_key_exists($name, $this->cookies)) {
            return $this->cookies[$name]['value'];
       }

        return null;
    }

    /**
     * @return string|null
     */
    public function getToken()
    {
        return $this->get('csrftoken');
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->cookies;
    }

    /**
     * @param string $file
     */
    public function fill($file)
    {
        $this->cookies = [];

        foreach (file($file) as $line) {
            if($cookie = $this->parseCookie($line)) {
                $this->cookies[$cookie['name']] = $cookie;
            }
        }
    }

    /**
     * @param $line
     * @return bool
     */
    protected function isHttp($line)
    {
        return substr($line, 0, 10) == '#HttpOnly_';
    }

    /**
     * @param $line
     * @return bool
     */
    protected function isValid($line)
    {
        return strlen($line) > 0 && $line[0] != '#' && substr_count($line, "\t") == 6;
    }

    /**
     * @param $line
     * @return array|bool
     */
    protected function parseCookie($line)
    {
        $cookie = [];

        // detect httponly cookies and remove #HttpOnly prefix
        $cookie['httponly'] = $this->isHttp($line);

        if ($cookie['httponly']) {
            $line = substr($line, 10);
        }

        if (!$this->isValid($line)) return false;

        // get tokens in an array
        $tokens = explode("\t", $line);
        // trim the tokens
        $tokens = array_map('trim', $tokens);

        // Extract the data
        $cookie['domain'] = $tokens[0]; // The domain that created AND can read the variable.
        $cookie['flag'] = $tokens[1];   // A TRUE/FALSE value indicating if all machines within a given domain can access the variable.
        $cookie['path'] = $tokens[2];   // The path within the domain that the variable is valid for.
        $cookie['secure'] = $tokens[3]; // A TRUE/FALSE value indicating if a secure connection with the domain is needed to access the variable.

        $cookie['expiration-epoch'] = $tokens[4];  // The UNIX time that the variable will expire on.
        $cookie['name'] = urldecode($tokens[5]);   // The name of the variable.
        $cookie['value'] = urldecode($tokens[6]);  // The value of the variable.

        // Convert date to a readable format
        $cookie['expiration'] = date('Y-m-d h:i:s', $tokens[4]);
        return $cookie;
    }
}
