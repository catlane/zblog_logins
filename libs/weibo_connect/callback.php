<?php
session_start();

include 'saetv2.ex.class.php';

$weibo_appkey = $zbp->Config('logins')->sinaAppid;
$weibo_appsecret = $zbp->Config('logins')->sinaAppkey;


$o = new SaeTOAuthV2($weibo_appkey, $weibo_appsecret);


// code换算token
if (isset($_REQUEST['code'])) {
	$keys = array();
	$keys['code'] = $_REQUEST['code'];
	$keys['redirect_uri'] = logins_Event_GetURL('sinaCallback');
	$token = $o->getAccessToken('code', $keys);
} else {
	echo '系统异常 1';
	exit;
}
if ($token) {
	$_SESSION['token'] = $token;
	setcookie('weibojs_'.$o->client_id, http_build_query($token));
} else {
	echo '系统异常 2';
	exit;
}
$weibo_token = $token['access_token'];
$weibo_uid = $token['uid'];

$wbc = new SaeTClientV2($weibo_appkey, $weibo_appsecret, $weibo_token);

// 第一步 查询绑定状态
$status = logins_sina_Event_GetThirdInfo($weibo_uid);
// 已绑定
if ($status) {
    // 执行第三方登录
    logins_sina_Event_ThirdLogin($weibo_uid, $weibo_token, $wbc);
} else {
    // 未绑定 再判断是否登录 如果登录就直接绑定
    if ($zbp->user->ID > 0) {
        // 执行绑定方法
        logins_sina_Event_ThirdBind($weibo_uid, $weibo_token, $wbc);
    } else {
        if (!session_id()) {
            session_start();
        }
		$_SESSION['weibo_token'] = $weibo_token; // 用户识别
		$_SESSION['weibo_uid'] = $weibo_uid; // 用户ID
        Redirect(logins_Event_GetURL('sinaBind'));
    }
}

// 方法执行完毕后 回到对应页面
$sourceUrl = GetVars('sourceUrl', 'COOKIE');
if (empty($sourceUrl)) {
    $sourceUrl = $zbp->host;
}
Redirect($sourceUrl);
