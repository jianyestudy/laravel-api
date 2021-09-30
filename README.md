**基于Laravel设计的Restful风格api三方包**

**该扩展包实现了 自动curd，场景验证，自定义异常，统一json返回等**

##### 安装说明：
为了不破坏框架现有的功能，不增加额外学习成本，使用了继承的方式，可随意覆盖或者选择性继承


```
1. composer require QCS/laravel-api
2. 继承基础控制器BaseController（命名空间：QCS\LaravelApi\Controllers）
3. 继承基础异常类BaseHandler （命名空间：QCS\LaravelApi\Exceptions）
4. 继承基础模型类 BaseModel （命名空间：QCS\LaravelApi\Models）
5. 继承基础验证类 BaseValidate （命名空间：QCS\LaravelApi\Validates）
```



自动curd：
基于restful api的控制器继承基类之后，并且需要建立对应的模型与验证类，注入到控制器中，无需书写index，update，show，destroy，store方法，可自动处理基本的curd需求，自动处理验证请求。

统一json返回：
在需要返回处 引入UserModelTrait， 调用对应的this->success,this->error,this->noData 可统一返回json格式响应，实现了自定义异常，可处理相关逻辑


