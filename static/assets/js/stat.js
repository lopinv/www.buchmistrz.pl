(function () {
  // ✅ 配置：仅需填写主域名（不带 www）
  const siteCodes = {
    'buchmistrz.pl': 'KqmtdhxdOsN9a7i7',
    'buchvape.pl': 'KqmtdhxdOsN9a7i7',
    'buyvapeshop.xyz': 'KqmtdhxdOsN9a7i7',
    'jednorazowki-online.pl': 'KqmtdhxdOsN9a7i7',
    'swiat-jednorazowek.pl': 'KqmtdhxdOsN9a7i7',
    'dymly.pl': 'KqmtdhxdOsN9a7i7',
    'smak-chmury.pl': 'KqmtdhxdOsN9a7i7'
  };
  
  // ✅ 检测是否为中文用户（包括 zh, zh-CN, zh-TW, zh-HK 等）
  const userLang = (navigator.language || navigator.userLanguage || '').toLowerCase();
  if (userLang.startsWith('zh')) {
    console.log('51.la: 检测到中文用户，跳过统计加载。');
    return;
  }
  
  // ✅ 获取当前域名，并移除开头的 "www."（如果存在）
  const hostname = window.location.hostname;
  const normalizedDomain = hostname.replace(/^www\./, '');

  // ✅ 检查标准化后的域名是否在配置中
  const code = siteCodes[normalizedDomain];

  if (!code) {
    console.warn('51.la: 未配置当前站点的统计代码:', hostname);
    return;
  }

  // ✅ 动态创建并加载 51.la 脚本
  const script = document.createElement('script');
  script.charset = 'UTF-8';
  script.id = 'LA_COLLECT';
  script.src = `//sdk.51.la/js-sdk-pro.min.js?id=${code}&ck=${code}&autoTrack=true&screenRecord=true`;
  script.async = true;

  document.head.appendChild(script);
})();
