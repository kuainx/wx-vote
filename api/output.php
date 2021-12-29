<?php
include('./config.php');
$mysql = new PDO("mysql:dbname={$dbname};host=localhost", $dbuser, $dbpass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8';"));
$a = $mysql->prepare('SELECT * FROM `vote_vote`');
$a->execute(array());
$vote = $a->fetchAll(PDO::FETCH_ASSOC);
$a = $mysql->prepare('SELECT * FROM `vote_option`');
$a->execute(array());
$option = $a->fetchAll(PDO::FETCH_ASSOC);
for ($i = 0; $i < count($option); $i++) {
    for ($j = 0; $j < count($vote); $j++) {
        if ($vote[$j]['id'] == $option[$i]['vote']) {
            $option[$i]['vote'] = $vote[$j]['content'];
            break;
        }
    }
}
$file = "./dat.csv";
$fp = fopen($file, 'w');
foreach ($option as $value) {
    $dat='';
    foreach ($value as $value1) {
        $dat.=iconv("UTF-8", "GB2312//IGNORE", $value1).',';
        // $dat.=$value1.',';
    }
    // echo($dat.'\n');
    fwrite($fp,$dat.PHP_EOL);
}
fclose($fp);
header("Location:https://lifestudio.cn/vote/api/dat.csv");