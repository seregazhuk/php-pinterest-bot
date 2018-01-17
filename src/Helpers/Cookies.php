<?php

namespace seregazhuk\PinterestBot\Helpers;

class Cookies
{
    /**
     * @var array
     */
    protected $cookies = [];

    /**
     * @param string $name
     * @return mixed
     */
    public function get($name)
    {
        if (array_key_exists($name, $this->cookies)) {
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
            if ($cookie = $this->parseCookieLine($line)) {
                $this->cookies[$cookie['name']] = $cookie;
            }
        }
    }

    /**
     * @param string $line
     * @return bool
     */
    protected function isHttp($line)
    {
        return substr($line, 0, 10) == '#HttpOnly_';
    }

    /**
     * @param string $line
     * @return bool
     */
    protected function isValidLine($line)
    {
        return strlen($line) > 0 && $line[0] != '#' && substr_count($line, "\t") == 6;
    }

    /**
     * @param string $line
     * @return array|bool
     */
    protected function parseCookieLine($line)
    {
        // detect http only cookies and remove #HttpOnly prefix
        $httpOnly = $this->isHttp($line);

        if ($httpOnly) {
            $line = substr($line, 10);
        }

        if (!$this->isValidLine($line)) {
            return false;
        }

        $data = $this->getCookieData($line);

        $data['httponly'] = $httpOnly;

        return $data;
    }

    /**
     * @param string $line
     * @return array
     */
    protected function getCookieData($line)
    {
        // execGet tokens in an array
        $data = explode("\t", $line);
        // trim the tokens
        $data =  array_map('trim', $data);

        return [
            'domain'     => $data[0],
            'flag'       => $data[1],
            'path'       => $data[2],
            'secure'     => $data[3],
            'name'       => urldecode($data[5]),
            'value'      => urldecode($data[6]),
            'expiration' => date('Y-m-d h:i:s', $data[4]),
        ];
    }
}
