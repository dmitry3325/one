<?php
/**
 * Created by PhpStorm.
 * User: dmi
 * Date: 07.12.2017
 * Time: 14:31
 */

namespace App\Http\Response;

abstract class AbstractResponse
{
    protected $code;
    protected $result;
    protected $message;
    protected $data;

    public function __construct($message = null, $data = null)
    {
        $this->setMessage($message);
        $this->setData($data);
    }

    public function setData($data): void
    {
        $this->data = $data;
    }

    private function setMessage($message): void
    {
        $this->message = $message;
    }

    public function setCode(int $code): void
    {
        $this->code = $code;
    }

    public function setResult(bool $result): void
    {
        $this->result = $result;
    }

    public function __toString()
    {
        return json_encode(get_object_vars($this));
    }
}