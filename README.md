# yii2-elasticsearch-sql
```php
#注册服务-单个
Rpc::setService($this->request, $this->response);
#客户端调用服务里的method
$client = Rpc::getService(\backend\modules\v1\service\Task::class, 'http://www.t.com/wms_v1/rpc/server');
$back = $client->add_test(1, 2);

#并行


```
#php依赖
msgpack
yar

https://pecl.php.net/

