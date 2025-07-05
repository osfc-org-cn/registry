<!doctype html>
<html lang="en">
<head>
    <meta lang="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - {{ config('sys.web.name','OSFC Registry') }}</title>
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
        
        .register-container {
            width: 100%;
            max-width: 380px;
            margin: 1.5rem auto;
            background-color: transparent;
            padding: 1.5rem 1.25rem;
        }
        
        .register-header {
            margin-bottom: 1.5rem;
            text-align: center;
        }
        
        .register-title {
            color: var(--dark);
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            letter-spacing: -0.05em;
        }
        
        .register-subtitle {
            color: var(--text-light);
            font-size: 0.95rem;
            line-height: 1.4;
            max-width: 320px;
            margin: 0 auto;
        }
        
        .form-group {
            margin-bottom: 1.25rem;
        }
        
        .form-label {
            display: block;
            font-size: 0.85rem;
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
        
        .captcha-container {
            display: flex;
            align-items: center;
            margin-top: 0.5rem;
        }
        
        .captcha-image {
            height: 38px;
            width: auto;
            border-radius: 6px;
            border: 1px solid var(--border);
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            background-color: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(10px);
        }
        
        .captcha-image:hover {
            border-color: var(--primary);
            transform: scale(1.02);
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
        
        .register-footer {
            margin-top: 1.75rem;
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-light);
        }
        
        .register-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.2s ease;
        }
        
        .register-footer a:hover {
            opacity: 0.7;
            text-decoration: underline;
            color: var(--primary);
        }
        
        .password-strength {
            margin-top: 0.75rem;
            height: 4px;
            border-radius: 2px;
            background-color: var(--border);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .password-strength-bar {
            height: 100%;
            width: 0%;
            border-radius: 2px;
            transition: all 0.3s ease;
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
        
        .github-info {
            background-color: rgba(36, 41, 46, 0.08);
            border: none;
            border-left: 3px solid #24292e;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            box-shadow: var(--shadow-sm);
            backdrop-filter: blur(10px);
        }
        
        .github-avatar {
            width: 38px;
            height: 38px;
            border-radius: 19px;
            margin-right: 0.75rem;
            object-fit: cover;
        }
        
        .github-details {
            flex: 1;
        }
        
        .github-username {
            font-weight: 500;
            margin-bottom: 0.25rem;
        }
        
        .github-email {
            font-size: 0.8rem;
            color: var(--text-light);
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
            
            .captcha-image {
                border-color: var(--border);
                background-color: rgba(44, 44, 46, 0.6);
            }
            
            .alert-invite {
                background-color: rgba(var(--primary-rgb), 0.15);
            }
            
            .github-info {
                background-color: rgba(140, 140, 140, 0.1);
                border-left: 3px solid #555;
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
        <div class="register-container">
            <div class="register-header">
                <h1 class="register-title">Create Account</h1>
                <p class="register-subtitle">Sign up to access all features</p>
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
                
                @if(session('github_user_info'))
                <div class="github-info">
                    @if(isset(session('github_user_info')['avatar_url']))
                    <img src="{{ session('github_user_info')['avatar_url'] }}" alt="GitHub Avatar" class="github-avatar">
                    @endif
                    <div class="github-details">
                        <div class="github-username">{{ session('github_user_info')['login'] ?? 'GitHub User' }}</div>
                        @if(isset(session('github_user_info')['email']) && !empty(session('github_user_info')['email']))
                        <div class="github-email">{{ session('github_user_info')['email'] }}</div>
                        @endif
                    </div>
                </div>
                @endif
                
                @if(session('nodeloc_user_info'))
                <div class="github-info nodeloc-info">
                    @if(isset(session('nodeloc_user_info')['picture']))
                    <img src="{{ session('nodeloc_user_info')['picture'] }}" alt="Nodeloc Avatar" class="github-avatar">
                    @endif
                    <div class="github-details">
                        <div class="github-username">{{ session('nodeloc_user_info')['username'] ?? 'Nodeloc User' }}</div>
                        @if(isset(session('nodeloc_user_info')['email']) && !empty(session('nodeloc_user_info')['email']))
                        <div class="github-email">{{ session('nodeloc_user_info')['email'] }}</div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
            <form id="form-reg">
                <div class="form-group">
                    <label class="form-label" for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Choose a username" 
                           value="{{ session('github_user_info.login') ?? session('nodeloc_user_info.username') ?? old('username') }}">
                </div>
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Create a password" @input="checkPasswordStrength">
                    <div class="password-strength" id="passwordStrength">
                        <div class="password-strength-bar" id="passwordStrengthBar"></div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email address" 
                           value="{{ session('github_user_info.email') ?? session('nodeloc_user_info.email') ?? old('email') }}">
                </div>
                <div class="form-group">
                    <label class="form-label" for="code">Verification Code</label>
                    <input type="text" id="code" name="code" class="form-control" placeholder="Enter the verification code">
                    <div class="captcha-container">
                        <img title="Click to refresh" src="/captcha" id="captchaImage"
                            class="captcha-image" onclick="this.src='/captcha?_='+Math.random();">
                        <i class="fas fa-sync-alt ml-2" style="color: var(--primary); cursor: pointer; font-size: 0.9rem;" 
                           onclick="document.getElementById('captchaImage').src='/captcha?_='+Math.random();"></i>
                    </div>
                </div>
                @if(isset($invite))
                <input type="hidden" name="invite" value="{{ $invite }}">
                <div class="alert-invite">
                    <i class="fas fa-gift"></i> 
                    <span>You are registering with an invitation link. After verifying your email, both you and the inviter will receive bonus points!</span>
                </div>
                @endif
                <button type="button" class="btn btn-primary btn-block" @click="reg">
                    Create Account
                </button>
                <div class="register-footer">
                    <p>Already have an account? <a href="/login">Sign in</a></p>
                </div>
            </form>
        </div>
    </div>
</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.10/vue.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/layer/2.3/layer.js"></script>
<script src="/js/main.js"></script>
<script>
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
            reg: function () {
                var vm = this;
                // 添加按钮加载状态
                const regBtn = document.querySelector('.btn-primary');
                const originalContent = regBtn.innerHTML;
                regBtn.innerHTML = 'Creating Account...';
                regBtn.disabled = true;
                
                this.$post('/reg', $("#form-reg").serialize())
                    .then(function (data) {
                        // 恢复按钮状态
                        regBtn.innerHTML = originalContent;
                        regBtn.disabled = false;
                        
                        $("#code").click();
                        if (data.status === 0) {
                            layer.alert(data.message, {
                                closeBtn: 0
                            }, function (i) {
                                layer.close(i);
                            });
                        } else {
                            if (data.whitelist_url) {
                                layer.confirm(data.message + '<br><br>Would you like to view the available email domains?', {
                                    btn: ['View List', 'Cancel'],
                                    title: 'Notice'
                                }, function(){
                                    window.open(data.whitelist_url);
                                });
                            } else {
                                layer.alert(data.message);
                            }
                        }
                    })
                    .catch(function() {
                        // 恢复按钮状态
                        regBtn.innerHTML = originalContent;
                        regBtn.disabled = false;
                    });
            },
            
            checkPasswordStrength: function() {
                const password = document.getElementById('password').value;
                const strengthBar = document.getElementById('passwordStrengthBar');
                
                if (!password) {
                    strengthBar.style.width = '0%';
                    strengthBar.style.backgroundColor = 'var(--border)';
                    return;
                }
                
                // 简单的密码强度检查
                let strength = 0;
                
                // 长度检查
                if (password.length > 6) strength += 1;
                if (password.length > 10) strength += 1;
                
                // 复杂性检查
                if (password.match(/[a-z]/)) strength += 1;
                if (password.match(/[A-Z]/)) strength += 1;
                if (password.match(/[0-9]/)) strength += 1;
                if (password.match(/[^a-zA-Z0-9]/)) strength += 1;
                
                // 转换强度到百分比
                const percentage = Math.min((strength / 6) * 100, 100);
                
                // 设置进度条颜色和宽度
                strengthBar.style.width = percentage + '%';
                
                // 根据强度调整颜色
                if (percentage <= 33) {
                    strengthBar.style.backgroundColor = 'var(--error)';
                } else if (percentage <= 66) {
                    strengthBar.style.backgroundColor = '#f4a100';
                } else {
                    strengthBar.style.backgroundColor = 'var(--success)';
                }
            }
        },
        mounted: function () {
            var vm = this;
            document.onkeyup = function (e) {
                var code = parseInt(e.charCode || e.keyCode);
                if (code === 13) {
                    vm.reg();
                }
            }
        }
    });
</script>
</body>
</html> 