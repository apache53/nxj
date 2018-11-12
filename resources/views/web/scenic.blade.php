<!DOCTYPE html>
<html lang="en">
<head>
    @include('web.head')
</head>
<body>
<div id="app">
    <div class="scenic">
        <ul>
            <li v-for="item in scenicList">
                <div>场景名称：@{{item.scenic_name}}</div>
                <div>语音：<span v-if="item.voice_path" style="color:#30dc05;">已上传</span><span v-else style="color:#808080;">未上传</span> <a style="color:#39c;" @click="handleUpload(item)">上传</a></div>
            </li>
        </ul>
    </div>
    <div style="width:0; height:0;overflow:hidden;"><form ref="form"><input ref="file" type="file" @change="onChangeFile" accept="audio/mpeg"></form></div>
</div>
<script>
    var login_token = localStorage.getItem('login_token');
    if(login_token=="" || !login_token){
        location.href = '/web/login';
    }

    var app = new Vue({
        el: '#app',
        data: {
            scenicList: [],
            edit: {},
        },
        created() {
            this.getScenicList()
        },
        methods: {
            getScenicList() {
                let token = localStorage.getItem('login_token')
                $.ajax({
                    dataType: "json",
                    url: "/api/scenic/list?login_token=" + token,
                    success: function (data) {
                        if (data.error == 1) {
                            app.scenicList = data.res
                            // location.href = 'scenic.html'
                        }
                    }
                });
            },
            handleUpload(item) {
                this.edit = item
                this.$refs.file.click()
            },
            onChangeFile(e) {
                this.submit()
            },
            submit() {
                var fd = app.edit
                var formData = new FormData()
                var token = localStorage.getItem('login_token')
                formData.append('file', this.$refs.file.files[0], this.$refs.file.files[0].name)

                formData.append('scenic_id', fd.id)
                formData.append('login_token', token)

                $.ajax({
                    type: "POST",
                    dataType: "json",
                    data: formData,
                    processData: false,
                    contentType: false,
                    url: "/api/file/scenic_upload",
                    success: function (data) {
                        if (data.error == 1) {

                            alert('上传成功')
                            app.getScenicList()

                        }else if(data.error == 101 || data.error == 30){
                            location.href = '/web/login';
                        }else{
                            alert(data.msg);
                        }
                    }
                });
            },
        },
    })
</script>
</body>
</html>