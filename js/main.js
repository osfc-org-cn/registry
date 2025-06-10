window.$_GET = function (name) {
    return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.href) || [, ""])[1].replace(/\+/g, '%20')) || '';
};

// 立即执行函数，在脚本加载时就重写layer方法，不等待DOMContentLoaded
(function() {
    // 创建强制覆盖样式，防止任何弹窗显示按钮
    var style = document.createElement('style');
    style.textContent = `
        /* 强制隐藏所有加载层的按钮 */
        .layui-layer-loading .layui-layer-btn,
        .layui-layer-loading.layui-layer .layui-layer-btn,
        div[type="loading"] .layui-layer-btn,
        div[type="loading"].layui-layer .layui-layer-btn {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            height: 0 !important;
            overflow: hidden !important;
            position: absolute !important;
            z-index: -1 !important;
        }
        
        /* 强制设置加载层的样式 */
        .layui-layer-loading {
            background-color: transparent !important;
            box-shadow: none !important;
            border: none !important;
            min-width: auto !important;
        }
        
        .layui-layer-loading .layui-layer-content {
            width: 60px !important;
            height: 60px !important;
            background-color: rgba(0, 0, 0, 0.7) !important;
            border-radius: 5px !important;
            overflow: hidden !important;
        }
        
        /* 修复z-index问题 */
        #layui-layer-shade0, 
        #layui-layer-shade1, 
        #layui-layer-shade2,
        #layui-layer-shade3,
        #layui-layer-shade4,
        #layui-layer-shade5 {
            z-index: 19891014 !important;
        }
        
        /* 确保加载层在最上层 */
        .layui-layer-loading,
        div[type="loading"].layui-layer {
            z-index: 19891015 !important;
        }
        
        /* 修复按钮问题 */
        .layui-layer-btn {
            min-height: 0 !important;
        }
    `;
    document.head.appendChild(style);
    
    // 当页面加载完成后重写layer方法
    window.addEventListener('load', function() {
        if (typeof layer !== 'undefined') {
            // 保存原始load方法
            var originalLoad = layer.load;
            
            // 重写load方法
            layer.load = function(icon, options) {
                // 处理参数
                if (typeof icon === 'object') {
                    options = icon;
                    icon = 0;
                }
                
                // 强制设置参数，防止任何情况下显示按钮
                options = options || {};
                options.type = 3; // 纯图标样式
                options.icon = icon || 0;
                options.title = false;
                options.closeBtn = 0;
                options.btn = null;
                options.btnAlign = null;
                options.yes = null;
                options.shade = options.shade || [0.3, '#000'];
                options.shadeClose = false;
                options.skin = 'layui-layer-loading';
                options.area = ['auto', 'auto'];
                options.offset = 'auto';
                options.time = options.time || 0;
                options.anim = options.anim || 0;
                options.isOutAnim = true;
                options.maxmin = false;
                options.fixed = true;
                options.resize = false;
                options.scrollbar = false;
                options.maxWidth = 60;
                options.maxHeight = 60;
                
                // 调用原始方法
                var index = originalLoad.call(layer, icon, options);
                
                // 立即执行DOM操作，强制移除按钮
                setTimeout(function() {
                    var layerElem = document.getElementById('layui-layer' + index);
                    if (layerElem) {
                        // 移除按钮区域
                        var btnElem = layerElem.querySelector('.layui-layer-btn');
                        if (btnElem) {
                            btnElem.style.display = 'none';
                            btnElem.innerHTML = '';
                        }
                        
                        // 设置样式强制隐藏任何可能的按钮
                        layerElem.style.backgroundColor = 'transparent';
                        layerElem.style.boxShadow = 'none';
                        layerElem.style.border = 'none';
                        
                        // 移除可能的点击事件处理函数
                        layerElem.onclick = null;
                        
                        // 设置内容区域
                        var contentElem = layerElem.querySelector('.layui-layer-content');
                        if (contentElem) {
                            contentElem.style.width = '60px';
                            contentElem.style.height = '60px';
                            contentElem.style.backgroundColor = 'rgba(0, 0, 0, 0.7)';
                            contentElem.style.borderRadius = '5px';
                        }
                        
                        // 插入覆盖层，防止任何按钮出现
                        var overlay = document.createElement('div');
                        overlay.style.position = 'absolute';
                        overlay.style.top = '0';
                        overlay.style.left = '0';
                        overlay.style.width = '100%';
                        overlay.style.height = '100%';
                        overlay.style.backgroundColor = 'transparent';
                        overlay.style.zIndex = '9999';
                        layerElem.appendChild(overlay);
                    }
                }, 0);
                
                return index;
            };
        }
    });
})();

window.$post = function (url, params1, params2, func) {
    var str = '';
    if (typeof (params1) === 'object') {
        for (var k in params1) {
            str += k + '=' + params1[k] + '&'
        }
    } else if (typeof (params1) === 'string') {
        str += params1 + '&'
    }
    if (typeof (params2) === 'object') {
        for (var k in params2) {
            str += k + '=' + params2[k] + '&'
        }
    } else if (typeof (params2) === 'string') {
        str += params2
    }
    var load;
    return $.ajax({
        type: "POST",
        url: url,
        data: (params1 instanceof FormData) ? params1 : str,
        beforeSend: function (request) {
            var token = document.head.querySelector('meta[name="csrf-token"]');
            if (token) {
                request.setRequestHeader("X-CSRF-TOKEN", token.content);
            } else {
                console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
            }
            load = layer.load(0, {
                shade: [0.3, '#000'],
                shadeClose: false
            });
        },
        error: function (request) {
            if (request.status === 419) {
                layer.alert('Page expired, please refresh the page!', {
                    closeBtn: 0
                }, function (i) {
                    window.location.reload();
                });
            } else {
                layer.close(load);
                layer.alert('Network error, please try again later!' + request.status + ' ' + request.statusText);
            }
        },
        success: function (ret) {
            layer.close(load);
        }
    });
};
Vue.prototype.$post = window.$post;

Vue.prototype.$message = function (message, type) {
    layer.alert(message);
};