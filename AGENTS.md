# AGENTS.md — Hugo 模板项目工作区说明

## 项目概述

这是一个基于 **Hugo** 的静态站点模板，当前部署为 **MSDN 镜像库**（msdn.lopins.cn），提供 Windows / Office 等微软官方原版镜像下载。部署方式：GitHub Pages（gh-pages 分支）。

**框架**：Hugo（非 Hexo / WordPress 等）  
**语言**：中文（`defaultContentLanguage = "zh-cn"`，`hasCJKLanguage = true`）

---

## 目录结构

```
hugo-template/
├── config.toml          # 站点主配置（必改项：baseURL、theme、菜单、params）
├── content/             # 所有 Markdown 内容
│   ├── _index.md        # 首页索引
│   ├── posts/           # 博客文章（分类：帮助中心）
│   ├── page/            # 静态页面（about、booklet、protocol、shortcuts）
│   └── soft/            # 软件下载条目（_index.md + 单篇 .md）
├── themes/msdn/         # 自定义主题（主题开发时修改此处）
│   ├── layouts/         # HTML 模板
│   ├── docs/            # 主题文档预览（不要提交）
│   └── theme.toml       # 主题元信息
├── static/              # 静态资源（CSS/JS/图片/ favicon）
│   ├── assets/css/      # 样式文件
│   ├── assets/js/       # 脚本文件
│   ├── assets/images/   # 图片资源
│   └── assets/fonts/    # 字体文件
├── archetypes/default.md # 新增文章时 Hugo 自动填充的默认 front matter
└── .github/workflows/   # CI 流水线（deploy to gh-pages）
```

---

## 构建与部署

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
- 触发：push 到 `main` 分支（忽略 images/、LICENSE、README 变更）
- 工具：`peaceiris/actions-hugo@v3`（Hugo 0.139.4 extended）
- 输出：`public/` → 部署到 `gh-pages` 分支
- CNAME：`msdn.lopins.cn`
- **注意**：工作流需要 `submodules: true`，主题通过 submodule 引入，提交时需确保 submodule 已初始化。

---

## 配置要点

| 字段 | 说明 |
|------|------|
| `baseURL` | 站点根 URL，发布前必改 |
| `theme` | 当前使用 `msdn`，切换主题需同步更新 `.gitmodules` |
| `permalinks.post` | 文章 URL 格式：`posts/:slug` |
| `permalinks.tags` | 标签页 URL：`tags/:slug` |
| `params.author.name` | 作者名，默认 `lopins` |
| `params.security.enableInlineShortcodes` | 允许内联 shortcode |

---

## 内容创作规范

### 新增文章（posts）
```bash
hugo new posts/文章标题.md
# 或手动在 content/posts/ 下新建 .md 文件
```
**Front Matter 必需字段**：
- `title` — 文章标题
- `date` — ISO 8601 格式（建议带时区）
- `draft` — 发布前设为 `false`
- `tags` / `categories` — 用于分类归档
- `thumbnail` — 缩略图 URL（影响列表页展示）
- `description` — SEO 描述

### 新增静态页面（page）
放置于 `content/page/`，Front Matter 中设置：
- `layout` — 指定使用的布局（如 `about`、`booklet`、`protocol`、`shortcuts`）
- `slug` — URL 路径（如 `about` → `/about.html`）
- `hidden` — 是否在菜单中隐藏

### 新增软件条目（soft）
放置于 `content/soft/`，使用与 posts 相同的 front matter 结构。

---

## 主题开发

主题位于 `themes/msdn/`，是 submodule。修改主题需注意：
1. 主题源码在 [zhaohuabing/hugo-theme-cleanwhite](https://github.com/zhaohuabing/hugo-theme-cleanwhite) 基础上定制
2. 布局文件在 `themes/msdn/layouts/` 下
3. 静态资源在 `static/` 目录下，由 Hugo 原样复制到 `public/`

---

## 已知注意事项

1. **Submodule 管理**：切换主题时需执行完整 submodule 操作流程（见 README_CN.md）。
2. **草稿文件**：`public/` 目录受 `.gitignore` 保护，不会被提交；构建前请确认草稿状态。
3. **搜索功能**：依赖 `index.json`（由 `_default/index.json` 布局生成），位于 `public/search/`。
4. **权限问题**：若 GitHub Actions 推送失败，需在仓库 Settings → Actions → General 中授予 `github-actions[bot]` Read & write permissions。
5. **副本文件**：`content/posts/` 中存在大量 `post-N - 副本 (X).md` 文件，为本地编辑时的备份，发布前建议清理。
6. **语言**：全站中文，Front Matter 中的字段名保持英文，内容使用中文。

---

## 相关文档

- [README_CN.md](./README_CN.md) — 中文快速入门
- [themes/msdn/README.md](./themes/msdn/README.md) — 主题说明
- [config.toml](./config.toml) — 完整站点配置
