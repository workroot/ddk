$(function(){
    var height = $("#popop").height();
    var width = $("#popop").width();
    $(".save").click(function(){
        var canvas = document.createElement("canvas")
        canvas.setAttribute('id','thecanvas');
        canvas.width = width * 2;
        canvas.height = height * 2;
        canvas.style.width = width + "px";
        canvas.style.height = height + "px";
        var context = canvas.getContext("2d");
        context.scale(2,2);
        html2canvas($("#popop"),{
            allowTaint: false,
            taintTest: true,
            canvas:canvas,
            onrendered:function(canvas) {
                var imgUri = canvas.toDataURL("image/png").replace("image/png", "image/octet-stream"); // 获取生成的图片的url
                window.location.href= imgUri;
            }
        })
    })
})