<?php

/**
 * User: Edward Yu
 * Date: 2021/11/4
 */

return [

    /*
     * 请求参数为驼峰请填写true，否则为false，默认false
     * 如果请求的参数是驼峰，需要在使用laravel-api之前转化成蛇形才能使用，
     */

    'request_camel' => false,

    /*
     * 需要返回的参数为驼峰请填写为true，否则为false，默认false
     */

    'response_camel' => false,

    /*
     * 是否需要在日志中记录所有异常信息
     */

    'exception_log' => true,
];
