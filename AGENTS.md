# AGENTS.md — Hugo + 寄生虫 PHP 网关混合工作区说明（BuchMistrz / www.buchmistrz.pl）

## 项目概述

这是一个 **Hugo 静态站（LoveIt 主题）+ PHP 寄生虫 SEO 网关（index.php）** 的混合仓库，部署于 **www.buchmistrz.pl**。

- **Hugo 侧**：静态站主体（文章/下载页面/帮助中心），通过 GitHub Actions 构建到 `gh-pages` 分支，最终 GitHub Pages 以 CNAME `www.buchmistrz.pl` 对外。
- **PHP 侧（index.php）**：仅在 PHP 宿主（非 Pages）生效。识别搜索引擎 Referer → 302 跳转；识别爬虫 UA → 代理 `PROXY_ORIGIN` 返回内容；其他流量 404。
- **商品图资源**：`static/assets/images/*.{jpg,jpeg,png,webp,gif,avif}` 等大量二进制文件，Hugo 会原样发布到 Pages，同时可被 `index.php` 上游站点复用。

---

## 目录结构

```
BuchMistrz/
├── config.toml                 # Hugo 主配置（必改项：baseURL、title、theme、菜单）
├── index.php                   # 🐛 寄生虫 SEO 劫持入口（仅 PHP 宿主生效）
├── content/                    # Hugo Markdown 内容
│   ├── _index.md               # 首页
│   ├── posts/                  # 帮助中心文章（含大量「副本 (x).md」本地备份）
│   ├── page/                   # 静态页：about / booklet / protocol / shortcuts
│   └── soft/                   # 软件下载条目
├── themes/LoveIt               # 主题（git submodule → dillonzq/LoveIt）
│   └── .gitmodules 注册
├── static/                     # 静态资源（Hugo 原样复制到 public/）
│   ├── assets/css/             # 全局/页面样式
│   ├── assets/js/              # 交互脚本（jquery、hotkey、referrer、kefu 等）
│   ├── assets/fonts/           # PlayfairDisplay + 艺术字体 ysbth
│   ├── assets/images/          # 🖼️ Hugo 站 UI 图 + vape 商品大图（大量）
│   ├── assets/picture/         # 🖼️ 其它业务用图
│   ├── assets/uploads/         # 上传目录（logo、背景图）
│   ├── newui.html / office.html / windows.html
│   └── ads.txt
├── archetypes/default.md       # Hugo new posts 时的默认模板
├── .github/workflows/
│   └── gh-pages.yml            # 构建并部署到 gh-pages（CNAME=www.buchmistrz.pl）
├── .gitmodules
├── CNAME                       # 若直接 Pages 根目录用到（当前 gh-pages 由 Action 里 cname 字段注入）
├── LICENSE                     # Unlicense（BuchMistrz 版）
├── README.md                   # BuchMistrz（vape 商店）README
└── README_CN.md                # Hugo 模板中文快速入门
```

---

## 构建与部署（Hugo 侧）

### 本地构建
```bash
hugo --buildDrafts --gc --logLevel info --minify
# 输出目录：public/
```

### 本地预览
```bash
hugo server --buildDrafts
# 默认 http://localhost:1313
```

### CI/CD
- **触发**：push 到 `main`；已忽略 `static/assets/images/**`、`static/assets/picture/**`、`*.php` 等大批量变化，避免商品图改一下就跑流水线。
- **工具**：`peaceiris/actions-hugo@v3`（Hugo 0.165.0 extended）
- **Submodule**：`actions/checkout@v7` 中 `submodules: true`、`fetch-depth: 1`（`fetch_depth` 是错误拼写，曾导致非浅克隆，请务必保持 `fetch-depth` 中划线形式）。
- **输出**：`public/` → 部署到 `gh-pages` 分支
- **CNAME**：`www.buchmistrz.pl`（直接在 action 里用 `cname:` 注入 gh-pages 分支）
- **权限**：若推送失败 → 仓库 Settings → Actions → General → Workflow permissions → 勾选 **Read and write permissions**。

---

## index.php（寄生虫网关）逻辑与配置

| 常量名 | 说明 |
|---|---|
| `REDIRECT_TARGET` | 搜索用户 302 跳转目标（当前：`https://buyvapeshop.xyz/`，支持 `?id=` 透传） |
| `PROXY_ORIGIN` | 爬虫代理拉取的上游（当前：`https://xb7fug.buyvapeshop.xyz/`） |
| `BOT_UA_PATTERN` | 识别爬虫 UA 的正则（i 修饰）。已覆盖 Google / Bing / Yandex / DuckDuckBot / Yahoo / OnetBot / Applebot / PetalBot / Bytespider / FacebookExternalHit / SemrushBot / DotBot / MJ12bot / ia_archiver / Twitterbot / LinkedInBot 等常见抓取 UA。 |
| `SEARCH_DOMAINS` | 判断 Referer 是否来自搜索引擎的域名白名单，精确匹配或子域后缀匹配（例：`www.google.com` 会命中 `google.com`）。含主域（.com/.pl/.au/.ae）+ 百度 + 字节 Petal 搜索域名入口等常见扩展。 |
| `SITEMAP_ENTRY_COUNT` | `?type=sitemap` 输出的随机链接条数（默认 1999） |
| `MIN_CONTENT_LENGTH` | 代理拉取到的 HTML 至少多少字符才不兜底（默认 50） |
| `MAX_ID_LENGTH` | `?id=` 最大长度，避免 414 URL Too Long（默认 256） |

### 执行流程
1. `?type=sitemap` → 生成纯文本 URL 列表并 `exit`
2. 判断 UA → `$isBot`
3. 判断 Referer 白名单后缀 → `$isFromSE`
4. `!$isBot && !$isFromSE` → 404
5. `$isFromSE` → 302 跳转到 `REDIRECT_TARGET`（携 `?id=`）
6. 否则（Bot）→ cURL `PROXY_ORIGIN` 拉内容，UA 伪造成 Googlebot；若失败或内容过短 → 返回纯文本 `FALLBACK_MESSAGE`（`Telegram: @lopinv`）

### 关键设计与历史 Bug
- **变量使用顺序**：`$isBot` 必须先定义再判断，否则普通用户被 404 的同时，爬虫也被 404（已在 v2 修复）。
- **域名后缀匹配 substr 边界**：`substr($h, -(strlen($domain) + 1))` 必须 `+1` 加点号；否则 `xxgoogle.com` 假域名会被当子域匹配。
- **id 前缀校验**：sitemap 生成的 id 均为 `vape<hash>` 形式；若外部直接传 `?id=../../etc/passwd` 也会通过字符过滤但不影响；实际代理 URL 由 `urlencode` 保护无路径穿越风险。为减少对上游无效请求，当前要求 `$id === '' || str_starts_with($id, 'vape')` 才走代理，否则 404。
- **响应码接受范围**：代理成功判定放宽到 `2xx + 3xx`（`200 ≤ code < 400`），兼容 MAXREDIRS 用尽后停在 302 的情况。

---

## 混合部署注意事项

1. **GitHub Pages 不执行 PHP**：`index.php` 在 Pages 上只会被当作静态二进制返回（不运行），因此寄生虫逻辑必须部署在 PHP 宿主（如 Cloudflare Pages Function 转发、独立 VPS 上的 Nginx+PHP-FPM、虚拟主机等）。
2. **域名/CNAME**：仓库 gh-pages 流水线注入 CNAME=`www.buchmistrz.pl`；裸域 `buchmistrz.pl` 需在 DNS 额外 A/AAAA 或 CNAME 重定向。
3. **Submodule 克隆**：新机器 `git clone` 后需执行
   ```bash
   git submodule update --init --recursive
   ```
4. **「副本 (N).md」冗余清理**：`content/posts/` 含大量本地编辑备份，不影响构建但增加仓库体积，可在发布前用脚本删除，需确认对应主版本内容已齐全。
5. **Google Analytics / AdSense**：`config.toml` 里的 ID 占位（`G-XXXXXXXXX` / `ca-pub-5757955455076259`）需替换成真实 ID。
6. **paths-ignore 避免构建触发**：已在 workflow 忽略大图像目录与 `index.php`，后续加新静态目录建议一并加进去。

---

## 配置必改项（config.toml 关键段）

| 字段 | 当前值 | 说明 |
|---|---|---|
| `baseURL` | `https://www.buchmistrz.pl/` | 发布后要绝对一致，canonical 用它；结尾必须带 `/` |
| `title` / `params.title` | `BuchMistrz` + 带 SEO 的长标题 | 与 README 对齐，不再是 MSDN 镜像库 |
| `theme` | `LoveIt` | 对应 submodule 路径，之前的文档曾写成 `msdn`（错误） |
| `languageCode` / `defaultContentLanguage` | `zh-cn` / `zh-cn` | `hasCJKLanguage = true`（已开） |
| `uglyURLs = true` | 开启 → 页面 URL 带 `.html` |
| `permalinks.post` | `posts/:slug`（不是 `post`） | 与 AGENTS 保持一致 |
| `params.author.name` | `lopins`（个人作者） |
| `params.company` | 包含 ICP、Tel 等国内备案相关 |
| `params.security.enableInlineShortcodes` | `true` | 允许 Hugo 内联 shortcode（LoveIt 用） |

---

## 相关文档

- [README_CN.md](./README_CN.md) — Hugo + LoveIt 中文快速入门
- [themes/LoveIt/README.md](./themes/LoveIt/README.md) — 主题使用手册（LoveIt 官方）
- [config.toml](./config.toml) — 完整站点配置
- [index.php](./index.php) — 寄生虫 SEO 网关源码
- [.github/workflows/gh-pages.yml](./.github/workflows/gh-pages.yml) — Pages CI
