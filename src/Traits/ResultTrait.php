<?php
/**
 * User: Edward Yu
 * Date: 2021/9/2

 */

namespace QCYX\LaravelApi\Traits;

use Illuminate\Support\Collection;
use QCYX\LaravelApi\Exceptions\ResultException;
use QCYX\LaravelApi\Interfaces\ResultCodeInterface;
use QCYX\LaravelApi\Interfaces\ResultMsgInterface;

/**
 * 统一返回异常
 * @auther Edward Yu
 * @package QCYX\LaravelApi\Traits
 */
trait ResultTrait
{
    /**
     * 成功返回
     * @param null $data
     * @param string $msg
     * @param int $code
     * @throws ResultException
     * @Another Edward Yu 2021/9/2上午11:05
     */
    public function success(string $msg = ResultMsgInterface::SUCCESS_MSG, $data = null,  int $code = ResultCodeInterface::SUCCESS): void
    {
        $this->abort( $data, $msg, $code);
    }

    /**
     * 失败返回
     * @param null $data
     * @param string $msg
     * @param int $code
     * @throws ResultException
     * @Another Edward Yu 2021/9/2上午11:05
     */
    public function error($data = null, string $msg = ResultMsgInterface::ERROR_MSG, int $code = ResultCodeInterface::ERROR): void
    {
         $this->abort($data, $msg, $code);
    }

    /**
     * 无数据返回
     * @param null $data
     * @param string $msg
     * @param int $code
     * @throws ResultException
     * @Another Edward Yu 2021/9/2上午11:05
     */
    public function noData($data = null, string $msg = ResultMsgInterface::NO_DATA_MSG, int $code = ResultCodeInterface::NO_DATA): void
    {
         $this->abort($data, $msg, $code);
    }

    /**
     * 统一异常返回
     * @param $data
     * @param string $msg
     * @param int $code
     * @param int $httpCode
     * @return ResultException
     * @throws ResultException
     * @Another Edward Yu 2021/9/2上午11:05
     */
    public function abort($data, string $msg, int $code,  int $httpCode = ResultCodeInterface::HTTP_CODE): ResultException
    {
        throw new ResultException($code, $msg, $data, $httpCode);
    }
}
