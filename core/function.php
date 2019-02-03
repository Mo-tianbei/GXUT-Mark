<?php
/**
 * 常用方法
 * @author Senchong <1282106816@qq.com>
 */
function getURI($num)
{
    $reqArr = explode('/', $_SERVER['REQUEST_URI']);
    return $reqArr[$num];
}

function newClass($name)
{
    $className = $name;
    $obj = new $className();
    return $obj;
}

function getPOST($name)
{
    return $_POST[$name];
}
function removeHtmlSpace($html)
{
    $air = array("　", "\t", "\n", "\r");
    $html = str_replace($air, '', $html);
    return $html;
}
function odd($key)
{
    return ($key & 1);
}
function getPostData($data)
{
    $thePostData = '';
    foreach ($data as $key => $value) {
        $thePostData .= $key . '=' . $value . '&';
    }
    return $thePostData;
}
function getStudentClass($cookie)
{
    $url = 'http://172.16.129.117/web_jxrw/cx_kb_xsgrkb.aspx';
    $html = Curl::get($url, $cookie);
    preg_match(
        "/.*<input name=\"Txtbj\" type=\"text\" value=\"(.*)\" id=\"Txtbj\"/U",
        $html,
        $pat_array
    );
    return $pat_array[1];
}

function getStudentName($cookie)
{
    $url = 'http://172.16.129.117/index3.aspx';
    $html = Curl::get($url, $cookie);
	preg_match("/.*<div class=\"left\">当前用户： (.*)<a href=/U", $html, $pat_array);
	return $pat_array[1];
}
function getMarkPoint($html)
{
	$preg = '/name=\"Txtjd\" type=\"text\" value=\"(.*)\" id=\"Txtjd\"/U';
	preg_match($preg, $html, $pat_array);
	return $pat_array[1];
}