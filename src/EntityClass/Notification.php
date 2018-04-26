<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Adlouni
 * Date: 4/26/2018
 * Time: 6:52 PM
 */

namespace App\EntityClass;



class Notification
{

    private $title;
    private $message;
    private $token;
    private $key;

    public function __construct($title, $message, $token, $key)
    {
        $this->title = $title;
        $this->message = $message;
        $this->token = $token;
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param mixed $key
     */
    public function setKey($key): void
    {
        $this->key = $key;
    }

    /**
     * @return array
     */
    public function build()
    {
        $to = $this->token;

        $dataPayload = array();
        $dataPayload['data']['title'] = $this->title;
        $dataPayload['data']['message'] = $this->message;

        $notificationPayload = array(
            'title' => $this->title,
            'body' => $this->message,
            'sound' => 'default',
        );

        $fields = array(
            'to' => $to,
            'data' => $dataPayload,
            'notification' => $notificationPayload
        );

        return $fields;
    }

    public function getHeader()
    {
        return array(
            'Authorization: key=' . $this->key,
            'Content-Type: application/json'
        );
    }
}