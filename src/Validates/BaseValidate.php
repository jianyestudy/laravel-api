<?php
/**
 * User: Edward Yu
 * Date: 2021/9/8

 */

namespace QCS\LaravelApi\Validates;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BaseValidate extends FormRequest
{
    protected $requestData = null;
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
     * @param bool $Pagination
     * @param array $messages
     * @param array $customAttributes
     * @return array
     * @Another Edward Yu 2021/9/8下午8:58
     */
    public function scene( array $rules = [], bool $Pagination = false, array $data = [],  array $messages = [], array $customAttributes = []): array
    {
        //参数默认值
        empty($rules) && $rules = $this->rules();
        empty($customAttributes) && $customAttributes = $this->attributes();
        empty($messages) && $messages = $this->messages();

        // 如果验证分页
        if ($Pagination) {
            $rules = array_merge($this->paginateRules(),$rules); //合并数组
            $customAttributes = array_merge($this->paginateAttributes(), $customAttributes);
        }

        //驼峰与id转换
        if (!$this->requestData) {
            $this->convertAttributes();
        }

        empty($data)  &&  $data = $this->requestData ?? $this->toArray();

        // 验证器
        $validate = Validator::make($data, $rules, $messages, $customAttributes);

        // 验证
        $validate->validate();

        // 返回验证数据
        return $validate->validated();
    }

    /**
     * 键名交集取指定的一些规则
     * @param array $keys
     * @return array
     * @Another Edward Yu 2021/9/8下午6:32
     */
    public function take(array $keys): array
    {
        if (empty($keys)) {
            return $this->rules();
        }

        return array_intersect_key($this->rules(), array_flip($keys));
    }

    /**
     * 根据传递归来的参数与规则交集进行验证
     * @return array
     * @Another Edward Yu 2021/9/26上午10:43
     */
    public function autoTake(): array
    {
        //驼峰与id转换
        if (!$this->requestData) {
            $this->convertAttributes();
        }
        return array_intersect_key($this->rules(), $this->requestData);
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

    /**
     * 分页验证规则
     * @Another Edward Yu 2021/9/26上午9:13
     * @return array
     */
    public function paginateRules(): array
    {
        return [
            'type'          => ['bail', 'nullable','string', Rule::in(['all', 'page'])],
            'limit'         => ['bail', 'nullable', 'numeric', 'max:10000'],
            'page'          => ['bail', 'nullable','numeric'],
            'keyword'       => ['bail','nullable', 'string'],
            'sort_field'    => ['bail','nullable', 'string'],
            'sort_type'     => ['bail','nullable', Rule::in(['desc'])],
        ];
    }

    /**
     * 分页验证属性
     * @Another Edward Yu 2021/9/26上午9:15
     * @return array
     */
    public function paginateAttributes(): array
    {
        return [
            'type'            => '分页类型',
            'limit'           => '条数',
            'page'            => '页码',
            'keyword'         => '关键词',
            'sort_field'      => '排序字段',
            'sort_type'       => '排序类型'
        ];
    }

    public function convertAttributes(): void
    {
        $this->requestData = $this->all();
        //请求添加id
        if ( !empty($this->route('id')) ) {
            $this->requestData['id'] =  $this->route('id');
        }

        //驼峰转蛇形
        if (config('laravel-api.request_camel')) {
            $newParameters = [];
            foreach ($this->requestData as $key => $value){
                $newParameters[Str::snake($key)] = $value;
            }
            $this->requestData =  $this->replace($newParameters)->toArray();
        }

    }
}
