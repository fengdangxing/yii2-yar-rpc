<?php

namespace fengdangxing\yii2YarRpc;

class RpcClient extends Base
{

    /**
     * @desc 调用远程服务
     * @author 1
     * @version v2.1
     * @date: 2020/10/16
     * @param $calss
     * @param $server_url
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
}
