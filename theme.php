<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load();
$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin('logins')) {$zbp->ShowError(48);die();}

$blogtitle='第三方登录';
require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';


if($_POST && count($_POST) > 0){
    foreach ($_POST as $k => $v){
        $zbp->Config('logins')->$k = $v;
    }
    $zbp->SaveConfig('logins');
    $zbp->SetHint('good', "保存成功");
    Redirect("./theme.php");
}
?>
<script src="<?php echo $zbp->host ?>zb_users/plugin/logins/resources/layer/layer.js"></script>
<style>
    .theme-img{
        width: 100px;
        -webkit-border-radius: 13px;
        -moz-border-radius: 13px;
        border-radius: 13px;
        border:2px solid #ff000000;
        float: left;
        margin: 5px;
        overflow: hidden;
        cursor: pointer;
    }
    .theme-img img{
        width: 100%;
        margin-bottom: -8px;
    }
</style>
<div id="divMain">
    <div class="divHeader"><?php echo $blogtitle;?></div>
    <div class="SubMenu"><?php logins_SubMenu(1);?></div>
    <div id="divMain2">
        <form action="" method="post">
            <table border="1" class="tableFull tableBorder tableBorder-thcenter" style="max-width: 1000px">
                <thead>
                <tr>
                    <th width="200px">配置名称</th>
                    <th>配置内容</th>
                </tr>
                </thead>
                <tbody>
<!--                <tr>-->
<!--                    <td>启用登录开关</td>-->
<!--                    <td>-->
<!--                        <input name="allActive" type="text" class="checkbox" style="display:none;" value="--><?php //echo $zbp->Config('logins')->allActive; ?><!--" />-->
<!--                    </td>-->
<!--                </tr>-->
                <tr>
                    <td>选择主题样式</td>
                    <td>
                        <select name="theme_id" class="edit" id="themes">
                            <?php
                            $theme_id = $zbp->Config('logins')->theme_id;
                            if (!isset($theme_id)) {
                                $theme_id = 0;
                            }
                            echo OutputOptionItemsOfTheme($theme_id);
                            ?>
                        </select>
                        <a id="showTheme" href="<?php echo $zbp->host; ?>zb_users/plugin/logins/show/theme.php" style="margin-left: 20px;" target="_blank">主题演示(根据下方背景或主题自动演示主题)</a>
                    </td>
                </tr>

                <tr id="theme_id_0" <?php if($zbp->Config('logins')->theme_id != 0){ echo 'style="display:none;"';} ?>  >
                    <td>选择背景</td>
                    <td>
                        <?php
                            echo divOfThemeBg();
                        ?>
                    </td>
                </tr>
                </tbody>
            </table>
            <input type="submit" value="保存配置" style="margin: 0; font-size: 1em;" />
        </form>
        <style>
            .readme {
                max-width: 1000px;
                padding: 10px;
                margin-bottom: 10px;
                background: #f9f9f9;
            }
            .readme h3 {
                font-size: 16px;
                font-weight: normal;
                color: #000;
            }
            .readme ul li {
                margin-bottom: 5px;
                line-height: 30px;
            }
            .readme a {
                color: #333 !important;
                text-decoration: underline;
            }
            .readme code {
                display: inline-block;
                margin: 0 5px;
                padding: 0 8px;
                line-height: 25px;
                font-size: 12px;
                font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
                color: #1a1a1a;
                border-radius: 4px;
                background: #eee;
            }
            .readme code.copy {
                cursor: pointer;
            }
            .readme-item {
                -webkit-display: flex;
                display: flex;
                margin-bottom: 10px;
            }
            .readme-item .name {
                display: block;
                width: 100px;
                height: 24px;
                line-height: 24px;
            }
            .readme-item .preview {
                display: block;
                width: 300px;
            }
            .readme-item .options {
                display: block;
                width: 300px;
                height: 24px;
            }
            .readme-item .code-pre {
                display: none;
            }
            .readme-item .copy-btn {
                display: inline-block;
                width: 64px;
                height: 24px;
                margin: 0;
                margin-left: 10px;
                padding: 0;
                line-height: 24px;
                font-size: 13px;
                color: #fff;
                border: none;
                border-radius: 2px;
                background: #3a6ea5;
                cursor: pointer;
            }
            .readme-item .copy-btn:active,
            .readme-item .copy-btn:focus {
                outline: 0;
            }
            .readme-item .copy-btn:active {
                opacity: .95;
            }
        </style>
        <div class="readme">
            <h3>插件配置说明</h3>
            <ul>
                <li>- 该插件默认修改网站后台登录模块，并且集成第三方登录</li>
                <li>- 第三方登录需要启用才能生效</li>
                <li>- 目前登录主题主要1款，后期会添加更多好看登录主题</li>
            </ul>
        </div>
    </div>
</div>
<script>
    function a(theme_id,bg_id,This) {
        //先让其他的取消选中
        $(This).css('border', '2px solid #c000ff');
        $(This).siblings('div').css('border', '2px solid #ff000000');
        $('input[name="theme0_bg"]').val(bg_id)
    }
    $('form').submit(function () {
        var theme = $('#themes').val();
        switch (theme){
            case '0':
                if(!$('input[name="theme0_bg"]').val()){
                    layer.alert('请选则背景样式')
                    return false;
                }
                break;
        }

        return true;
    })

    /**
     * 修改主题演示链接
     */
    function showThemeUrl(first = null) {
        var url = "<?php echo $zbp->host; ?>zb_users/plugin/logins/show/theme.php";
        var theme_id = parseInt($("#themes").val());

        var bg_id = $('input[name="theme0_bg"]').val();
        switch (theme_id){
            case 0:
                url += '?theme_id=' + theme_id + '&theme0_bg=' + bg_id;
                break;
            case 1:
                url += '?theme_id=' + theme_id;
            case 2:
                url += '?theme_id=' + theme_id;
        }
        $("#showTheme").attr('href', url);
    }
    showThemeUrl();//一打开页面
    $('.theme-img').click(function () {
        showThemeUrl();
    })// 选择下边的时候
    $("#themes").change(function () {
        showThemeUrl();//修改主题演示url
        if($(this).val() == 0){
            $('#theme_id_0').show();
        }
        if($(this).val() == 1){
            $('#theme_id_0').hide();
        }
        if($(this).val() == 2){
            $('#theme_id_0').hide();
        }

    })





    var index = null;
    $('.theme-img').hover(function () {
        var theme0_bg = $(this).attr('theme0_bg');
        var str = '';
        switch (theme0_bg){
            case '0':
                str = '默认背景';
                break;
            case '1':
                str = '爱心背景';
                break;
            case '2':
                str = '黑客帝国';
                break;
        }
        index = layer.tips(str, $(this), {
            tips: [2, '#78BA32']
        });
    },function () {
        layer.close(index);
    })
</script>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>