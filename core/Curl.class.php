<?php
/**
 * 模拟浏览器行为
 * @author Senchong <1282106816@qq.com>
 */
class Curl
{
    /**
     * 以GET的方式获取html页面
     *
     * @param string $url 获取页面地址
     * @param string $cookie 登录后的cookie（可选）
     * @return string  html
     **/
    public static function get($url, $cookie = '')
    {
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_TIMEOUT => 3,
            CURLOPT_COOKIE => $cookie,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => array(
                "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36"
            )
        );
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $html = curl_exec($ch);
        //超时检测处理
        self::curlTimeout(curl_errno($ch));
        curl_close($ch);
        return $html;
    }

    /**
     * 登录教务系统
     *
     * @param string $url 登录地址
     * @param string $data POST参数
     * @return string  cookie
     **/
    public static function login($url, $data)
    {
        $ch = curl_init();
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_POST => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_TIMEOUT => 3,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HEADER => 1,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36"
            )
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);

        //检查用户名跟密码是否匹配
        self::checkUserPw($result);

        list($header, $body) = explode("\r\n\r\n", $result);
        preg_match("/set\-cookie:([^\r\n]*)/i", $header, $matches);
        preg_match("/ASP.NET_SessionId=[^;]*/i", $matches[1], $matche);
        //超时检测处理
        self::curlTimeout(curl_errno($ch));
        curl_close($ch);
        return $matche[0];
    }
    public static function curlTimeout($curlErrNo)
    {
        if ($curlErrNo == CURLE_OPERATION_TIMEDOUT) {
            throw new Exception('longtime');
        }
    }
    public static function checkUserPw($html)
    {
        $preg =
            '/id=\"Message\" class=\"LableCss\">你输入的用户名称或者密码有误，请重新输入！<\/span><\/div>/U';
        preg_match($preg, $html, $pat_array);
        if ($pat_array[0]) {
            throw new Exception('password');
        }
    }
}
