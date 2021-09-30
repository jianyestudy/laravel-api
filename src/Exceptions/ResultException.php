<?php
/**
 * User: Edward Yu
 * Date: 2021/9/2

 */

namespace QCS\LaravelApi\Exceptions;

use Exception;

/**
 * 处理请求结果
 * @auther Edward Yu
 * @package QCS\LaravelApi\Exceptions
 */
class ResultException extends Exception
{
    protected $data;
    protected $httpCode;

    public function __construct(int $code, string $msg, $data, int $httpCode)
    {
        $this->setData($data);
        $this->setHttpCode($httpCode);
        parent::__construct($msg, $code, null);
    }

    /**
     * @return mixed
     * @auther Edward Yu
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return mixed
     * @auther Edward Yu
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }

    /**
     * @param mixed $data
     * @auther Edward Yu
     */
    public function setData($data): void
    {
        $this->data = $data;
    }

    /**
     * @param mixed $httpCode
     * @auther Edward Yu
     */
    public function setHttpCode($httpCode): void
    {
        $this->httpCode = $httpCode;
    }
}
