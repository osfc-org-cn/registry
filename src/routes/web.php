<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('captcha', 'Index\IndexController@captcha');//验证码
Route::get('captcha/preview', 'Index\IndexController@captchaPreview');//验证码预览
Route::any('login', 'Auth\LoginController@userLogin')->name('login');//登录
Route::post('reg', 'Index\IndexController@reg');//注册
Route::get('register', 'Index\IndexController@registerForm');//注册页面
Route::any('password', 'Index\IndexController@password');//找回密码
Route::get('forgot-password', 'Index\IndexController@forgotPasswordForm');//找回密码页面
Route::any('logout', 'Auth\LoginController@logout');//退出
Route::any('admin/login', 'Auth\LoginController@adminLogin');//后台登录
Route::any('admin/logout', 'Auth\LoginController@adminLogout');//后台退出
Route::any('verify', 'Index\IndexController@verify');//邮件认证
Route::get('email-whitelist', 'Index\IndexController@emailWhitelist');//邮箱白名单列表
Route::get('version', function () {
    return ['status' => 0, 'version' => config('version')];
});

Route::get('/', function () {
    return view('index');
});

Route::any('install', 'InstallController@install');

Route::get('cache', function () {
    opcache_reset();
    echo "Del cache success!";
});

Route::get('cron/check/{key}', 'Index\IndexController@autoCheck');

Route::post('check', 'Index\IndexController@check');

Route::prefix('home')->middleware(['auth', 'auth.session:web'])->namespace('Home')->group(function () {
    Route::get('/', function () {
        return view('home.index');
    });
    Route::get('point', function () {
        return view('home.point');
    });
    Route::get('profile', function () {
        return view('home.profile');
    });
    Route::post('/', 'HomeController@post');
    Route::get('/invite', 'InviteController@index')->name('home.invite');
});

Route::prefix('admin')->middleware('auth:admin', 'auth.session:admin')->namespace('Admin')->group(function () {
    Route::get('/', function () {
        return view('admin.index');
    });
    Route::get('profile', function () {
        return view('admin.profile');
    });
    Route::post('/', 'AdminController@post');

    Route::get('testview-email', 'AdminController@testEmailView');
    Route::get('email-test', 'AdminController@testEmailIndex');
    
    // API路由
    Route::prefix('api')->group(function () {
        Route::get('get-all-domains', 'AdminController@getAllDomains');
        Route::get('get-all-users', 'AdminController@getAllUsers');
    });

    // API文档页面
    Route::get('api-docs', function () {
        return view('admin.api-docs');
    });

    Route::prefix('user')->group(function () {
        Route::post('/', 'UserController@post');
        Route::get('list', function () {
            return view('admin.user.list');
        });

        Route::post('group', 'UserGroupController@post');
        Route::get('group', function () {
            return view('admin.user.group');
        });

        Route::get('point', function () {
            return view('admin.user.point');
        });
        
        Route::get('invite', 'InviteController@index')->name('admin.invite');
        Route::post('invite', 'InviteController@post');
    });

    Route::prefix('config')->group(function () {
        Route::post('dns', 'DnsConfigController@post');
        Route::get('dns', function () {
            return view('admin.config.dns');
        });
        Route::get('sys', function () {
            return view('admin.config.sys');
        });
        Route::get('check', function () {
            return view('admin.config.check');
        });
        Route::get('github', function () {
            return view('admin.config.github');
        });
        Route::get('nodeloc', function () {
            return view('admin.config.nodeloc');
        });
        Route::post('/', 'ConfigController@post');
    });

    Route::prefix('domain')->group(function () {
        Route::post('/', 'DomainController@post');
        Route::get('list', function () {
            return view('admin.domain.list');
        });

        Route::post('record', 'DomainRecordController@post');
        Route::get('record', function () {
            return view('admin.domain.record');
        });
    });
});

// GitHub认证路由
Route::get('/github/redirect', 'GithubAuthController@redirect')->name('github.redirect');
Route::get('/github/callback', 'GithubAuthController@callback')->name('github.callback');
Route::post('/github/unbind', 'GithubAuthController@unbind')->name('github.unbind');
Route::get('/github/login', 'GithubAuthController@login')->name('github.login');

// Nodeloc认证路由
Route::match(['get', 'post'], '/nodeloc/redirect', 'NodelocAuthController@redirect')->name('nodeloc.redirect');
Route::get('/nodeloc/callback', 'NodelocAuthController@callback')->name('nodeloc.callback');
Route::post('/nodeloc/unbind', 'NodelocAuthController@unbind')->name('nodeloc.unbind');
Route::get('/nodeloc/login', 'NodelocAuthController@login')->name('nodeloc.login');
