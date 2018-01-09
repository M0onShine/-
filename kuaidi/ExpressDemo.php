<?php
/**
 * 获取快递公司接口
 */
require_once 'curl.func.php';

$appcode = '3cbbef7dfbbc4b4dabf7359b65935835';//appcode
$cfg['header'][] = "Authorization:APPCODE " . $appcode;
$result = curlOpen('http://jisukdcx.market.alicloudapi.com/express/type', $cfg);
$jsonarr = json_decode($result, true);

if($jsonarr['status'] != 0)
{
    echo $jsonarr['msg'];
    exit();
}

foreach($jsonarr['result'] as $val)
{
    echo $val['name'].' '.$val['type'].'<br />';
}

/**
 * 快递查询接口
 */
require_once 'curl.func.php';

$appcode = '3cbbef7dfbbc4b4dabf7359b65935835';//appcode
$number = '221560575341'; //快递单号
$type = 'SFEXPRESS'; //快递公司
$cfg['header'][] = "Authorization:APPCODE ".$appcode;

$result = curlOpen("http://jisukdcx.market.alicloudapi.com/express/query?number=$number&type=$type", $cfg);
$jsonarr = json_decode($result, true);

if($jsonarr['status'] != 0)
{
    echo $jsonarr['msg'];
    exit();
}

$result = $jsonarr['result'];
var_dump($result);die;
if($result['issign'] == 1) echo '已签收'.'<br />';
else echo '未签收'.'<br />';
foreach($result['list'] as $val)
{
    echo $val['time'].' '.$val['status'].'<br />';
}
