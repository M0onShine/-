<?php  

/**
 * 获取快递公司接口
 */

    $host = "http://jisukdcx.market.alicloudapi.com";
    $path = "/express/type";
    $method = "GET";
    $appcode = "3cbbef7dfbbc4b4dabf7359b65935835";
    $headers = array();
    array_push($headers, "Authorization:APPCODE " . $appcode);
    $querys = "";
    $bodys = "";
    $url = $host . $path;

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_FAILONERROR, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    if (1 == strpos("$".$host, "https://"))
    {
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    }

    $result = curl_exec($curl);
    $jsonarr = json_decode($result, true);

    if($jsonarr['status'] != 0)
    {
        echo $jsonarr['msg'];
        exit();
    }

    foreach($jsonarr['result'] as $val)
    {
		$kuaidi[$val['name']]=$val['type'];
        //echo $val['name'].' '.$val['type'].'<br />';
    }

/**
 * 快递查询接口
 */
    //$number=$_GET['yundan'];
    $number='221560575341';
	//$type=$_GET['kdgongsi'];
	$type='SFEXPRESS';
    $host = "http://jisukdcx.market.alicloudapi.com";
    $path = "/express/query";
    $method = "GET";
    $appcode = "3cbbef7dfbbc4b4dabf7359b65935835";
    $headers = array();
    array_push($headers, "Authorization:APPCODE " . $appcode);
    $querys = "number=".$number."&type=".$type;
    //$querys = "number=".$number."&type=".$kuaidi[$type];
    $bodys = "";
    $url = $host . $path . "?" . $querys;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_FAILONERROR, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    if (1 == strpos("$".$host, "https://"))
    {
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    }

    $result = curl_exec($curl);
    $jsonarr = json_decode($result,true);
    $result = $jsonarr['result'];
    
    if($result['issign'] == 1) echo '已签收'.'<br />';
    else echo '未签收'.'<br />';
    foreach($result['list'] as $val)
    {
        echo $val['time'].' '.$val['status'].'<br />';
    }