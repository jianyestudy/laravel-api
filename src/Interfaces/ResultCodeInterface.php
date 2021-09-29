<?php
/**
 * User: Edward Yu
 * Date: 2021/9/2

 */

namespace QCYX\LaravelApi\Interfaces;

/**
 * 枚举状态码
 * @auther Edward Yu
 * @package QCYX\LaravelApi\Interfacesz
 */
interface ResultCodeInterface
{
    public const HTTP_CODE     = 200;

    public const SUCCESS                = 2000;
    public const NO_DATA                = 2001;
    public const MAINTENANCE            = 3000;
    public const ERROR                  = 4000;
    public const NOT_UNIQUE             = 4001;
    public const INVALID_REQUEST        = 4002;
    public const NO_PERMISSION          = 4003;
    public const TOKEN_NOT_EXIST        = 4005;
    public const TOKEN_INVALID          = 4006;
    public const TOKEN_EXPIRED          = 4007;
    public const PROHIBITED_LOGIN       = 4008;
    public const REQUEST_LOCKED         = 4009;
    public const SYS_EXCEPTION          = 5000;
    public const SYS_ERROR              = 5001;
    public const DOWN_UP                = 5003;
}


