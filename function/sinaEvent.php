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
function logins_sina_Event_GetThirdInfo($weibo_uid) {
    global $zbp;

    if ($zbp->Config('logins')->sinaActive != "1") {
        return false;
    }

    $t = new logins();
    $status = $t->LoadInfoByOpenID($weibo_uid, 1);

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
function logins_sina_Event_ThirdBindCreate() {
    global $zbp;
    if ($zbp->Config('logins')->sinaActive != "1") {
        return false;
    }
    if ($zbp->Config('logins')->sina_user_auto_create != "1") {
        return false;
    }
    if (!session_id()) {
        session_start();
    }
    $access_token = $_SESSION['weibo_token']; // 用户识别
    $openid = $_SESSION['weibo_uid']; // 用户ID
    if (empty($openid) || empty($access_token)) {
        return false;
    }
    // 生成唯一Name
    $md5ID = md5($openid.time());
    $md5ID = substr($md5ID, 8, 16);

    $level = 6;
    if ($zbp->Config('logins')->sina_user_reg_level) {
        $level = $zbp->Config('logins')->sina_user_reg_level;
    }

    $mem = new Member;
    $mem->Name = "third_weibo_".$md5ID;
    $mem->Level = $level;
    $mem->IP = GetGuestIP();
    $mem->Guid = GetGuid();
    $mem->PostTime = time();
    $mem->Password = Member::GetPassWordByGuid($access_token, $mem->Guid);
    // 自动同步昵称
    $mem->Metas->logins_sina_third_info = "1";
    $mem->Save();

    CountMember($mem, array(null, null, null, null));

    $zbp->user = $mem;

    // 调用系统登录记录
    SetLoginCookie($mem, 0);

    // 执行绑定
    include ZBP_PATH . 'zb_users/plugin/logins/libs/weibo_connect/saetv2.ex.class.php';

    $access_token = $_SESSION['weibo_token']; // 用户识别
    $weibo_appkey = $zbp->Config('logins')->sinaAppid;
    $weibo_appsecret = $zbp->Config('logins')->sinaAppkey;
    $wbc = new SaeTClientV2($weibo_appkey, $weibo_appsecret, $access_token);
    logins_sina_Event_ThirdBind($openid, $access_token, $wbc);

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
function logins_sina_Event_ThirdBindLogin() {
    global $zbp;
    if ($zbp->Config('logins')->sinaActive != "1") {
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
            $access_token = isset($_SESSION['weibo_token']) ? $_SESSION['weibo_token'] : ''; // 用户识别
            $openid = isset($_SESSION['weibo_uid']) ? $_SESSION['weibo_uid'] : ''; // 用户ID
            if (empty($openid) || empty($access_token)) {
                $json['code'] = 200101;
                $json['message'] = "绑定失败，授权信息遗失";
            } else {
                include ZBP_PATH . 'zb_users/plugin/logins/libs/weibo_connect/saetv2.ex.class.php';
                $access_token = $_SESSION['weibo_token']; // 用户识别
                $weibo_appkey = $zbp->Config('logins')->sinaAppid;
                $weibo_appsecret = $zbp->Config('logins')->sinaAppkey;
                $wbc = new SaeTClientV2($weibo_appkey, $weibo_appsecret, $access_token);
                // 执行绑定
                logins_sina_Event_ThirdBind($openid, $access_token,$wbc);
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
function logins_sina_Event_ThirdBind($openid, $token, $wbc) {
    global $zbp;

    if ($zbp->Config('logins')->sinaActive != "1") {
        return false;
    }

    $t = new logins();
    $t->Type = 1;
    $t->OpenID = $openid;
    $t->Token = $token;
    $t->UID = $zbp->user->ID;
    $t->Save();

    logins_sina_Event_ThirdSyncInfoBysina($openid, $token, $wbc);

    return true;
}
/**
 * logins_sina_Event_ThirdSyncInfoBysina
 * 同步用户的sina信息回来
 */
function logins_sina_Event_ThirdSyncInfoBysina($uid, $token, $wbc) {
    global $zbp;

    if ($zbp->Config('logins')->sinaActive != "1") {
        return false;
    }

    $t = new logins();
    $status = $t->LoadInfoByOpenID($uid, 1);
    if (!$status) {
        return false;
    }

    $result = $wbc->oauth->get('https://api.weibo.com/2/users/show.json', array(
        'access_token' => $token,
        'uid' => $uid,
    ));

    if (!empty($result->error_code)) {
        return false;
    }

    // 保存资料
    $t->Nickname = $result['screen_name'];
    $Avatar = empty($result['avatar_hd'])?$result['avatar_large']:$result['avatar_hd'];

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
    // 同步头像 -> Weibo
    $m->Metas->logins_avatar_sina = $t->Avatar;
    // 判断用户是否需要同步昵称
    if ($m->Metas->logins_sina_third_info == '1') {
        $m->Alias = $t->Nickname;
        $m->Metas->Del('logins_sina_third_info');
    }
    $m->Save();
    return true;
}


/**
 * 第三方的登录方法(在已经绑定的情况下)
 */
function logins_sina_Event_ThirdLogin($openid, $token, $thirdClass = null) {
    global $zbp;

    if ($zbp->Config('logins')->sinaActive != "1") {
        return false;
    }

    $t = new logins();
    $status = $t->LoadInfoByOpenID($openid, 1);
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

    // 调用系统登录记录
    SetLoginCookie($m, 0);

    // 挂载上接口 会传入third
    if(isset($GLOBALS['hooks']['Filter_Plugin_VerifyLogin_Succeed'])){
        foreach ($GLOBALS['hooks']['Filter_Plugin_VerifyLogin_Succeed'] as $fpname => &$fpsignal) {
            $fpname('third');
        }
    }

    logins_sina_Event_ThirdSyncInfoBysina($openid, $token, $thirdClass);

    return true;
}

/**
 * 前台插入cookie来源
 */
function logins_sina_Event_FrontOutput() {
    global $zbp;
    if ($zbp->Config('logins')->sina_source_switch != "1") {
        return null;
    }
    echo "\r\n".'<script>!function() {$(document).on("click", ".logins-weibo-connect-link", function() { zbp.cookie.set("sourceUrl", window.location.href); })}();</script>'."\r\n";
}


/**
 * 显示绑定用户列表
 */
function logins_sina_Event_GetUserList() {
    global $zbp;
    $page = GetVars("page", "GET");
    $page = (int)$page>0?(int)$page:1;
    $pagebar = new Pagebar('{%host%}zb_users/plugin/logins/userList.php?page={%page%}', false);
    $pagebar->PageCount = 20;
    $pagebar->PageNow = $page;
    $pagebar->PageBarCount = $zbp->pagebarcount;
    $pagebar->UrlRule->Rules['{%page%}'] = $page;

    $w = array();
    $w = array("=", "third_Type", "0");

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
function logins_sina_Event_ManageUser() {
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

        if($zbp->user->Level == 1){
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


