<?php
/**
 * Created by PhpStorm.
 * User: 猫巷
 * Email:catlane@foxmail.com
 * Date: 18-7-9
 * Time: 下午3:08
 */
/**
 * 硬修改配置
 * API/class/Recorder.class.php
 *
 * 请求这个文件激活的是登录
 */
if (empty($zbp)) {
    echo '管理员已经关闭了登录';
    exit;
} else if ($zbp->Config('logins')->qqActive != '1') {
    echo '管理员已经关闭了QQ互联登录';
    exit;
}



// 开始接入SDK
require "API/qqConnectAPI.php";
$qc = new QC();
$qc->qq_login();
