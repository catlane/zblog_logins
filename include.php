<?php
include_once __DIR__.'/database/index.php';
include_once __DIR__.'/function/main.php';
#注册插件
RegisterPlugin("logins","ActivePlugin_logins");

$GLOBALS['actions']['logins'] = 6;
function ActivePlugin_logins() {
    Add_Filter_Plugin('Filter_Plugin_Login_Header','login_header');
    Add_Filter_Plugin('Filter_Plugin_ViewAuto_Begin','logins_Watch');
    Add_Filter_Plugin('Filter_Plugin_Cmd_Begin','logins_WatchCmd');//监听cmd路由事件
    Add_Filter_Plugin("Filter_Plugin_Mebmer_Avatar","logins_WatchAvatar");
    Add_Filter_Plugin('Filter_Plugin_Zbp_MakeTemplatetags', "theme2");
//    Add_Filter_Plugin('Filter_Plugin_Html_Js_Add', 'logins_Event_FrontOutput');
}








function login_header(){
    global $zbp;
    if($zbp->Config( 'logins' )->theme_id === null || $zbp->Config( 'logins' )->theme0_bg === null){
        $zbp->Config( 'logins' )->theme_id =  0;
        $zbp->Config( 'logins' )->theme0_bg =  0;

    }
    $loginHtml = selectTheme($zbp->Config( 'logins' )->theme_id);

    if($zbp->Config('logins')->allActive){
        echo $loginHtml;
        echo logins_Event_FrontOutput();
    }
}

function selectTheme($id){
    switch ($id){
        case 0:
            return theme0();
            break;
        case 1:
            return theme1();
            break;
        case 2:
            return theme1();
            break;
    }
}

/**
 * 主题1
 * @return string
 */
function theme0(){
    global $zbp;
    if($zbp->Config('logins')->qqActive || $zbp->Config('logins')->qqActive || $zbp->Config('logins')->gitActive){
        $sina = '第三方登录咯';
        $tip = '其它登录方式';
    }else{
        $sina = '联系站长注册';
        $tip = '';
    }


    $otherLogin = '';

    $count = intval($zbp->Config( 'logins' )->qqActive) + intval($zbp->Config( 'logins' )->sinaActive) + intval($zbp->Config( 'logins' )->gitActive) ;
//利用第三方登录个数计算每个li宽
    if($count){
        $otherCount = intval(100 / $count );
        if($zbp->Config('logins')->qqActive){
            $qqLoginUrl = logins_Event_GetURL('login');
            $otherLogin .= <<<eof
<li style="width:{$otherCount}%;">
    <a href="{$qqLoginUrl}">
        <img src="{$zbp->host}zb_users/plugin/logins/resources/images/qq1.png" alt="qq登录">
    </a>
</li>
eof;
        }

        if($zbp->Config('logins')->sinaActive){
            $sinaLoginUrl = logins_Event_GetURL('sinaLogin');
            $otherLogin .= <<<eof
<li style="width:{$otherCount}%;">
    <a href="{$sinaLoginUrl}">
        <img src="{$zbp->host}zb_users/plugin/logins/resources/images/sina.png" alt="微博登录">
    </a>
</li>
eof;
        }

        if($zbp->Config('logins')->gitActive){
            $gitLoginUrl = logins_Event_GetURL('gitLogin');
            $otherLogin .= <<<eof
<li style="width:{$otherCount}%;">
    <a href="{$gitLoginUrl}">
        <img src="{$zbp->host}zb_users/plugin/logins/resources/images/git.png" alt="gitHub登录">
    </a>
</li>
eof;
        }


    }else{
        $otherLogin = '';
    }


    /**
     * 这里判断背景
     */
    switch ($zbp->Config('logins')->theme0_bg){
        case 0:
            $loginHtml = '';
            break;
        case 1:
            $loginHtml = <<<eof
            <script src="{$zbp->host}zb_users/plugin/logins/resources/js/love_canvas_bg.js"></script>
<canvas id="love" style="top:0px;position: absolute;z-index: -1;text-align: center;" width="1680" height="341"></canvas>
eof;
;
            break;
        case 2:
            $loginHtml = <<<eof
            <script src="{$zbp->host}zb_users/plugin/logins/resources/js/haike_canvas_bg.js"></script>
<canvas id="haike" style="top:0px;position: absolute;z-index: -1;text-align: center;" width="1680" height="341"></canvas>
eof;
        break;
    }
    $loginHtml .= <<<eof
    
    
    
<div id="loginalert" style=" display: block;">

    <!--登录-->
    <div class="pd20 loginpd logins" style="display: block;">


        <div class="loginwrap">

            <div class="loginh">

                <div class="fl">

                    会员登录

                </div>

                <div class="fr">

                    还没有账号<a id="sigup_now" href="javascript:;" onclick="return false;">{$sina}</a>

                </div>

                <div class="clear">

                </div>

            </div>

            <h3><span class="login_warning">用户名或密码错误</span>

                <div class="clear">

                </div>

            </h3>

            <div class="clear">

            </div>

            <form action="cmd.php?act=verify" method="post" id="login_form">

                <div class="logininput">

                    <input id="edtUserName" required type="text" class="loginusername" placeholder="邮箱/用户名" ploc="">

                    <input type="password" required id="edtPassWord" placeholder="密码">
                    
                </div>

                         

                <div class="loginbtn">

                    <div class="loginsubmit fl">

                        <input type="submit" name="btnPost" value="登录">

                        <div class="loginsubmiting">

                            <div class="loginsubmiting_inner">

                            </div>

                        </div>

                    </div>

                    <div class="logcheckbox fl">
                        <input id="bcdl" type="checkbox" name="chkRemember">
                        <label for="bcdl">保持登录</label>
                    </div>

                    <div class="fr"><a href="javascript:;" class="forget_pwd">忘记密码?</a>

                    </div>

                    <div class="clear">

                    </div>

                </div>
                    <input type="hidden" name="savedate" value="30">
                    <input type="hidden" name="username" value="">
                    <input type="hidden" name="password" value="">
            </form>

        </div>

    </div>

    <!--第三方登录-->
    <div class="thirdlogin">

        <div class="pd50">

            <h4>{$tip}</h4>

            <ul>

               {$otherLogin}

                <div class="clear">

                </div>

            </ul>

            <div class="clear">

            </div>

        </div>

    </div>

</div>
eof;
    $loginHtml .=  '<link rel="stylesheet" type="text/css" href="'. $zbp->host .'zb_users/plugin/logins/resources/css/style2.css"/>' . "\r\n";
    $loginHtml .= '<script src="'. $zbp->host .'zb_users/plugin/logins/resources/js/md5.js"/> </script>' . "\r\n";
    $loginHtml .= '<script src="'. $zbp->host .'zb_users/plugin/logins/resources/js/logins.js"/> </script>' . "\r\n";
    return $loginHtml;
}

/**
 * 主题2
 */
function theme1(){
    global $zbp;

    $otherLogin = '';

    $count = intval($zbp->Config( 'logins' )->qqActive) + intval($zbp->Config( 'logins' )->sinaActive) + intval($zbp->Config( 'logins' )->gitActive) ;
//利用第三方登录个数计算每个li宽
    if($count){
        $otherCount = intval(100 / $count );
        if($zbp->Config('logins')->qqActive){
            $qqLoginUrl = logins_Event_GetURL('login');
            $otherLogin .= <<<eof
            <a href="{$qqLoginUrl}" style="width: {$otherCount}%">
                <img src="{$zbp->host}/zb_users/plugin/logins/resources/theme1/icon/qq.png" alt="qq登录">
            </a>
eof;
        }

        if($zbp->Config('logins')->sinaActive){
            $sinaLoginUrl = logins_Event_GetURL('sinaLogin');
            $otherLogin .= <<<eof
            <a href="{$sinaLoginUrl}" style="width: {$otherCount}%;">
                <img src="/zb_users/plugin/logins/resources/theme1/icon/sina.png" alt="微博登录">
            </a>
eof;
        }

        if($zbp->Config('logins')->gitActive){
            $gitLoginUrl = logins_Event_GetURL('gitLogin');
            $otherLogin .= <<<eof
            <a href="{$gitLoginUrl}" style="width: {$otherCount}%;">
                <img src="/zb_users/plugin/logins/resources/theme1/icon/git.png" alt="gitHub登录">
            </a>
eof;
        }


    }else{
        $otherLogin = '';
    }

    $loginHtml = '';
//    }
    $loginHtml .= <<<eof
    
    
    
<div class="page-container">

    <h1>{$zbp->name}-登录</h1>

    <form action="cmd.php?act=verify" method="post">

        <input id="edtUserName" type="text" class="username" placeholder="用户名" required>

        <input id="edtPassWord" type="password" class="password" placeholder="密码" required>

        <input type="checkbox" id="bcdl" name="chkRemember"><label for="bcdl">保持登录</label>
        <span id="forget"><a href="javascript:;">找回密码</a></span>
        <button type="submit">登录</button>
        <input type="hidden" name="savedate" value="30">
        <input type="hidden" name="username" value="">
        <input type="hidden" name="password" value="">

        <div class="error"><span>+</span></div>

    </form>
eof;


    //第三方登录
    if($zbp->Config('logins')->qqActive || $zbp->Config('logins')->qqActive || $zbp->Config('logins')->gitActive) {
        $loginHtml .= "<div class='connect'><p>第三方登录:</p>{$otherLogin}<p></p></div>";
    }else{
        $loginHtml .= "<div class='connect'><p>暂不提供注册</p><p></p></div>";
    }

    //引入css
    $loginHtml .= <<<eof
    </div>
    <script src="{$zbp->host}zb_users/plugin/logins/resources/theme1/js/supersized.3.2.7.min.js"></script>
    <script src="{$zbp->host}zb_users/plugin/logins/resources/theme1/js/supersized-init.js"></script>
    <script src="{$zbp->host}zb_users/plugin/logins/resources/theme1/js/scripts.js"></script>
    <script src="{$zbp->host}zb_users/plugin/logins/resources/js/md5.js"></script>
    <link rel="stylesheet" href="{$zbp->host}zb_users/plugin/logins/resources/theme1/css/reset.css">
    <link rel="stylesheet" href="{$zbp->host}zb_users/plugin/logins/resources/theme1/css/supersized.css">
    <link rel="stylesheet" href="{$zbp->host}zb_users/plugin/logins/resources/theme1/css/style.css">
eof;

    return $loginHtml;
}


/**
 * 主题3
 */
function theme2(){
    global $zbp;
    $otherLogin = '';

    $count = intval($zbp->Config( 'logins' )->qqActive) + intval($zbp->Config( 'logins' )->sinaActive) + intval($zbp->Config( 'logins' )->gitActive) ;
//利用第三方登录个数计算每个li宽
    if($count){
        if($zbp->Config('logins')->qqActive){
            $qqLoginUrl = logins_Event_GetURL('login');
            $otherLogin .= '<a href="' . $qqLoginUrl . '">QQ登录</a>';
        }

        if($zbp->Config('logins')->sinaActive){
            $sinaLoginUrl = logins_Event_GetURL('sinaLogin');
            $otherLogin .= '<a href="' . $sinaLoginUrl . '">微博登录</a>';
        }

        if($zbp->Config('logins')->gitActive){
            $gitLoginUrl = logins_Event_GetURL('gitLogin');
            $otherLogin .= '<a href="' . $gitLoginUrl . '">gitHub登录</a>';
        }


    }else{
        $otherLogin = '';
    }


    $html = '<script src="/zb_users/plugin/logins/resources/js/alertLogin.js"></script>';
    $html .= <<<eof
<div class="wrap login_wrap"></div>
<div class="login_box">
    <div class="login_title">
        登录
        <span class="close"></span>
    </div>
    <form action="javascript:;" method="post" id="login_form">
        
        <div class="form_text_ipt">
            <input id="edtUserName" type="text" placeholder="手机号/邮箱">
        </div>
        <div class="form_text_ipt">
            <input id="edtPassWord" type="password" placeholder="密码">
        </div>
        <div class="ececk_warning"><span></span></div>
        <div class="form_check_ipt">
            <div class="left check_left">
                <label><input name="checkbox" type="checkbox"> 保持登录</label>
            </div>
            <div class="right check_right">
                <a href="#">忘记密码</a>
            </div>
        </div>
        <div class="form_btn">
            <button type="submit">登录</button>
        </div>
        <input type="hidden" name="savedate" value="30">
        <input type="hidden" name="username">
        <input type="hidden" name="password" >
    </form>
    <div class="other_login">
        <div class="left other_left">
            <span>其它登录方式</span>
        </div>
        <div class="right other_right">
            {$otherLogin}
        </div>
    </div>
</div>
		
eof;

    if($zbp->Config('logins')->theme_id == 2){
        $zbp->footer .= $html;
    }


}

function InstallPlugin_logins() {
    logins_CreateTable();
}
function UninstallPlugin_logins() {}





/**
 * 自定义函数
 */
function logins_SubMenu($id){
    $arySubMenu = array(
        0 => array('应用设置', 'main', 'left', false),
        1 => array('主题(背景)设置', 'theme', 'left', false),
        2 => array('QQ登录设置', 'qqConnect', 'left', false),
        3 => array('微博登录设置', 'sinaConnect', 'left', false),
        4 => array('github登录设置', 'gitConnect', 'left', false),
        5 => array('用户列表', 'userList', 'left', false),
    );

    foreach($arySubMenu as $k => $v){
        echo '<a href="./'.$v[1].'.php" '.($v[3]==true?'target="_blank"':'').'><span class="m-'.$v[2].' '.($id==$k?'m-now':'').'">'.$v[0].'</span></a>';
    }
}

/**
 * 返回时间天数
 */
function logins_AgoTime($ptime) {
    // $ptime = strtotime($ptime);
    $etime = time() - $ptime;
    if($etime < 10) return '刚刚';
    $nowYear = date('Y');
    $setYear = date('Y',$ptime);
    if ($nowYear != $setYear) {
        return date('Y/m/d H:i', $ptime);
    }
    $nowMonth = date('m');
    $setMonth = date('m',$ptime);
    if ($nowMonth != $setMonth) {
        return date('m/d H:i', $ptime);
    }
    $interval = array (
        24 * 60 * 60            =>  '天前',
        60 * 60                 =>  '小时前',
        60                      =>  '分钟前',
        1                       =>  '秒前'
    );
    foreach ($interval as $secs => $str) {
        $d = $etime / $secs;
        if ($d >= 1) {
            $r = round($d);
            return $r . $str;
        }
    };
}

/**
 * 生成主题select表单
 * @param $default
 * @return null|string
 */
function OutputOptionItemsOfTheme($default)
{
    $themes = [
        0 => '主题1(默认主题)' ,
        1 => '主题2' ,
        2 => '主题3(弹出登录,只适应Web)',
    ];
    $s = '';
    foreach ($themes as $key => $value) {
        $s .= '<option value="' . $key . '" ' . ($default == $key ? 'selected="selected"' : '') . ' >' . $value . '</option>';
    }

    return $s;
}

/**
 * 生成主题背景div(主题1)
 * @param $default
 * @return null|string
 */
function divOfThemeBg()
{
    global $zbp;

    $themes = [
        0 => '背景1（淡灰色）' ,
        1 => '背景2（love心）' ,
        2 => '背景3（黑客帝国）'
    ];
    $s = '';
    $theme_id = $zbp->Config('logins')->theme_id;
    $theme_bg = 'theme' . $theme_id .'_bg';
    $theme_bg = $zbp->Config( 'logins' )->$theme_bg;
    foreach ($themes as $key => $value) {
//        $s .= '<option value="' . $key . '" ' . ($default == $key ? 'selected="selected"' : '') . ' >' . $value . '</option>';
        $s .= '<div theme0_bg="' . $key .'" ' . ($theme_bg == $key ? ' style="border:2px solid #c000ff; "' : '') . ' class="theme-img" onclick="a(' . $theme_id .',' . $key . ',this)"><img src="' . $zbp->host .'zb_users/plugin/logins/resources/themes/theme0/' . $key . '_bg.png" alt="主题' . $key . '--' . $value . '"></div>';

    }
        $s .= '<input type="text" name="theme0_bg" value="' . $theme_bg . '" hidden>';
    return $s;
}