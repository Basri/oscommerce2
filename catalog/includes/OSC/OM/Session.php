<?php
/**
  * osCommerce Online Merchant
  *
  * @copyright (c) 2016 osCommerce; https://www.oscommerce.com
  * @license GPL; https://www.oscommerce.com/gpllicense.txt
  */

namespace OSC\OM;

use OSC\OM\OSCOM;

class Session
{
    public static function load($name = null)
    {
        $class_name = 'OSC\\OM\\Session\\' . OSCOM::getConfig('store_sessions');

        if (!class_exists($class_name)) {
            trigger_error('Session Handler \'' . $class_name . '\' does not exist, using default \'OSC\\OM\\Session\\File\'', E_USER_NOTICE);

            $class_name = 'OSC\\OM\\Session\\File';
        } elseif (!is_subclass_of($class_name, 'OSC\OM\SessionAbstract')) {
            trigger_error('Session Handler \'' . $class_name . '\' does not extend OSC\\OM\\SessionAbstract, using default \'OSC\\OM\\Session\\File\'', E_USER_NOTICE);

            $class_name = 'OSC\\OM\\Session\\File';
        }

        $obj = new $class_name();

        if (!isset($name)) {
            $name = 'oscomid';
        }

        $obj->setName($name);

        $force_cookies = false;

        if ((OSCOM::getConfig('http_cookie_domain') == OSCOM::getConfig('https_cookie_domain')) && (OSCOM::getConfig('http_cookie_path') == OSCOM::getConfig('https_cookie_path'))) {
            $force_cookies = true;
        }

        $obj->setForceCookies($force_cookies);

        return $obj;
    }
}
