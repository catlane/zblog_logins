<?php
/**
 * Created by PhpStorm.
 * User: 猫巷
 * Email:catlane@foxmail.com
 * Date: 18-7-9
 * Time: 下午2:58
 */

/**
 * 查询是否绑定
 */
function logins_git_Event_GetThirdInfo($openid) {
    global $zbp;

    if ($zbp->Config('logins')->gitActive != "1") {
        return false;
    }

    $t = new logins();
    $status = $t->LoadInfoByOpenID($openid, 2);

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
function logins_git_Event_ThirdBindCreate() {
    global $zbp;
    if ($zbp->Config('logins')->gitActive != "1") {
        return false;
    }
    if ($zbp->Config('logins')->git_user_auto_create != "1") {
        return false;
    }
    if (!session_id()) {
        session_start();
    }
    $access_token = $_SESSION['git_token']; // 用户识别
    $openid = $_SESSION['git_uid']; // 用户ID
    if (empty($openid) || empty($access_token)) {
        return false;
    }
    // 生成唯一Name
    $md5ID = md5($openid.time());
    $md5ID = substr($md5ID, 8, 16);

    $level = 6;
    if ($zbp->Config('logins')->git_user_reg_level) {
        $level = $zbp->Config('logins')->git_user_reg_level;
    }

    $mem = new Member;
    $mem->Name = "third_git_".$md5ID;
    $mem->Level = $level;
    $mem->IP = GetGuestIP();
    $mem->Guid = GetGuid();
    $mem->PostTime = time();
    $mem->Password = Member::GetPassWordByGuid($access_token, $mem->Guid);
    // 自动同步昵称
    $mem->Metas->logins_git_third_info = "1";
    $mem->Save();

    CountMember($mem, array(null, null, null, null));

    $zbp->user = $mem;

    // 调用系统登录记录
    SetLoginCookie($mem, 0);


    // 执行绑定
    logins_git_Event_ThirdBind($openid, $access_token);

    // 方法执行完毕后 回到对应页面
    $sourceUrl = GetVars('gitSourceUrl', 'COOKIE');
    if (empty($sourceUrl)) {
        $sourceUrl = $zbp->host;
    }
    Redirect($sourceUrl);
}


/**
 * 第三方绑定登录
 */
function logins_git_Event_ThirdBindLogin() {
    global $zbp;
    if ($zbp->Config('logins')->gitActive != "1") {
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
            $access_token = $_SESSION['git_token']; // 用户识别
            $openid = $_SESSION['git_uid']; // 用户ID
            if (empty($openid) || empty($access_token)) {
                $json['code'] = 200101;
                $json['message'] = "绑定失败，授权信息遗失";
            } else {
                // 执行绑定
                logins_git_Event_ThirdBind($openid, $access_token);
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
function logins_git_Event_ThirdBind($openid, $token) {
    global $zbp;
    if ($zbp->Config('logins')->gitActive != "1") {
        return false;
    }

    $t = new logins();
    $t->Type = 2;
    $t->OpenID = $openid;
    $t->Token = $token;
    $t->UID = $zbp->user->ID;
    $t->Save();

    logins_git_Event_ThirdSyncInfoByGit($openid, $token);

    return true;
}
/**
 * logins_Event_ThirdSyncInfoByGit
 * 同步用户的Git信息回来
 */
function logins_git_Event_ThirdSyncInfoByGit($openid, $token) {
    global $zbp;

    if ($zbp->Config('logins')->gitActive != "1") {
        return false;
    }

    $t = new logins();
    $status = $t->LoadInfoByOpenID($openid, 2);
    if (!$status) {
        return false;
    }



    if(!isset($_SESSION['git_avatar']) || !isset($_SESSION['git_login'])){
        return false;
    }


    // 保存资料
    $t->Nickname = $_SESSION['git_login'];
    $t->Avatar = $_SESSION['git_avatar'];

    $t->Save();


    // 确认是否需要同步资料
    $m = new Member();
    $status = $m->LoadInfoByID($t->UID);
    if (!$status) {
        return false;
    }
    $update_status = false;
    // 同步头像 -> Git
    $m->Metas->logins_avatar_git = $t->Avatar;
    // 同步email
    if(isset($_SESSION['git_email'])){
        $m->Email = $_SESSION[ 'git_email' ];
    }
    // 判断用户是否需要同步昵称
    if ($m->Metas->logins_git_third_info == '1') {
        $m->Alias = $t->Nickname;
        $m->Metas->Del('logins_git_third_info');
    }
    $m->Save();
    return true;
}

/**
 * 第三方的登录方法(在已经绑定的情况下)
 */
function logins_git_Event_ThirdLogin($openid, $token, $thirdClass = null) {
    global $zbp;

    if ($zbp->Config('logins')->gitActive != "1") {
        return false;
    }

    $t = new logins();
    $status = $t->LoadInfoByOpenID($openid, 2);
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

    logins_git_Event_ThirdSyncInfoByGit($openid, $token);

    return true;
}
