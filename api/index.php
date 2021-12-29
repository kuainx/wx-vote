<?php
include('./config.php');
if (empty($_GET['code'])) {
    header("Location:https://mp.weixin.qq.com/mp/profile_ext?action=home&__biz=MzUxNjM2NzM3Ng==#wechat_redirect");
    exit;
}
include('./util.php');

//https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx61b12b15f8cbb912&redirect_uri=https%3A%2F%2Flifestudio.cn%2Fvote%2Fapi%2Findex.php&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect
$mysql = new PDO("mysql:dbname={$dbname};host=localhost", $dbuser, $dbpass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8';"));
$ret = httpRequest("https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$secret}&code=".$_GET['code'].'&grant_type=authorization_code');
$ret = httpRequest('https://api.weixin.qq.com/sns/userinfo?access_token='.$ret['access_token'].'&openid='.$ret['openid'].'&lang=zh_CN');
print_r($ret);
// $a = $mysql->prepare('INSERT INTO `wechat` (`openid`, `user`, `phone`) VALUES (?, ?, ?)');
$a = $mysql->prepare('SELECT * FROM `vote_user` WHERE `openid` = ? LIMIT 1');
$a->execute(array($ret['openid']));
$res = $a->fetchAll();
if (empty($res)) {
    $a = $mysql->prepare('INSERT INTO `vote_user` (`openid`, `username`, `vote`) VALUES (?, ?, "[]")');
    $a->execute(array($ret['openid'], $ret['nickname']));
} else if ($res[0]['username'] !== $ret['nickname']) {
    $a = $mysql->prepare('UPDATE `vote_user` SET `username` = ? WHERE `id` = ?');
    $a->execute(array($ret['nickname'], $res[0]['id']));
}
header("Location:https://lifestudio.cn/vote/?user=".$ret['openid'].'&vote='.$_GET['state']);