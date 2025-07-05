<!doctype html>
<html lang="en">
<head>
<meta lang="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - {{ config('sys.web.name','OSFC Registry') }}</title>
    <meta name="keywords" content="{{ config('sys.web.keywords') }}"/>
    <meta name="description" content="{{ config('sys.web.description') }}"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.1/css/all.min.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">
    <style>
        :root {
            --primary: #0070c9;
            --primary-light: #147bcc;
            --primary-dark: #00529b;
            --primary-rgb: 0, 112, 201;
            --secondary: #86868b;
            --dark: #1d1d1f;
            --light: #f5f5f7;
            --border: #d2d2d7;
            --text: #333336;
            --text-light: #86868b;
            --error: #ff3b30;
            --success: #34c759;
            --focus-ring: rgba(0, 125, 250, 0.6);
            --shadow-sm: 0 2px 6px rgba(0, 0, 0, 0.08);
            --shadow: 0 10px 20px rgba(0, 0, 0, 0.04);
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: var(--light);
            color: var(--text);
            line-height: 1.47059;
            font-weight: 400;
            letter-spacing: -0.022em;
        }
        
        .navbar {
            background-color: rgba(255, 255, 255, 0.85);
            backdrop-filter: saturate(180%) blur(20px);
            padding: 0.75rem 1.5rem;
            box-shadow: none;
            border-bottom: 1px solid rgba(0, 0, 0, 0.03);
        }
        
        .navbar-light .navbar-nav .nav-link {
            color: var(--text);
            font-weight: 400;
            padding: 0.5rem 1rem;
            transition: color 0.2s ease, opacity 0.2s ease;
        }
        
        .navbar-light .navbar-nav .nav-link:hover {
            color: var(--primary);
            opacity: 0.7;
        }
        
        .navbar-brand img {
            height: 30px;
            width: auto;
            transition: opacity 0.2s ease;
        }
        
        .navbar-brand:hover img {
            opacity: 0.8;
        }
        
        .content-area {
            min-height: calc(100vh - 62px);
            display: flex;
            align-items: center;
            animation: fadeIn 0.8s ease;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-container {
            width: 100%;
            max-width: 380px;
            margin: 1.5rem auto;
            background-color: transparent;
            padding: 1.5rem 1.25rem;
        }
        
        .login-header {
            margin-bottom: 1.5rem;
            text-align: center;
        }
        
        .login-title {
            color: var(--dark);
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            letter-spacing: -0.05em;
        }
        
        .login-subtitle {
            color: var(--text-light);
            font-size: 0.95rem;
            line-height: 1.4;
            max-width: 320px;
            margin: 0 auto;
        }
        
        .time-greeting {
            color: var(--primary);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            font-weight: 500;
            opacity: 0.9;
        }
        
        .form-group {
            margin-bottom: 1.25rem;
        }
        
        .form-label {
            display: block;
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--text);
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            width: 100%;
            padding: 0.625rem 0.875rem;
            font-size: 0.95rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            background-color: rgba(255, 255, 255, 0.8);
            transition: all 0.25s ease-in-out;
            -webkit-appearance: none;
            appearance: none;
            color: var(--dark);
            backdrop-filter: blur(10px);
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--focus-ring);
            outline: none;
            background-color: rgba(255, 255, 255, 0.95);
        }
        
        .form-control::placeholder {
            color: var(--text-light);
            opacity: 0.6;
        }
        
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
        }
        
        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-right: 8px;
            border-radius: 5px;
            border: 1px solid var(--border);
            appearance: none;
            -webkit-appearance: none;
            background-color: white;
            cursor: pointer;
            position: relative;
            vertical-align: middle;
            transition: all 0.2s ease;
        }
        
        .remember-me input[type="checkbox"]:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .remember-me input[type="checkbox"]:checked:after {
            content: '';
            position: absolute;
            left: 6px;
            top: 2px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }
        
        .remember-me label {
            font-size: 0.9rem;
            margin-bottom: 0;
            color: var(--text);
            cursor: pointer;
        }
        
        .forgot-link {
            font-size: 0.9rem;
            color: var(--primary);
            text-decoration: none;
            transition: opacity 0.2s ease;
        }
        
        .forgot-link:hover {
            opacity: 0.7;
            text-decoration: underline;
            color: var(--primary);
        }
        
        .btn {
            display: inline-block;
            font-weight: 500;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            user-select: none;
            border: 1px solid transparent;
            padding: 0.625rem 1.25rem;
            font-size: 0.95rem;
            line-height: 1.5;
            border-radius: 8px;
            transition: all 0.25s ease-in-out;
            cursor: pointer;
            letter-spacing: -0.01em;
        }
        
        .btn-primary {
            color: white;
            background-color: var(--primary);
            border-color: var(--primary);
            box-shadow: 0 2px 5px rgba(0, 112, 201, 0.2);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-light);
            border-color: var(--primary-light);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 112, 201, 0.3);
        }
        
        .btn-primary:active {
            transform: translateY(0);
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            box-shadow: 0 2px 3px rgba(0, 112, 201, 0.2);
        }
        
        .btn-block {
            display: block;
            width: 100%;
        }
        
        .login-footer {
            margin-top: 1.75rem;
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-light);
        }
        
        .login-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.2s ease;
        }
        
        .login-footer a:hover {
            opacity: 0.7;
            text-decoration: underline;
            color: var(--primary);
        }
        
        @media (prefers-color-scheme: dark) {
            :root {
                --primary: #0a84ff;
                --primary-light: #3395ff;
                --primary-dark: #0774e8;
                --primary-rgb: 10, 132, 255;
                --secondary: #98989d;
                --dark: #f5f5f7;
                --light: #1d1d1f;
                --border: #424245;
                --text: #f5f5f7;
                --text-light: #98989d;
                --focus-ring: rgba(10, 132, 255, 0.3);
            }
            
            body {
                background-color: var(--light);
            }
            
            .navbar {
                background-color: rgba(29, 29, 31, 0.85);
                backdrop-filter: saturate(180%) blur(20px);
                border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            }
            
            .form-control {
                background-color: rgba(44, 44, 46, 0.8);
                border-color: var(--border);
                color: var(--text);
            }
            
            .form-control:focus {
                background-color: rgba(44, 44, 46, 0.95);
            }
            
            .remember-me input[type="checkbox"] {
                background-color: rgba(44, 44, 46, 0.8);
            }
        }
        
        /* Login Tabs Styles */
        .login-tabs {
            display: flex;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid var(--border);
        }
        
        .login-tab {
            padding: 0.75rem 0;
            margin-right: 1.5rem;
            font-size: 0.95rem;
            color: var(--text-light);
            cursor: pointer;
            position: relative;
            transition: all 0.2s ease;
            background: transparent;
            border: none;
            outline: none;
        }
        
        .login-tab.active {
            color: var(--primary);
            font-weight: 500;
        }
        
        .login-tab.active:after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: var(--primary);
            border-radius: 1px;
        }
        
        .login-tab:hover {
            color: var(--primary-light);
        }
        
        .login-tab-content {
            display: none;
        }
        
        .login-tab-content.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }
        
        /* Third-party login styles */
        .third-party-options {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.5rem;
            margin-top: 0.5rem;
        }
        
        .third-party-button {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.2rem;
            background-color: rgba(255, 255, 255, 0.8);
            border: 1px solid var(--border);
            border-radius: 0;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            color: var(--dark);
            font-size: 0.9rem;
            backdrop-filter: blur(10px);
        }
        
        .third-party-button:hover {
            border-color: var(--primary);
        }
        
        /* 去除所有按钮颜色类 */
        .third-party-button.google,
        .third-party-button.facebook,
        .third-party-button.twitter,
        .third-party-button.github {
            color: var(--dark);
        }
        
        .third-party-hint {
            text-align: center;
            margin-top: 0.75rem;
            font-size: 0.75rem;
            color: var(--text-light);
        }
        
        @media (prefers-color-scheme: dark) {
            .third-party-button {
                background-color: rgba(44, 44, 46, 0.8);
                border-color: var(--border);
                color: var(--text);
            }
            
            .third-party-button.google,
            .third-party-button.facebook,
            .third-party-button.twitter,
            .third-party-button.github {
                color: var(--text);
            }
        }
        
        /* Alert styles */
        .alert {
            font-size: 0.85rem;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin: 1rem 0;
            display: flex;
            align-items: flex-start;
            line-height: 1.5;
            border: none;
            box-shadow: var(--shadow-sm);
            background-color: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
        }
        
        .alert:before {
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            margin-right: 0.75rem;
            font-size: 1rem;
        }
        
        .alert-info {
            background-color: rgba(var(--primary-rgb), 0.08);
            color: var(--text);
            border-left: 3px solid var(--primary);
        }
        
        .alert-info:before {
            content: '\f05a';
            color: var(--primary);
        }
        
        .alert-success {
            background-color: rgba(52, 199, 89, 0.08);
            color: var(--text);
            border-left: 3px solid #34c759;
        }
        
        .alert-success:before {
            content: '\f00c';
            color: #34c759;
        }
        
        .alert-danger {
            background-color: rgba(255, 59, 48, 0.08);
            color: var(--text);
            border-left: 3px solid #ff3b30;
        }
        
        .alert-danger:before {
            content: '\f071';
            color: #ff3b30;
        }
        
        @media (prefers-color-scheme: dark) {
            .alert {
                background-color: rgba(44, 44, 46, 0.8);
            }
            
            .alert-info {
                background-color: rgba(var(--primary-rgb), 0.15);
            }
            
            .alert-success {
                background-color: rgba(52, 199, 89, 0.15);
            }
            
            .alert-danger {
                background-color: rgba(255, 59, 48, 0.15);
            }
        }
        
        .alert-invite {
            padding: 0.75rem 1rem;
            background-color: rgba(var(--primary-rgb), 0.08);
            border-left: 3px solid var(--primary);
            color: var(--text);
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 1.25rem;
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: center;
            backdrop-filter: blur(10px);
        }
        
        .alert-invite i {
            color: var(--primary);
            margin-right: 0.75rem;
            font-size: 1.25rem;
        }
        
        @media (prefers-color-scheme: dark) {
            .alert-invite {
                background-color: rgba(var(--primary-rgb), 0.15);
            }
        }
    </style>
</head>
<body>
<header class="navbar navbar-expand navbar-light">
    <a class="navbar-brand" href="/">
        <img src="/images/logo.png" alt="Logo">
    </a>
    <div class="navbar-nav-scroll ml-auto">
        <ul class="navbar-nav flex-row">
            <li class="nav-item mr-3">
                <a class="nav-link" href="/">Home</a>
            </li>
            @foreach(\App\Helper::getIndexUrls() as $url)
                <li class="nav-item mr-3">
                    <a href="{{ $url[1] }}" target="_blank" class="nav-link">{{ $url[0] }}</a>
                </li>
            @endforeach
        </ul>
    </div>
</header>
<main class="content-area" id="content">
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <p class="time-greeting" id="timeGreeting">Good day!</p>
                <h1 class="login-title">Welcome back!</h1>
                <p class="login-subtitle">Enter your credentials to access your account</p>
                @if(session('notice'))
                <div class="alert alert-info">
                    {{ session('notice') }}
                                </div>
                @endif
                @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                            </div>
                @endif
                @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                                </div>
                @endif
                            </div>
            
            <div class="login-tabs">
                <button class="login-tab active" id="tab-password">Password</button>
                <button class="login-tab" id="tab-third-party">Social Login</button>
                                    </div>
            
            <div class="login-tab-content active" id="content-password">
                <form id="form-login">
                    <div class="form-group">
                        <label class="form-label" for="username">Username</label>
                        <input type="text" id="username" name="username" class="form-control" placeholder="Enter your username">
                                </div>
                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password">
                            </div>
                    <div class="remember-forgot">
                        <div class="remember-me">
                            <input type="checkbox" name="remember" id="remember">
                            <label for="remember">Remember me</label>
                            </div>
                        <a href="/forgot-password" class="forgot-link">Forgot password?</a>
                    </div>
                    <button type="button" class="btn btn-primary btn-block" @click="login">Sign in</button>
                </form>
            </div>
            
            <div class="login-tab-content" id="content-third-party">
                <p class="text-center mb-3" style="color: var(--text-light); font-size: 0.85rem;">
                    Sign in with your social account
                </p>
                <div class="third-party-options">
                    <a href="{{ route('github.login') }}" class="third-party-button github">GitHub</a>
                    <a href="{{ route('nodeloc.login') }}" class="third-party-button">Nodeloc</a>
                </div>
                <p class="third-party-hint">
                    Your account information will be securely shared with us
                    <br><small style="color: var(--text-light); font-size: 0.7rem;">(Note: In some cases, you may need to click multiple times to login successfully)</small>
                </p>
            </div>
            
            <div class="login-footer">
                <p>Don't have an account? <a href="/register">Sign up</a></p>
            </div>
        </div>
    </div>
</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.10/vue.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/layer/2.3/layer.js"></script>
<script src="/js/main.js"></script>
<script>
    // 设置时间相关问候语
    function setTimeGreeting() {
        const hour = new Date().getHours();
        let greeting = "Good day!";
        if (hour < 5) greeting = "Working late?";
        else if (hour < 12) greeting = "Good morning!";
        else if (hour < 18) greeting = "Good afternoon!";
        else greeting = "Good evening!";
        document.getElementById('timeGreeting').textContent = greeting;
    }
    
    // 页面加载时设置问候语
    document.addEventListener('DOMContentLoaded', function() {
        setTimeGreeting();
        
        // Tab switching functionality
        const passwordTab = document.getElementById('tab-password');
        const thirdPartyTab = document.getElementById('tab-third-party');
        const passwordContent = document.getElementById('content-password');
        const thirdPartyContent = document.getElementById('content-third-party');
        
        passwordTab.addEventListener('click', function() {
            passwordTab.classList.add('active');
            thirdPartyTab.classList.remove('active');
            passwordContent.classList.add('active');
            thirdPartyContent.classList.remove('active');
        });
        
        thirdPartyTab.addEventListener('click', function() {
            thirdPartyTab.classList.add('active');
            passwordTab.classList.remove('active');
            thirdPartyContent.classList.add('active');
            passwordContent.classList.remove('active');
        });
    });
    
    // 自定义弹窗样式，匹配苹果风格
    layer.config({
        skin: 'layer-apple-skin',
        btnAlign: 'c',
        title: 'Notice',
        closeBtn: 1,
        btn: ['OK'],
        yes: function(index, layero) {
            layer.close(index);
        }
    });
    
    // 为弹窗添加自定义样式，确保文本在深色模式下可见
    document.head.insertAdjacentHTML('beforeend', `
        <style>
            .layui-layer {
                background-color: rgba(255, 255, 255, 0.95) !important;
                color: var(--text) !important;
                border-radius: 12px !important;
                box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12) !important;
                overflow: hidden !important;
                animation: layerFadeIn 0.3s ease;
                backdrop-filter: blur(20px) !important;
                border: none !important;
            }
            
            @keyframes layerFadeIn {
                from {
                    opacity: 0;
                    transform: scale(0.97);
                }
                to {
                    opacity: 1;
                    transform: scale(1);
                }
            }
            
            .layui-layer-title {
                background-color: transparent !important;
                color: var(--dark) !important;
                border-bottom: 1px solid var(--border) !important;
                font-weight: 600 !important;
                padding: 16px 20px !important;
                font-size: 1.1rem !important;
                text-align: center !important;
                line-height: 1.2 !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                height: auto !important;
            }
            
            .layui-layer-btn {
                border-top: 1px solid var(--border) !important;
                padding: 12px 20px !important;
                text-align: center !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                height: auto !important;
            }
            
            .layui-layer-btn a {
                background-color: var(--primary) !important;
                color: white !important;
                border-color: var(--primary) !important;
                border-radius: 8px !important;
                padding: 8px 18px !important;
                font-weight: 500 !important;
                transition: all 0.2s ease !important;
                font-size: 0.95rem !important;
                letter-spacing: -0.01em !important;
                float: none !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                height: auto !important;
                line-height: 1.2 !important;
            }
            
            .layui-layer-btn a:hover {
                background-color: var(--primary-light) !important;
                border-color: var(--primary-light) !important;
                transform: translateY(-1px) !important;
                box-shadow: 0 4px 10px rgba(0, 112, 201, 0.2) !important;
            }
            
            .layui-layer-content {
                color: var(--text) !important;
                padding: 24px 20px !important;
                font-size: 0.95rem !important;
                line-height: 1.5 !important;
                text-align: center !important;
            }
            
            .layui-layer-close {
                color: var(--text) !important;
                font-weight: 300 !important;
                transition: opacity 0.2s ease !important;
                right: 12px !important;
                top: 12px !important;
            }
            
            .layui-layer-close:hover {
                transform: none !important;
                opacity: 0.7 !important;
            }
            
            @media (prefers-color-scheme: dark) {
                .layui-layer {
                    background-color: rgba(44, 44, 46, 0.95) !important;
                }
                
                .layui-layer-title {
                    background-color: transparent !important;
                    color: #f5f5f7 !important;
                }
                
                .layui-layer-content {
                    color: #f5f5f7 !important;
                }
                
                .layui-layer-btn {
                    border-top: 1px solid rgba(255, 255, 255, 0.1) !important;
                }
                
                .layui-layer-btn a {
                    background-color: var(--primary) !important;
                    color: white !important;
                    border-color: var(--primary) !important;
                }
                
                .layui-layer-close {
                    color: #f5f5f7 !important;
                }
            }
        </style>
    `);
    
    new Vue({
        el: '#content',
        methods: {
            login: function () {
                var vm = this;
                // 添加按钮加载状态
                const loginBtn = document.querySelector('.btn-primary');
                loginBtn.innerHTML = '<span class="loading-text">Signing in...</span>';
                loginBtn.disabled = true;
                
                this.$post('/login', $("#form-login").serialize())
                    .then(function (data) {
                        // 恢复按钮状态
                        loginBtn.innerHTML = 'Sign in';
                        loginBtn.disabled = false;
                        
                        if (data.status === 0) {
                            location.href = data.go ? data.go : "{{ request()->get('go','/') }}";
                        } else {
                            layer.alert(data.message);
                        }
                    })
                    .catch(function() {
                        // 恢复按钮状态
                        loginBtn.innerHTML = 'Sign in';
                        loginBtn.disabled = false;
                    });
            }
        },
        mounted: function () {
            var vm = this;
            document.onkeyup = function (e) {
                var code = parseInt(e.charCode || e.keyCode);
                if (code === 13) {
                    vm.login();
                }
            }
        }
    });
</script>
</body>
</html>