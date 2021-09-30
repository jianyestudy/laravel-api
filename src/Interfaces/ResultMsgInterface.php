<?php
/**
 * User: Edward Yu
 * Date: 2021/9/2

 */

namespace QCS\LaravelApi\Interfaces;

/**
 * 状态信息
 * @auther Edward Yu
 * @package QCS\LaravelApi\Interfacesz
 */
interface ResultMsgInterface
{
    public const SUCCESS_MSG                 =  'success';
    public const NO_DATA_MSG                 =  '暂无数据';
    public const MAINTENANCE_MSG             =  '系统维护中...';
    public const ERROR_MSG                   =  'error';
    public const NOT_UNIQUE_MSG              =  '当前账号在其他地方登录';
    public const INVALID_REQUEST_MSG         =  '无权限';
    public const NO_PERMISSION_MSG           =  '无效请求';
    public const TOKEN_NOT_EXIST_MSG         =  '登录失败，token不存在';
    public const TOKEN_INVALID_MSG           =  '登录失败，token无效';
    public const TOKEN_EXPIRED_MSG           =  '登录失败，token已过期';
    public const PROHIBITED_LOGIN_MSG        =  '禁止登录';
    public const REQUEST_LOCKED_MSG          =  '请求被锁定';
    public const SYS_EXCEPTION_MSG           =  '系统异常，请稍后重试';
    public const SYS_ERROR_MSG               =  '系统错误，请稍后重试';
    public const DOWN_UP_MSG                 =  '系统升级中,请耐心的等待';
}


