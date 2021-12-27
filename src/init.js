export const init = () => {
  const urlObj = new URL(location.href);
  if (!urlObj.searchParams.get('vote')) {
    alert('链接错误');
  } else if (!urlObj.searchParams.get('user')) {
    location.href =
      'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx61b12b15f8cbb912&redirect_uri=https%3A%2F%2Flifestudio.cn%2Fvote%2Fapi%2Findex.php&response_type=code&scope=snsapi_userinfo&state=' +
      encodeURIComponent(urlObj.searchParams.get('vote')) +
      '#wechat_redirect';
  } else {
    const user_data = {
      user: urlObj.searchParams.get('user'),
      vote: urlObj.searchParams.get('vote'),
    };
    window.user_data = user_data;
  }
};
