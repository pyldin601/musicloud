<?php
/**
 * Created by PhpStorm.
 * UserModel: roman
 * Date: 13.12.14
 * Time: 18:54
 */

namespace app\core\http;


use app\core\injector\Injectable;
use app\lang\option\Option;
use app\lang\singleton\Singleton;

class HttpServer implements Injectable {

    use Singleton;

    /**
     * @return string
     */
    public function getMethod() {
        return $this->filterInputServer("REQUEST_METHOD");
    }

    /**
     * @return mixed
     */
    public function getServerAddress() {
        return $this->filterInputServer("SERVER_ADDR");
    }

    /**
     * @return mixed
     */
    public function getServerName() {
        return $this->filterInputServer("SERVER_NAME");
    }

    /**
     * @return mixed
     */
    public function getServerProtocol() {
        return $this->filterInputServer("SERVER_PROTOCOL");
    }

    /**
     * @return mixed
     */
    public function getRequestTime() {
        return $this->filterInputServer("REQUEST_TIME");
    }

    /**
     * @return Option
     */
    public function getLanguage() {
        if (filter_input(INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE')) {
            return Option::Some(substr(filter_input(INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE'), 0, 2));
        }
        return Option::None();
    }

    /**
     * @return mixed
     */
    public function getQueryString() {
        return $this->filterInputServer("QUERY_STRING");
    }

    /**
     * @return mixed
     */
    public function getHttpAccept() {
        return $this->filterInputServer("HTTP_ACCEPT");
    }

    /**
     * @return mixed
     */
    public function getHttpHost() {
        return $this->filterInputServer("HTTP_HOST");
    }

    /**
     * @return mixed
     */
    public function getHttpReferer() {
        return $this->filterInputServer("HTTP_REFERER");
    }

    /**
     * @return mixed
     */
    public function getHttpUserAgent() {
        return $this->filterInputServer("HTTP_USER_AGENT");
    }

    /**
     * @return mixed
     */
    public function getHttps() {
        return $this->filterInputServer("HTTPS");
    }

    /**
     * @return mixed
     */
    public function getRemoteAddress() {
        return $this->filterInputServer("HTTP_X_REAL_IP")
            ? $this->filterInputServer("HTTP_X_REAL_IP")
            : $this->filterInputServer("REMOTE_ADDR");
    }

    /**
     * @return mixed
     */
    public function getRemotePort() {
        return $this->filterInputServer("REMOTE_PORT");
    }

    /**
     * @return mixed
     */
    public function getRequestUri() {
        return $this->filterInputServer("REQUEST_URI");
    }

    /**
     * @param string $param
     * @return mixed
     */
    private function filterInputServer($param) {
        return FILTER_INPUT(INPUT_SERVER, $param);
    }

} 