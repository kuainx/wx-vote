import axios from 'axios';
export const wx_init = async () => {
  const r = await axios.get('https://lifestudio.cn/vote/api/sign.php');
  const config = r.data;
  wx.config({
    debug: false, //开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
    appId: config.appId, // 必填，公众号的唯一标识
    timestamp: parseInt(config.timestamp), // 必填，生成签名的时间戳
    nonceStr: config.nonce, //必填， 生成签名的随机串
    signature: config.signature, //必填，签名
    jsApiList: ['updateAppMessageShareData', 'onMenuShareAppMessage'], //必填， JS接口列表，这里只填写了分享需要的接口
  });
};

export function wx_share(title, link, imgurl, desc) {
  wx.updateAppMessageShareData({
    title: title, // 分享标题
    desc: desc, // 分享描述
    link: link, // 分享链接
    imgUrl: imgurl, // 分享图标
    type: 'link', // 分享类型,music、video或link，不填默认为link
    dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
    success: function () {
      // 用户确认分享后执行的回调函数
    },
    cancel: function () {
      // 用户取消分享后执行的回调函数
    },
  });

  wx.onMenuShareAppMessage({
    title: title, // 分享标题
    desc: desc, // 分享描述
    link: link, //分享点击之后的链接
    imgUrl: imgurl, // 分享图标
    // type: 'link', // 分享类型,music、video或link，不填默认为link
  });
}
wx.error(function (res) {
  // config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。
});
