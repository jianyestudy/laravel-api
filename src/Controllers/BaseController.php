<?php

namespace QCS\LaravelApi\Controllers;

use Closure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use QCS\LaravelApi\Exceptions\ResultException;
use QCS\LaravelApi\Traits\ResultTrait;

/**
 *资源控制器
 * @auther Edward Yu
 * @package QCS\LaravelApi\Controller
 */
class BaseController extends Controller
{
    use ResultTrait;
    /**
     * 模型实例
     * @var Model
     */
    protected $model;

    /**
     * 请求实例
     * @var Request
     */
    protected $request;

    /**
     * index查询的字段
     * @var array
     */
    protected $indexColumns = ['*'];

    /**
     * store 允许保存的字段
     * @var string[]
     */
    protected $storeColumns = ['*'];


    /**
     * show 详情允许查出的字段
     * @var string[]
     */
    protected $showColumns =  ['*'];

    /**
     * update 允许更新的字段
     * @var string[]
     */
    protected $updateColumns = ['*'];

    /**
     * 是否需要分页
     * @var bool
     */
    protected $isPaging = true;


    /**
     * 列表查询 分页查询
     * @Another Edward Yu 2021/9/27上午9:29
     * @throws ResultException
     */
    public function index(): void
    {
        //查询前置 验证参数
        $requestData    = $this->indexBeforeHandler();

        //分页赋值
        $this->isPaging = $this->isPaging($requestData);

        //查询前置 构造构造器
        $builder        = $this->model::query();

        //构造查询操作构造器 join with 等
        $this->withIndexBuilder($builder, $requestData);

        //组装搜索条件 where order group 等
        $this->indexSearch($builder, $requestData);

        //查询操作
        $collect = $this->indexSelect($builder, $requestData);
        if ($collect->isEmpty()) {
            $this->noData();
        }

        //后置操作
        $callback = $this->indexAfterHandler($collect, $requestData);
        if ($callback instanceof Closure) {
            $callback($collect, $requestData);
        }

        //返回最终数据 是否需要重新分页
        $this->success($this->indexResult($collect, $requestData));

    }

    /**
     * store 保存数据
     * @throws ResultException
     */
    public function store(): void
    {
        // 新增前置 返回验证过的数据
        $requestData    = $this->storeBeforeHandler();

        //构造器
        $builder        = $this->model::query();

        //保存前处理数据
        $requestData    =  $this->storeHandler($builder, $requestData);

        //只保存允许值
        if (count($this->storeColumns) > 1) {
            $requestData = Arr::only($requestData, $this->storeColumns);
        }

        //执行保存
        $result         = $this->storeSave($builder, $requestData);

        //保存后置
        $this->storeAfterHandler($builder, $result);

        $this->success($result->id);

    }

    /**
     * show 查看数据
     * @throws ResultException
     */
    public function show(int $id): void
    {
        //查询前置操作
        $this->showBeforeHandler();

        //构造查询器
        $builder = $this->model::query();

        //默认通过主键查询
        if (!$id) {
            $this->noData(null, '只能通过主键查询，重写方法可实现自定义查询');
        }

        $builder->where($builder->getModel()->getKeyName(), $id);

        $result = $builder->firstOr($this->showColumns, function (){
            $this->noData();
        });
        //查询后置操作
        $this->showAfterHandler($result);

        $this->success($result);
    }


    /**
     * update 更新
     * @Another Edward Yu 2021/9/28上午9:22
     * @throws ResultException
     */
    public function update(int $id): void
    {
        //更新前置操作
        $requestData = $this->updateBeforeHandler();
        //更新构造器
        $builder = $this->model::query();

        //执行更新
        if (!$id) {
            $this->error('主键值无效');
        }

        if (empty($requestData)) {
            $this->error('参数为空');
        }

        //只更新允许值
        if ( count($this->updateColumns)> 1 ) {
            $requestData = Arr::only($requestData, $this->updateColumns);
        }

        $info = $builder
            ->where($builder->getModel()->getKeyName(), $id)
            ->update($requestData);

        if (!$info) {
            $this->error('更新失败');
        }

        //更新后置操作
        $this->updateAfterHandler($id,$requestData);

        $this->success();

    }

    public function destroy(int $id)
    {
        //删除前置
        $this->destroyBeforeHandler();

        //删除
        $result = $this->model::destroy($id);

        if (!$result) {
            $this->error();
        }

        //后置删除
        $this->destroyAfterHandler($id);

        $this->success();

    }

    /**
     * index 验证方法
     * @Another Edward Yu 2021/9/27上午9:44
     * @return mixed
     */
    public function indexBeforeHandler()
    {
        //不存在就内置方法就不验证
        return method_exists($this->request,'indexValidate')
            ? $this->request->indexValidate()
            : $this->request->toArray();
    }

    /**
     * 查询构造器
     * @Another Edward Yu 2021/9/27上午10:04
     */
    public function withIndexBuilder(Builder $builder, array $requestData) :void
    {}

    /**
     * 搜索构造器
     * @Another Edward Yu 2021/9/27上午10:13
     */
    public function indexSearch(Builder $builder, array $requestData) : void
    {}

    /**
     * index执行查询部分
     * @param Builder $builder
     * @param array $requestData
     * @return LengthAwarePaginator|Builder[]|Collection
     * @Another Edward Yu 2021/9/27上午10:18
     */
    public function indexSelect(Builder $builder, array $requestData)
    {
        //是否启用分页
        if (!$this->isPaging) {
            return $builder->select($this->indexColumns)->offset(0)->limit(1000)->get();
        }

        $pageSize = $requestData['limit'] ?? 10;
        $page     = $requestData['page'] ?? 1;
        return $builder->paginate($pageSize, $this->indexColumns, 'page', $page);
    }

    /**
     * 查询结果的后置处理
     * @param  $collect
     * @param array $requestData
     * @return void |Collection
     * @Another Edward Yu 2021/9/27上午10:51
     */
    public function indexAfterHandler( $collect, array $requestData){}


    /**
     * 是否需要分页
     * @param array $requestData
     * @Another Edward Yu 2021/9/27上午11:20
     * @return bool
     */
    public function isPaging(array $requestData): bool
    {
        return $this->isPaging = !(!empty($requestData['type']) && $requestData['type'] === 'all');
    }

    /**
     * 最终返回数据
     * @param $collection
     * @Another Edward Yu 2021/9/27上午11:47
     * @return LengthAwarePaginator|\Illuminate\Pagination\LengthAwarePaginator
     */
    public function indexResult($collection)
    {
        if ($this->isPaging) {
            if ($collection instanceof LengthAwarePaginator) {
                return $collection;
            }
            return  new \Illuminate\Pagination\LengthAwarePaginator($collection, $collection->total(), $collection->perPage());
        }
        return $collection;
    }

    /**
     * store 验证规则
     * @Another Edward Yu 2021/9/27下午2:00
     * @return mixed
     */
    public function storeBeforeHandler()
    {
        //不存在就内置方法就不验证
        return method_exists($this->request,'storeValidate')
            ? $this->request->storeValidate()
            : $this->request->toArray();
    }

    /**
     * 对保存的数据处理
     * @Another Edward Yu 2021/9/27下午2:21
     */
    public function storeHandler(Builder $builder, array $requestData) : array
    {
        return $requestData;
    }


    /**
     * @throws ResultException
     */
    public function storeSave(Builder $builder, array $requestData)
    {
        $result = $builder->create($requestData);

        if ($result === null) {
            $this->error('写入失败');
        }
        return $result;
    }

    /**
     * store 后置
     * @Another Edward Yu 2021/9/27下午4:33
     */
    public function storeAfterHandler($builder, $result)
    {}



    /**
     * show 验证过后的数据 （id）
     * @Another Edward Yu 2021/9/27下午4:52
     * @return mixed
     */
    public function showBeforeHandler()
    {
        //不存在就内置方法就不验证
        return method_exists($this->request,'showValidate')
            ? $this->request->showValidate()
            : $this->request->toArray();
    }


    /**
     * 查询的后置操作
     * @Another Edward Yu 2021/9/27下午5:03
     */
    public function showAfterHandler($result){}


    /**
     * 更新前置操作方法
     * @Another Edward Yu 2021/9/28上午9:20
     * @return mixed
     */
    public function updateBeforeHandler()
    {
        //不存在就内置方法就不验证
        $requestData = method_exists($this->request,'updateValidate')
            ? $this->request->updateValidate()
            : $this->request->toArray();

        //卸载id
        if (isset($requestData['id'])) {
            unset($requestData['id']);
        }

        return $requestData;
    }

    /**
     * 更新后置操作
     * @param int $id
     * @param array $requestData
     * @Another Edward Yu 2021/9/28上午9:21
     */
    public function updateAfterHandler(int $id, array $requestData){}


    /**
     * 删除前置操作
     * @Another Edward Yu 2021/9/28上午10:53
     * @return mixed
     */
    public function destroyBeforeHandler()
    {
        //不存在就内置方法就不验证
        return method_exists($this->request,'destroyValidate')
            ? $this->request->destroyValidate()
            : $this->request->toArray();
    }

    /**
     * 删除后置
     * @param int $id
     * @Another Edward Yu 2021/9/28上午11:44
     */
    public function destroyAfterHandler(int $id){}

}
