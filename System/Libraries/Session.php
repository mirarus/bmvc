<?php

/**
 * Session
 *
 * @package System\Libraries
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 1.0
*/

class Session
{

    function __construct()
    {
        //@ini_set('session.cookie_httponly', 1);
        //@ini_set('session.use_only_cookies', 1);
        //@ini_set('session.gc_maxlifetime', 3600);
        //@session_set_cookie_params(3600);
        self::init();
    }

    protected static function init()
    {
        if (session_status() !== PHP_SESSION_ACTIVE || session_id() === "") {
            session_name("MMVC");
            session_start();
            
            self::set(md5('session_hash'), self::generateHash());
        } else {
            if (self::get(md5('session_hash')) != self::generateHash()) {
                self::destroy();
            }
        }
    }

    static function set($storage, $content=null)
    {
        if (is_array($storage)) {
            foreach ($storage as $key => $value) {
                $_SESSION[$key] = $value;
            }
        } else {
            $_SESSION[$storage] = $content;
        }
    }

    static function get($storage=null, $child=false)
    {
        if (is_null($storage)) {
            return $_SESSION;
        }
        if ($child == false) {
            return @$_SESSION[$storage];
        } else {
            if (isset($_SESSION[$storage][$child])) {
                return $_SESSION[$storage][$child];
            }
        }
    }

    static function has($storage, $child=false)
    {
        if ($child == false) {
            return isset($_SESSION[$storage]);
        } else {
            if (isset($_SESSION[$storage][$child])) {
                return $_SESSION[$storage][$child];
            }
        }
    }

    static function delete($storage=null)
    {
        if (is_null($storage)) {
            session_unset();
        } else {
            unset($_SESSION[$storage]);
        }
    }

    static function destroy()
    {
        session_destroy();
    }

    protected static function generateHash()
    {
        if (array_key_exists('REMOTE_ADDR', $_SERVER) && array_key_exists('HTTP_USER_AGENT', $_SERVER)) {
            return md5(sha1(md5($_SERVER['REMOTE_ADDR'] . 'u2LMq1h4oUV0ohL9svqedoB5LebiIE4z' . $_SERVER['HTTP_USER_AGENT'])));
        }
        return md5(sha1(md5('u2LMq1h4oUV0ohL9svqedoB5LebiIE4z')));
    }
}

# Initialize
new Session;