<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 4/27/2018
 * Time: 8:00 PM
 */

namespace App\EntityClass;


class Firebase
{

    private $url;
    private $headers;
    private $notification;

    public function __construct($url, $headers, $notification)
    {
        $this->url = $url;
        $this->headers = $headers;
        $this->notification = $notification;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url): void
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param mixed $headers
     */
    public function setHeaders($headers): void
    {
        $this->headers = $headers;
    }

    /**
     * @return mixed
     */
    public function getNotification()
    {
        return $this->notification;
    }

    /**
     * @param mixed $notification
     */
    public function setNotification($notification): void
    {
        $this->notification = $notification;
    }

    /**
     * @return bool|string
     */
    public function sendNotification()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->notification));

        if (curl_exec($ch) === false) {
            $error = curl_error($ch);
            curl_close($ch);
            return $error;
        }

        curl_close($ch);
        return true;
    }

}