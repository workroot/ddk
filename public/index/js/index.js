/**
 * 后台JS主入口
 */

var layer = layui.layer,
    element = layui.element,
    laydate = layui.laydate,
    form = layui.form;

/**
 * AJAX全局设置
 */
$.ajaxSetup({
    type: "post",
    dataType: "json"
});


if($('.brie').find('ul li').eq(0).hasClass('layui-this')){
    $('input:hidden[name="type"]').val(1);
}




/**
 * 切换
 */

element.on('tab(docDemoTabBrief)',function(){
	if($(this).attr('data-type') == 2){
	    $('input:hidden[name="type"]').val(2);
		$('.code').addClass('layui-show').prev('.sou').removeClass('layui-show');
	}else{
        $('input:hidden[name="type"]').val(1);
		$('.sou').addClass('layui-show').next('.code').removeClass('layui-show');
	}
})


/**
 * 通用日期时间选择
 */

laydate.render({
    elem: '.start_time',
    type: 'datetime',
});

laydate.render({
    elem: '.end_time',
    type: 'datetime',
});

laydate.render({
    elem: '.established',
});




/**
 * 通用表单提交(AJAX方式)
 */
form.on('submit(*)', function (data) {
    $.ajax({
        url: data.form.action,
        type: data.form.method,
        data: $(data.form).serialize(),
        success: function (info) {
            if (info.code === 0) {
                setTimeout(function () {
                    location.href = info.data.redirect;
                }, 500);
            }else{
                layer.msg(info.message);
            }
        }
    });
	return false;
});


/**
 * 验证
 */
form.verify({
    confirm: function(value){ //value：表单的值、item：表单的DOM对象
        if(!/^[\S]{6,15}$/.test(value)){
            return '密码必须6到12位,且不能出现空格';
        }
        var psd =  $('input[name=password]').val();

        if(value != psd){
            return '两次密码不一致';
        }
    },

    loginPass:function(value){
        if($('.brie').find('ul li').eq(0).hasClass('layui-this')){
            if(value == ''){
                return '密码不能为空';
            }
            if(!/^[\S]{6,15}$/.test(value)){
                return '密码必须6到12位,且不能出现空格';
            }
        }
    } ,
    verification:function(value){
        if($('.brie').find('ul li').eq(1).hasClass('layui-this')){
            if(value == ''){
                return '验证码不能为空';
            }
        }

    }
});



/**
 * 通用批量处理（审核、取消审核、删除）
 */
$('.ajax-action').on('click', function () {
    var _action = $(this).data('action');
    layer.open({
        shade: false,
        content: '确定执行此操作？',
        btn: ['确定', '取消'],
        yes: function (index) {
            $.ajax({
                url: _action,
                data: $('.ajax-form').serialize(),
                success: function (info) {
                    if (info.code === 1) {
                        setTimeout(function () {
                            location.href = info.url;
                        }, 1000);
                    }
                    layer.msg(info.msg);
                }
            });
            layer.close(index);
        }
    });

    return false;
});

/**
 * 通用全选
 */
$('.check-all').on('click', function () {
    $(this).parents('table').find('input[type="checkbox"]').prop('checked', $(this).prop('checked'));
});


/**
 * 通用删除
 */
$('.ajax-delete').on('click', function () {
    var _href = $(this).attr('href');
    layer.open({
        shade: false,
        content: '确定删除？',
        btn: ['确定', '取消'],
        yes: function (index) {
            $.ajax({
                url: _href,
                type: "get",
                success: function (info) {
                    if (info.code === 1) {
                        setTimeout(function () {
                            location.href = info.url;
                        }, 1000);
                    }
                    layer.msg(info.msg);
                }
            });
            layer.close(index);
        }
    });
    return false;
});

/**
 * 清除缓存
 */
$('#clear-cache').on('click', function () {
    var _url = $(this).data('url');
    if (_url !== 'undefined') {
        $.ajax({
            url: _url,
            success: function (data) {
                if (data.code === 1) {
                    setTimeout(function () {
                        location.href = location.pathname;
                    }, 1000);
                }
                layer.msg(data.msg);
            }
        });
    }
    return false;
});


/**
 * 查看图片
 */

layer.photos({
    photos: '.image'
    ,anim: 1 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
});


/**
 * 通用单图上传
 */
layui.use('upload', function () {
    var upload = layui.upload;
    var type = $('#file_upload').attr('data-type');
    var uploadInst = upload.render({
        elem: '#file_upload',
        url: '/backend/uploads/single',
        data: {type:type},
        accept: 'images',
        exts: 'jpg|png|gif|bmp|jpeg',
        multiple: true,
        number: 5,
        done: function (res, index, uploads) {
            if (res){
                document.getElementById('thumb').value = res.file.url;
            } else {
                layer.msg(res);
            }
        }

    })
});


/**
 *  excel 导入
 */
layui.use('upload', function () {
    var upload = layui.upload;
    var uploadInst = upload.render({
        elem: '#file_excel',
        url: '/backend/company/imports',
        data: {'type':3},
        accept: 'file',
        multiple: true,
        number: 5,
        done: function (info, index, uploads) {
            if (info.code === 0) {
                setTimeout(function () {
                    location.href = info.data.redirect;
                }, 500);
            }
            layer.msg(info.message);
        }

    })
});









layui.use('upload', function () {
    var upload = layui.upload;
    var uploadInst = upload.render({
        elem: '#file_uploadss',
        url: '/backend/uploads/single',
        data: {type:$(this).attr('data-type')},
        accept: 'images',
        exts: 'jpg|png|gif|bmp|jpeg',
        choose:function(obj){
            obj.preview(function(index, file, result){
                var f = $('.filid').children().length;
                $('.jishu').html(f-1);
                if(f == 6){
                    $('.fileImg').css('display','none');
                }
            })
        },
        multiple: true,
        number: 2,
        done: function (res, index, uploads) {
            if (res){
                var aa = $('input:hidden[name="commodity_img"]').val();
                var html = '<li class="img"><img src="' + res.file.url + '" width="80" height="80"><a data-img="'+res.file.url+'" res="imgxx" style="margin-left:-19px;color: red;position: absolute; font-size: 25px;font-weight: bold;" >X</a></li>';
                $('input:hidden[name="commodity_img"]').val((aa ? (aa + ",") : '')+res.file.url);
                $('.fileImg').before(html);
            } else {
                layer.msg(res);
            }
        }

    })
})





/**
 * 短信验证码
 */

var InterValObj; //timer变量，控制时间
var count = 60; //间隔函数，1秒执行
			
$('.pull-right').click(function(){
    var mobile = $('input[name="mobile"]').val();
	$(this).attr("disabled","disabled");
	$.ajax({
        url:'/api/login/sms',
        type:'post',
        data:{mobile:mobile},
        dataType:'json',
        success:function(json){
            if(json.code == 0){
                InterValObj = window.setInterval(SetRemainTime, 1000);
            }else{
                layer.msg(json.msg);
            }
        }
    })
            
});


function SetRemainTime() {
    if (count == 0) {
        window.clearInterval(InterValObj);//停止计时器
        $(".pull-right").removeAttr("disabled");//启用按钮
        $(".pull-right").css('background','#1E9FFF').html(count);
        $(".pull-right").html('获取验证码');
        count = 60;
    }else {
        count--;
        $(".pull-right").css('background','#e6e6e6').html(count);
    }
}