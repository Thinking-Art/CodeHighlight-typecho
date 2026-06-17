<?php
/**
 * 代码高亮插件，支持显示行号、代码复制、语言类型显示
 * 
 * @package CodeHighlight
 * @author Thinking
 * @version 0.1
 * @link https://github.com/Thinking-Art/CodeHighlight-typecho
 */
class CodeHighlight_Plugin implements Typecho_Plugin_Interface
{
    public static function activate()
    {
        Typecho_Plugin::factory('Widget_Archive')->header = array('CodeHighlight_Plugin', 'header');
        Typecho_Plugin::factory('Widget_Archive')->footer = array('CodeHighlight_Plugin', 'footer');
        Typecho_Plugin::factory('Widget_Abstract_Contents')->contentEx = array('CodeHighlight_Plugin', 'parse');
        Typecho_Plugin::factory('Widget_Abstract_Contents')->excerptEx = array('CodeHighlight_Plugin', 'parse');
        return _t('插件已启用');
    }
    
    public static function deactivate()
    {
        return _t('插件已禁用');
    }
    
    public static function config(Typecho_Widget_Helper_Form $form)
    {
        $theme = new Typecho_Widget_Helper_Form_Element_Select(
            'theme',
            array(
                'light' => _t('Minimal Light (极简亮色)'),
                'dark' => _t('Modern Dark (现代暗色)'),
                'glass' => _t('Glassmorphism (毛玻璃风格)')
            ),
            'glass',
            _t('代码高亮主题'),
            _t('选择现代化极简风格的主题配色')
        );
        $form->addInput($theme);
        
        $showLineNumbers = new Typecho_Widget_Helper_Form_Element_Radio(
            'showLineNumbers',
            array('1' => _t('是'), '0' => _t('否')),
            '1',
            _t('显示行号'),
            _t('是否显示代码行号')
        );
        $form->addInput($showLineNumbers);
    }
    
    public static function personalConfig(Typecho_Widget_Helper_Form $form) {}
    
    public static function header()
    {
        $options = Helper::options();
        $theme = $options->plugin('CodeHighlight')->theme;
        
        echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/prismjs@1.30.0/plugins/line-numbers/prism-line-numbers.css">';
        
        $cssVars = '';
        if ($theme == 'light') {
            $cssVars = '--ch-bg: #f8fafc; --ch-border: #e2e8f0; --ch-text: #334155; --ch-keyword: #d946ef; --ch-string: #16a34a; --ch-function: #2563eb; --ch-number: #ea580c; --ch-comment: #94a3b8; --ch-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); --ch-btn-bg: #ffffff; --ch-btn-hover: #f1f5f9; --ch-radius: 8px; --ch-backdrop: none; --ch-txt-shadow: none;';
        } elseif ($theme == 'dark') {
            $cssVars = '--ch-bg: #1e1e2e; --ch-border: #313244; --ch-text: #cdd6f4; --ch-keyword: #cba6f7; --ch-string: #a6e3a1; --ch-function: #89b4fa; --ch-number: #fab387; --ch-comment: #7f849c; --ch-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3); --ch-btn-bg: #313244; --ch-btn-hover: #45475a; --ch-radius: 8px; --ch-backdrop: none; --ch-txt-shadow: none;';
        } elseif ($theme == 'glass') {
            $cssVars = '
                --ch-bg: linear-gradient(145deg, rgba(30, 35, 45, 0.75), rgba(15, 20, 30, 0.85)); 
                --ch-border: rgba(255, 255, 255, 0.15); 
                --ch-text: #f8f9fa; 
                --ch-keyword: #ff7b72; 
                --ch-string: #7ee787; 
                --ch-function: #d2a8ff; 
                --ch-number: #f2cc60; 
                --ch-comment: #a3b3c6; 
                --ch-shadow: 0 10px 30px rgba(0, 0, 0, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.15); 
                --ch-btn-bg: rgba(255, 255, 255, 0.1); 
                --ch-btn-hover: rgba(255, 255, 255, 0.25); 
                --ch-radius: 12px; 
                --ch-backdrop: blur(20px) saturate(150%);
                --ch-txt-shadow: 0 1px 3px rgba(0, 0, 0, 0.9); 
            ';
        }

        echo '<style>
            :root { ' . $cssVars . ' }
            
            body pre[class*="language-"] {
                position: relative !important;
                padding: 3.5em 1.2em 1.2em 1.2em !important;
                margin: 1.5em 0 !important;
                background: var(--ch-bg) !important;
                border: 1px solid var(--ch-border) !important;
                border-radius: var(--ch-radius) !important;
                box-shadow: var(--ch-shadow) !important;
                backdrop-filter: var(--ch-backdrop) !important;
                -webkit-backdrop-filter: var(--ch-backdrop) !important;
                overflow: auto !important;
                text-align: left !important;
            }

            /* 【终极修复 1】扩充左侧总空间至 4.5em */
            body pre[class*="language-"].line-numbers {
                padding-left: 4.5em !important; 
            }

            body pre[class*="language-"] > code {
                position: relative !important;
                display: block !important;
                margin: 0 !important;
                padding: 0 !important;
                color: var(--ch-text) !important;
                font-family: "JetBrains Mono", "Fira Code", Consolas, Monaco, monospace !important;
                font-size: 14px !important;
                line-height: 1.6 !important;
                white-space: pre !important;
                text-shadow: var(--ch-txt-shadow) !important; 
            }

            /* 【终极修复 2】行号区宽 3.5em，强制在竖线和代码间留出 1em 的空白隔离带！解决贴脸Bug */
            body pre[class*="language-"].line-numbers .line-numbers-rows {
                position: absolute !important;
                top: 0 !important;
                left: -4.5em !important; 
                width: 3.5em !important; 
                padding: 0 !important; 
                margin: 0 !important;
                border-right: 1px solid var(--ch-border) !important;
                box-sizing: border-box !important; 
                background: transparent !important;
                pointer-events: none !important;
            }

            body pre[class*="language-"].line-numbers .line-numbers-rows > span {
                display: block !important;
                counter-increment: linenumber !important;
                line-height: 1.6 !important; 
            }

            /* 【终极修复 3】数字严格右对齐，且距离竖线保留 0.8em 的优美间距 */
            body pre[class*="language-"].line-numbers .line-numbers-rows > span:before {
                content: counter(linenumber) !important;
                color: var(--ch-comment) !important;
                display: block !important;
                text-align: right !important; 
                padding-right: 0.8em !important; 
                text-shadow: none !important; 
            }

            /* Token 词法高亮颜色 */
            body code[class*="language-"] .token.comment, body code[class*="language-"] .token.prolog { color: var(--ch-comment) !important; font-style: italic !important; }
            body code[class*="language-"] .token.punctuation { color: var(--ch-text) !important; opacity: 0.7 !important; }
            body code[class*="language-"] .token.property, body code[class*="language-"] .token.tag, body code[class*="language-"] .token.constant { color: var(--ch-keyword) !important; }
            body code[class*="language-"] .token.boolean, body code[class*="language-"] .token.number { color: var(--ch-number) !important; }
            body code[class*="language-"] .token.selector, body code[class*="language-"] .token.attr-name, body code[class*="language-"] .token.string, body code[class*="language-"] .token.builtin { color: var(--ch-string) !important; }
            body code[class*="language-"] .token.operator, body code[class*="language-"] .token.entity, body code[class*="language-"] .token.url { color: var(--ch-text) !important; }
            body code[class*="language-"] .token.keyword, body code[class*="language-"] .token.atrule, body code[class*="language-"] .token.attr-value { color: var(--ch-keyword) !important; }
            body code[class*="language-"] .token.function, body code[class*="language-"] .token.class-name { color: var(--ch-function) !important; }
            body code[class*="language-"] .token.regex, body code[class*="language-"] .token.variable { color: var(--ch-keyword) !important; opacity: 0.8 !important; }

            /* 顶部工具栏设计 */
            body pre[class*="language-"]::before {
                content: "" !important;
                position: absolute !important;
                top: 15px !important;
                left: 15px !important;
                width: 12px !important;
                height: 12px !important;
                border-radius: 50% !important;
                background: #fc625d !important;
                box-shadow: 20px 0 0 #fdbc40, 40px 0 0 #35cd4b !important;
                z-index: 10 !important;
            }

            /* 语言标记 */
            body .language-mark {
                position: absolute !important;
                top: 13px !important;
                left: 78px !important;
                font-size: 12px !important;
                font-family: ui-sans-serif, system-ui, sans-serif !important;
                font-weight: 700 !important;
                letter-spacing: 1px !important;
                text-transform: uppercase !important;
                color: var(--ch-comment) !important;
                z-index: 10 !important;
                user-select: none !important;
            }

            /* 复制按钮 */
            body .copy-button {
                position: absolute !important;
                top: 10px !important;
                right: 10px !important;
                padding: 4px 10px !important;
                background: var(--ch-btn-bg) !important;
                border: 1px solid var(--ch-border) !important;
                border-radius: 6px !important;
                cursor: pointer !important;
                font-size: 12px !important;
                color: var(--ch-text) !important;
                transition: all 0.2s ease !important;
                z-index: 10 !important;
                opacity: 0;
            }
            body pre[class*="language-"]:hover .copy-button { opacity: 1 !important; }
            body .copy-button:hover { background: var(--ch-btn-hover) !important; transform: translateY(-1px) !important; }
            
            /* 滚动条优化 */
            body pre[class*="language-"]::-webkit-scrollbar { width: 8px !important; height: 8px !important; }
            body pre[class*="language-"]::-webkit-scrollbar-track { background: transparent !important; }
            body pre[class*="language-"]::-webkit-scrollbar-thumb { background: rgba(136, 136, 136, 0.3) !important; border-radius: 4px !important; }
            body pre[class*="language-"]::-webkit-scrollbar-thumb:hover { background: rgba(136, 136, 136, 0.6) !important; }
        </style>';
    }
    
    public static function footer()
    {
        $options = Helper::options();
        $showLineNumbers = $options->plugin('CodeHighlight')->showLineNumbers;
        
        // 【终极修复 4】给 PrismJS 增加 data-manual 属性，阻止其自行启动，让我们有机会先清洗 DOM！
        echo '<script data-manual src="https://cdn.jsdelivr.net/npm/prismjs@1.30.0/prism.min.js"></script>';
        
        if ($showLineNumbers) {
            echo '<script src="https://cdn.jsdelivr.net/npm/prismjs@1.30.0/plugins/line-numbers/prism-line-numbers.min.js"></script>';
        }
        
        $languages = array('markup', 'css', 'clike', 'javascript', 'markup-templating', 'php', 'python', 'java', 'bash', 'sql', 'json', 'yaml');
        foreach ($languages as $lang) {
            echo '<script src="https://cdn.jsdelivr.net/npm/prismjs@1.30.0/components/prism-' . $lang . '.min.js"></script>';
        }
        
        echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll("pre code").forEach(function(block) {
                
                // 【终极修复 5】致命打击！在 Prism 渲染前，强制清除 Typecho 解析器暗中加在开头的多余换行符！解决第1行空行Bug
                block.innerHTML = block.innerHTML.replace(/^[\r\n]+/, "");
                
                var pre = block.parentNode;
                
                if (' . $showLineNumbers . ') {
                    pre.classList.add("line-numbers");
                }
                
                var language = "";
                Array.from(block.classList).forEach(function(className) {
                    if (className.startsWith("language-")) {
                        language = className.replace("language-", "");
                    }
                });
                
                if (language && language !== "none") {
                    var languageMark = document.createElement("span");
                    languageMark.className = "language-mark";
                    languageMark.textContent = language;
                    pre.appendChild(languageMark);
                }
                
                var button = document.createElement("button");
                button.className = "copy-button";
                button.textContent = "Copy";
                
                button.addEventListener("click", function() {
                    var code = block.textContent;
                    navigator.clipboard.writeText(code).then(function() {
                        button.textContent = "Copied!";
                        button.style.color = "#10b981";
                        setTimeout(function() {
                            button.textContent = "Copy";
                            button.style.color = "";
                        }, 2000);
                    }).catch(function() {
                        button.textContent = "Failed";
                        button.style.color = "#ef4444";
                    });
                });
                
                pre.appendChild(button);
            });
            
            // 所有 DOM 清洗、预处理工作完毕后，手动召唤 Prism 渲染，彻底告别双重渲染和错乱！
            Prism.highlightAll();
        });
        </script>';
    }
    
    public static function parse($text, $widget)
    {
        $text = preg_replace_callback('/```([\w-]+)?\n(.*?)\n```/s', function($matches) {
            $language = empty($matches[1]) ? 'none' : $matches[1];
            $code = trim($matches[2]);
            $code = htmlspecialchars($code, ENT_QUOTES, 'UTF-8');
            return sprintf('<pre class="line-numbers"><code class="language-%s">%s</code></pre>', htmlspecialchars($language, ENT_QUOTES, 'UTF-8'), $code);
        }, $text);

        $text = preg_replace_callback('/`(.*?)`/', function($matches) {
            $code = htmlspecialchars($matches[1], ENT_QUOTES, 'UTF-8');
            return sprintf('<code class="language-none">%s</code>', $code);
        }, $text);

        return $text;
    }
}