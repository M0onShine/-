<form action="" method="post">
手机号码:<br>
<textarea rows="5" cols="40" name="phone" placeholder="群发短信需传入多个号码，以英文逗号分隔，一次调用最多传入200个号码" >
</textarea>
<br>
短信内容:<br>
<textarea rows="5" cols="40" name="content" >
</textarea>
<br><br>
<input type="submit" value="Submit">
</form>
<?php
require_once './alidayu/TopSdk.php';
require_once './alidayu/top/TopClient.php';

if(!empty($_POST['phone'])&&!empty($_POST['content'])){
	$content = $_POST['content'];
	$m = $_POST['phone'];
	echo $m."<br />";;
	echo $content;die;
	$c = new TopClient;
	$c->appkey = '24670755';
	$c->secretKey = '9d38b605e5a426ed065d0f6990c8cdea';
	$req = new AlibabaAliqinFcSmsNumSendRequest;
	$req->setExtend("123456");
	$req->setSmsType("normal");
	$req->setSmsFreeSignName("服务台");
	$req->setSmsParam("{content:'$content'}");
	$req->setRecNum($m);
	$req->setSmsTemplateCode("SMS_70095454");
	$resp = $c->execute($req);
	if($resp){
		$host = 'localhost';
		$username = 'root';
		$password = '';
		$database = 'liuyan';
		$mysqli = mysqli_connect($host,$username,$password,$database);
		if (!$mysqli)
		{
			die('Could not connect: ' . mysql_error());
		}
		$mysqli->set_charset('utf8');
		$stmt = $mysqli->prepare("INSERT INTO duanxin (phone,content,time) VALUES (?, ?, ?)");
		$stmt->bind_param("sssssss", $phone, $content, $time);
		$phone = $m;
		$content = $content;
		$time = time();
		if($stmt->execute()){
		echo "<script>alert('添加成功');window.history.go(-1); </script>";
		$stmt->close();
		}
	}
}
?>