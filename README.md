<div align="right">
   <a href="README_CN.md">中文</a> | <strong>English</strong>
</div>

<br/>

<div align="center">
  <img src="https://cdn.fosstodon.org/accounts/avatars/111/137/925/464/599/407/original/5a723b0c04db75a8.png" alt="Hugo logo" width="120" height="120" />
  <h1>Hugo Template</h1>
  <p>A fast and flexible static site generator built with love by bep, spf13, and friends in <a href="https://go.dev/">Go</a>.</p>
</div>

<br/>

<div align="center">
  <a href="https://themes.gohugo.io"><kbd>Demo</kbd></a>
  &nbsp;·&nbsp;
  <a href="https://gohugo.io"><kbd>Official</kbd></a>
  &nbsp;·&nbsp;
  <a href="https://gohugo.io/documentation/"><kbd>Docs</kbd></a>
  &nbsp;·&nbsp;
  <a href="https://discourse.gohugo.io"><kbd>Community</kbd></a>
</div>

<br/>

---

## 🚀 Get Started

### 1. Define Domain

Change the `baseURL` in `config.toml` to your site's URL:

```toml
baseURL = "https://lopins.github.io/hugo-template"
```

### 2. Configure Theme

<details>
<summary>Click to expand theme configuration steps</summary>

**Step 1 — Add the theme as a submodule**

```bash
git submodule add https://github.com/dillonzq/LoveIt.git themes/LoveIt
git submodule update --init --recursive
cp -r exampleSite/* ../
```

**Step 2 — Set the theme in config**

In `config.toml`, change the `theme` property:

```toml
theme = "LoveIt"
```

</details>

### 3. Push to GitHub

```bash
git add . && git commit -m "🔧 docs(themes): Add or update configuration files" && git push origin master
```

---

## ✍️ Publish Articles

<details>
<summary>Click to expand article publishing guide</summary>

**1. Install**

Click **"Use this template"** → **"Create a new repository"** to fork this template.

![1. Create a new repository](https://github.com/user-attachments/assets/1046f118-8c2a-4ed8-bc8b-1258941455c4)

**2. Set up GitHub Pages**

Go to **Settings → Pages** and select the `gh-pages` branch as the source.

![2. Set up GitHub Pages branch](https://github.com/user-attachments/assets/acd90bfd-0a25-4809-a39b-fc5d562f414b)

**3. Publish an article**

Add a new Markdown file under the `content/posts/` directory, then push to the `main` branch. GitHub Actions will build and deploy automatically.

![3. Write or Upload a Markdown file](https://github.com/user-attachments/assets/2b1a97ea-ac79-4647-a340-f71569699c11)

</details>

---

## ⚠️ Notice

### Submodule Management

```bash
# ── Add / Update Submodule ──────────────────────────────────────
git submodule add https://github.com/dillonzq/LoveIt.git themes/LoveIt
git submodule update --init --recursive

# ── Delete Submodule ────────────────────────────────────────────
git submodule deinit -f themes/LoveIt
rm -rf .git/modules/themes/LoveIt
rm -rf themes/LoveIt
git config --remove-section submodule.themes/LoveIt
git config -f .gitmodules --remove-section submodule.themes/LoveIt
git rm --cached themes/LoveIt
git add . && git commit -m 'Update .gitmodules before removing submodule'

# ── View / Pull / Sync Submodule ────────────────────────────────
git submodule status
git submodule foreach git pull origin master
git submodule sync
```

### Permission Issues

If you encounter the error:

> `remote: Permission to xxx denied to github-actions[bot].`

Grant write permissions to `github-actions[bot]` in your repository:

> **Settings → Actions → General → Workflow permissions → Read and write permissions → Save**

---

## 📄 License

[MIT License](LICENSE)
