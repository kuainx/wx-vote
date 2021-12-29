<?php
include('./config.php');
// 获取token
$token_data = file_get_contents('./wechat_token.txt');
if (!empty($token_data)) {
    $token_data = json_decode($token_data, true);
}

$time = time() - $token_data['time']||0;
if ($time > 3600) {
    $token_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
    $token_res = https_request($token_url);
    $token_res = json_decode($token_res, true);
    $token = $token_res['access_token'];

    $data = array(
        'time' => time(),
        'token' => $token
    );
    $res = file_put_contents('./wechat_token.txt', json_encode($data));
} else {
    $token = $token_data['token'];
}


// 获取ticket
$ticket_data = file_get_contents('./wechat_ticket.txt');
if (!empty($ticket_data)) {
    $ticket_data = json_decode($ticket_data, true);
}

$time = time() - $ticket_data['time']||0;
if ($time > 3600) {
    $ticket_url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token={$token}&type=jsapi";
    $ticket_res = https_request($ticket_url);
    $ticket_res = json_decode($ticket_res, true);
    $ticket = $ticket_res['ticket'];

    $data = array(
        'time' => time(),
        'ticket' => $ticket
    );
    $res = file_put_contents('./wechat_ticket.txt', json_encode($data));
} else {
    $ticket = $ticket_data['ticket'];
}


// 进行sha1签名
$timestamp = time();
$nonceStr = createNonceStr();

// 注意 URL 建议动态获取(也可以写死).
// $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
// $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; // 调用JSSDK的页面地址
$url = $_SERVER['HTTP_REFERER']; // 前后端分离的, 获取请求地址(此值不准确时可以通过其他方式解决)

$str = "jsapi_ticket={$ticket}&noncestr={$nonceStr}&timestamp={$timestamp}&url={$url}";
$sha_str = sha1($str);

echo json_encode(array('appId' => $appid, 'timestamp' => $timestamp, 'nonce' => $nonceStr, 'signature' => $sha_str), JSON_UNESCAPED_UNICODE);

function createNonceStr($length = 16) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
        $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
}


/**
* 模拟 http 请求
* @param  String $url  请求网址
* @param  Array  $data 数据
*/
function https_request($url, $data = null) {
    // curl 初始化
    $curl = curl_init();

    // curl 设置
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);

    // 判断 $data get  or post
    if (!empty($data)) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    // 执行
    $res = curl_exec($curl);
    curl_close($curl);
    return $res;
}

?>