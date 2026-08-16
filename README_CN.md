<div align="right">
   <strong>中文</strong> | <a href="README.md">English</a>
</div>

<br/>

<div align="center">
  <img src="https://cdn.fosstodon.org/accounts/avatars/111/137/925/464/599/407/original/5a723b0c04db75a8.png" alt="Hugo logo" width="120" height="120" />
  <h1>Hugo Template</h1>
  <p>一个由 bep、spf13 和其好友基于 <a href="https://go.dev/">Go</a> 构建的快速灵活的静态站点生成器框架。这是它的模板。</p>
</div>

<br/>

<div align="center">
  <a href="https://themes.gohugo.io"><kbd>演示</kbd></a>
  &nbsp;·&nbsp;
  <a href="https://gohugo.io"><kbd>官网</kbd></a>
  &nbsp;·&nbsp;
  <a href="https://gohugo.io/documentation/"><kbd>文档</kbd></a>
  &nbsp;·&nbsp;
  <a href="https://discourse.gohugo.io"><kbd>社区</kbd></a>
</div>

<br/>

---

## 🚀 快速开始

### 1. 修改域名

修改 `config.toml` 中的 `baseURL` 值：

```toml
baseURL = "https://lopins.github.io/hugo-template"
```

### 2. 配置主题

<details>
<summary>点击展开主题配置步骤</summary>

**第一步 — 添加主题作为 submodule**

```bash
git submodule add https://github.com/dillonzq/LoveIt.git themes/LoveIt
git submodule update --init --recursive
cp -r exampleSite/* ../
```

**第二步 — 在配置中启用主题**

在 `config.toml` 中修改 `theme` 属性：

```toml
theme = "LoveIt"
```

</details>

### 3. 推送到 GitHub

```bash
git add . && git commit -m "🔧 docs(themes): 添加或更新配置文件" && git push origin master
```

---

## ✍️ 发布文章

<details>
<summary>点击展开文章发布指南</summary>

**1. 安装（创建仓库）**

点击 **"Use this template"** → **"Create a new repository"** 来基于此模板创建新仓库。

![1. Create a new repository](https://github.com/user-attachments/assets/1046f118-8c2a-4ed8-bc8b-1258941455c4)

**2. 配置 GitHub Pages**

进入 **Settings → Pages**，选择 `gh-pages` 分支作为部署源。

![2. Set up GitHub Pages branch](https://github.com/user-attachments/assets/acd90bfd-0a25-4809-a39b-fc5d562f414b)

**3. 发布文章**

在 `content/posts/` 目录下添加新的 Markdown 文件，然后推送到 `main` 分支。GitHub Actions 将自动构建并部署。

![3. Write or Upload a Markdown file](https://github.com/user-attachments/assets/2b1a97ea-ac79-4647-a340-f71569699c11)

</details>

---

## ⚠️ 特别注意

### 使用 Submodule 切换主题

```bash
# ── 添加 / 更新 Submodule ───────────────────────────────────────
git submodule add https://github.com/dillonzq/LoveIt.git themes/LoveIt
git submodule update --init --recursive

# ── 删除 Submodule ──────────────────────────────────────────────
git submodule deinit -f themes/LoveIt
rm -rf .git/modules/themes/LoveIt
rm -rf themes/LoveIt
git config --remove-section submodule.themes/LoveIt
git config -f .gitmodules --remove-section submodule.themes/LoveIt
git rm --cached themes/LoveIt
git add . && git commit -m 'Update .gitmodules before removing submodule'

# ── 查看 / 拉取 / 同步 Submodule ────────────────────────────────
git submodule status
git submodule foreach git pull origin master
git submodule sync
```

### 权限问题

如果遇到类似以下错误：

> `remote: Permission to xxx denied to github-actions[bot].`

请为仓库中的 `github-actions[bot]` 授予写入权限：

> **Settings → Actions → General → Workflow permissions → Read and write permissions → Save**

---

## 📄 开源协议

[MIT License](LICENSE)
