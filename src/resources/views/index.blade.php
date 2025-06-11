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
                        <a class="nav-link" href="/login">Login</a>
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