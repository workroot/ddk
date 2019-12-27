
    var $wx_account = wxdata.wx_account, // 自定义数据，见wxShare_data.js
        $wx_share = wxdata.wx_share;  // 自定义数据 ，见wxShare_data.js


    //配置微信信息
    wx.config({
        debug: false,  // true:调试时候弹窗
        appId: $wx_account[0], // 微信appid
        timestamp: $wx_account[1], // 时间戳
        nonceStr: $wx_account[2], // 随机字符串
        signature: $wx_account[3], // 签名
        jsApiList: [
            // 所有要调用的 API 都要加到这个列表中
            'onMenuShareTimeline',    // 分享到朋友圈接口
            'onMenuShareAppMessage', // 分享到朋友接口
            'onMenuShareQQ',     // 分享到QQ接口
            'onMenuShareWeibo'   // 分享到微博接口
        ]
    });

    wx.ready(function () {
        // config信息验证后会执行ready方法，所有接口调用都必须在config接口获得结果之后，config是一个客户端的异步操作，所以如果需要在页面加载时就调用相关接口，则须把相关接口放在ready函数中调用来确保正确执行。对于用户触发时才调用的接口，则可以直接调用，不需要放在ready函数中。
        // 微信分享的数据
        var shareData = {
            "imgUrl": $wx_share[0],  // 分享显示的缩略图地址
            "link": $wx_share[1],  // 分享地址
            "desc": $wx_share[2],  // 分享描述
            "title": $wx_share[3],  // 分享标题
            success: function () {
                alert("分享成功");
            }
        };
        wx.onMenuShareTimeline(shareData);
        wx.onMenuShareAppMessage(shareData);
        wx.onMenuShareQQ(shareData);
        wx.onMenuShareWeibo(shareData);
    });

    wx.error(function (res) {
        // config信息验证失败会执行error函数，如签名过期导致验证失败，
        // 具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，
        //对于SPA可以在这里更新签名。
        alert(res.errMsg);
    });

