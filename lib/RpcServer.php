<?php

namespace fengdangxing\yii2YarRpc;

use yii\base\Request;
use yii\base\Response;

class RpcServer extends Base
{
    public static function setService(Request $request, Response $response)
    {
        $response->send();
        $requestData = $request->get();

        if ($requestData['timeStamp'] + self::$timeStampOut < time()) {
            return false;
        }

        if (!self::verifySign($requestData)) {
            return false;
        }

        $data = self::decodeDataStr($requestData['dataStr']);
        try {
            $ob = new \ReflectionClass($data['class']);
            $class = $ob->getName();
            $server = new \Yar_Server(new $class());
            return $server->handle();
        } catch (\Exception $e) {
            return false;
        }
    }
}
