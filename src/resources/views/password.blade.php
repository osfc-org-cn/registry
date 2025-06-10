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
        /* 添加页面样式，使其与现代风格更加一致 */
        .bd-navbar-index {
            background-color: #343a40;
        }
        .bd-masthead {
            padding-top: 2rem;
            padding-bottom: 2rem;
            background-color: #f8f9fa;
            min-height: calc(100vh - 60px);
        }
        .card {
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
        }
        .btn-info {
            border-radius: 0.25rem;
            padding: 0.5rem 2rem;
        }
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #121212;
                color: #f8f9fa;
            }
            .bd-masthead {
                background-color: #121212;
            }
            .card {
                background-color: #1e1e1e;
                border-color: #2a2a2a;
            }
            .form-control {
                background-color: #333;
                border-color: #444;
                color: #f8f9fa;
            }
            a {
                color: #58a6ff;
            }
        }
    </style>
</head>
<body>
<header class="navbar navbar-expand navbar-dark flex-column flex-md-row bd-navbar bd-navbar-index">
    <a class="navbar-brand mr-0 mr-md-2" href="/" aria-label="Bootstrap">
        <img src="/images/logo.png" width="36" height="36">
    </a>

    <div class="navbar-nav-scroll">
        <ul class="navbar-nav bd-navbar-nav flex-row">
            <li class="nav-item">
                <a class="nav-link active" href="/">Home</a>
            </li>
            @foreach(\App\Helper::getIndexUrls() as $url)
                <li class="nav-item">
                    <a href="{{ $url[1] }}" target="_blank" class="nav-link">{{ $url[0] }}</a>
                </li>
            @endforeach
        </ul>
    </div>
    <ul class="navbar-nav flex-row ml-md-auto d-md-flex">
        <li class="nav-item">
            <a class="nav-link p-2" href="/login">Login</a>
        </li>
        <li class="nav-item">
            <a class="nav-link p-2" href="/register">Register</a>
        </li>
    </ul>
</header>
<main class="bd-masthead" id="content">
    <div class="container">
        <div class="col-12 col-md-6 offset-md-3 mt-3 mt-sm-5">
            <div class="card mb-3">
                <div class="card-header text-white bg-info">Reset Password</div>
                <div class="card-body">
                    <form id="form-password">
                        <input type="hidden" name="action" value="setPassword">
                        <input type="hidden" name="code"
                               value="{{ \Illuminate\Support\Facades\Crypt::encrypt($user->sid) }}">
                        <div class="form-group row">
                            <label for="staticEmail" class="col-3 col-form-label">Username</label>
                            <div class="col-9">
                                <input type="text" class="form-control" value="{{ $user->username }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-3 col-form-label">New Password</label>
                            <div class="col-9">
                                <input type="password" name="password" class="form-control" placeholder="Enter new password">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-3 col-form-label">Repeat Password</label>
                            <div class="col-9">
                                <input type="password" name="re_password" class="form-control" placeholder="Repeat the new password">
                            </div>
                        </div>
                        <div class="form-group text-center">
                            <a class="btn btn-info text-white" @click="password">Reset Password</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.10/vue.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/layer/2.3/layer.js"></script>
<script src="/js/main.js"></script>
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
    
    // 为弹窗添加自定义样式，确保文本在深色模式下可见
    document.head.insertAdjacentHTML('beforeend', `
        <style>
            .layui-layer {
                background-color: var(--bg-color, #fff) !important;
                color: var(--text-color, #333) !important;
            }
            .layui-layer-title {
                background-color: var(--bg-color-offset, #f8f8f8) !important;
                color: var(--text-color, #333) !important;
                border-bottom: 1px solid var(--border-color, #e6e6e6) !important;
            }
            .layui-layer-btn {
                border-top: 1px solid var(--border-color, #e6e6e6) !important;
            }
            .layui-layer-btn a {
                background-color: var(--primary-color, #17a2b8) !important;
                color: white !important;
                border-color: var(--primary-color, #17a2b8) !important;
            }
            .layui-layer-btn .layui-layer-btn1 {
                background-color: var(--bg-color-offset, #f8f8f8) !important;
                color: var(--text-color, #333) !important;
            }
            .layui-layer-content {
                color: var(--text-color, #333) !important;
            }
            .layui-layer-close {
                color: var(--text-color-light, #999) !important;
            }
            
            @media (prefers-color-scheme: dark) {
                :root {
                    --bg-color: #1e1e1e;
                    --bg-color-offset: #2a2a2a;
                    --text-color: #f8f9fa;
                    --text-color-light: #aaa;
                    --border-color: #444;
                    --primary-color: #17a2b8;
                }
            }
        </style>
    `);
    
    new Vue({
        el: '#content',
        methods: {
            password: function () {
                var vm = this;
                this.$post('/password', $("#form-password").serialize())
                    .then(function (data) {
                        if (data.status === 0) {
                            layer.alert(data.message, {
                                closeBtn: 0
                            }, function (i) {
                                window.location.href = "/login";
                            });
                        } else {
                            layer.alert(data.message);
                        }
                    });
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
</html>