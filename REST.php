<?php

/**
 *
 */
class REST
{
    //云通讯平台主帐号，在云通讯平台注册帐号获取
    protected $AccountSid = '8aaf07086561410101657541497a0d35';
    //云通讯平台主帐号token，在云通讯平台注册帐号获取
    protected $AccountToken = '76c80240a494401fa66b059fb8d1e5bd';
    //云通讯平台应用id，在云通讯官网登录后创建应用获取，demo应用和未上线应用只能在沙盒测试环境使用
    protected $AppId = '8aaf0708656141010165754149c70d3b';
    //REST请求地址，sandboxapp.cloopen.com为沙盒测试地址，app.cloopen.com为上线生产地址，不需要写https://
    protected $ServerIP = 'sandboxapp.cloopen.com';
    //REST请求端口
    protected $ServerPort = '8883';
    //REST版本号
    protected $SoftVersion = '2013-12-26';
    protected $BodyType    = 'json';
    protected $Batch       = '';

    public function __construct($APPId = '', $AccountToken = '')
    {
        $this->Batch = date("YmdHis");
        if ($APPId != '') {
            $this->AppId = $APPId;
        }
        if ($AccountToken != '') {
            $this->AccountToken = $AccountToken;
        }
    }

    /**
     * 双向回拨
     * @param        $from            主叫电话号码，可以是手机、座机和voip。被叫为座机时需要添加区号，如：01052823298
     * @param        $to              被叫电话号码，可以是手机、座机和voip。被叫为座机时需要添加区号，如：01052823298；被叫为分机时分机号由‘-’隔开，如：01052823298-3627
     * @param string $customerSerNum  被叫侧显示的号码，根据平台侧显号规则控制(有显号需求请联系云通讯商务，并且说明显号的方式)，不在平台规则内或空则显示云通讯平台默认号码。默认值空。注：被叫侧显号不能和被叫号码相同，否则显示云通讯平台默认号码。显号号码为座机时需要添加区号，如：01052823298。
     * @param string $fromSerNum      主叫侧显示的号码，根据平台侧显号规则控制(有显号需求请联系云通讯商务，并且说明显号的方式)，不在平台规则内或空则显示云通讯平台默认号码。默认值空。注：主叫侧显号不能和主叫号码相同，否则显示云通讯平台默认号码。显号号码为座机时需要添加区号，如：01052823298。
     * @param string $promptTone      wav格式的文件名，第三方自定义回拨提示音，为空则播放云通讯平台公共提示音，默认值为空。语音文件通过官网上传审核后才可使用，放音文件的格式样本如下：位速 128kbps，音频采样大小16位，频道 1(单声道)， 音频采样级别 8 kHz，音频格式 PCM，这样能保证放音的清晰度。
     * @param string $alwaysPlay      是否一直播放提示音，提示音通过promptTone参数设置：true表示直到被叫应答或挂机后才停止播放，不能听呼叫的回铃音和呼叫失败的提示音；false表示被叫振铃停止播放提示音，改听被叫回铃音；默认值为false。
     * @param string $terminalDtmf    用于终止播放promptTone参数定义的提示音，主叫通过指定按键终止播放当前提示音，强制听被叫回铃音。只有当被叫振铃按键才有效，默认值为空。
     * @param string $userData        第三方私有数据，可在鉴权通知(CallAuth)或实时话单通知接口中获取此参数。默认值为空。支持英文字母和数字，长度最大支持256字节。
     * @param string $maxCallTime     通话的最大时长,单位为秒。当通话时长到达最大时长则挂断通话。默认值空，不限制通话时长。与鉴权通知(CallAuth)响应的sessiontime参数作用相同，同时使用时以maxCallTime为准。
     * @param string $hangupCdrUrl    实时话单通知接口回调地址，云通讯平台将向该Url地址（必须符合URL规范，完整的url路径地址，如:http://www.cloopen.com/hangupurl）发送实时话单通知。勾选应用鉴权则此参数无效。
     * @param string $needBothCdr     是否给主被叫发送话单：0表示发送主叫话单；1表示发送主被叫话单； 默认值0。话单通过实时话单通知接口回调第三方服务器，只有当hangupCdrUrl参数有效时才可用。
     * @param int    $needRecord      是否录音：0表示不录音；1表示录音；默认值0。实时下载录音文件在鉴权通知(Hangup)或实时话单通知接口中获取录音下载地址，非实时可以第二天在官网打包进行下载。注：因为录音文件需要时间同步到下载服务器，建议在获取到录音下载地址10秒后再进行下载。
     * @param string $countDownTime   设置通话倒计时时间，当被叫接听时开始计时，否则此参数不生效，单位秒，为空则countDownPrompt参数无效，默认值为空。需要设置maxCallTime参数或鉴权通知(CallAuth)响应的sessiontime参数，并且两个参数设置的时长大于0的同时两个参数的时长需要大于countDownTime参数设置的时长，否则无效。
     * @param string $countDownPrompt wav格式的文件名，第三方自定义倒计时提示音，为空则播放云通讯平台公共提示音（云通讯平台公共提示音有5秒、30秒和60秒三种，只有countDownTime参数设置这三个时间点才会播放对应的公共提示音）。只有当countDownTime参数有效时才生效，默认值为空。语音文件通过官网上传审核后才可使用，放音文件的格式样本如下：位速 128kbps，音频采样大小16位，频道 1(单声道)， 音频采样级别 8 kHz，音频格式 PCM，这样能保证放音的清晰度。
     * @param string $recordPoint     主叫接听还是被叫接听开始录音：0表示主叫接听开始录音；1表示被叫接听开始录音；默认值0。
     */
    public function twoWayCallback($from, $to, $customerSerNum = '', $fromSerNum = '', $promptTone = '', $alwaysPlay = false, $terminalDtmf = '', $userData = '', $maxCallTime = '', $hangupCdrUrl = '', $needBothCdr = 0, $needRecord = 1, $countDownTime = '', $countDownPrompt = '', $recordPoint = 0)
    {
        $body = ["from"            => $from,
                 "to"              => $to,
                 "customerSerNum"  => $customerSerNum,
                 "fromSerNum"      => $fromSerNum,
                 "promptTone"      => $promptTone,
                 "alwaysPlay"      => $alwaysPlay,
                 "terminalDtmf"    => $terminalDtmf,
                 "userData"        => $userData,
                 "maxCallTime"     => $maxCallTime,
                 "hangupCdrUrl"    => $hangupCdrUrl,
                 "needBothCdr"     => $needBothCdr,
                 "needRecord"      => $needRecord,
                 "countDownTime"   => $countDownTime,
                 "countDownPrompt" => $countDownPrompt,
                 "recordPoint"     => $recordPoint
        ];
        // 生成请求URL
        $url    = "https://$this->ServerIP:$this->ServerPort/$this->SoftVersion/SubAccounts/$this->AccountToken/$this->AccountSid/Calls/Callback";
        $result = $this->action(json_encode($body), $url);
        return $result;
    }

    /**
     * 取消回拨
     * @param string $callSid 一个由32个字符组成的通话唯一标识符，通过云通讯双向回拨的接口请求成功后产生。
     * @param int    $type    0： 任意时间都可以挂断电话；1 ：被叫应答前可以挂断电话，其他时段返回错误代码；2： 主叫应答前可以挂断电话，其他时段返回错误代码；默认值为0。
     */
    public function cancelCallback($callSid, $type = 0)
    {
        $body   = ["appId"   => $this->AppId,
                   "callSid" => $callSid,
                   "type"    => $type
        ];
        $url    = "https://$this->ServerIP:$this->ServerPort/$this->SoftVersion/SubAccounts/$this->AccountToken/$this->AccountSid/Calls/CallCancel";
        $result = $this->action(json_encode($body), $url);
        return $result;
    }

    /**
     * 查询中间号
     * @param int    $size
     * @param string $areaCode 區號
     */
    public function queryMiddleNumber($size = 20, $areaCode = '020')
    {
        $body = ["appId:" => $this->AppId,
                 "size"   => $size
        ];
        // 大写的sig参数
        $sig    = strtoupper(md5($this->AccountSid . $this->AccountToken . $this->Batch));
        $url    = "https://$this->ServerIP:$this->ServerPort/$this->SoftVersion/Accounts/$this->AccountSid/nme/axb/$areaCode/querynumber?sig=$sig";
        $result = $this->action(json_encode($body), $url);
        return $result;
    }

    /**
     * 设置中间号
     * @param        $from A方的电话号码。
     * @param        $to B方的电话号码
     * @param        $servingNumber 指定的服务号（即中间号）
     * @param string $areaCode
     * @param bool   $laterAnswer 是否启用“晚应答”。“true”或“f alse”。
     * @param bool   $needRecord 是否需要录音。
     * @return mixed
     */
    public function setTheMiddleNumber($from, $to, $servingNumber, $areaCode = '020', $laterAnswer = false, $needRecord = true)
    {
        $body = ["appId:"        => $this->AppId,
                 "aNumber"       => $from,
                 "bNumber"       => $to,
                 "servingNumber" => $servingNumber,
                 "laterAnswer"   => $laterAnswer,
                 "needRecord"    => $needRecord,
        ];
        // 大写的sig参数
        $sig    = strtoupper(md5($this->AccountSid . $this->AccountToken . $this->Batch));
        $url    = "https://$this->ServerIP:$this->ServerPort/$this->SoftVersion/Accounts/{$this->AccountSid}/nme/axb/{$areaCode}/setnumber?sig=$sig";
        $result = $this->action(json_encode($body), $url);
        return $result;
    }


    protected function action($data, $url)
    {
        //exit(json_encode($url));
        // 生成授权：主帐户Id + 英文冒号 + 时间戳。
        $authen = base64_encode($this->AccountSid . ":" . $this->Batch);
        // 生成包头
        $header = ["Accept:application/{$this->BodyType}",
                   "Content-Type:application/{$this->BodyType};charset=utf-8;",
                   "Authorization:$authen"
        ];
        // 发送请求
        $result = $this->curl_post($url, $data, $header);
        return json_decode($result);
    }

    /**
     * 发起HTTPS请求
     */
    function curl_post($url, $data, $header, $post = 1)
    {
        //初始化curl
        $ch = curl_init();
        //参数设置
        $res = curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, $post);
        if ($post) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($ch);
        //连接失败
        if ($result == false) {
            if ($this->BodyType == 'json') {
                $result = "{\"statusCode\":\"172001\",\"statusMsg\":\"网络错误\"}";
            } else {
                $result = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?><Response><statusCode>172001</statusCode><statusMsg>网络错误</statusMsg></Response>";
            }
        }
        curl_close($ch);
        return $result;
    }
}