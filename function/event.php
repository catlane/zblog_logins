<?php
/**
 * Created by PhpStorm.
 * User: 猫巷
 * Email:catlane@foxmail.com
 * Date: 18-7-9
 * Time: 下午2:58
 */
/**
 * 获取链接地址
 */
function logins_Event_GetURL($type) {
    global $zbp;

    if ($zbp->option['ZC_STATIC_MODE'] == 'REWRITE') {
        $third_url = $zbp->host . 'logins/';
    } else {
        $third_url = $zbp->host . 'zb_system/cmd.php?act=logins&type=';
        if ($type == "callback") {
            return $zbp->host . 'zb_users/plugin/logins/callback.php';
        }
    }



    switch ($type) {
        case 'login'://登录
            $third_url .= 'login';
            break;
        case 'callback'://回调
            $third_url .= 'callback';
            break;
        case 'bind'://绑定的页面
            $third_url .= 'bind';
            break;
        case 'bind-account':
            $third_url .= 'bind_account';
            break;
        case 'create-account'://生成账户
            $third_url .= 'create_account';
            break;
        case 'manage'://管理
            $third_url .= 'manage';
            break;

        /**
         * 微博登录
         */
        case 'sinaLogin':
            $third_url .= 'sina_login';
            break;
        case 'sinaCallback':
            $third_url .= 'sina_callback';
            break;
        case 'sinaBind':
            $third_url .= 'sina_bind';
            break;
        case 'sinaBindAccount':
            $third_url .= 'sina_bind_account';
            break;
        case 'sinaCreateAccount':
            $third_url .= 'sina_create_account';
            break;
        case 'sinaManage':
            $third_url .= 'sina_manage';
            break;

        /**
         * gitHub登录
         */
        case 'gitLogin':
            $third_url .= 'git_login';
            break;
        case 'gitCallback':
            $third_url .= 'git_callback';
            break;
        case 'gitBind':
            $third_url .= 'git_bind';
            break;
        case 'gitBindAccount':
            $third_url .= 'git_bind_account';
            break;
        case 'gitCreateAccount':
            $third_url .= 'git_create_account';
            break;
        case 'gitManage':
            $third_url .= 'git_manage';
            break;

    }

    return $third_url;
}

/**
 * 查询是否绑定
 */
function logins_Event_GetThirdInfo($openid) {
    global $zbp;

    if ($zbp->Config('logins')->qqActive != "1") {
        return false;
    }

    $t = new logins();
    $status = $t->LoadInfoByOpenID($openid, 0);

    if (!$status) {
        return false;
    }

    $m = new Member;
    $status = $m->LoadInfoByID($t->UID);
    if (!$status) {
        return false;
    }

    return true;
}

/**
 * 绑定自动生成的账户
 */
function logins_Event_ThirdBindCreate() {
    global $zbp;
    if ($zbp->Config('logins')->qqActive != "1") {
        return false;
    }
    if ($zbp->Config('logins')->qq_user_auto_create != "1") {
        return false;
    }
    if (!session_id()) {
        session_start();
    }
    $access_token = $_SESSION['qq_token']; // 用户识别
    $openid = $_SESSION['qq_openid']; // 用户ID
    if (empty($openid) || empty($access_token)) {
        return false;
    }
    // 生成唯一Name
    $md5ID = md5($openid.time());
    $md5ID = substr($md5ID, 8, 16);

    $level = 6;
    if ($zbp->Config('logins')->qq_user_reg_level) {
        $level = $zbp->Config('logins')->qq_user_reg_level;
    }

    $mem = new Member;
    $mem->Name = "third_qq_".$md5ID;
    $mem->Level = $level;
    $mem->IP = GetGuestIP();
    $mem->Guid = GetGuid();
    $mem->PostTime = time();
    $mem->Password = Member::GetPassWordByGuid($access_token, $mem->Guid);
    // 自动同步昵称
    $mem->Metas->logins_third_info = "1";
    $mem->Save();

    CountMember($mem, array(null, null, null, null));

    $zbp->user = $mem;

    // 调用系统登录记录
    SetLoginCookie($mem, 0);

    // 执行绑定
    logins_Event_ThirdBind($openid, $access_token);

    // 方法执行完毕后 回到对应页面
    $sourceUrl = GetVars('sourceUrl', 'COOKIE');
    if (empty($sourceUrl)) {
        $sourceUrl = $zbp->host;
    }
    Redirect($sourceUrl);
}


/**
 * 第三方绑定登录
 */
function logins_Event_ThirdBindLogin() {
    global $zbp;
    if ($zbp->Config('logins')->qqActive != "1") {
        return false;
    }
    $json = array();
    $username = trim(GetVars("username", "POST"));
    $password = trim(GetVars("password", "POST"));
    if ($zbp->Verify_MD5(GetVars('username', 'POST'), GetVars('password', 'POST'), $m)) {
        $zbp->user = $m;
        if ($zbp->user->Status != 0) {
            $json['code'] = 200100;
            $json['message'] = "已被限制登录";
        } else {

            // 调用系统登录记录
            SetLoginCookie($m, 0);

            if (!session_id()) {
                session_start();
            }
            $access_token = $_SESSION['qq_token']; // 用户识别
            $openid = $_SESSION['qq_openid']; // 用户ID
            if (empty($openid) || empty($access_token)) {
                $json['code'] = 200101;
                $json['message'] = "绑定失败，授权信息遗失";
            } else {
                // 执行绑定
                logins_Event_ThirdBind($openid, $access_token);
                $json['code'] = 100000;
                $json['message'] = "绑定成功";
            }
        }
    } else {
        $json['code'] = 200000;
        $json['message'] = "用户名或密码错误";
    }

    echo json_encode($json);
    exit;
}
/**
 * 社交账户绑定(执行绑定)
 */
function logins_Event_ThirdBind($openid, $token) {
    global $zbp;
    if ($zbp->Config('logins')->qqActive != "1") {
        return false;
    }

    $t = new logins();
    $t->Type = 0;
    $t->OpenID = $openid;
    $t->Token = $token;
    $t->UID = $zbp->user->ID;
    $t->Save();

    logins_Event_ThirdSyncInfoByQQ($openid, $token);

    return true;
}
/**
 * logins_Event_ThirdSyncInfoByQQ
 * 同步用户的QQ信息回来
 */
function logins_Event_ThirdSyncInfoByQQ($openid, $token) {
    global $zbp;

    if ($zbp->Config('logins')->qqActive != "1") {
        return false;
    }

    $t = new logins();
    $status = $t->LoadInfoByOpenID($openid, 0);
    if (!$status) {
        return false;
    }

    $result = file_get_contents('https://graph.qq.com/user/get_user_info?access_token='.$token.'&oauth_consumer_key='.$zbp->Config('logins')->qqAppid.'&openid='.$openid);
    $result = json_decode($result);




    if ($result->ret != '0') {
        return false;
    }


    // 保存资料
    $t->Nickname = $result->nickname;
    $Avatar = empty($result->figureurl_qq_2)?$result->figureurl_2:$result->figureurl_qq_2;
    //将其换为https协议的
    $Avatars = str_replace('http://','https://',$Avatar);
    if(check_file_exists($Avatars)){
        $t->Avatar = $Avatars;
    }else{
        $t->Avatar = $Avatar;
    }

    $t->Save();


    // 确认是否需要同步资料
    $m = new Member();
    $status = $m->LoadInfoByID($t->UID);
    if (!$status) {
        return false;
    }
    $update_status = false;
    // 同步头像 -> QQ
    $m->Metas->logins_avatar_qq = $t->Avatar;

    // 判断用户是否需要同步昵称
    if ($m->Metas->logins_third_info == '1') {
        $m->Alias = $t->Nickname;
        $m->Metas->Del('logins_third_info');
    }
    $m->Save();
    return true;
}


/**
 * 第三方的登录方法(在已经绑定的情况下)
 */
function logins_Event_ThirdLogin($openid, $token, $thirdClass = null) {
    global $zbp;

    if ($zbp->Config('logins')->qqActive != "1") {
        return false;
    }

    $t = new logins();
    $status = $t->LoadInfoByOpenID($openid, 0);
    if (!$status) {
        echo 'Login Error 1, 登录异常';
        exit;
    }

    $m = new Member;
    $status = $m->LoadInfoByID($t->UID);
    if (!$status) {
        echo 'Login Error 2, 登录异常';
        exit;
    }

    // 将用户信息载入$zbp中
    $zbp->user = $m;

    // 调用系统登录cookie
    SetLoginCookie($m, 0);

    // 挂载上接口 会传入third
    if(isset($GLOBALS['hooks']['Filter_Plugin_VerifyLogin_Succeed'])){
        foreach ($GLOBALS['hooks']['Filter_Plugin_VerifyLogin_Succeed'] as $fpname => &$fpsignal) {
            $fpname('third');
        }
    }

    logins_Event_ThirdSyncInfoByQQ($openid, $token);

    return true;
}

/**
* 前台插入cookie来源
*/
function logins_Event_FrontOutput() {
    global $zbp;
    if ($zbp->Config('logins')->qq_source_switch != "1") {
        return null;
    }
    echo "\r\n".'<script>!function() {$(document).on("click", ".os-qqconnect-link", function() { zbp.cookie.set("sourceUrl", window.location.href); })}();</script>'."\r\n";
}


/**
 * 显示绑定用户列表
 */
function logins_Event_GetUserList() {
    global $zbp;
    $page = GetVars("page", "GET");
    $page = (int)$page>0?(int)$page:1;
    $pagebar = new Pagebar('{%host%}zb_users/plugin/logins/userList.php?page={%page%}', false);
    $pagebar->PageCount = 20;
    $pagebar->PageNow = $page;
    $pagebar->PageBarCount = $zbp->pagebarcount;
    $pagebar->UrlRule->Rules['{%page%}'] = $page;

    $w = array();
//    $w = array("=", "third_Type", "0");

    $limit = array(($pagebar->PageNow - 1) * $pagebar->PageCount, $pagebar->PageCount);
    $option = array('pagebar' => $pagebar);

    $sql = $zbp->db->sql->Select(
        $zbp->table['logins'],
        array("*"),
        $w,
        null,
        $limit,
        $option
    );
    $result = $zbp->GetListType('logins', $sql);

    return array(
        "list"     => $result,
        "pagebar"  => $pagebar,
    );
}


/**
 * 管理操作
 */
function logins_Event_ManageUser() {
    global $zbp;
    $json = array();

    if ($zbp->user->Level > 1) {
        $json['code'] = 200200;
        $json['message'] = "您的权限不足";
        echo json_encode($json);
        exit;
    }

    $id = GetVars('id', "POST");
    $type = GetVars('type', "POST");
    $t = new logins();
    $t->LoadInfoByID($id);

    if ($type == "unbind") {
        $t->Del();
        $json['code'] = 100000;
        $json['message'] = "操作成功";
    } elseif ($type == "lock") {

        if($t->User->Level == 1){
            $json['code'] = 200200;
            $json['message'] = "最高管理员不允许限制其登录";
            echo json_encode($json);
            exit;
        }
        $t->User->Status = $t->User->Status==1?0:1;
        $t->User->Save();
        $json['code'] = 100000;
        $json['message'] = "操作成功";
        $json['result'] = $t->User->Status;
    }
    echo json_encode($json);
    exit;
}

/**
 * 检测文件是否存在
 * @param $url
 * @return bool
 */
function check_file_exists($url)
{
    $curl = curl_init($url);
    // 不取回数据
    curl_setopt($curl, CURLOPT_NOBODY, true);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET'); //不加这个会返回403，加了才返回正确的200，原因不明
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    // 发送请求
    $result = curl_exec($curl);
    $found = false;
    // 如果请求没有发送失败
    if ($result !== false)
    {
        // 再检查http响应码是否为200
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($statusCode == 200)
        {
            $found = true;
        }
    }
    curl_close($curl);
    return $found;
}


//function showTheme(){
//    global $zbp;
//    $theme_id = GetVars( 'theme_id' , 'GET' );
//    $host =  ;
//    return $host;
//}

