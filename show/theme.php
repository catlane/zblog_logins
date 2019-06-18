<?php
/**
 * Created by PhpStorm.
 * User: 猫巷
 * Email:catlane@foxmail.com
 * Date: 18-7-11
 * Time: 下午5:43
 */
require '../../../../zb_system/function/c_system_base.php';
require '../../../../zb_system/function/c_system_admin.php';
$zbp->Load();

$theme_id = intval(GetVars( 'theme_id' , 'GET' ));


if(!isset($_GET['theme_id'])){
    echo '非法访问';die;
}
unset( $_GET[ 'theme_id' ] );
$url = './theme' . $theme_id .'.php';
foreach ($_GET as $k => $v){
//    if(count($_GET) > 1){
//
//    }
    $url .= '?' . $k . '=' . $v;
}
Redirect( $url );