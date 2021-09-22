<?php
/**
 * User: Edward Yu
 * Date: 2021/9/13
 */

namespace QCYX\LaravelApi\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * 驼峰转蛇行 添加请求id
 * @auther Edward Yu
 * @package QCYX\LaravelApi\Middleware
 */
class ApiCaseConverter
{
    public function handle(Request $request, \Closure $next)
    {
        $parameters = $request->all();
        //请求添加id
        if ( !empty($request->route('id')) ) {
            $parameters['id'] =  $request->route('id');
        }

        //驼峰转蛇形
        $newParameters = [];
        foreach ($parameters as $key => $value){
            $newParameters[Str::snake($key)] = $value;
        }
        $request->replace($newParameters);

        return $next($request);
    }
}
