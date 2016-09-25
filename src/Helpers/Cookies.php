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
        // detect http only cookies and remove #HttpOnly prefix
        $httpOnly = $this->isHttp($line);

        if ($httpOnly) {
            $line = substr($line, 10);
        }

        if (!$this->isValid($line)) return false;

        // get tokens in an array
        $tokens = explode("\t", $line);
        // trim the tokens
        $tokens = array_map('trim', $tokens);

        return [
            'httponly'   => $httpOnly,
            'domain'     => $tokens[0],
            'flag'       => $tokens[1],
            'path'       => $tokens[2],
            'secure'     => $tokens[3],
            'name'       => urldecode($tokens[5]),
            'value'      => urldecode($tokens[6]),
            'expiration' => date('Y-m-d h:i:s', $tokens[4]),
        ];
    }
}
