<!doctype html>
<html lang="en">
<head>
<meta lang="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password - {{ config('sys.web.name','OSFC Registry') }}</title>
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
        
        .reset-container {
            width: 100%;
            max-width: 380px;
            margin: 1.5rem auto;
            background-color: transparent;
            padding: 1.5rem 1.25rem;
        }
        
        .reset-header {
            margin-bottom: 1.5rem;
            text-align: center;
        }
        
        .reset-title {
            color: var(--dark);
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            letter-spacing: -0.05em;
        }
        
        .reset-subtitle {
            color: var(--text-light);
            font-size: 0.95rem;
            line-height: 1.4;
            max-width: 320px;
            margin: 0 auto;
        }
        
        .progress-steps {
            display: flex;
            justify-content: center;
            margin-bottom: 1.5rem;
        }
        
        .progress-step {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: var(--border);
            margin: 0 4px;
        }
        
        .progress-step.active {
            background-color: var(--primary);
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
        
        .form-control:disabled {
            background-color: rgba(0, 0, 0, 0.03);
            color: var(--text-light);
            border-color: var(--border);
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
        
        .password-strength-text {
            font-size: 0.75rem;
            margin-top: 0.5rem;
            color: var(--text-light);
            display: flex;
            justify-content: space-between;
            transition: all 0.3s ease;
        }
        
        .strength-weak .password-strength-bar {
            width: 33%;
            background-color: var(--error);
        }
        
        .strength-medium .password-strength-bar {
            width: 66%;
            background-color: #f4a100;
        }
        
        .strength-strong .password-strength-bar {
            width: 100%;
            background-color: var(--success);
        }
        
        .password-match-indicator {
            font-size: 0.75rem;
            margin-top: 0.5rem;
            color: var(--text-light);
            transition: all 0.3s ease;
        }
        
        .passwords-match {
            color: var(--success);
        }
        
        .passwords-not-match {
            color: var(--error);
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
        
        .success-animation {
            transition: all 0.3s ease;
        }
        
        .reset-footer {
            margin-top: 1.75rem;
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-light);
        }
        
        .reset-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.2s ease;
        }
        
        .reset-footer a:hover {
            opacity: 0.7;
            text-decoration: underline;
            color: var(--primary);
        }
        
        @media (prefers-color-scheme: dark) {
            :root {
                --primary: #0a84ff;
                --primary-light: #3395ff;
                --primary-dark: #0774e8;
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
            
            .reset-container {
                background-color: #2c2c2e;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            }
            
            .form-control {
                background-color: rgba(44, 44, 46, 0.8);
                border-color: var(--border);
                color: var(--text);
            }
            
            .form-control:disabled {
                background-color: rgba(255, 255, 255, 0.08);
                color: var(--text-light);
            }
            
            .form-control:focus {
                background-color: rgba(44, 44, 46, 0.95);
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
        }

        .alert-info:before {
            content: '\f05a';
            color: var(--primary);
        }

        .alert-success {
            background-color: rgba(52, 199, 89, 0.08);
            color: var(--text);
        }

        .alert-success:before {
            content: '\f00c';
            color: #34c759;
        }

        .alert-danger {
            background-color: rgba(255, 59, 48, 0.08);
            color: var(--text);
        }

        .alert-danger:before {
            content: '\f071';
            color: #ff3b30;
        }

        @media (prefers-color-scheme: dark) {
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
    <ul class="navbar-nav flex-row ml-md-auto d-none d-md-flex">
        <li class="nav-item mr-3">
            <a class="nav-link" href="/login">Login</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/register">Register</a>
        </li>
    </ul>
</header>
<main class="content-area" id="content">
    <div class="container">
        <div class="reset-container">
            <div class="reset-header">
                <div class="progress-steps">
                    <div class="progress-step active"></div>
                    <div class="progress-step active"></div>
                    <div class="progress-step"></div>
                </div>
                <h1 class="reset-title">Almost there!</h1>
                <p class="reset-subtitle">Create a new secure password for your account</p>
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
                    <form id="form-password">
                        <input type="hidden" name="action" value="setPassword">
                <input type="hidden" name="code" value="{{ \Illuminate\Support\Facades\Crypt::encrypt($user->sid) }}">
                
                <div class="form-group">
                    <label class="form-label" for="username">Username</label>
                    <input type="text" id="username" class="form-control" value="{{ $user->username }}" disabled>
                            </div>
                
                <div class="form-group">
                    <label class="form-label" for="password">New Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter your new password" @input="checkPasswordStrength">
                    <div class="password-strength" id="passwordStrength">
                        <div class="password-strength-bar" id="passwordStrengthBar"></div>
                        </div>
                    <div class="password-strength-text">
                        <span id="passwordStrengthText">Password strength</span>
                        <span id="passwordStrengthPercent"></span>
                            </div>
                        </div>
                
                <div class="form-group">
                    <label class="form-label" for="re_password">Confirm Password</label>
                    <input type="password" id="re_password" name="re_password" class="form-control" placeholder="Confirm your new password" @input="checkPasswordMatch">
                    <div class="password-match-indicator" id="passwordMatch"></div>
                </div>
                
                <button type="button" class="btn btn-primary btn-block" @click="password">
                    Reset Password
                </button>
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
            password: function () {
                var vm = this;
                // 添加按钮加载状态
                const resetBtn = document.querySelector('.btn-primary');
                const originalContent = resetBtn.innerHTML;
                resetBtn.innerHTML = 'Resetting Password...';
                resetBtn.disabled = true;
                
                this.$post('/password', $("#form-password").serialize())
                    .then(function (data) {
                        // 恢复按钮状态
                        resetBtn.innerHTML = originalContent;
                        resetBtn.disabled = false;
                        
                        if (data.status === 0) {
                            // 添加成功动画
                            resetBtn.innerHTML = 'Success!';
                            resetBtn.style.backgroundColor = 'var(--success)';
                            resetBtn.style.borderColor = 'var(--success)';
                            resetBtn.classList.add('success-animation');
                            
                            layer.alert(data.message, {
                                closeBtn: 0
                            }, function (i) {
                                window.location.href = "/login";
                            });
                        } else {
                            layer.alert(data.message);
                        }
                    })
                    .catch(function() {
                        // 恢复按钮状态
                        resetBtn.innerHTML = originalContent;
                        resetBtn.disabled = false;
                    });
            },
            
            checkPasswordStrength: function() {
                const password = document.getElementById('password').value;
                const strengthBar = document.getElementById('passwordStrengthBar');
                const strengthText = document.getElementById('passwordStrengthText');
                const strengthPercent = document.getElementById('passwordStrengthPercent');
                const strengthContainer = document.getElementById('passwordStrength');
                
                // 移除之前的类
                strengthContainer.classList.remove('strength-weak', 'strength-medium', 'strength-strong');
                
                if (password.length === 0) {
                    strengthText.textContent = 'Password strength';
                    strengthPercent.textContent = '';
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
                
                // 根据密码强度设置样式和文本
                if (strength <= 2) {
                    strengthContainer.classList.add('strength-weak');
                    strengthText.textContent = 'Weak';
                    strengthPercent.textContent = '33%';
                } else if (strength <= 4) {
                    strengthContainer.classList.add('strength-medium');
                    strengthText.textContent = 'Medium';
                    strengthPercent.textContent = '66%';
                } else {
                    strengthContainer.classList.add('strength-strong');
                    strengthText.textContent = 'Strong';
                    strengthPercent.textContent = '100%';
                }
            },
            
            checkPasswordMatch: function() {
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('re_password').value;
                const matchIndicator = document.getElementById('passwordMatch');
                
                if (confirmPassword.length === 0) {
                    matchIndicator.textContent = '';
                    matchIndicator.classList.remove('passwords-match', 'passwords-not-match');
                    return;
                }
                
                if (password === confirmPassword) {
                    matchIndicator.textContent = 'Passwords match';
                    matchIndicator.classList.add('passwords-match');
                    matchIndicator.classList.remove('passwords-not-match');
                } else {
                    matchIndicator.textContent = 'Passwords do not match';
                    matchIndicator.classList.add('passwords-not-match');
                    matchIndicator.classList.remove('passwords-match');
                }
            }
        },
        mounted: function () {
            var vm = this;
            document.onkeyup = function (e) {
                var code = parseInt(e.charCode || e.keyCode);
                if (code === 13) {
                    vm.password();
                }
            }
        }
    });
</script>
</body>
</html>