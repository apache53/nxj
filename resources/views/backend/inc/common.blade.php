<div id="errorTipModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">

    <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>

        <h3 id="errorTipModal_title">提示</h3>

    </div>

    <div class="modal-body" id="errorTipModal_body">

        <p>内容</p>

    </div>

    <div class="modal-footer">

        <button data-dismiss="modal" class="btn green">关闭</button>

    </div>

</div>

<script>

    function alertErrorTip(content,title){

         if(title!="")
         $("#errorTipModal #errorTipModal_title").html(title);
         if(content!="")
         $("#errorTipModal #errorTipModal_body").html(content);
         $("#errorTipModal").modal("show");
        /*if($.trim(title)=="") title = '提示';
        if($.trim(content)=="") content = '';
        $.dialog({
            id:'alertErrorTip_div',
            title:title,
            icon:'warning',		//face-smile  face-sad  warning
            content:content,
            cancel:true,
            cancelVal:'关闭'
        });*/
    }
</script>
