<?php
/**
 * User: Edward Yu
 * Date: 2021/9/8

 */

namespace QCYX\LaravelApi\Validates;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class BaseValidate extends FormRequest
{

    /**
     * 覆盖自动验证
     * @Another Edward Yu 2021/9/8下午6:03
     */
    public function validateResolved(): array
    {
        return [];
    }

    /**
     * 验证规则
     * @Another Edward Yu 2021/9/8下午6:04
     */
    public function rules(): array
    {
        return [];
    }


    /**
     * 错误信息
     */
    public function messages(): array
    {
        return [];
    }

    /**
     * 属性
     * @Another Edward Yu 2021/9/8下午6:28
     * @return array
     */
    public function attributes(): array
    {
        return [];
    }

    /**
     * 校念方法
     * @param array $data
     * @Another Edward Yu 2021/9/8下午6:08
     * @return array
     */
    public function check(array $data): array
    {
        return $this->scene($data);
    }

    /**
     * 返回验证过的数据
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @Another Edward Yu 2021/9/8下午8:58
     * @return array
     */
    public function scene(array $data, array $rules = [], array $messages = [], array $customAttributes = []): array
    {
        // 参数默认值
        empty($rules) && $rules = $this->rules();
        empty($messages) && $messages = $this->messages();
        empty($customAttributes) && $customAttributes = $this->attributes();

        // 验证器
        $validate = Validator::make($data, $rules, $messages, $customAttributes);

        // 验证
        $validate->validate();

        // 返回验证数据
        return $validate->validated();
    }

    /**
     * 键名交集取规则
     * @param array $data
     * @Another Edward Yu 2021/9/8下午6:32
     * @return array
     */
    public function many(array $data): array
    {
        if (empty($data)) {
            return $this->rules();
        }
        return array_intersect_key($this->rules(), array_flip($data));
    }

    /**
     * 单例
     * @Another Edward Yu 2021/9/8下午8:44

    public static function getInstance()
    {
        if (!app()->has(__CLASS__)) {
            app()->instance(__CLASS__, app()->make(__CLASS__));
        }
        return app()->get(__CLASS__);
    }*/
}
