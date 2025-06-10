<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta lang="en">
    <meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - {{ config('sys.web.name') }}</title>
    <!-- MDUI CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdui/1.0.2/css/mdui.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="/js/layer-fixes.css">
    <style>
        :root {
            --primary-color: #2196f3;
            --accent-color: #ff4081;
            --background-color: #121212;
            --card-color: #1e1e1e;
            --border-radius: 8px;
            --text-primary: #e0e0e0;
            --text-secondary: #a0a0a0;
            --drawer-width: 240px;
            --header-height: 56px;
            --content-padding: 20px;
            --content-max-width: 1200px;
            --transition-speed: 0.3s;
            --shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }
        
        body {
            background-color: var(--background-color);
            transition: all var(--transition-speed);
            color: var(--text-primary);
        }
        
        .mdui-appbar .mdui-toolbar {
            height: var(--header-height);
            padding: 0 16px;
            box-shadow: var(--shadow);
            background-color: #242424;
        }
        
        .mdui-toolbar .mdui-typo-headline {
            font-size: 18px;
            font-weight: 500;
        }
        
        .mdui-drawer {
            background-color: #1a1a1a;
            width: var(--drawer-width);
            border-right: none;
            box-shadow: var(--shadow);
            transition: all var(--transition-speed);
        }
        
        .user-info {
            padding: 24px 16px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            background-color: rgba(33, 150, 243, 0.08);
            margin-bottom: 8px;
        }
        
        .user-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            margin-right: 16px;
            background-color: var(--primary-color);
            color: white;
            line-height: 42px;
            text-align: center;
            font-size: 18px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        
        .user-name {
            font-size: 16px;
            font-weight: 500;
            line-height: 1.4;
            margin-bottom: 4px;
        }
        
        .user-role {
            font-size: 13px;
            color: var(--text-secondary);
        }
        
        .mdui-list {
            padding: 8px;
        }
        
        .mdui-list-item {
            border-radius: var(--border-radius);
            margin-bottom: 4px;
            transition: all var(--transition-speed);
            height: 48px;
        }
        
        .mdui-list-item-icon {
            color: var(--text-secondary);
            transition: all var(--transition-speed);
        }
        
        .mdui-list-item-active {
            color: var(--primary-color);
            background-color: rgba(33, 150, 243, 0.1);
        }
        
        .mdui-list-item-active .mdui-list-item-icon {
            color: var(--primary-color);
        }
        
        .mdui-list-item:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }
        
        .main-content {
            padding: var(--content-padding);
            margin-top: var(--header-height);
            max-width: var(--content-max-width);
            width: 100%;
            margin-left: auto;
            margin-right: auto;
            transition: all var(--transition-speed);
            box-sizing: border-box;
        }
        
        @media (min-width: 1024px) {
            .main-content {
                margin-left: var(--drawer-width);
                width: calc(100% - var(--drawer-width));
                max-width: calc(1000px + var(--drawer-width));
                padding-left: 40px;
                padding-right: 40px;
            }
            
            .mdui-drawer-body-left .main-content {
                padding-left: 40px;
            }
        }
        
        @media (min-width: 1400px) {
            .main-content {
                margin-left: auto;
                margin-right: auto;
                padding-left: 40px;
                padding-right: 40px;
                max-width: 1200px;
                left: calc(var(--drawer-width) / 2);
                position: relative;
            }
        }
        
        .user-dropdown {
            min-width: 200px;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .mdui-menu-item a {
            padding: 0 16px;
            height: 48px;
            line-height: 48px;
            color: var(--text-primary);
        }
        
        .mdui-menu-item a:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }
        
        .mdui-menu-item a i {
            color: var(--text-secondary);
            margin-right: 16px;
        }
        
        .mdui-card {
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-bottom: 24px;
            overflow: hidden;
            background-color: var(--card-color);
        }
        
        .mdui-card-primary {
            padding: 20px 20px 12px;
        }
        
        .mdui-card-content {
            padding: 12px 20px 20px;
        }
        
        .mdui-card-actions {
            padding: 8px 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }
        
        .mdui-card-primary-title {
            font-size: 18px;
            line-height: 1.4;
            font-weight: 500;
            margin-bottom: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .mdui-card-primary-subtitle {
            font-size: 14px;
            color: var(--text-secondary);
        }
        
        .mdui-textfield-input {
            border-bottom-color: rgba(255, 255, 255, 0.15);
            color: var(--text-primary);
        }
        
        .mdui-textfield-focus .mdui-textfield-input {
            border-bottom-color: var(--primary-color);
        }
        
        .mdui-textfield-focus .mdui-textfield-label {
            color: var(--primary-color);
        }
        
        .mdui-btn {
            border-radius: 4px;
            text-transform: none;
            font-weight: 500;
            letter-spacing: 0.3px;
        }
        
        .mdui-btn-raised {
            box-shadow: 0 3px 5px rgba(0, 0, 0, 0.15);
        }
        
        .mdui-table-fluid {
            background-color: var(--card-color);
            border-radius: var(--border-radius);
            overflow: hidden;
        }
        
        /* 使表格在移动设备上可以水平滚动 */
        @media (max-width: 768px) {
            .mdui-table-fluid {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            .mdui-table {
                min-width: 800px; /* 确保表格足够宽，以便在移动设备上可以滚动查看 */
            }
        }
        
        .mdui-table {
            background-color: transparent;
        }
        
        .mdui-table th {
            background-color: rgba(255, 255, 255, 0.05);
            color: var(--text-primary);
        }
        
        .mdui-table td {
            color: var(--text-primary);
        }
        
        .mdui-table tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.03);
        }
        
        .mdui-select {
            color: var(--text-primary);
        }
        
        .mdui-select-menu {
            background-color: #242424;
            color: var(--text-primary);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }
        
        .mdui-select-menu-item {
            color: var(--text-primary);
        }
        
        .mdui-select-menu-item:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }
        
        .mdui-dialog {
            background-color: var(--card-color);
            color: var(--text-primary);
            border-radius: var(--border-radius);
        }
        
        .mdui-dialog-title {
            color: var(--text-primary);
        }
        
        .mdui-dialog-content {
            color: var(--text-primary);
        }
        
        .mdui-panel-item {
            background-color: var(--card-color);
            color: var(--text-primary);
            border-radius: var(--border-radius);
            margin-bottom: 8px;
        }
        
        .mdui-panel-item-header {
            color: var(--text-primary);
        }
        
        .mdui-panel-item-body {
            color: var(--text-primary);
        }
        
        .mdui-menu {
            background-color: #242424;
            color: var(--text-primary);
        }
    </style>
    @yield('head')
</head>
<body class="mdui-theme-primary-blue mdui-theme-accent-pink mdui-drawer-body-left mdui-appbar-with-toolbar mdui-theme-layout-dark">
    <!-- 顶部应用栏 -->
    <header class="mdui-appbar mdui-appbar-fixed">
        <div class="mdui-toolbar mdui-color-theme">
            <span class="mdui-btn mdui-btn-icon mdui-ripple" mdui-drawer="{target: '#main-drawer', swipe: true}">
                <i class="mdui-icon material-icons">menu</i>
            </span>
            <a href="/" class="mdui-typo-headline" style="display: flex; align-items: center; padding: 4px 0;">
                <img src="/logo.png" alt="OSFC Registry" style="height: 36px; margin-right: 8px;">
                {{ config('sys.web.name') }}
            </a>
            <div class="mdui-toolbar-spacer"></div>
            <div class="mdui-btn mdui-btn-icon mdui-ripple" mdui-menu="{target: '#user-menu'}" title="User menu">
                <i class="mdui-icon material-icons">account_circle</i>
            </div>
            <ul class="mdui-menu user-dropdown" id="user-menu">
                <li class="mdui-menu-item">
                    <a href="/home" class="mdui-ripple">
                        <i class="mdui-menu-item-icon mdui-icon material-icons">home</i>
                        Dashboard
                    </a>
                </li>
                <li class="mdui-menu-item">
                    <a href="/home/profile" class="mdui-ripple">
                        <i class="mdui-menu-item-icon mdui-icon material-icons">person</i>
                        Profile
                    </a>
                </li>
                <li class="mdui-divider"></li>
                <li class="mdui-menu-item">
                    <a href="/logout" class="mdui-ripple" onclick="return confirm('Confirm logout?');">
                        <i class="mdui-menu-item-icon mdui-icon material-icons">exit_to_app</i>
                        Logout
                    </a>
                </li>
            </ul>
        </div>
    </header>

    <!-- 侧边栏 -->
    <div class="mdui-drawer" id="main-drawer">
        <div class="user-info">
            <div class="user-avatar">{{ substr(auth()->user()->username, 0, 1) }}</div>
            <div>
                <div class="user-name">{{ auth()->user()->username }}</div>
                <div class="user-role">{{ auth()->user()->group ? auth()->user()->group->name : 'User' }}</div>
                <div class="user-points" style="display: flex; align-items: center; margin-top: 4px; font-size: 12px;">
                    <i class="mdui-icon material-icons" style="font-size: 14px; margin-right: 4px; color: var(--accent-color);">monetization_on</i>
                    <span>{{ auth()->user()->point }} Points</span>
                </div>
                <div class="user-id" style="font-size: 12px; margin-top: 4px; opacity: 0.7;">ID: {{ auth()->user()->uid }}</div>
            </div>
        </div>
        <div class="mdui-list" mdui-collapse="{accordion: true}">
            <a href="/home" class="mdui-list-item mdui-ripple {{ request()->is('home') ? 'mdui-list-item-active' : '' }}">
                <i class="mdui-list-item-icon mdui-icon material-icons">dns</i>
                <div class="mdui-list-item-content">Record List</div>
            </a>
            <a href="/home/point" class="mdui-list-item mdui-ripple {{ request()->is('home/point') ? 'mdui-list-item-active' : '' }}">
                <i class="mdui-list-item-icon mdui-icon material-icons">monetization_on</i>
                <div class="mdui-list-item-content">Point Details</div>
            </a>
            <a href="/home/profile" class="mdui-list-item mdui-ripple {{ request()->is('home/profile') ? 'mdui-list-item-active' : '' }}">
                <i class="mdui-list-item-icon mdui-icon material-icons">settings</i>
                <div class="mdui-list-item-content">Profile Settings</div>
            </a>
            @if(config('sys.invite.enabled', 0) == 1)
            <a href="/home/invite" class="mdui-list-item mdui-ripple {{ request()->is('home/invite') ? 'mdui-list-item-active' : '' }}">
                <i class="mdui-list-item-icon mdui-icon material-icons">people</i>
                <div class="mdui-list-item-content">Invitation</div>
            </a>
            @endif
            <div class="mdui-divider" style="margin: 8px 16px;"></div>
            <a href="/" class="mdui-list-item mdui-ripple">
                <i class="mdui-list-item-icon mdui-icon material-icons">public</i>
                <div class="mdui-list-item-content">Main Website</div>
            </a>
        </div>
    </div>

    <!-- 主要内容 -->
    <div class="main-content">
        @yield('content')
    </div>

    <!-- MDUI JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdui/1.0.2/js/mdui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.10/vue.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/layer/2.3/layer.js"></script>
    <script src="/js/main.js"></script>
    <script>
        // 初始化MDUI组件
        mdui.mutation();
        
        // 设置layer弹窗配置
        layer.config({
            skin: 'layui-layer-molv',
            btnAlign: 'c',
            title: 'Message',
            closeBtn: 1,
            btn: ['OK'],
            yes: function(index, layero) {
                layer.close(index);
            }
        });
        
        // 为弹窗添加自定义样式，确保文本可见
        document.head.insertAdjacentHTML('beforeend', `
            <style>
                .layui-layer {
                    background-color: #242424 !important;
                    color: var(--text-primary) !important;
                    border-radius: var(--border-radius) !important;
                    overflow: hidden !important;
                }
                .layui-layer-title {
                    background-color: rgba(255, 255, 255, 0.05) !important;
                    color: var(--text-primary) !important;
                    border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
                    font-weight: 500 !important;
                }
                .layui-layer-btn {
                    border-top: 1px solid rgba(255, 255, 255, 0.08) !important;
                    background-color: rgba(255, 255, 255, 0.02) !important;
                }
                .layui-layer-btn a {
                    background-color: var(--primary-color) !important;
                    color: white !important;
                    border-color: var(--primary-color) !important;
                    border-radius: 4px !important;
                }
                .layui-layer-btn .layui-layer-btn1 {
                    background-color: transparent !important;
                    color: var(--text-primary) !important;
                    border: 1px solid rgba(255, 255, 255, 0.15) !important;
                }
                .layui-layer-content {
                    color: var(--text-primary) !important;
                }
                .layui-layer-close {
                    color: var(--text-secondary) !important;
                }
            </style>
        `);
    </script>
    @yield('foot')
</body>
</html> 