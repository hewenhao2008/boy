<?php
    define("TPL_DIR",ADMIN_ROOT_PATH."/templates/");
    require TPL_DIR."/header.php";
    require TPL_DIR."/navbar.php";
?>
<style>
    body{
        padding-bottom: 50px;
    }

    .wp-carousel-mgr-wrapper{
        background-color: #222;
        border:1px solid #000;
        position: relative;
        height: 420px;
        margin-bottom: 20px;
    }

    .wp-carousel-mgr-wrapper img{
        max-height: 100%;
        width: 100%;
    }

    .wp-carousel-mgr-wrapper .carouse-upload-progress-card{
        display: none;
        opacity: .85;
    }

    .wp-carousel-mgr-wrapper .carousel-waitupload-card{
        background-color: #222;
        padding-top: 15px;
    }

    .wp-carousel-mgr-wrapper .carousel-used-card{
        height:100%;
        width: 100%;
        background-color: #222;
        overflow: hidden;
    }

    .wp-carousel-mgr-wrapper .uploader{
        position: absolute;
        height: 100px;
        width: 100px;
        border: 4px dashed #999;
        top:50%;
        left: 50%;
        margin-top:-50px;
        margin-left: -50px;
        line-height: 100px;
        text-align: center;
        font-size: 45px;
        color:#999;
    }

    .wp-carousel-mgr-wrapper .uploader-progress{
        position: absolute;
        top : 40%;
        left: 50%;
        width: 200px;
        font-size: 14px;
        margin-left: -100px;
        color:#269abc;
        text-align: center;
    }

    /*.wp-carousel-mgr-wrapper .uploader-progress .progress-bar{*/
        /*width:0%;*/
        /*background-color: #269abc;*/
        /*height: 10px;*/
        /*margin-bottom:10px;*/
    /*}*/

    .wp-carousel-mgr-wrapper .uploader-progress .progress-redo-btn{
        display: none;
    }

    .wp-carousel-mgr-wrapper .picmetaeditor{
        position: absolute;
        height: 100%;
        width: 100%;
        top:0;
        left:0;
        opacity: .9;
        background-color: #FFF;
        /*padding: 5px;*/
        z-index: 99;
        display: none;
    }

    .wp-carousel-mgr-wrapper .picmetaeditor form{
        /*padding: 0px 10px;*/
    }

    .wp-carousel-mgr-wrapper .tools{
        position: absolute;
        bottom: 0px;
        width: 100%;
        height: 60px;
        background-color: #000;
        opacity: .8;
        z-index: 99;
    }

    .wp-carousel-mgr-wrapper .tools .tool-item{
        color:#FFF;
        font-size: 14px;
        font-weight: bold;
        text-align: center;
        line-height: 60px;
    }
</style>

<div class="container">
    <h4>
        <a href="http://m.wipark.cn" target="_blank" class="btn btn-primary btn-m pull-right" style="margin-top: -10px;margin-left: 20px;"><i class="glyphicon glyphicon-globe"></i> 访问微站</a>
        <a href="/wisite/preview" target="_blank" class="btn btn-success btn-m pull-right" style="margin-top: -10px;"><i class="glyphicon glyphicon-eye-open"></i> 预览</a>
        我的微站
    </h4>
    <hr>
    <div class="panel panel-default">
        <div class="panel-heading"><h5>微站基本设置</h5></div>
        <div class="panel-body">
<!--            <h4>-->
<!--                <a style="margin-top:-10px;display: inline-block;" class="pull-right btn btn-m btn-success"><i class="glyphicon glyphicon-play">预览微站</i></a>-->
<!--                微站基本设置-->
<!--            </h4>-->
<!--            <hr>-->
            <form role="form" class="form wp-basic-form" method="POST" action="/wisite/basicedit/" >
                <div class="form-group">
                    <label>微站标题</label>
                    <input type="text" name="wsite_title" class="form-control input-lg" value="<?=$wpWSiteStore['wsite_title']?>" placeholder="微站的标题将在浏览器头部显示">
                </div>
                <div class="form-group">
                    <label>成功获取时长后跳转链接</label>
                    <input type="text" name="wsite_success_redirecturl" class="form-control input-lg" value="<?=$wpWSiteStore['wsite_success_redirecturl']?>" placeholder="如果不设置则使用默认的网址">
                </div>
                <button type="submit" class="btn btn-success btn-lg">保存</button>
            </form>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading"><h5>微站轮播图片设置</h5></div>
        <div class="panel-body">
            <div class="row">
                <?foreach($wpWSiteStore['wsite_carousels'] as $carouselKey => $carousel):?>
                    <div class="col-md-6">
                        <div class="wp-carousel-mgr-wrapper" data-id="<?=$carouselKey?>">
                            <div class="carouse-upload-progress-card card">
                                <div class="uploader-progress">
                                    <p class="progress-msg">....</p>
                                    <div class="progress">
                                        <div class="progress-bar"></div>
                                    </div>
                                    <div class="progress-redo-btn"><a href="javascript:void(0);" class="btn btn-sm btn-primary">重新上传</a></div>
                                </div>
                            </div>
                            <div class="carousel-waitupload-card card" <?if(!empty($carousel)):?>style="display: none;"<?endif;?>>
                                <div class="uploader fileinput-button">
                                    <i class="glyphicon glyphicon-plus"></i>
                                    <input data-form-data = '{"carousel_id":"<?=$carouselKey?>"}' class="wp-view-upload-btn" type="file" name="file">
                                </div>
                                <p style="color:#555;font-size: 12px;line-height: 20px;font-weight: bold;text-shadow: 0.5px 0.5px 0.5px #333;" class="text-center">
                                    点击 <i class="glyphicon glyphicon-plus"></i>上传图片<br>
                                    建议上传宽高比为3:4尺寸的图片
                                </p>
                            </div>
                            <div class="carousel-used-card card" <?if(empty($carousel)):?>style="display: none;"<?endif;?>>
                                <div style="display: table;height:100%;width:100%;">
                                    <span style="display: table-cell;vertical-align: middle;">
                                    <?if(empty($carousel)):?>
                                        <img class="carousel_img" style="display:none;" src="#"/>
                                    <?else:?>
                                        <img class="carousel_img" src="<?=M_BASE_URL?>/contents/images/<?=$carousel['carousel_img']?>"/>
                                    <?endif;?>
                                </span>
                                </div>
                                <div class="picmetaeditor panel panel-default">
                                    <div class="panel-heading"><h5>图片信息编辑</h5></div>
                                    <div class="panel-body">
                                        <form class="form carousel-edit-form" role="form" action="/wisite/editcarousel/" method="POST">
                                            <input type="hidden" name="carousel_id" value="<?=$carouselKey?>"/>
                                            <div class="form-group">
                                                <label>图片链接</label>
                                                <textarea class="form-control" name="carousel_redirecturl" style="width: 100%;height: 80px;"><?if(!empty($carousel)):?><?=$carousel['carousel_redirecturl']?><?endif?></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-success btn-lg">保存</button>
                                            <button type="button" class="btn btn-danger wp-action-leaveeditor btn-lg">取消</button>
                                        </form>
                                    </div>
                                </div>
                                <div class="tools">
                                    <div class="tool-item pull-left" style="width: 33%;">
                                        <div style="color:#FFF;" class="wp-action-change fileinput-button">
                                            <i class="glyphicon glyphicon-retweet"></i> 更换
                                            <input class="wp-view-upload-btn" data-form-data = '{"carousel_id":"<?=$carouselKey?>"}'  type="file" name="file">
                                        </div>
                                    </div>
                                    <div class="tool-item pull-left" style="width: 33%;">
                                        <a href="javascript:void(0);"  style="color:#FFF;" class="wp-action-edit">
                                            <i class="glyphicon glyphicon-pencil"></i> 编辑
                                        </a>
                                    </div>
                                    <div class="tool-item pull-left" style="width: 34%;">
                                        <a href="javascript:void(0);" class="text-danger wp-action-remove">
                                            <i class="text-danger glyphicon glyphicon-trash"></i> 移除
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?endforeach;?>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="/assets/css/jquery.fileupload-ui.css"/>
<script src="/assets/js/underscore-min.js"></script>
<script src="/assets/js/lib/jquery.form.js"></script>
<script src="/assets/js/lib/jqfileupload/jquery.ui.widget.js"></script>
<script src="/assets/js/lib/jqfileupload/load-image.min.js"></script>
<script src="/assets/js/lib/jqfileupload/canvas-to-blob.min.js"></script>
<script src="/assets/js/lib/jqfileupload/jquery.iframe-transport.js"></script>
<script src="/assets/js/lib/jqfileupload/jquery.fileupload.js"></script>
<script src="/assets/js/lib/jqfileupload/jquery.fileupload-process.js"></script>
<script src="/assets/js/lib/jqfileupload/jquery.fileupload-image.js"></script>

<script>
    $(function(){
        var CONTENT_PREFIX = "<?=M_BASE_URL?>/contents/images/";

        function resizeCarouselHeight(){
            var carouselWidth = $(".wp-carousel-mgr-wrapper").width();
            var carouselHeight = (carouselWidth*4)/3;
            $(".wp-carousel-mgr-wrapper").css({height:carouselHeight});
        }

        resizeCarouselHeight();

        $(window).on("resize",function(){
            resizeCarouselHeight();
        });

        function isURL (str_url) {
            var strRegex = '^((https|http|ftp|rtsp|mms)?://)'
                + '?(([0-9a-z_!~*\'().&=+$%-]+: )?[0-9a-z_!~*\'().&=+$%-]+@)?' //ftp的user@
                + '(([0-9]{1,3}.){3}[0-9]{1,3}' // IP形式的URL- 199.194.52.184
                + '|' // 允许IP和DOMAIN（域名）
                + '([0-9a-z_!~*\'()-]+.)*' // 域名- www.
                + '([0-9a-z][0-9a-z-]{0,61})?[0-9a-z].' // 二级域名
                + '[a-z]{2,6})' // first level domain- .com or .museum
                + '(:[0-9]{1,4})?' // 端口- :80
                + '((/?)|' // a slash isn't required if there is no file name
                + '(/[0-9a-z_!~*\'().;?:@&=+$,%#-]+)+/?)$';
            var re=new RegExp(strRegex);
            if (re.test(str_url)) {
                return (true);
            } else {
                return (false);
            }
        }

        /**
         * 轮播Card切换
         *
         **/
        function showCard(parent,cardName){
            $(".card",parent).hide();
            if(cardName == ".carouse-upload-progress-card"){
                $(".progress-rebo-btn",parent).hide();
                $(".progress-bar",parent).css({"width":"0%"});
                $(".progress-msg",parent).removeClass("text-danger").addClass("text-primary").html("");
                $(".progress",parent).show();
           }
            $(cardName,parent).show();
        }
        /**
         * 编辑图片信息
         */
         $(document).on("click",".wp-action-edit",function(){
            var wrapper = $(this).closest(".wp-carousel-mgr-wrapper");
            var picMetaEditorEl = $(".picmetaeditor",wrapper);
            var toolsEl = $(".tools",wrapper);
            picMetaEditorEl.slideDown(function(){
                toolsEl.hide();
            });
        });


        $(".wp-basic-form").ajaxForm({
            dataType:"json",
            beforeSubmit:function(arr, $form, options){
                var submitBtn = $("button[type='submit']",$form);
                var url = $("input[name='wsite_success_redirecturl']",$form).val();
                url = url.trim();
                if(url.length > 0 && !isURL(url)){
                    alert("你输入的跳转链接格式不正确，链接地址格式为http://xxxx.xx");
                    return false;
                }
                submitBtn.html("提交中，稍候..");
                return true;
            },
            success:function(rsp,st,xhr,$form){
                var submitBtn = $("button[type='submit']",$form);
                submitBtn.html("提交");
                if(rsp.status == "ok"){
                    alert("数据已更新成功");
                    $(".wp-action-leaveeditor",$form).trigger("click");
                }else{
                    alert("更改数据时发生错误："+rsp.info+"，请重试！");
                }
            },
            error:function(e,msg,text,$form){
                var submitBtn = $("button[type='submit']",$form);
                submitBtn.html("提交");
                alert("更改数据时发生错误："+msg+"，请重试！");
            }
        });


        /**
         * 轮播图片编辑表单
         */

         $(".carousel-edit-form").ajaxForm({
            dataType:"json",
            beforeSubmit:function(arr, $form, options){
                var submitBtn = $("button[type='submit']",$form);
                var url = $("textarea[name='carousel_redirecturl']",$form).val();
                url = url.trim();
                if(url.length > 0 && !isURL(url)){
                    alert("你输入的跳转链接格式不正确，链接地址格式为http://xxxx.xx");
                    return false;
                }
                submitBtn.html("提交中，稍候..");
                return true;
            },
            success:function(rsp,st,xhr,$form){
                var submitBtn = $("button[type='submit']",$form);
                submitBtn.html("提交");
                if(rsp.status == "ok"){
                    alert("数据已更新成功");
                    $(".wp-action-leaveeditor",$form).trigger("click");
                }else{
                    alert("更改数据时发生错误："+rsp.info+"，请重试！");
                }
            },
            error:function(e,msg,text,$form){
                var submitBtn = $("button[type='submit']",$form);
                submitBtn.html("提交");
                alert("更改数据时发生错误："+msg+"，请重试！");
            }
        });


        /**
         * 移除图片
         */
        $(document).on("click",".wp-action-remove",function(){
            if(confirm("你确定要移除图片吗？移除后不能恢复")){
                var wrapper = $(this).closest(".wp-carousel-mgr-wrapper");
                var carousel_id = wrapper.attr("data-id");
                var origHtml = $(this).html();
                var self = this;
                $(this).html("稍候..");
                $.ajax({
                    url:"/wisite/remove/",
                    type:"post",
                    data:{
                        carousel_id : carousel_id
                    },
                    dataType:"json",
                    success:function(rsp){
                        $(self).html(origHtml);
                        if(rsp.status == "ok"){
                            showCard(wrapper,".carousel-waitupload-card");
                        }else{
                            alert("移除轮播图片时发生错误："+rsp.info+"，请稍候重试！");
                        }
                    },
                    error:function(err,msg){
                        $(self).html(origHtml);
                        alert("移除轮播图片发生错误:"+msg+",请重试!");
                    }
                });
            }
        });

        $(document).on("click",".wp-action-leaveeditor",function(){
            var wrapper = $(this).closest(".wp-carousel-mgr-wrapper");
            var picMetaEditorEl = $(".picmetaeditor",wrapper);
            var toolsEl = $(".tools",wrapper);
            picMetaEditorEl.slideUp(function(){
                toolsEl.show();
            });
        });

        $(document).on("click",".progress-redo-btn",function(){
            var wrapper = $(this).closest(".wp-carousel-mgr-wrapper");
            showCard(wrapper,".carousel-waitupload-card");
        });

        $(".wp-view-upload-btn").fileupload({
            url: "wisite/upload",
            dataType: 'json',
            autoUpload: true,
            maxFileSize:500000,
            acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
            disableImageResize: /Android(?!.*Chrome)|Opera/.test(window.navigator && navigator.userAgent),
            imageMaxWidth: 400,
            imageMaxWidth:400,
//            imageForceResize:true,
            imageQuality : 0.3,
            imageType:"image/jpg"
//            imageCrop: true
        }).on('fileuploadadd',function (e, data) {
                console.log(arguments);
                var wrapper = $(this).closest(".wp-carousel-mgr-wrapper");
                var uploader = $(".uploader",wrapper);
                var uploadProgress = $(".uploader-progress",wrapper);
                var uploadProgressBar = $(".uploader-progress .progress-bar",wrapper);
                var uploadProgressMsg = $(".uploader-progress .progress-msg",wrapper);
                showCard(wrapper,".carouse-upload-progress-card");
                var fileData = data.files[0];
                console.log(fileData);
                if (fileData.error) {
                    uploadProgressMsg.addClass("text-danger").html(fileData.error);
                } else {
                    uploadProgressMsg.html("..等待上传..");
                }
            }).on('fileuploadprocessalways',function (e, data) {
                var fileData = data.files[0];
                var wrapper = $(this).closest(".wp-carousel-mgr-wrapper");
                var uploader = $(".uploader",wrapper);
                var uploadProgress = $(".uploader-progress",wrapper);
                var uploadProgressBar = $(".uploader-progress .progress-bar",wrapper);
                var uploadProgressMsg = $(".uploader-progress .progress-msg",wrapper);
                if (fileData.error) {
                    uploadProgressMsg.addClass("text-danger").html(fileData.error);
                } else {
                    uploadProgressMsg.removeClass("text-danger").html("等待上传").show();
                }
            }).on('fileuploadprogress',function (e, data) {
                var wrapper = $(this).closest(".wp-carousel-mgr-wrapper");
                var uploader = $(".uploader",wrapper);
                var uploadProgress = $(".uploader-progress",wrapper);
                var uploadProgressBar = $(".uploader-progress .progress-bar",wrapper);
                var uploadProgressMsg = $(".uploader-progress .progress-msg",wrapper);
                var progress = parseInt(data.loaded / data.total * 100, 10);
                uploadProgressMsg.html('已上传' + progress + '%');
                uploadProgressBar.css({width:progress+"%"});
            }).on('fileuploadstop',function (e) {
            }).on('fileuploaddone',function (e, data) {
                var wrapper = $(this).closest(".wp-carousel-mgr-wrapper");
                var uploader = $(".uploader",wrapper);
                var uploadProgress = $(".uploader-progress",wrapper);
                var uploadProgressBar = $(".uploader-progress .progress-bar",wrapper);
                var uploadProgressMsg = $(".uploader-progress .progress-msg",wrapper);
                $(".uploader-progress .progress",wrapper).hide();
                if (data.result.status == "ok") {
                    showCard(wrapper,".carousel-used-card");
                    var rsp = data.result.rsp;
                    $("img.carousel_img",wrapper).attr("src",CONTENT_PREFIX+rsp.carousel_img+"?_="+(Math.floor(Math.random() * 1000000000))).show();
                    $("textarea[name='carousel_redirecturl']",wrapper).val(rsp.carousel_redirecturl);
                    $("select[name='carousel_showbtn']",wrapper).val(rsp.carousel_showbtn);
                } else {
                    uploadProgressMsg.addClass("text-danger").html(data.result.rsp);
                    $(".progress-redo-btn",wrapper).show();
                }
            }).on('fileuploadfail', function (e, data) {
                var wrapper = $(this).closest(".wp-carousel-mgr-wrapper");
                var uploader = $(".uploader",wrapper);
                var uploadProgressEl = $(".uploader-progress .progress",wrapper);
                var uploadProgressBar = $(".uploader-progress .progress-bar",wrapper);
                uploadProgressEl.hide();
                var uploadProgressMsg = $(".uploader-progress .progress-msg",wrapper);
                uploadProgressMsg.addClass("text-danger").html("上传文件失败，请重试!");
                $(".progress-redo-btn",wrapper).show();
            });

    });
</script>


