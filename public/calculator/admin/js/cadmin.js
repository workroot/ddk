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

/**
 * 后台侧边菜单选中状态
 */
$('.layui-nav-item').find('a').removeClass('layui-this');
$('.layui-nav-tree').find('a[href*="' + GV.current_controller + '"]').parent().addClass('layui-this').parents('.layui-nav-item').addClass('layui-nav-itemed');


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
                if(info.data.redirect){
                    setTimeout(function () {
                        location.href = info.data.redirect;
                    }, 500);
                }else{
                    layer.msg(info.message);
                }
            }
            layer.msg(info.message);
        }
    });

    return false;
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