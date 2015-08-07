<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 07.08.2015
 * Time: 13:46
 */

namespace app\libs;


use app\core\exceptions\ControllerException;

class AudioScrobbler {

    const API_KEY        = "b065b504b26346b67e7678ec61d7b907";
    const SECRET         = "b5858ee5842425f5bf7e4049e63c58be";

    const API_URL        = "http://ws.audioscrobbler.com/2.0/";

    /**
     * Marks track as 'Now Playing'.
     *
     * @param $title
     * @param $artist
     */
    public function nowPlaying($title, $artist) {

        if (empty($_COOKIE["fm_sk"]))
            return;

        self::exec(self::API_URL, array(
            "format"    => "json",
            "method"    => "track.updateNowPlaying",
            "api_key"   => self::API_KEY,
            "sk"        => $_COOKIE["fm_sk"],
            "artist" => $artist,
            "track"  => $title
        ));

    }

    /**
     * Scrobbles track.
     *
     * @param $title
     * @param $artist
     * @param null|int $time
     */
    public function scrobble($title, $artist, $time = null) {

        if (empty($_COOKIE["fm_sk"]))
            return;

        self::exec(self::API_URL, array(
            "format"    => "json",
            "method"    => "track.scrobble",
            "api_key"   => self::API_KEY,
            "sk"        => $_COOKIE["fm_sk"],
            "artist" => $artist,
            "track"  => $title,
            "timestamp" => $time ?: time()
        ));

    }

    /**
     * Marks track as loved.
     *
     * @param $title
     * @param $artist
     */
    public function love($title, $artist) {

        if (empty($_COOKIE["fm_sk"]))
            return;

        self::exec(self::API_URL, array(
            "format"    => "json",
            "method"    => "track.love",
            "api_key"   => self::API_KEY,
            "sk"        => $_COOKIE["fm_sk"],
            "artist" => $artist,
            "track"  => $title,
        ));

    }

    /**
     * Authorizes user on Last.fm services using $token.
     *
     * @param $token
     * @throws ControllerException
     */
    public function login($token) {

        $key_data = $this->getKey($token);

        $expire = time() + 3600 * 24 * 365;

        setcookie('fm_sk',      $key_data["key"],   $expire, '/');
        setcookie('fm_user',    $key_data["user"],  $expire, '/');
        setcookie('fm_enabled', 'on',               $expire, '/');

    }

    public function logout() {

        setcookie('fm_sk',      null,  -1, '/');
        setcookie('fm_user',    null,  -1, '/');
        setcookie('fm_enabled', null,  -1, '/');

    }

    /**
     * @param $token
     * @throws ControllerException
     * @return mixed
     */
    private function getKey($token) {

        $result = self::exec(self::API_URL, array(
            "format"  => "json",
            "method"  => "auth.getSession",
            "api_key" => self::API_KEY,
            "token"   => $token
        ));

        if (isset($result["error"])) {
            throw new ControllerException($result["message"]);
        }

        if (!isset($result["session"])) {
            throw new ControllerException("Last.fm server returned wrong response.");
        }

        return $result["session"];

    }

    /**
     * @param array $data
     * @return null|string
     */
    private function arrayToPostData(array $data) {
        if (is_null($data)) {
            return null;
        }
        $acc = array();
        foreach ($data as $key => $value) {
            $acc[] = $key . "=" . urlencode($value);
        }
        return implode("&", $acc);
    }

    /**
     * @param array $data
     * @return string
     */
    private function arrayToSignatureString(array $data) {
        ksort($data);
        $acc = "";
        foreach ($data as $key => $value) {
            if ($key == "format" || $key == "callback")
                continue;
            $acc .= $key . $value;
        }
        return md5($acc.self::SECRET);
    }

    /**
     * @param string $url
     * @param null|array $post
     * @return mixed
     */
    private function exec($url, array $post = null) {

        $ch = curl_init($url);

        if ($post !== null) {
            $post["api_sig"] = $this->arrayToSignatureString($post);
        }

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, "musicLoud Audio Scrobbler Library");
        curl_setopt($ch, CURLOPT_POSTFIELDS, self::arrayToPostData($post));
        curl_setopt($ch, CURLOPT_REFERER, "http://music.homefs.biz/");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        $result = json_decode(curl_exec($ch), true);

        curl_close($ch);

        return $result;

    }

}