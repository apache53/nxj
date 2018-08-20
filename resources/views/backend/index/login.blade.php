<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
    @include('backend.inc.head')

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
@include('backend.inc.script')
@include('backend.inc.common')
</body>
<!-- END BODY -->

<script>
    jQuery(document).ready(function() {
        Metronic.init(); // init metronic core components
        Layout.init(); // init current layout
        Login.init();
    });
</script>
<!-- END JAVASCRIPTS -->
</html>