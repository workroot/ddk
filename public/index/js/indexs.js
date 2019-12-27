$(function(){

    if($(".se").val() != ''){
        $("#search_ipt").removeAttr("placeholder");
        $("#search_ipt").css({"background": "rgba(255,190,98,0)","border": "1px solid #ffbe62","padding": "0 2.8rem 0 0.8rem"});
        $(".search_btn").show();
    }


    $("#search_ipt").focus(function(){
            $(this).removeAttr("placeholder");
            $(this).css({
                "background": "rgba(255,190,98,0)",
                "border": "1px solid #ffbe62",
                "padding": "0 2.8rem 0 0.8rem"
            });
            $(".search_btn").show()
    });
    $("#search_ipt").blur(function(){
        if($(".se").val() == ''){
            $(this).attr("placeholder","输入您想要查询的网贷平台");
            $(this).css({"background":"#ffbe62","border":"none","padding": "0 0.8rem"});
            $(".search_btn").hide()
        }else{
            $(this).removeAttr("placeholder");
            $(this).css({
                "background": "rgba(255,190,98,0)",
                "border": "1px solid #ffbe62",
                "padding": "0 2.8rem 0 0.8rem"
            });
            $(".search_btn").show();
        }
    })
})