<?php

/**
 * Request
 *
 * Mirarus BMVC
 * @package System\Libraries
 * @author  Ali Güçlü (Mirarus) <aliguclutr@gmail.com>
 * @link https://github.com/mirarus/bmvc
 * @license http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version 1.0
 */

class Request
{
    const METHOD_HEAD = 'HEAD';
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_PATCH = 'PATCH';
    const METHOD_DELETE = 'DELETE';
    const METHOD_OPTIONS = 'OPTIONS';
    const METHOD_OVERRIDE = '_METHOD';

    private static $formDataMediaTypes = array('application/x-www-form-urlencoded');
    private $server;
    public $header;

    public function __construct()
    {
        $this->server = $_SERVER;
        $this->header = Header::extract($this->server);
    }

    public function getMethod()
    {
        return $this->server['REQUEST_METHOD'];
    }

    public function isGet()
    {
        return $this->getMethod() === self::METHOD_GET;
    }

    public function isPost()
    {
        return $this->getMethod() === self::METHOD_POST;
    }

    public function isPut()
    {
        return $this->getMethod() === self::METHOD_PUT;
    }

    public function isPatch()
    {
        return $this->getMethod() === self::METHOD_PATCH;
    }

    public function isDelete()
    {
        return $this->getMethod() === self::METHOD_DELETE;
    }

    public function isHead()
    {
        return $this->getMethod() === self::METHOD_HEAD;
    }

    public function isOptions()
    {
        return $this->getMethod() === self::METHOD_OPTIONS;
    }

    public function isAjax()
    {
        if ($this->params('isajax')) {
            return true;
        } elseif (isset($this->header['X_REQUESTED_WITH']) && $this->header['X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            return true;
        }

        return false;
    }

    public function isXhr()
    {
        return $this->isAjax();
    }

    public function isFormData()
    {
        return ($this->getMethod() === self::METHOD_POST && is_null($this->getContentType())) || in_array($this->getMediaType(), self::$formDataMediaTypes);
    }

    public function headers($key = null, $default = null)
    {
        if ($key) {
            return $this->header->get($key, $default);
        }
        return $this->header;
    }

    public function getContentType()
    {
        return $this->header->get('CONTENT_TYPE');
    }

    public function getMediaType()
    {
        $contentType = $this->getContentType();
        if ($contentType) {
            $contentTypeParts = preg_split('/\s*[;,]\s*/', $contentType);

            return strtolower($contentTypeParts[0]);
        }

        return null;
    }

    public function getMediaTypeParams()
    {
        $contentType = $this->getContentType();
        $contentTypeParams = array();
        if ($contentType) {
            $contentTypeParts = preg_split('/\s*[;,]\s*/', $contentType);
            $contentTypePartsLength = count($contentTypeParts);
            for ($i = 1; $i < $contentTypePartsLength; $i++) {
                $paramParts = explode('=', $contentTypeParts[$i]);
                $contentTypeParams[strtolower($paramParts[0])] = $paramParts[1];
            }
        }

        return $contentTypeParams;
    }

    public function getContentCharset()
    {
        $mediaTypeParams = $this->getMediaTypeParams();
        if (isset($mediaTypeParams['charset'])) {
            return $mediaTypeParams['charset'];
        }

        return null;
    }

    public function getContentLength()
    {
        return $this->header->get('CONTENT_LENGTH', 0);
    }

    public function getHost()
    {
        if (isset($this->server['HTTP_HOST'])) {
            if (strpos($this->server['HTTP_HOST'], ':') !== false) {
                $hostParts = explode(':', $this->server['HTTP_HOST']);

                return $hostParts[0];
            }

            return $this->server['HTTP_HOST'];
        }

        return $this->server['SERVER_NAME'];
    }

    public function getHostWithPort()
    {
        return sprintf('%s:%s', $this->getHost(), $this->getPort());
    }

    public function getPort()
    {
        return (int)$this->server['SERVER_PORT'];
    }

    public function getScheme()
    {
        return stripos($this->server['SERVER_PROTOCOL'], 'https') === true ? 'https' : 'http';
    }

    public function getScriptName()
    {
        return $this->server['SCRIPT_NAME'];
    }

    public function getRootUri()
    {
        return $this->getScriptName();
    }

    public function getPath()
    {
        return $this->getScriptName() . $this->getPathInfo();
    }

    public function getPathInfo()
    {
        return $this->server['PATH_INFO'];
    }

    public function getResourceUri()
    {
        return $this->getPathInfo();
    }

    public function getUrl()
    {
        $url = $this->getScheme() . '://' . $this->getHost();
        if (($this->getScheme() === 'https' && $this->getPort() !== 443) || ($this->getScheme() === 'http' && $this->getPort() !== 80)) {
            $url .= sprintf(':%s', $this->getPort());
        }

        return $url;
    }

    public function getIp()
    {
        $keys = array('X_FORWARDED_FOR', 'HTTP_X_FORWARDED_FOR', 'CLIENT_IP', 'REMOTE_ADDR');
        foreach ($keys as $key) {
            if (isset($this->server[$key])) {
                return $this->server[$key];
            }
        }

        return $this->server['REMOTE_ADDR'];
    }

    public function getReferrer()
    {
        return $this->header->get('HTTP_REFERER');
    }

    public function getReferer()
    {
        return $this->getReferrer();
    }

    public function getUserAgent()
    {
        return $this->header->get('HTTP_USER_AGENT');
    }

    static public function getRequestMethod()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == self::METHOD_HEAD) {
            ob_start();
            $method = self::METHOD_GET;
        } elseif ($method == self::METHOD_POST) {
            if (function_exists('getallheaders'))
                getallheaders();
            $headers = [];
            foreach ($_SERVER as $name => $value) {
                if ((substr($name, 0, 5) == 'HTTP_') || ($name == 'CONTENT_TYPE') || ($name == 'CONTENT_LENGTH'))
                    $headers[str_replace(array(' ', 'Http'), array('-', 'HTTP'), ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
            if (isset($headers['X-HTTP-Method-Override']) && in_array($headers['X-HTTP-Method-Override'], array(self::METHOD_PUT, self::METHOD_DELETE, self::METHOD_PATCH)))
                $method = $headers['X-HTTP-Method-Override'];
        }
        return $method;
    }

    static public function checkDomain($domain)
    {
        if (isset($domain) && !empty($domain)) {
            if ($domain !== trim(str_replace('www.', '', $_SERVER['SERVER_NAME']), '/'))
                return false;
            return true;
        }
        return true;
    }

    static public function checkIp($ip)
    {
        if (isset($ip) && !empty($ip)) {
            if (is_array($ip)) {
                if (!in_array($_SERVER['REMOTE_ADDR'], $ip))
                    return false;
                return true;
            } else {
                if ($_SERVER['REMOTE_ADDR'] != $ip)
                    return false;
                return true;
            }
            return true;
        }
        return true;
    }
}

# Initialize
new Request;