@extends('admin.layout.index')
@section('title', '系统配置')
@section('content')
    <div id="vue" class="pt-3 pt-sm-0 row">
        <div class="col-12 col-md-6 mt-2">
            <div class="card">
                <div class="card-header">
                    站点设置
                </div>
                <div class="card-body">
                    <form id="form-web">
                        <input type="hidden" name="action" value="config">
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">站点名称</label>
                            <div class="col-sm-9">
                                <input type="text" name="web[name]" class="form-control" placeholder="输入站点名称"
                                       value="{{ config('sys.web.name') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">首页标题</label>
                            <div class="col-sm-9">
                                <input type="text" name="web[title]" class="form-control" placeholder="输入首页标题"
                                       value="{{ config('sys.web.title') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">网站关键词</label>
                            <div class="col-sm-9">
                                <input type="text" name="web[keywords]" class="form-control" placeholder="输入网站关键词"
                                       value="{{ config('sys.web.keywords') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">网站描述</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="web[description]" placeholder="输入网站描述"
                                >{{ config('sys.web.description') }}</textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">首页代码</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="html_header" placeholder="输入首页代码（支持html）" rows="5"
                                >{!! config('sys.html_header') !!}</textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">用户公告</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="html_home" placeholder="输入首页代码（支持html）" rows="5"
                                >{!! config('sys.html_home') !!}</textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">首页链接</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="index_urls" placeholder="输入首页顶部链接" rows="3"
                                >{!! config('sys.index_urls') !!}</textarea>
                                <div class="input_tips">
                                    格式：链接名称|链接地址  一行一条
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <a class="btn btn-info text-white float-right" @click="form('web')">保存</a>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 mt-2">
            <div class="card">
                <div class="card-header">
                    用户配置
                </div>
                <div class="card-body">
                    <form id="form-user">
                        <input type="hidden" name="action" value="config">
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">开启注册</label>
                            <div class="col-sm-9">
                                <select name="user[reg]" :value="{{ config('sys.user.reg',0) }}" class="form-control">
                                    <option value="0">关闭注册</option>
                                    <option value="1">开启注册</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">邮箱认证</label>
                            <div class="col-sm-9">
                                <select name="user[email]" :value="{{ config('sys.user.email',0) }}"
                                        class="form-control">
                                    <option value="0">不需要认证</option>
                                    <option value="1">需要认证</option>
                                </select>
                                <div class="input_tips">开启认证，则用户注册后是待认证状态，系统会发送一封认证邮件，用户点击邮件中链接进行认证！</div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">注册赠送积分</label>
                            <div class="col-sm-9">
                                <input type="number" name="user[point]" class="form-control" placeholder="输入注册赠送积分"
                                       value="{{ config('sys.user.point',0) }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">白名单邮箱列表</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="user[email_whitelist]" placeholder="输入允许注册的邮箱域名，一行一个" rows="5"
                                >{{ config('sys.user.email_whitelist') }}</textarea>
                                <div class="input_tips">每行输入一个邮箱域名，例如：gmail.com，只有这些域名的邮箱才能注册。留空表示不限制。</div>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">允许带点邮箱注册</label>
                            <div class="col-sm-9">
                                <select name="user[allow_dot_email]" :value="{{ config('sys.user.allow_dot_email',1) }}" class="form-control">
                                    <option value="0">不允许</option>
                                    <option value="1">允许</option>
                                </select>
                                <div class="input_tips">是否允许用户使用带点的邮箱（如thus.a.word@gmail.com）注册。设置为不允许可以过滤一些临时邮箱。注意：此设置只影响@前面的部分，不影响域名部分。</div>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">开启邀请功能</label>
                            <div class="col-sm-9">
                                <select name="invite[enabled]" :value="{{ config('sys.invite.enabled',0) }}" class="form-control">
                                    <option value="0">关闭邀请</option>
                                    <option value="1">开启邀请</option>
                                </select>
                                <div class="input_tips">开启后，用户可以通过邀请链接邀请新用户注册</div>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">弹框登录</label>
                            <div class="col-sm-9">
                                <select name="login_modal_enabled" :value="{{ config('sys.login_modal_enabled',0) }}" class="form-control">
                                    <option value="0">关闭弹框登录</option>
                                    <option value="1">开启弹框登录</option>
                                </select>
                                <div class="input_tips">开启后，点击登录按钮会弹出登录框，而不是跳转到登录页面</div>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">邀请人奖励积分</label>
                            <div class="col-sm-9">
                                <input type="number" name="invite[inviter_point]" class="form-control" placeholder="邀请人获得的积分奖励"
                                       value="{{ config('sys.invite.inviter_point',0) }}">
                                <div class="input_tips">当被邀请人注册并验证邮箱后，邀请人获得的积分奖励</div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">被邀请人奖励积分</label>
                            <div class="col-sm-9">
                                <input type="number" name="invite[invitee_point]" class="form-control" placeholder="被邀请人获得的积分奖励"
                                       value="{{ config('sys.invite.invitee_point',0) }}">
                                <div class="input_tips">当被邀请人注册并验证邮箱后，被邀请人额外获得的积分奖励</div>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">验证码强度</label>
                            <div class="col-sm-9">
                                <select name="captcha[difficulty]" :value="{{ config('sys.captcha.difficulty', 1) }}" class="form-control" v-model="captchaDifficulty" @change="refreshCaptchaPreview">
                                    <option value="0">简单 - 4位数字</option>
                                    <option value="1">普通 - 4位数字字母混合</option>
                                    <option value="2">较难 - 5位数字字母混合</option>
                                    <option value="3">困难 - 6位数字字母混合</option>
                                </select>
                                <div class="input_tips">设置验证码的难度级别。越高的级别可以提供更好的安全性，但可能会降低用户体验</div>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">验证码图片干扰度</label>
                            <div class="col-sm-9">
                                <select name="captcha[noise]" :value="{{ config('sys.captcha.noise', 1) }}" class="form-control" v-model="captchaNoise" @change="refreshCaptchaPreview">
                                    <option value="0">无干扰 - 纯文字</option>
                                    <option value="1">低 - 少量干扰线</option>
                                    <option value="2">中 - 适中干扰线和噪点</option>
                                    <option value="3">高 - 大量干扰线和噪点</option>
                                </select>
                                <div class="input_tips">设置验证码图片的干扰程度。增加干扰可以防止自动识别，但过高的干扰可能影响用户体验</div>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">验证码预览</label>
                            <div class="col-sm-9">
                                <div class="d-flex align-items-center">
                                    <img :src="captchaPreviewUrl" alt="验证码预览" style="height: 40px; border: 1px solid #ddd; border-radius: 4px;">
                                    <button type="button" class="btn btn-sm btn-outline-secondary ml-2" @click="refreshCaptchaPreview">
                                        <i class="fas fa-sync-alt"></i> 刷新
                                    </button>
                                </div>
                                <div class="input_tips mt-2">这是当前设置下生成的验证码预览效果，可以点击刷新按钮查看不同的效果</div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <a class="btn btn-info text-white float-right" @click="form('user')">保存</a>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 mt-2">
            <div class="card">
                <div class="card-header">
                    邮箱配置
                </div>
                <div class="card-body">
                    <form id="form-mail">
                        <input type="hidden" name="action" value="config">
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">SMTP服务器地址(host)</label>
                            <div class="col-sm-9">
                                <input type="text" name="mail[host]" class="form-control" placeholder="SMTP服务器地址"
                                       value="{{ config('sys.mail.host','smtp.qq.com') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">SMTP服务器端口(port)</label>
                            <div class="col-sm-9">
                                <input type="text" name="mail[port]" class="form-control" placeholder="SMTP服务器端口"
                                       value="{{ config('sys.mail.port','465') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">加密类型</label>
                            <div class="col-sm-9">
                                <select name="mail[encryption]" :value="'{{ config('sys.mail.encryption','ssl') }}'"
                                        class="form-control">
                                    <option value="ssl">SSL</option>
                                    <option value="tls">TSL</option>
                                    <option value="">不加密</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">邮箱账号</label>
                            <div class="col-sm-9">
                                <input type="text" name="mail[username]" class="form-control" placeholder="邮箱账号"
                                       value="{{ config('sys.mail.username') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">邮箱密码</label>
                            <div class="col-sm-9">
                                <input type="text" name="mail[password]" class="form-control" placeholder="邮箱密码"
                                       value="{{ config('sys.mail.password') }}">
                                <div class="input_tips">这个密码可能不是邮箱登录密码，需要在邮箱里单独获取或者设置</div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">发送测试</label>
                            <div class="col-sm-9">
                                <input type="text" name="mail[test]" class="form-control" placeholder="输入一个邮箱地址"
                                       value="{{ config('sys.mail.test','123456@qq.com') }}">
                                <div class="input_tips">输入一个邮箱地址，用于测试发送邮件！</div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <a class="btn btn-info text-white float-right" @click="form('mail')">保存</a>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 mt-2">
            <div class="card">
                <div class="card-header">
                    域名配置
                </div>
                <div class="card-body">
                    <form id="form-domain">
                        <input type="hidden" name="action" value="config">
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">保留前缀</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="reserve_domain_name" placeholder="输入你想保留的域名前缀"
                                          rows="3"
                                >{{ config('sys.reserve_domain_name') }}</textarea>
                                <div class="input_tips">多个用,隔开 举例：www,m,3g,4g</div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">下级域名积分</label>
                            <div class="col-sm-9">
                                <input type="number" name="subdomain_point" class="form-control" placeholder="管理下级域名所需积分"
                                       value="{{ config('sys.subdomain_point', 0) }}">
                                <div class="input_tips">用户添加自己域名下的二级域名（如example.oooo.osfc.org.cn）所需的积分，设为0表示免费</div>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">启用 Fuckabuser</label>
                            <div class="col-sm-9">
                                <select name="domain[fuckabuser_enabled]" :value="{{ config('sys.domain.fuckabuser_enabled', 1) }}" class="form-control">
                                    <option value="0">关闭</option>
                                    <option value="1">开启</option>
                                </select>
                                <div class="input_tips">是否启用 Fuckabuser API 检查用户添加的 IP 地址。启用后，系统会检查 IP 是否在黑名单中，如果在黑名单中则拒绝添加。</div>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">允许 NS 记录</label>
                            <div class="col-sm-9">
                                <select name="domain[ns_enabled]" :value="{{ config('sys.domain.ns_enabled', 0) }}" class="form-control">
                                    <option value="0">关闭</option>
                                    <option value="1">全局开启</option>
                                </select>
                                <div class="input_tips">是否允许用户添加 NS 记录。NS 记录用于指定域名的权威域名服务器，开启后用户可以将子域名委托给其他 DNS 服务器管理。</div>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">单独域名允许NS记录</label>
                            <div class="col-sm-9">
                                <input type="text" name="domain[ns_enabled_domains]" class="form-control" placeholder="输入允许添加NS记录的域名ID，用逗号分隔"
                                       value="{{ config('sys.domain.ns_enabled_domains', '') }}">
                                <div class="input_tips">当全局NS记录设置为"关闭"时，可以指定特定域名允许添加NS记录。请输入域名的did（域名ID），多个ID用英文逗号分隔。例如：1,2,3</div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <a class="btn btn-info text-white float-right" @click="form('domain')">保存</a>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 mt-2">
            <div class="card">
                <div class="card-header">
                    友情链接
                </div>
                <div class="card-body">
                    <form id="form-friendlinks">
                        <input type="hidden" name="action" value="config">
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">启用友情链接</label>
                            <div class="col-sm-9">
                                <select name="friendlinks[enabled]" :value="{{ config('sys.friendlinks.enabled', 1) }}" class="form-control">
                                    <option value="0">关闭</option>
                                    <option value="1">开启</option>
                                </select>
                                <div class="input_tips">是否在首页底部显示友情链接</div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">链接列表</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="friendlinks[links]" placeholder="输入友情链接列表，一行一个链接，格式：名称|链接地址|描述(可选)" rows="8"
                                >{{ config('sys.friendlinks.links') }}</textarea>
                                <div class="input_tips">
                                    格式：网站名称|网站链接|网站描述(可选)<br>
                                    每行一个链接，例如：<br>
                                    OSFC Registry|https://osfc.org.cn|Open Source & Free Code<br>
                                    JiShareDomain|https://lsdt.top
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">链接标题</label>
                            <div class="col-sm-9">
                                <input type="text" name="friendlinks[title]" class="form-control" placeholder="友情链接区域标题"
                                       value="{{ config('sys.friendlinks.title', 'Our Partners') }}">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <a class="btn btn-info text-white float-right" @click="form('friendlinks')">保存</a>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 mt-2">
            <div class="card">
                <div class="card-header">
                    首页文本配置
                </div>
                <div class="card-body">
                    <form id="form-homepage">
                        <input type="hidden" name="action" value="config">
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">Hero 标题</label>
                            <div class="col-sm-9">
                                <input type="text" name="homepage[hero_title]" class="form-control" placeholder="输入首页大标题"
                                       value="{{ config('sys.homepage.hero_title', 'Free Domain Registration') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">Hero 描述</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="homepage[hero_description]" placeholder="输入首页描述文字" rows="2"
                                >{{ config('sys.homepage.hero_description', 'Get your own domain from OSFC. Our mission is promoting Open Source & Free Code for everyone.') }}</textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">搜索框占位符</label>
                            <div class="col-sm-9">
                                <input type="text" name="homepage[search_placeholder]" class="form-control" placeholder="输入搜索框占位符"
                                       value="{{ config('sys.homepage.search_placeholder', 'Enter your desired subdomain prefix') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">特点标题</label>
                            <div class="col-sm-9">
                                <input type="text" name="homepage[features_title]" class="form-control" placeholder="输入特点部分标题"
                                       value="{{ config('sys.homepage.features_title', 'Why Choose OSFC Registry?') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">特点内容</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="homepage[features_content]" placeholder="输入特点内容，格式：标题|图标|描述，一行一个特点" rows="6"
                                >{{ config('sys.homepage.features_content', "Completely Free|fas fa-gift|Our domain registration service is 100% free, with no hidden fees or charges. Get your domain without spending a penny.\nOpen Source|fas fa-code|The entire platform is open source. You can contribute, inspect, or fork our code on GitHub.\nSecure & Reliable|fas fa-shield-alt|We provide robust DNS management with high availability and security features to protect your domain.") }}</textarea>
                                <div class="input_tips">
                                    格式：标题|图标|描述<br>
                                    每行一个特点，图标使用Font Awesome类名
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">关于标题</label>
                            <div class="col-sm-9">
                                <input type="text" name="homepage[about_title]" class="form-control" placeholder="输入关于部分标题"
                                       value="{{ config('sys.homepage.about_title', 'About OSFC') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">关于内容</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="homepage[about_content]" placeholder="输入关于部分内容，每段一行" rows="8"
                                >{{ config('sys.homepage.about_content', "OSFC is a community-driven initiative dedicated to supporting open source projects and providing free services to developers worldwide. The name represents our core values: Open Source & Free Code.\nOur mission is to make technology more accessible by removing financial barriers. We believe that everyone should have access to the tools they need to create, learn, and share their work online.\nOSFC is open to all people, regardless of background, experience level, or location. We welcome contributions from anyone who shares our vision of a more open and accessible internet.\nHowever, we firmly oppose anyone using OSFC resources for any illegal activities. We operate in accordance with the relevant laws and regulations of the People's Republic of China.") }}</textarea>
                                <div class="input_tips">
                                    每行一段文字，将自动分段显示
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">使用步骤标题</label>
                            <div class="col-sm-9">
                                <input type="text" name="homepage[steps_title]" class="form-control" placeholder="输入使用步骤部分标题"
                                       value="{{ config('sys.homepage.steps_title', 'How It Works') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">使用步骤内容</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="homepage[steps_content]" placeholder="输入使用步骤内容，格式：标题|描述，一行一个步骤" rows="6"
                                >{{ config('sys.homepage.steps_content', "Choose Your Domain|Enter your preferred subdomain name and select an available extension from our list.\nCreate an Account|Sign up for a free account to manage your domains and DNS records.\nSet Up DNS Records|Add A records to point your domain to an IP address, or CNAME records to link to another domain.\nGo Live|Your domain is ready to use! DNS changes typically propagate within minutes.") }}</textarea>
                                <div class="input_tips">
                                    格式：标题|描述<br>
                                    每行一个步骤
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">按钮文本</label>
                            <div class="col-sm-9">
                                <input type="text" name="homepage[cta_button]" class="form-control" placeholder="输入按钮文本"
                                       value="{{ config('sys.homepage.cta_button', 'Get Started Now') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="staticEmail" class="col-sm-3 col-form-label">页脚文本</label>
                            <div class="col-sm-9">
                                <input type="text" name="homepage[footer_text]" class="form-control" placeholder="输入页脚文本"
                                       value="{{ config('sys.homepage.footer_text', 'OSFC.org.cn - Open Source & Free Code. All rights reserved.') }}">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <a class="btn btn-info text-white float-right" @click="form('homepage')">保存</a>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('foot')
    <script>
        new Vue({
            el: '#vue',
            data: function () {
                return {
                    captchaDifficulty: {{ config('sys.captcha.difficulty', 1) }},
                    captchaNoise: {{ config('sys.captcha.noise', 1) }},
                    captchaPreviewUrl: ''
                };
            },
            mounted: function () {
                this.refreshCaptchaPreview();
            },
            methods: {
                form: function (id) {
                    var vm = this;
                    this.$post("/admin/config", $("#form-" + id).serialize())
                        .then(function (data) {
                            if (data.status === 0) {
                                vm.$message(data.message, 'success');
                            } else {
                                vm.$message(data.message, 'error');
                            }
                        });
                },
                refreshCaptchaPreview: function () {
                    this.captchaPreviewUrl = "/captcha/preview?difficulty=" + this.captchaDifficulty + "&noise=" + this.captchaNoise + "&_=" + Math.random();
                }
            }
        });
    </script>
@endsection