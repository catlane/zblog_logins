
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
        $zbp->Config( 'logins' )->$k = GetVars( $k , 'post' );
    }
    $zbp->SaveConfig('logins');
    $zbp->SetHint('good', "保存成功");
    Redirect("./sinaConnect.php");
}
?>
<script src="<?php echo $zbp->host ?>zb_users/plugin/logins/resources/js/common.js"></script>
<script src="<?php echo $zbp->host ?>zb_users/plugin/logins/resources/clipboard/clipboard-polyfill.js"></script>
<script src="<?php echo $zbp->host ?>zb_users/plugin/logins/resources/layer/layer.js"></script>
<style>
    .edit-input {
        display: block;
        width: 100%;
        height: 40px;
        line-height: 24px;
        font-size: 14px;
        padding: 8px;
        box-sizing: border-box;
    }
</style>
<div id="divMain">
    <div class="divHeader"><?php echo $blogtitle;?></div>
    <div class="SubMenu"><?php logins_SubMenu(3);?></div>
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
                <tr>
                    <td>启用开关</td>
                    <td>
                        <input name="sinaActive" type="text" class="checkbox" style="display:none;" value="<?php echo $zbp->Config('logins')->sinaActive; ?>" />
                    </td>
                </tr>
                <tr>
                    <td>APP ID</td>
                    <td>
                        <input name="sinaAppid" type="text" class="edit-input" value="<?php echo $zbp->Config('logins')->sinaAppid; ?>" placeholder="请填写微博应用的APPKey" />
                    </td>
                </tr>
                <tr>
                    <td>APP Key</td>
                    <td>
                        <input name="sinaAppkey" type="text" class="edit-input" value="<?php echo $zbp->Config('logins')->sinaAppkey; ?>" placeholder="请填写微博应用的App Secret" />
                    </td>
                </tr>
                <tr>
                    <td>自动生成账号</td>
                    <td>
                        <input name="sina_user_auto_create" type="text" class="checkbox" style="display:none;" value="<?php echo $zbp->Config('logins')->sina_user_auto_create; ?>" />
                    </td>
                </tr>
                <tr>
                    <td>自动注册身份</td>
                    <td>
                        <select name="sina_user_reg_level" class="edit">
                            <?php
                            $level = $zbp->Config('logins')->sina_user_reg_level;
                            if (!isset($level)) {
                                $level = 6;
                            }
                            echo OutputOptionItemsOfMemberLevel($level);
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>跳转至来源页</td>
                    <td>
                        <input name="sina_source_switch" type="text" class="checkbox" style="display:none;" value="<?php echo $zbp->Config('logins')->sina_source_switch; ?>" />
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
                <li>- 如果您没有App Key和App Secret，请前往<a href="http://open.weibo.com/" target="_blank">open.weibo.com</a>申请</li>
                <li>- 您用于应用填写网站回调域的地址是<code class="copy" title="点击复制"><?php echo logins_Event_GetURL('sinaCallback'); ?></code></li>
                <li>- 登录访问地址<code class="copy" title="点击复制"><?php echo logins_Event_GetURL('sinaLogin'); ?></code></li>
                <li style="text-decoration: line-through">- 跳转至来源基于a标签上面的class<code class="copy" title="点击复制">logins-weibo-connect-link</code></li>
                <li style="text-decoration: line-through">- 开发实现跳转至来源，在cookie中写入key为<code>SinaSourceUrl</code>:value为来源地址即可</li>
                <li>- 获取最新的教程文档支持<a href="https://www.lovyou.top/post/106.html" target="_blank">https://www.lovyou.top/post/106.html</a></li>
            </ul>
        </div>
        <div class="readme">
            <h3>调用内容</h3>
            <div class="readme-item">
                <div class="name">示例1：</div>
                <div class="preview">
                    <a href="<?php echo logins_Event_GetURL('sinaLogin'); ?>" class="logins-weibo-connect-link"><img src="<?php echo $zbp->host ?>zb_users/plugin/logins/resources/icon/sinaicon/logo_24.png" alt="微博登录" /></a>
                </div>
                <div class="options">
                    <label><input type="checkbox" /> 新窗口打开</label>
                    <button class="copy-btn">复制代码</button>
                </div>
                <textarea class="code-pre"><a href="<?php echo logins_Event_GetURL('sinaLogin'); ?>" class="logins-weibo-connect-link"><img src="<?php echo $zbp->host ?>zb_users/plugin/logins/resources/icon/sinaicon/logo_24.png" alt="微博登录" /></a></textarea>
            </div>
            <div class="readme-item">
                <div class="name">示例2：</div>
                <div class="preview">
                    <a href="<?php echo logins_Event_GetURL('sinaLogin'); ?>" class="logins-weibo-connect-link"><img src="<?php echo $zbp->host ?>zb_users/plugin/logins/resources/icon/sinaicon/logo_ico_24.png" alt="微博登录" /></a>
                </div>
                <div class="options">
                    <label><input type="checkbox" /> 新窗口打开</label>
                    <button class="copy-btn">复制代码</button>
                </div>
                <textarea class="code-pre"><a href="<?php echo logins_Event_GetURL('sinaLogin'); ?>" class="logins-weibo-connect-link"><img src="<?php echo $zbp->host ?>zb_users/plugin/logins/resources/icon/sinaicon/logo_ico_24.png" alt="微博登录" /></a></textarea>
            </div>
            <div class="readme-item">
                <div class="name">示例3：</div>
                <div class="preview">
                    <a href="<?php echo logins_Event_GetURL('sinaLogin'); ?>" class="logins-weibo-connect-link"><img src="<?php echo $zbp->host ?>zb_users/plugin/logins/resources/icon/sinaicon/login_16.png" alt="微博登录" /></a>
                </div>
                <div class="options">
                    <label><input type="checkbox" /> 新窗口打开</label>
                    <button class="copy-btn">复制代码</button>
                </div>
                <textarea class="code-pre"><a href="<?php echo logins_Event_GetURL('sinaLogin'); ?>" class="logins-weibo-connect-link"><img src="<?php echo $zbp->host ?>zb_users/plugin/logins/resources/icon/sinaicon/login_16.png" alt="微博登录" /></a></textarea>
            </div>
            <div class="readme-item">
                <div class="name">示例4：</div>
                <div class="preview">
                    <a href="<?php echo logins_Event_GetURL('sinaLogin'); ?>" class="logins-weibo-connect-link"><img src="<?php echo $zbp->host ?>zb_users/plugin/logins/resources/icon/sinaicon/login_24.png" alt="微博登录" /></a>
                </div>
                <div class="options">
                    <label><input type="checkbox" /> 新窗口打开</label>
                    <button class="copy-btn">复制代码</button>
                </div>
                <textarea class="code-pre"><a href="<?php echo logins_Event_GetURL('sinaLogin'); ?>" class="logins-weibo-connect-link"><img src="<?php echo $zbp->host ?>zb_users/plugin/logins/resources/icon/sinaicon/login_24.png" alt="微博登录" /></a></textarea>
            </div>
            <div class="readme-item">
                <div class="name">示例5：</div>
                <div class="preview">
                    <a href="<?php echo logins_Event_GetURL('sinaLogin'); ?>" class="logins-weibo-connect-link"><img src="<?php echo $zbp->host ?>zb_users/plugin/logins/resources/icon/sinaicon/login_32.png" alt="微博登录" /></a>
                </div>
                <div class="options">
                    <label><input type="checkbox" /> 新窗口打开</label>
                    <button class="copy-btn">复制代码</button>
                </div>
                <textarea class="code-pre"><a href="<?php echo logins_Event_GetURL('sinaLogin'); ?>" class="logins-weibo-connect-link"><img src="<?php echo $zbp->host ?>zb_users/plugin/logins/resources/icon/sinaicon/login_32.png" alt="微博登录" /></a></textarea>
            </div>
            <div class="readme-item">
                <div class="name">示例6：</div>
                <div class="preview">
                    <a href="<?php echo logins_Event_GetURL('sinaLogin'); ?>" class="logins-weibo-connect-link"><img src="<?php echo $zbp->host ?>zb_users/plugin/logins/resources/icon/sinaicon/login_48.png" alt="微博登录" /></a>
                </div>
                <div class="options">
                    <label><input type="checkbox" /> 新窗口打开</label>
                    <button class="copy-btn">复制代码</button>
                </div>
                <textarea class="code-pre"><a href="<?php echo logins_Event_GetURL('sinaLogin'); ?>" class="logins-weibo-connect-link"><img src="<?php echo $zbp->host ?>zb_users/plugin/logins/resources/icon/sinaicon/login_48.png" alt="微博登录" /></a></textarea>
            </div>
        </div>


    </div>
</div>

<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>