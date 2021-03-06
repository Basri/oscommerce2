<?php
/**
  * osCommerce Online Merchant
  *
  * @copyright (c) 2016 osCommerce; https://www.oscommerce.com
  * @license GPL; https://www.oscommerce.com/gpllicense.txt
  */

namespace OSC\OM;

use OSC\OM\OSCOM;

class Cookies
{
    protected $domain;
    protected $path;

    public function __construct()
    {
        if ((isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) == 'on')) || (isset($_SERVER['SERVER_PORT']) && ($_SERVER['SERVER_PORT'] == 443))) {
            $this->domain = OSCOM::getConfig('https_cookie_domain');
            $this->path = OSCOM::getConfig('https_cookie_path');
        } else {
            $this->domain = OSCOM::getConfig('http_cookie_domain');
            $this->path = OSCOM::getConfig('http_cookie_path');
        }
    }

    public function set($name, $value = '', $expire = 0, $path = null, $domain = null, $secure = false, $httponly = false)
    {
        return setcookie($name, $value, $expire, isset($path) ? $path : $this->path, isset($domain) ? $domain : $this->domain, $secure, $httponly);
    }

    public function del($name, $path = null, $domain = null, $secure = false, $httponly = false)
    {
        if ($this->set($name, '', time() - 3600, $path, $domain, $secure, $httponly)) {
            if (isset($_COOKIE[$name])) {
                unset($_COOKIE[$name]);
            }

            return true;
        }

        return false;
    }

    public function getDomain()
    {
        return $this->domain;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }
}
