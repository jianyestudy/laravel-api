<?php
/**
 * User: Edward Yu
 * Date: 2021/9/2

 */

namespace QCS\LaravelApi\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use QCS\LaravelApi\Exceptions\ResultException;
use QCS\LaravelApi\Interfaces\ResultCodeInterface;
use QCS\LaravelApi\Interfaces\ResultMsgInterface;

/**
 * 统一返回异常
 * @auther Edward Yu
 * @package QCS\LaravelApi\Traits
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
    public function success( $data = null, string $msg = ResultMsgInterface::SUCCESS_MSG, int $code = ResultCodeInterface::SUCCESS): void
    {
        $this->abort($data, $msg, $code);
    }

    /**
     * 失败返回
     * @param null $data
     * @param string $msg
     * @param int $code
     * @throws ResultException
     * @Another Edward Yu 2021/9/2上午11:05
     */
    public function error(string $msg = ResultMsgInterface::ERROR_MSG, int $code = ResultCodeInterface::ERROR, $data = null): void
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
        //如果返回结果需要转化成驼峰
        if (config('laravel-api.response_camel')) {
            $data = self::camelCase($data);
        }

        throw new ResultException($code, $msg, $data, $httpCode);
    }

    /**
     * 将结果转化为驼峰
     * @param null $data
     * @Another Edward Yu 2021/11/3下午2:40
     * @return mixed
     */
    public static function camelCase( $data = null )
    {
        if(is_bool($data) || is_integer($data) || is_float($data) || is_null($data)){
            return $data;
        }

        if(is_string($data)){
            return Str::camel($data);
        }

        //对象变量转数组
        if(is_object($data)){
            try {
                $data = $data->toArray();
            }catch (\Exception $e){
                $data = get_object_vars($data);
            }
        }

        $newParameters = [];

        foreach ($data as $key => $value){
            //如果还有下级 递归
            if(is_array($value) || is_object($value)) {
                $newParameters[$key] = self::camelCase($value);
            }else{
                $newParameters[Str::camel($key)] = $value;
            }
        }

        return $newParameters ?? $data;
    }
}
