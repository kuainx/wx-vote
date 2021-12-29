<?php
include('./config.php');
$mysql = new PDO("mysql:dbname={$dbname};host=localhost", $dbuser, $dbpass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8';"));

$T = empty($_GET['t'])?'':$_GET['t'];
switch ($T) {
    case 'getuser':
        if (strlen($_POST['openid']) == 0) {
            $ret = array('status' => 110001, 'ret' => '用户校验失败');
        } else {
            $a = $mysql->prepare('SELECT * FROM `vote_user` WHERE `openid` = ? LIMIT 1');
            $a->execute(array($_POST['openid']));
            $res = $a->fetchAll(PDO::FETCH_ASSOC);
            if (empty($res)) {
                $ret = array('status' => 110002, 'ret' => '用户校验失败');
            } else {
                $ret = array('status' => 0, 'ret' => $res);
            }
        }
        break;

    case 'getvote':
        $a = $mysql->prepare('SELECT * FROM `vote_vote` WHERE `url` = ? LIMIT 1');
        $a->execute(array($_POST['voteid']));
        $res = $a->fetchAll(PDO::FETCH_ASSOC);
        if (empty($res)) {
            $ret = array('status' => 120001, 'ret' => '链接已失效或不存在');
        } else {
            $a = $mysql->prepare('SELECT * FROM `vote_option` WHERE `vote` = ?');
            $a->execute(array($res[0]['id']));
            $res1 = $a->fetchAll(PDO::FETCH_ASSOC);
            $ret = array('status' => 0, 'ret' => [$res, $res1]);
        }
        break;

    case 'vote':
        $a = $mysql->prepare('SELECT * FROM `vote_option` WHERE `id` = ? LIMIT 1');
        $a->execute(array($_POST['optionid']));
        $option = $a->fetchAll(PDO::FETCH_ASSOC);
        if (empty($option)) {
            $ret = array('status' => 130001, 'ret' => '投票已失效或不存在');
        } else {
            $a = $mysql->prepare('SELECT * FROM `vote_vote` WHERE `id` = ? LIMIT 1');
            $a->execute(array($option[0]['vote']));
            $vote = $a->fetchAll(PDO::FETCH_ASSOC);
            if ($vote[0]['start_time'] > time()) {
                $ret = array('status' => 130004, 'ret' => '投票暂未开始');
            } else if ($vote[0]['end_time'] < time()) {
                $ret = array('status' => 130005, 'ret' => '投票已结束');
            } else {
                $a = $mysql->prepare('SELECT * FROM `vote_user` WHERE `openid` = ? LIMIT 1');
                $a->execute(array($_POST['openid']));
                $res = $a->fetchAll(PDO::FETCH_ASSOC);
                if (empty($res)) {
                    $ret = array('status' => 130002, 'ret' => '用户校验失败');
                } else {
                    $vote = json_decode($res[0]['vote'], true);
                    if (!empty($vote[$option[0]['vote']])) {
                        $ret = array('status' => 130003, 'ret' => '已参加过该投票');
                    } else {
                        $a = $mysql->prepare('UPDATE `vote_option` SET `num` = num+1 WHERE `id` = ?');
                        $a->execute(array($_POST['optionid']));
                        $vote[$option[0]['vote']] = $option[0]['id'];
                        $vote1 = json_encode($vote, JSON_UNESCAPED_UNICODE);
                        $a = $mysql->prepare('UPDATE `vote_user` SET `vote` = ?,`nickname` = ? WHERE `id` = ?');
                        $a->execute(array($vote1, $_POST['nickname'], $res[0]['id']));
                        $ret = array('status' => 0, 'ret' => 0);
                    }
                }
            }
        }
        break;

    default:
        $ret = array('status' => 170001, 'ret' => 'Request denied');
        break;
}
$return = json_encode($ret, JSON_UNESCAPED_UNICODE);
header('Content-Type:application/json');
echo $return;