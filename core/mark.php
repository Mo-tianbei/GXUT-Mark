<?php
/**
 * Created by Sublime Text 3.
 * @author Senchong <1282106816@qq.com>
 * @version 2019/2/3-1.0
 */

// 允许跨域访问
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

require_once('function.php');
require_once('Curl.class.php');

// 记录log
@file_put_contents('../mark.log', md5($_POST['studentid']) . "----".$_SERVER['REMOTE_ADDR'].'----'.date('Y-m-d H:i:s',time())."\r\n", FILE_APPEND);

//开始
markApp();

// *************************************************************************************
// 核心  
function markApp() 
{
	try {
		$cookie = login();
		ob_clean();
		echo json_encode(getChengji($cookie), JSON_UNESCAPED_UNICODE);
	} catch (Exception $e) {
	    $err = '{"status":"' . $e->getMessage() . '"}';
	    ob_clean();
	    echo $err;	
	}	
}

function login() 
{
	$hiddenData = getLoginHiddenData();
	$data = array(
	    'UserName' => $_POST['studentid'],
	    'Password' => $_POST['password'],
	    'getpassword' => '%E7%99%BB%E9%99%86'
	);
	$data = array_merge($data, $hiddenData);
	$data = getPostData($data);
	$cookie = Curl::login('http://172.16.129.117', $data);
	return $cookie;
}
function getLoginHiddenData()
{
	$html = Curl::get('http://172.16.129.117');
	$hiddenArr = array('__VIEWSTATE', '__VIEWSTATEGENERATOR', '__EVENTVALIDATION');
	foreach ($hiddenArr as $value) {
		$pcre = '/id="' . $value . '".value="(?<hidden_value>.+?)"/';
		preg_match_all($pcre, $html, $pat_array);
		$data[$value] = urlencode($pat_array['hidden_value'][0]);
	}
	return $data;
}

function getChengji($cookie)
{
	$html = Curl::get('http://172.16.129.117/web_cjgl/cx_cj_xh.aspx', $cookie);
	//获取绩点
	$markPoint = getMarkPoint($html);
	//获取所有成绩
	$chengji = _getChengji($html);
	if($chengji[0]['姓名'] != '') {
		$data = array(
			'status' => 'success',
			'获取时间' => date("Y-m-d H:i"),
			'学号' => $chengji[0]['学号'],
			'姓名' => $chengji[0]['姓名'],
			'绩点' => $markPoint,
			'result' => $chengji
		);
	} else {
		$data = array(
			'status' => 'error'
		);
	}
	return $data;
}
function _getChengji($html)
{
	$html = removeHtmlSpace($html);
	$preg = '/<table class=\"dg1-table\"(.*)<\/table>/U';
	preg_match($preg, $html, $pat_array);
	//先获取到成绩表格
	$htmlTable = $pat_array[0];
	$preg =
		'/<tr class=\"dg1-item\"><td style(.*)>(.*)<\/td><td style(.*)>(.*)<\/td><td style(.*)>(.*)<\/td><td style(.*)>(.*)<\/td><td style(.*)>(.*)<\/td><td style(.*)>(.*)<\/td><td style(.*)>(.*)<\/td><td style(.*)>(.*)<\/td><td style(.*)>(.*)<\/td><td style(.*)>(.*)<\/td><td style(.*)>(.*)<\/td><td style(.*)>(.*)<\/td><td style(.*)>(.*)<\/td><td style(.*)>(.*)<\/td><\/tr>/U';
	preg_match_all($preg, $htmlTable, $pat_array);

	array_shift($pat_array);
	$chengji = array_merge(array_filter($pat_array, "odd", ARRAY_FILTER_USE_KEY));

	$chengjiInfoArr = array(
		'学号',
		'姓名',
		'学期',
		'课程名称',
		'类别',
		'学分',
		'平时',
		'期中',
		'期末',
		'总评成绩',
		'考试性质',
		'绩点',
		'课程代码',
		'学时'
	);

	/**
	 * @var int 总成绩数
	 */
	$chengjiNum = count($chengji[0]);
	/**
	 * @var int 成绩信息数
	 */
	$chengjiInfoNum = count($chengji);
	for ($j = 0; $j < $chengjiNum; $j++) {
		$tempArr = array();
		for ($i = 0; $i < $chengjiInfoNum; $i++) {
			$tempArr[$chengjiInfoArr[$i]] = $chengji[$i][$j];
		}
		$chengjiArr[] = $tempArr;
	}

	return $chengjiArr;
}
// *************************************************************************************