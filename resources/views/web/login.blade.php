<!DOCTYPE html>
<html lang="en">
<head>
    @include('web.head')
</head>
<body>
<div id="app">
    <div class="login">
        <div class="title">楠溪江语音管理平台</div>
        <div class="row"><input v-model="username" type="text" placeholder="管理员账号"></div>
        <div class="row"><input v-model="password" type="password" placeholder="管理员密码"></div>
        <button class="button" @click="login">登录</button>
    </div>
</div>
<script>
    var app = new Vue({
        el: '#app',
        data: {
            username: '',
            password: '',
        },
        methods: {
            login() {
                var formData = {}
                formData.username = this.username
                formData.password = this.password
                formData.login_type = 1
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    data: formData,
                    url: "/api/user/login",
                    success: function (data) {
                        if (data.error == 1) {
                            localStorage.setItem('login_token', data.res.login_token)
                            location.href = '/web/scenic'
                        }
                    }
                });
            },
        },
    })
</script>
</body>
</html>