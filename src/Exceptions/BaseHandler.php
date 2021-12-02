<?php
/**
 * User: Edward Yu
 * Date: 2021/9/2

 */

namespace QCS\LaravelApi\Exceptions;

use HttpException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Foundation\Http\Exceptions\MaintenanceModeException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use JsonException;
use QCS\LaravelApi\Interfaces\ResultCodeInterface;
use QCS\LaravelApi\Interfaces\ResultMsgInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class BaseHandler extends Handler
{
    protected $code = ResultCodeInterface::ERROR;
    protected $data = null;
    protected $msg  = ResultMsgInterface::ERROR_MSG;

    protected $httpCode = ResultCodeInterface::HTTP_CODE;


    /**
     * 覆盖异常报告
     * @param Throwable $e
     * @Another Edward Yu 2021/9/2上午11:42
     */
    public function report(Throwable $e) : void
    {}

    /**
     * 自定义异常渲染
     * @param Request $request
     * @param Throwable $e
     * @Another Edward Yu 2021/9/2下午5:22
     * @return JsonResponse
     * @throws JsonException
     */
    public function render($request, Throwable $e): JsonResponse
    {
        //设置响应数据
        $this->response($e);

        //响应数据
        $responseData = [
            'code' => $this->code,
            'msg'  => $this->msg,
            'data' => $this->data,
        ];

        //是否是调试模式
        if (( !$e instanceof ResultException) && config('app.debug', false)) {
            $responseData['debug'] = $this->convertExceptionToArray($e);
        }

        //是否需要加密返回
        if (config('qc.encrypt', false)) {
            $responseData = rawurlencode($this->setEncrypt($responseData));
        }

        //返回json信息
        return response()->json($responseData, $this->httpCode, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * 处理异常类型
     * @param $e
     * @Another Edward Yu 2021/9/2下午4:05
     */
    public function response($e): void
    {
        //自定义异常
        if ($e instanceof ResultException) {

            $this->code     = $e->getCode();
            $this->msg      = $e->getMessage();
            $this->data     = $e->getData();
            $this->httpCode = $e->getHttpCode();

        }else if ($e instanceof MaintenanceModeException){

            //维护模式将在未来的版本删除
            $this->code = ResultCodeInterface::MAINTENANCE;
            $this->msg  = ResultMsgInterface::MAINTENANCE_MSG;

        }else if ($e instanceof ValidationException){

            $this->msg = $e->validator->getMessageBag()->first();

        }else if ($e instanceof HttpException){

            $this->msg = ResultMsgInterface::INVALID_REQUEST_MSG;

        }else if ($e instanceof QueryException){

            $this->code = ResultCodeInterface::SYS_EXCEPTION;
            $this->msg  = ResultMsgInterface::SYS_EXCEPTION_MSG;

            //记录数据库错误
            $requestErrors = $this->getRequest();
            /*Log::error('数据库异常,请求信息：', $requestErrors);
            $databaseErrors = $e->getMessage().PHP_EOL.$e->getTraceAsString();
            Log::error($databaseErrors);*/
        }else if ($e instanceof NotFoundHttpException || $e instanceof \UnexpectedValueException){

            $this->code = ResultCodeInterface::INVALID_REQUEST;
            $this->msg  = ResultMsgInterface::INVALID_REQUEST_MSG;

           /* //记录错误错误
            $requestErrors = $this->getRequest();
            Log::error('错误请求,请求信息：', $requestErrors);*/
        }else{

            //其他反射 绑定 解析错误 与系统错误 系统异常
            $this->code = ResultCodeInterface::SYS_ERROR;
            $this->msg  = ResultMsgInterface::SYS_ERROR_MSG;

            /*//记录其他错误
            $requestErrors = $this->getRequest();
            Log::error('系统错误,请求信息:', $requestErrors);
            $databaseErrors = $e->getMessage().PHP_EOL.$e->getTraceAsString();
            Log::error($databaseErrors);*/
        }
    }

    /**
     * 获取请求实例
     * @Another Edward Yu 2021/9/2下午3:40
     * @return array
     */
    public function getRequest(): array
    {
        $request = App::make('request');
        return [
            'url' => $request->url(),
            'ip' => $request->ip(),
            'referer' => $request->server('HTTP_REFERER', '-'),
            'user_agent' => $request->userAgent(),
            'headers' => $request->headers->all(),
            'method' => $request->method(),
            'data' => $request->json(),
            'queryString' => $request->query->all(),
            'body' => $request->request->all(),
        ];
    }

    /**
     * 返回加密后的数据
     * @throws JsonException
     */
    public function setEncrypt($responseData)
    {
        $method = config('qc.encrypt.method') ?? '';
        $key = config('qc.encrypt.key') ?? '';
        $data = json_encode($responseData, JSON_THROW_ON_ERROR);

        return openssl_encrypt($data, $method, $key);
    }
}
