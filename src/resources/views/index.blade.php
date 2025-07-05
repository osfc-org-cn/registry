<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta lang="en">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('sys.web.title', config('sys.web.name', 'Registry.OSFC.org.cn - Open Source & Free: Code')) }}</title>
    <meta name="keywords" content="{{ config('sys.web.keywords', 'free domain registration, open source domain registry, OSFC') }}"/>
    <meta name="description" content="{{ config('sys.web.description', 'Free domain registration service provided by OSFC.org.cn') }}"/>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="/js/layer-fixes.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link href="/css/modern.css" rel="stylesheet">
    <style>
        /* 友情链接样式 */
        .friend-links {
            background-color: var(--bg-color-offset);
            padding: 40px 0;
            margin-top: 60px;
        }
        
        .friend-links-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .friend-links-title {
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: var(--text-color);
            text-align: center;
        }
        
        .friend-links-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
        }
        
        .friend-link-item {
            background-color: var(--bg-color);
            border-radius: 8px;
            padding: 15px;
            box-shadow: var(--card-shadow);
            transition: transform 0.3s, box-shadow 0.3s;
            width: calc(33.333% - 15px);
            text-align: center;
        }
        
        .friend-link-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .friend-link-name {
            font-weight: bold;
            margin-bottom: 5px;
            color: var(--primary-color);
        }
        
        .friend-link-desc {
            font-size: 0.9rem;
            color: var(--text-color-light);
        }
        
        @media (max-width: 768px) {
            .friend-link-item {
                width: calc(50% - 15px);
            }
        }
        
        @media (max-width: 480px) {
            .friend-link-item {
                width: 100%;
            }
        }
        
        /* 登录弹框样式 */
        #login-modal {
            display: none;
        }
        
        .login-modal-content {
            padding: 25px;
            background-color: var(--bg-color);
            color: var(--text-color);
            border-radius: 12px;
            max-width: 100%;
            overflow: hidden;
        }
        
        .login-modal-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 20px;
            text-align: center;
            color: var(--text-color);
        }
        
        .form-group {
            margin-bottom: 1.25rem;
        }
        
        .form-label {
            display: block;
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--text-color);
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            width: 100%;
            padding: 0.625rem 0.875rem;
            font-size: 0.95rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background-color: var(--input-bg);
            transition: all 0.25s ease-in-out;
            color: var(--text-color);
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px var(--focus-ring);
            outline: none;
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
            margin-right: 8px;
        }
        
        .forgot-link {
            font-size: 0.9rem;
            color: var(--primary-color);
            text-decoration: none;
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
        }
        
        .btn-primary {
            color: white;
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-block {
            display: block;
            width: 100%;
        }
        
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
            padding: 0.5rem;
            background-color: var(--bg-color-offset);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            color: var(--text-color);
            font-size: 0.9rem;
        }
        
        .third-party-button:hover {
            border-color: var(--primary-color);
        }
        
        .third-party-hint {
            text-align: center;
            margin-top: 0.75rem;
            font-size: 0.75rem;
            color: var(--text-color-light);
        }
        
        .login-footer {
            margin-top: 1.75rem;
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-color-light);
        }
        
        .login-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .loading-text {
            opacity: 0.8;
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
        
        /* 为弹窗添加自定义样式 */
        .login-modal-layer {
            background-color: var(--bg-color) !important;
            border-radius: 12px !important;
            overflow: hidden !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
        }
        
        .login-modal-layer .layui-layer-content {
            padding: 0 !important;
            overflow: hidden !important;
        }
        
        .login-modal-layer .layui-layer-close {
            color: var(--text-color) !important;
            font-size: 16px !important;
            right: 12px !important;
            top: 12px !important;
        }
        
        .btn-primary {
            color: white;
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            transition: all 0.2s ease;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-color-light);
            transform: translateY(-1px);
        }
        
        .btn-primary:active {
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <header class="navbar">
        <div class="container nav-container">
            <a class="navbar-brand" href="/">
                <img src="/logo.png" alt="OSFC Registry" class="logo-image">
            </a>
            
            <input type="checkbox" id="mobile-menu" class="mobile-toggle">
            <label for="mobile-menu" class="mobile-toggle-label">
                <i class="fas fa-bars"></i>
            </label>
            
            <ul class="navbar-nav">
            <li class="nav-item">
                    <a class="nav-link active" href="/">Home</a>
            </li>
            @foreach(\App\Helper::getIndexUrls() as $url)
                <li class="nav-item">
                    <a href="{{ $url[1] }}" target="_blank" class="nav-link">{{ $url[0] }}</a>
                </li>
            @endforeach

    @if(auth()->check())
                    <li class="nav-item dropdown">
                        <a class="nav-link" href="#">
                            {{ auth()->user()->username }}
                            <span class="d-none d-sm-inline">[{{ auth()->user()->group ? auth()->user()->group->name : '' }}]</span>
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="/home">My Domains</a>
                            <a class="dropdown-item" href="/home/profile">Change Password</a>
                            <a class="dropdown-item" href="/logout" onclick="return confirm('Confirm logout?');">Logout</a>
                </div>
            </li>
    @else
            <li class="nav-item">
                        <a class="nav-link" href="javascript:void(0);" onclick="handleLogin()">Login</a>
            </li>
            <li class="nav-item">
                        <a class="nav-link" href="/register">Register</a>
                    </li>
                @endif
                
                <li class="nav-item">
                    <button id="theme-toggle" class="theme-toggle" aria-label="Toggle dark mode">
                        <i id="theme-icon" class="fas fa-moon"></i>
                    </button>
            </li>
        </ul>
        </div>
</header>

    <section class="hero">
    <div class="container">
            <h1>{{ config('sys.homepage.hero_title', 'Free Domain Registration') }}</h1>
            <p>{{ config('sys.homepage.hero_description', 'Get your own domain from OSFC. Our mission is promoting Open Source & Free Code for everyone.') }}</p>
            
            <div class="search-box">
                <input type="text" class="search-input" id="domain-prefix" placeholder="{{ config('sys.homepage.search_placeholder', 'Enter your desired subdomain prefix') }}">
                <select id="domain-select" class="domain-select">
                    @foreach(\App\Helper::getAllDomains() as $domain)
                                <option value="{{ $domain->did }}">.{{ $domain->domain }}</option>
                            @endforeach
                        </select>
                <button class="search-button" onclick="checkDomain()">
                    <i class="fas fa-search"></i> Check
                </button>
            </div>
        </div>
    </section>

    <section class="features">
        <div class="container">
            <h2 class="section-title">{{ config('sys.homepage.features_title', 'Why Choose OSFC Registry?') }}</h2>
            
            <div class="features-grid">
                @php
                    $featuresContent = config('sys.homepage.features_content', "Completely Free|fas fa-gift|Our domain registration service is 100% free, with no hidden fees or charges. Get your domain without spending a penny.\nOpen Source|fas fa-code|The entire platform is open source. You can contribute, inspect, or fork our code on GitHub.\nSecure & Reliable|fas fa-shield-alt|We provide robust DNS management with high availability and security features to protect your domain.");
                    $features = explode("\n", $featuresContent);
                @endphp
                
                @foreach($features as $feature)
                    @php
                        $featureParts = explode('|', $feature);
                        $featureTitle = $featureParts[0] ?? '';
                        $featureIcon = $featureParts[1] ?? 'fas fa-star';
                        $featureDesc = $featureParts[2] ?? '';
                    @endphp
                    <div class="feature-card">
                        <div class="feature-icon"><i class="{{ $featureIcon }}"></i></div>
                        <h3 class="feature-title">{{ $featureTitle }}</h3>
                        <p>{{ $featureDesc }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="about">
        <div class="container">
            <h2 class="section-title">{{ config('sys.homepage.about_title', 'About OSFC') }}</h2>
            
            <div class="about-content">
                @php
                    $aboutContent = config('sys.homepage.about_content', "OSFC is a community-driven initiative dedicated to supporting open source projects and providing free services to developers worldwide. The name represents our core values: Open Source & Free Code.\nOur mission is to make technology more accessible by removing financial barriers. We believe that everyone should have access to the tools they need to create, learn, and share their work online.\nOSFC is open to all people, regardless of background, experience level, or location. We welcome contributions from anyone who shares our vision of a more open and accessible internet.\nHowever, we firmly oppose anyone using OSFC resources for any illegal activities. We operate in accordance with the relevant laws and regulations of the People's Republic of China.");
                    $paragraphs = explode("\n", $aboutContent);
                @endphp
                
                @foreach($paragraphs as $paragraph)
                    <p>{{ $paragraph }}</p>
                @endforeach
            </div>
        </div>
    </section>

    <section class="how-it-works">
        <div class="container">
            <h2 class="section-title">{{ config('sys.homepage.steps_title', 'How It Works') }}</h2>
            
            <div class="steps-container">
                @php
                    $stepsContent = config('sys.homepage.steps_content', "Choose Your Domain|Enter your preferred subdomain name and select an available extension from our list.\nCreate an Account|Sign up for a free account to manage your domains and DNS records.\nSet Up DNS Records|Add A records to point your domain to an IP address, or CNAME records to link to another domain.\nGo Live|Your domain is ready to use! DNS changes typically propagate within minutes.");
                    $steps = explode("\n", $stepsContent);
                @endphp
                
                @foreach($steps as $index => $step)
                    @php
                        $stepParts = explode('|', $step);
                        $stepTitle = $stepParts[0] ?? '';
                        $stepDesc = $stepParts[1] ?? '';
                    @endphp
                    <div class="step-card">
                        <div class="step-number">{{ $index + 1 }}</div>
                        <div class="step-content">
                            <h3>{{ $stepTitle }}</h3>
                            <p>{{ $stepDesc }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="cta-container">
                <a href="/register" class="cta-button">{{ config('sys.homepage.cta_button', 'Get Started Now') }}</a>
            </div>
        </div>
    </section>

    @php
    $friendLinks = \App\Helper::getFriendLinks();
    $friendLinksTitle = config('sys.friendlinks.title', 'Our Partners');
    @endphp
    
    @if(count($friendLinks) > 0)
    <section class="friend-links">
        <div class="friend-links-container">
            <h2 class="friend-links-title">{{ $friendLinksTitle }}</h2>
            
            <div class="friend-links-grid">
                @foreach($friendLinks as $link)
                <a href="{{ $link['url'] }}" target="_blank" class="friend-link-item">
                    <div class="friend-link-name">{{ $link['name'] }}</div>
                    @if(!empty($link['description']))
                    <div class="friend-link-desc">{{ $link['description'] }}</div>
                    @endif
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <footer class="footer">
        <div class="container">
            <p>&copy; {{ date('Y') }} {{ config('sys.homepage.footer_text', 'OSFC.org.cn - Open Source & Free Code. All rights reserved.') }}</p>
            <div class="footer-links">
                <a href="https://github.com/osfc-org-cn" class="footer-link" target="_blank"><i class="fab fa-github"></i> GitHub</a>
        </div>
    </div>
    </footer>

    <!-- 登录弹框 -->
    <div id="login-modal" style="display: none;">
        <div class="login-modal-content">
            <h3 class="login-modal-title">Login to Your Account</h3>
            
            <form id="modal-form-login">
                <div class="form-group">
                    <label class="form-label" for="modal-username">Username</label>
                    <input type="text" id="modal-username" name="username" class="form-control" placeholder="Enter your username">
                </div>
                <div class="form-group">
                    <label class="form-label" for="modal-password">Password</label>
                    <input type="password" id="modal-password" name="password" class="form-control" placeholder="Enter your password">
                </div>
                <div class="remember-forgot">
                    <div class="remember-me">
                        <input type="checkbox" name="remember" id="modal-remember">
                        <label for="modal-remember">Remember me</label>
                    </div>
                    <a href="/forgot-password" class="forgot-link">Forgot password?</a>
                </div>
                <button type="button" class="btn btn-primary btn-block" onclick="modalLoginAction(this)">Sign in</button>
            </form>
            
            <div class="login-footer">
                <p>Don't have an account? <a href="/register">Sign up</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/layer/3.5.1/layer.js"></script>
    <script src="/js/main.js"></script>
    <script src="/js/theme.js"></script>
<script>
        // 初始化layer配置，确保文本可见并使用英文按钮
        layer.config({
            skin: 'layer-custom-skin',
            btnAlign: 'c',
            title: 'Message',
            closeBtn: 1,
            btn: ['OK'],
            yes: function(index, layero) {
                layer.close(index);
            }
        });
        
        // 处理登录按钮点击事件
        function handleLogin() {
            // 检查后台配置，决定是弹出登录框还是跳转到登录页面
            // 这里使用配置值，如果配置为1则使用弹框登录，否则跳转到登录页面
            const useModalLogin = "{{ config('sys.login_modal_enabled', '0') }}" === "1";
            
            if (useModalLogin) {
                showLoginModal();
            } else {
                window.location.href = "/login";
            }
        }
        
        // 显示登录弹框
        function showLoginModal() {
            layer.open({
                type: 1,
                title: false,
                closeBtn: 1,
                shade: 0.8,
                id: 'LAY_loginView',
                area: ['350px', 'auto'],
                skin: 'login-modal-layer',
                content: $('#login-modal').html(),
                success: function(layero, index) {
                    // 绑定回车键登录
                    layero.find('#modal-password').on('keyup', function(e) {
                        if (e.key === 'Enter') {
                            modalLoginAction(layero.find('.btn-primary')[0]);
                        }
                    });
                }
            });
        }
        
        // 弹框登录处理函数
        function modalLoginAction(btn) {
            const layero = $(btn).closest('.layui-layer-content');
            const username = layero.find('#modal-username').val();
            const password = layero.find('#modal-password').val();
            const remember = layero.find('#modal-remember').is(':checked') ? 1 : 0;
            
            if (!username || !password) {
                layer.msg('Please enter username and password');
                return;
            }
            
            const loginBtn = layero.find('.btn-primary');
            loginBtn.html('<span class="loading-text">Signing in...</span>');
            loginBtn.prop('disabled', true);
            
            $post("/login", {
                username: username,
                password: password,
                remember: remember
            })
            .then(function(data) {
                loginBtn.html('Sign in');
                loginBtn.prop('disabled', false);
                
                if (data.status === 0) {
                    location.href = data.go ? data.go : "/home";
                } else {
                    layer.msg(data.message);
                }
            })
            .catch(function() {
                loginBtn.html('Sign in');
                loginBtn.prop('disabled', false);
                layer.msg('Login failed. Please try again.');
            });
        }
        
        function checkDomain() {
            const prefix = document.getElementById('domain-prefix').value;
            const did = document.getElementById('domain-select').value;
            
            if (!prefix) {
                layer.alert('Please enter a domain prefix', {
                    title: 'Message',
                    btn: ['OK']
                });
                return;
            }
            
            $post("/check", {name: prefix, did: did})
            .then(function (data) {
                if (data.status === 0) {
                    layer.confirm(data.message, {
                            title: 'Confirmation',
                            btn: ['Register', 'Cancel'],
                            skin: 'layer-custom-skin'
                    }, function () {
                        window.location.href = "/home/"
                    });
                } else {
                        layer.alert(data.message, {
                            title: 'Message',
                            btn: ['OK'],
                            skin: 'layer-custom-skin'
                        });
                }
            });
    }

        // Enter key to trigger domain check
        document.getElementById('domain-prefix').addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                checkDomain();
            }
        });
        
        // Close mobile menu when clicking a link
        document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
            link.addEventListener('click', function() {
                document.getElementById('mobile-menu').checked = false;
            });
        });
        
        // 为弹窗添加自定义样式，确保文本在深色模式下可见
        document.head.insertAdjacentHTML('beforeend', `
            <style>
                .layui-layer {
                    background-color: var(--bg-color) !important;
                    color: var(--text-color) !important;
                }
                .layui-layer-title {
                    background-color: var(--bg-color-offset) !important;
                    color: var(--text-color) !important;
                    border-bottom: 1px solid var(--border-color) !important;
                }
                .layui-layer-btn {
                    border-top: 1px solid var(--border-color) !important;
                }
                .layui-layer-btn a {
                    background-color: var(--primary-color) !important;
                    color: white !important;
                    border-color: var(--primary-color) !important;
                }
                .layui-layer-btn .layui-layer-btn1 {
                    background-color: var(--bg-color-offset) !important;
                    color: var(--text-color) !important;
                }
                .layui-layer-content {
                    color: var(--text-color) !important;
                }
                .layui-layer-close {
                    color: var(--text-color-light) !important;
                }
            </style>
        `);
</script>
</body>
</html>