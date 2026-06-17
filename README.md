# 🌟 Typecho 代码高亮插件 (CodeHighlight)

![Typecho Version](https://img.shields.io/badge/Typecho-1.2+-blue.svg)
![PrismJS Version](https://img.shields.io/badge/Prism.js-1.30.0-green.svg)
![License](https://img.shields.io/badge/License-MIT-blue.svg)

一个现代、优雅且功能丰富的 Typecho 代码高亮插件。基于强大的 **Prism.js** 核心驱动，提供智能语法解析，并集成了极致美观的现代化主题、完美对齐的行号显示以及一键代码复制等增强特性。

---

## ✨ 核心特性

- 💡 **智能语法高亮**
  - 基于最新版 Prism.js (v1.30.0) 引擎构建
  - 原生支持 10+ 种常用编程语言（PHP、JavaScript、Python、Java、HTML、CSS、Bash 等）
  - 自动识别并渲染代码片段
- 🎨 **现代化精美主题**
  - **Minimal Light (极简亮色)**：清爽干净，适合各类常规博客主题。
  - **Modern Dark (现代暗色)**：护眼深色模式，充满极客质感。
  - **Glassmorphism (毛玻璃风格)**：结合渐变与高斯模糊的超现代拟物风，视觉效果拉满！
- 📑 **完美的行号显示**
  - 可在后台随时开启/关闭行号
  - **终极修复**：重写底层 CSS 逻辑，行号与代码完全隔离排版，彻底解决「行号贴脸」、「错位」等历史遗留问题！
- 📋 **交互式快捷复制**
  - 悬浮式「一键复制」按钮（Hover 时优雅出现）
  - 复制成功/失败实时状态反馈机制
  - 采用现代 Clipboard API，兼容主流浏览器
- 🏷️ **Mac 风格顶部栏 & 语言标记**
  - 注入类似 macOS 窗口的「红黄绿」控制台圆点 UI，极具高级感。
  - 左上角自动提取并展示当前代码块语言类型。

---

## 🚀 安装方法

1. **下载插件**
   下载本仓库的最新版本压缩包。
2. **解压与上传**
   将解压后的文件夹重命名为 `CodeHighlight`（注意大小写必须完全一致）。
   然后将该目录上传至您 Typecho 站点的 `/usr/plugins/` 目录下。
3. **启用插件**
   登录 Typecho 后台，进入 **控制台 -> 插件**，找到 `CodeHighlight`，点击 **启用**。

---

## ⚙️ 后台配置

插件启用后，点击「设置」按钮即可进行个性化调整：

1. **代码高亮主题**：下拉选择您喜欢的现代化主题（Light / Dark / Glass）。切换后实时应用于前台所有代码块。
2. **显示行号**：一键开启或关闭行号显示。

---

## 📝 文章编写指南

### 1. 多行代码块
在撰写文章时，使用标准的 Markdown 语法（三个反引号）包裹代码，并在开头指定语言类型，即可获得完美的渲染效果：

```php
<?php
// PHP 代码示例
echo "Hello, CodeHighlight!";
?>
```

```javascript
// JavaScript 代码示例
const sayHello = () => {
  console.log("Hello, World!");
};
```

### 2. 行内代码
使用单个反引号包裹简短代码：
这是一段 `inline code` 演示。

### 3. 支持的语言标识符
为了获得最精确的高亮匹配，推荐在反引号后附加以下常用标识符之一：
`html` (或 `markup`), `css`, `clike`, `javascript` (或 `js`), `php`, `python`, `java`, `bash`, `sql`, `json`, `yaml` 等。

> **Tips**: 若未指定语言或使用不支持的标识符，代码块依然会被精美格式化渲染，只是没有具体的关键字高亮和语言标签显示。

---

## 🛠️ 技术重构说明 (开发者必看)

本次重构主要解决并优化了以下痛点：
1. **DOM 渲染清洗**：拦截 Typecho Markdown 解析器带来的首行多余 `\n` 或 `\r\n`，解决第一行常常是空行的 Bug。
2. **阻止自动执行**：向 Prism 注入 `data-manual` 属性接管其渲染生命周期，避免多重重复渲染造成的卡顿和样式错乱。
3. **行号容器重绘**：通过 CSS 绝对定位加负边距技术，为行号区域单独开辟 `3.5em` 的独立空间，并在竖线与代码之间强制留白 `1em` 隔离带，让排版严丝合缝、不再紧凑拥挤。
4. **滚动条与层级优化**：优化代码块内部滚动条（`::-webkit-scrollbar`），支持多层级 z-index 管理，确保悬浮复制按钮不被遮挡。

---

## ⚠️ 注意事项

- **CDN 依赖**：本插件前端资源静态拉取自 `jsdelivr` CDN，请确保访客网络环境可正常访问。
- **主题冲突**：如果您当前使用的主题自带代码高亮方案（如包含 highlight.js 或 其他版本的 prism.js），强烈建议在主题设置中将其关闭，以免产生样式冲突。

---

## 🤝 贡献与反馈

如果您在使用过程中遇到任何问题，或对这套代码高亮机制有更酷炫的想法，欢迎提交 Issues 或发起 Pull Requests！

### 更新日志
- 🎨 重构三大现代主题（Minimal Light / Modern Dark / Glassmorphism）
- ✨ 新增 macOS 风格视窗控制点 UI
- 🚀 优化了复制按钮反馈逻辑
- 📦 将 Prism.js 升级至稳定的 v1.30.0
