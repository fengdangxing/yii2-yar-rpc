<?php

namespace fengdangxing\rpc;

use yii\base\Request;
use yii\base\Response;

class Yar extends Base
{
    /**
     * @desc 调用远程服务
     * @author 1
     * @version v2.1
     * @date: 2020/10/16
     * @param $calss
     * @param $server_url
     * @param null $func
     * @return \Yar_Client |bool
     */
    public static function getService($calss, $server_url)
    {
        try {
            if (!isset($server_url)) {
                throw new \Exception('please set params rpc_host');
            }
            $data = [];
            $data['class'] = $calss;
            $dataStr = self::encodeDataStr($data);

            $urlData = $tmpData = [
                'dataStr=' . $dataStr,
                'timeStamp=' . time(),
                'nonceStr=' . self::getRandStr(),
            ];
            $urlData[] = 'sign=' . self::createSign($tmpData);
            $url = "{$server_url}?" . implode('&', $urlData);

            $object = new \Yar_Client($url);
            $object->SetOpt(YAR_OPT_CONNECT_TIMEOUT, 10000);
            $object->SetOpt(YAR_OPT_TIMEOUT, 10000);
        } catch (\Exception $e) {
            return false;
        }
        return $object;
    }

    /**
     * @desc 开放服务
     * @author 1
     * @version v2.1
     * @date: 2020/10/22
     * @param Request $request
     * @param Response $response
     * @return bool
     */
    public static function setService(Request $request, Response $response, $func = null)
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
            if (is_callable($func)) {
                call_user_func($func, $data);
            }
            return $server->handle();
        } catch (\Exception $e) {
            return false;
        }
    }
}
