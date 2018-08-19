<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
    <meta charset="utf-8"/>
    <title>楠溪江旅游 | Login</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8">
    <meta content="" name="description"/>
    <meta content="" name="author"/>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->

    <link href="{{ URL::asset('/resource/') }}/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::asset('/resource/') }}/assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::asset('/resource/') }}/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::asset('/resource/') }}/assets/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL STYLES -->
    <link href="{{ URL::asset('/resource/') }}/assets/admin/pages/css/login.css" rel="stylesheet" type="text/css"/>
    <!-- END PAGE LEVEL SCRIPTS -->
    <!-- BEGIN THEME STYLES -->
    <link href="{{ URL::asset('/resource/') }}/assets/global/css/components.css" id="style_components" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::asset('/resource/') }}/assets/global/css/plugins.css" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::asset('/resource/') }}/assets/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>
    <link href="{{ URL::asset('/resource/') }}/assets/admin/layout/css/themes/darkblue.css" rel="stylesheet" type="text/css" id="style_color"/>
    <link href="{{ URL::asset('/resource/') }}/assets/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>
    <!-- END THEME STYLES -->
    <link rel="shortcut icon" href="favicon.ico"/>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="login">
<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
<div class="menu-toggler sidebar-toggler">
</div>
<!-- END SIDEBAR TOGGLER BUTTON -->
<!-- BEGIN LOGO -->
<div class="logo">
    <a href="index.html">
        <img src="{{ URL::asset('/resource/') }}/assets/admin/layout/img/logo-big.png" alt=""/>
    </a>
</div>
<!-- END LOGO -->
<!-- BEGIN LOGIN -->
<div class="content">
    <!-- BEGIN LOGIN FORM -->
    <form class="login-form" >
        <h3 class="form-title">登录</h3>
        <div class="alert alert-danger display-hide">
            <button class="close" data-close="alert"></button>
            <span>
			请输入用户名和密码. </span>
        </div>
        <div class="form-group">
            <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
            <label class="control-label visible-ie8 visible-ie9">用户名</label>
            <input class="form-control form-control-solid placeholder-no-fix" type="text" autocomplete="off" placeholder="用户名" name="username"/>
        </div>
        <div class="form-group">
            <label class="control-label visible-ie8 visible-ie9">密码</label>
            <input class="form-control form-control-solid placeholder-no-fix" type="password" autocomplete="off" placeholder="密码" name="password"/>
        </div>
        <div class="form-group">
            <div class="input-group">
                <input class="form-control form-control-solid placeholder-no-fix input-small" type="text" placeholder="验证码" name="vcode"  />
                <span class="input-group-btn">
                    <img id="vcode_img" onclick="this.src='/backend/index/vcode?d='+Math.random();" src="/backend/index/vcode?d="+Math.random(); style="cursor: pointer;">
                </span>
            </div>
        </div>
        <div class="form-actions">
            <button type="button" class="btn btn-success uppercase">登录</button>
            <a href="javascript:;" id="forget-password" class="forget-password">忘记密码?</a>
        </div>
    </form>
    <!-- END LOGIN FORM -->
</div>
<div class="copyright">
    2018 © 楠溪江.
</div>
<!-- END LOGIN -->
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="{{ URL::asset('/resource/') }}/assets/global/plugins/respond.min.js"></script>
<script src="{{ URL::asset('/resource/') }}/assets/global/plugins/excanvas.min.js"></script>
<![endif]-->
<script src="{{ URL::asset('/resource/') }}/assets/global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="{{ URL::asset('/resource/') }}/assets/global/plugins/jquery-migrate.min.js" type="text/javascript"></script>
<script src="{{ URL::asset('/resource/') }}/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="{{ URL::asset('/resource/') }}/assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="{{ URL::asset('/resource/') }}/assets/global/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="{{ URL::asset('/resource/') }}/assets/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="{{ URL::asset('/resource/') }}/assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="{{ URL::asset('/resource/') }}/assets/global/scripts/metronic.js" type="text/javascript"></script>
<script src="{{ URL::asset('/resource/') }}/assets/admin/layout/scripts/layout.js" type="text/javascript"></script>
<script src="{{ URL::asset('/resource/') }}/assets/admin/pages/scripts/login.js" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->
<script>
    jQuery(document).ready(function() {
        Metronic.init(); // init metronic core components
        Layout.init(); // init current layout
        Login.init();
    });
</script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>