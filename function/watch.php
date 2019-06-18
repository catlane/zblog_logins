<?php
/**
 * 监听路由
 */
function logins_Watch($url) {
    global $zbp;
  
    $status = strripos($url, '/logins');
    if ($status == -1) {
        return false;
    }
    // 匹配路由
    $regexp = "/\/logins\/([a-z0-9\-\_]*)/";
    $routes = array();
    preg_match_all($regexp, $url, $routes);

    $type = null;
    if (isset($routes[1]) && count($routes[1]) > 0) {
        $type = $routes[1][0];
    }

    $status = logins_WatchHandler($type);

    if (!$status) return false;

    // 阻断后面内容
    $GLOBALS['hooks']['Filter_Plugin_ViewAuto_Begin']['logins_Watch'] = 'return';
}

/**
 * 监听cmd接口
 */
function logins_WatchCmd() {



    global $zbp;
    $action = GetVars('act','GET');
    if ($action != "logins") {
        return false;
    }

    $type = GetVars('type','GET');

    logins_WatchHandler($type);
}

/**
 * 处理相关事件
 */
function logins_WatchHandler($type) {
    global $zbp;
    switch ($type) {
        /**
         * qq登录
         */
        case 'login':
            include ZBP_PATH . 'zb_users/plugin/logins/libs/qq_connect/index.php';
            return true;
        case 'callback':
        
            include ZBP_PATH . 'zb_users/plugin/logins/libs/qq_connect/callback.php';
            return true;
        case 'bind':
            if ($zbp->Config('logins')->qqActive == '1') {
                include ZBP_PATH . 'zb_users/plugin/logins/page/bind.php';
            } else {
                return false;
            }
            echo "<!--本插件由猫巷提供，https://www.lovyou.top/-->\r\n";
            return true;
        case 'bind_account':
            logins_Event_ThirdBindLogin();
            return true;
        case 'create_account':
            logins_Event_ThirdBindCreate();
            return true;
        case 'manage':
            logins_Event_ManageUser();
            return true;
        /**
         * sina登录
         */
        case 'sina_login':
            include ZBP_PATH . 'zb_users/plugin/logins/libs/weibo_connect/index.php';
            return true;
        case 'sina_callback':
            include ZBP_PATH . 'zb_users/plugin/logins/libs/weibo_connect/callback.php';
            return true;
        case 'sina_bind':
            if ($zbp->Config('logins')->sinaActive == '1') {
                include ZBP_PATH . 'zb_users/plugin/logins/page/sinaBind.php';
            } else {
                return false;
            }
            echo "<!--本插件由猫巷提供，https://www.lovyou.top/-->\r\n";
            return true;
        case 'sina_bind_account':
            logins_sina_Event_ThirdBindLogin();
            return true;
        case 'sina_create_account':
            logins_sina_Event_ThirdBindCreate();
            return true;
        /**
         * gitHub登录
         */
        case 'git_login':
            include ZBP_PATH . 'zb_users/plugin/logins/libs/git/index.php';
            return true;
        case 'git_callback':
            include ZBP_PATH . 'zb_users/plugin/logins/libs/git/callback.php';
            return true;
        case 'git_bind':
            if ($zbp->Config('logins')->gitActive == '1') {
                include ZBP_PATH . 'zb_users/plugin/logins/page/gitBind.php';
            } else {
                return false;
            }
            echo "<!--本插件由猫巷提供，https://www.lovyou.top/-->\r\n";
            return true;
        case 'git_bind_account':
            logins_git_Event_ThirdBindLogin();
            return true;
        case 'git_create_account':
            logins_git_Event_ThirdBindCreate();
            return true;
    }
    return false;
}

/**
 * 处理用户头像输出
 */
function logins_WatchAvatar($member) {
    global $zbp;
    $s = $zbp->usersdir . 'avatar/' . $member->ID . '.png';
    if (is_readable($s)) {
        return $zbp->host . 'zb_users/avatar/' . $member->ID . '.png';
    } else if ($member->Metas->logins_avatar_qq) {
        return $member->Metas->logins_avatar_qq;
    }else if ($member->Metas->logins_avatar_sina){
        return $member->Metas->logins_avatar_sina;
    }if ($member->Metas->logins_avatar_git){
        return $member->Metas->logins_avatar_git;
    }
}
