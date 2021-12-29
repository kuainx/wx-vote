<?php
function httpRequest($url = '', $data = '') {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    if ($data) {
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
    $res = curl_exec($ch);
    if (!$res) {
        $data['return_code'] = 'FAIL';
        $data['return_msg'] = 'curl出错，错误码: ' . curl_errorno($ch) . '详情: ' . curl_error($ch);
    } else {
        $data = json_decode($res, true);
    }
    curl_close($ch);
    return $data;
}
?>