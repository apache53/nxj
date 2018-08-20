var Login = function() {

    var handleLogin = function() {

        $('.login-form').validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                username: {
                    required: true
                },
                password: {
                    required: true
                },
                remember: {
                    required: false
                }
            },

            messages: {
                username: {
                    required: "用户名不能为空."
                },
                password: {
                    required: "密码不能为空."
                }
            },

            invalidHandler: function(event, validator) { //display error alert on form submit   
                $('.alert-danger', $('.login-form')).show();
            },

            highlight: function(element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            },

            success: function(label) {
                label.closest('.form-group').removeClass('has-error');
                label.remove();
            },

            errorPlacement: function(error, element) {
                error.insertAfter(element.closest('.input-icon'));
            },

            submitHandler: function(form) {
                return false;
            }
        });

        $('.login-form input').keypress(function(e) {
            if (e.which == 13) {
                if ($('.login-form').validate().form()) {
                    login_submit();
                }
                return false;
            }
        });

        $('.form-actions .btn').click(function(e) {
            if ($('.login-form').validate().form()) {
                login_submit();
            }
            return false;
        });

        function login_submit(){
            var data = $('.login-form').serialize();
            $.ajax({
                url:'/backend/index/dologin',
                type:'POST', //GET
                async:false,    //或false,是否异步
                data:data,
                dataType:'json',    //返回的数据格式：json/xml/html/script/jsonp/text
                success:function(data){
                    alert(data.msg);
                    if(data.error != 1){
                        alertErrorTip('错误',data.msg);
                        return false;
                    }
                    alert(data.msg);
                    alertErrorTip('提示',data.msg);
                    return false;
                },
                error:function(xhr,textStatus){

                    console.log('error');
                    return false;
                }
            })
            return false;
        }
    }

    return {
        //main function to initiate the module
        init: function() {

            handleLogin();

        }

    };

}();