@extends('home.layout.mdui')
@section('title', 'Profile')
@section('content')
    <div id="vue">
        <div class="mdui-card">
            <div class="mdui-card-primary">
                <div class="mdui-card-primary-title">Profile Settings</div>
                <div class="mdui-card-primary-subtitle">Manage your account information and password</div>
            </div>
            <div class="mdui-card-content">
                <form id="form-profile">
                    <input type="hidden" name="action" value="profile">
                    
                    <div class="mdui-textfield">
                        <label class="mdui-textfield-label">User ID</label>
                        <input class="mdui-textfield-input" type="text" value="{{ auth()->user()->uid }}" disabled/>
                    </div>
                    
                    <div class="mdui-textfield">
                        <label class="mdui-textfield-label">Username</label>
                        <input class="mdui-textfield-input" type="text" value="{{ auth()->user()->username }}" disabled/>
                    </div>
                    
                    <div class="mdui-textfield">
                        <label class="mdui-textfield-label">User Group</label>
                        <input class="mdui-textfield-input" type="text" value="{{ auth()->user()->group ? auth()->user()->group->name : '' }}" disabled/>
                    </div>
                    
                    <div class="mdui-textfield">
                        <label class="mdui-textfield-label">Points</label>
                        <input class="mdui-textfield-input" type="text" value="{{ auth()->user()->point }}" disabled/>
                    </div>
                    
                    @if(config('sys.github_auth_enabled', '0') === '1')
                    <div class="mdui-panel mdui-panel-popout" mdui-panel>
                        <div class="mdui-panel-item">
                            <div class="mdui-panel-item-header">
                                <div class="mdui-panel-item-title">GitHub Authentication</div>
                                <div class="mdui-panel-item-summary">
                                    @php
                                        $githubAuth = \App\Models\GithubAuth::getByUid(auth()->user()->uid);
                                    @endphp
                                    @if($githubAuth)
                                        Connected: {{ $githubAuth->github_login }}
                                    @else
                                        Not Connected
                                    @endif
                                </div>
                                <i class="mdui-panel-item-arrow mdui-icon material-icons">keyboard_arrow_down</i>
                            </div>
                            <div class="mdui-panel-item-body">
                                <p>GitHub authentication is required to add NS and MX records. Your GitHub account must be at least {{ config('sys.github_auth_required_days', '180') }} days old.</p>
                                
                                @if($githubAuth)
                                    <div class="mdui-textfield">
                                        <label class="mdui-textfield-label">GitHub Username</label>
                                        <input class="mdui-textfield-input" type="text" value="{{ $githubAuth->github_login }}" disabled/>
                                    </div>
                                    @if($githubAuth->github_name)
                                    <div class="mdui-textfield">
                                        <label class="mdui-textfield-label">GitHub Name</label>
                                        <input class="mdui-textfield-input" type="text" value="{{ $githubAuth->github_name }}" disabled/>
                                    </div>
                                    @endif
                                    @if($githubAuth->github_email)
                                    <div class="mdui-textfield">
                                        <label class="mdui-textfield-label">GitHub Email</label>
                                        <input class="mdui-textfield-input" type="text" value="{{ $githubAuth->github_email }}" disabled/>
                                    </div>
                                    @endif
                                    <div class="mdui-textfield">
                                        <label class="mdui-textfield-label">GitHub Registration Date</label>
                                        <input class="mdui-textfield-input" type="text" value="{{ $githubAuth->github_created_at }}" disabled/>
                                    </div>
                                    <div class="mdui-textfield">
                                        <label class="mdui-textfield-label">Authentication Status</label>
                                        <input class="mdui-textfield-input" type="text" value="{{ $githubAuth->isQualified() ? 'Verified (Can add NS and MX records)' : 'Unverified (GitHub account is too new)' }}" disabled/>
                                    </div>
                                    <form id="form-github-unbind">
                                        <input type="hidden" name="action" value="github_unbind">
                                        <button class="mdui-btn mdui-btn-raised mdui-ripple mdui-color-red-accent" type="button" @click="unbindGithub">Disconnect GitHub</button>
                                    </form>
                                @else
                                    <a href="{{ route('github.redirect') }}" class="mdui-btn mdui-btn-raised mdui-ripple mdui-color-theme-accent">
                                        <i class="mdui-icon material-icons">link</i> Connect GitHub Account
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if(config('sys.nodeloc_auth_enabled', '0') === '1')
                    <div class="mdui-panel mdui-panel-popout" mdui-panel>
                        <div class="mdui-panel-item">
                            <div class="mdui-panel-item-header">
                                <div class="mdui-panel-item-title">Nodeloc Authentication</div>
                                <div class="mdui-panel-item-summary">
                                    @php
                                        $nodelocAuth = \App\Models\UserThird::where('user_id', auth()->user()->uid)
                                            ->where('platform', 'nodeloc')
                                            ->first();
                                    @endphp
                                    @if($nodelocAuth)
                                        Connected: {{ $nodelocAuth->openid }}
                                    @else
                                        Not Connected
                                    @endif
                                </div>
                                <i class="mdui-panel-item-arrow mdui-icon material-icons">keyboard_arrow_down</i>
                            </div>
                            <div class="mdui-panel-item-body">
                                <p>Connect your Nodeloc account for easier login and account management.</p>
                                
                                @if($nodelocAuth)
                                    <div class="mdui-textfield">
                                        <label class="mdui-textfield-label">Nodeloc ID</label>
                                        <input class="mdui-textfield-input" type="text" value="{{ $nodelocAuth->openid }}" disabled/>
                                    </div>
                                    <form id="form-nodeloc-unbind">
                                        <input type="hidden" name="action" value="nodeloc_unbind">
                                        <button class="mdui-btn mdui-btn-raised mdui-ripple mdui-color-red-accent" type="button" @click="unbindNodeloc">Disconnect Nodeloc</button>
                                    </form>
                                @else
                                    <a href="{{ route('nodeloc.redirect') }}" class="mdui-btn mdui-btn-raised mdui-ripple mdui-color-theme-accent">
                                        <i class="mdui-icon material-icons">link</i> Connect Nodeloc Account
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <div class="mdui-textfield">
                        <label class="mdui-textfield-label">Status</label>
                        <select class="mdui-select" :value="{{ auth()->user()->status }}" disabled>
                            <option value="0">Disabled</option>
                            <option value="2">Verified</option>
                            <option value="1">Pending</option>
                        </select>
                        @if(auth()->user()->status==1)
                            <button class="mdui-btn mdui-btn-raised mdui-ripple mdui-color-theme mdui-m-t-1" @click.prevent="verify">Verify Now</button>
                        @endif
                    </div>
                    
                    <div class="mdui-textfield">
                        <label class="mdui-textfield-label">Email</label>
                        <input class="mdui-textfield-input" type="text" value="{{ auth()->user()->email }}" disabled/>
                    </div>
                    
                    <div class="mdui-divider" style="margin: 32px 0 24px"></div>
                    
                    <h2 class="mdui-text-color-theme">Change Password</h2>
                    
                    <div class="mdui-textfield">
                        <label class="mdui-textfield-label">Old Password</label>
                        <input class="mdui-textfield-input" type="password" name="old_password"/>
                    </div>
                    
                    <div class="mdui-textfield">
                        <label class="mdui-textfield-label">New Password</label>
                        <input class="mdui-textfield-input" type="password" name="new_password"/>
                    </div>
                    
                    <button class="mdui-btn mdui-btn-raised mdui-ripple mdui-color-theme-accent" type="button" @click="form('profile')">Save</button>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('foot')
    <script>
        new Vue({
            el: '#vue',
            data: {},
            methods: {
                verify: function () {
                    var vm = this;
                    this.$post("/home", {action: 'verify'})
                        .then(function (data) {
                            if (data.status === 0) {
                                mdui.snackbar({
                                    message: data.message,
                                    position: 'bottom',
                                    timeout: 2000
                                });
                            } else {
                                mdui.snackbar({
                                    message: data.message,
                                    position: 'bottom',
                                    timeout: 2000
                                });
                            }
                        });
                },
                unbindGithub: function() {
                    var vm = this;
                    this.$post("/github/unbind", {})
                        .then(function (data) {
                            if (data.status === 0) {
                                mdui.snackbar({
                                    message: data.message || 'GitHub account disconnected successfully',
                                    position: 'bottom',
                                    timeout: 2000
                                });
                                // 刷新页面以显示更新后的状态
                                setTimeout(function() {
                                    window.location.reload();
                                }, 2000);
                            } else {
                                mdui.snackbar({
                                    message: data.message || 'Failed to disconnect GitHub account',
                                    position: 'bottom',
                                    timeout: 2000
                                });
                            }
                        });
                },
                unbindNodeloc: function() {
                    var vm = this;
                    this.$post("/nodeloc/unbind", {})
                        .then(function (data) {
                            if (data.status === 0) {
                                mdui.snackbar({
                                    message: data.message || 'Nodeloc account disconnected successfully',
                                    position: 'bottom',
                                    timeout: 2000
                                });
                                // 刷新页面以显示更新后的状态
                                setTimeout(function() {
                                    window.location.reload();
                                }, 2000);
                            } else {
                                mdui.snackbar({
                                    message: data.message || 'Failed to disconnect Nodeloc account',
                                    position: 'bottom',
                                    timeout: 2000
                                });
                            }
                        });
                },
                form: function (id) {
                    var vm = this;
                    this.$post("/home", $("#form-" + id).serialize())
                        .then(function (data) {
                            if (data.status === 0) {
                                mdui.snackbar({
                                    message: data.message,
                                    position: 'bottom',
                                    timeout: 2000
                                });
                            } else {
                                mdui.snackbar({
                                    message: data.message,
                                    position: 'bottom',
                                    timeout: 2000
                                });
                            }
                        });
                },
            },
            mounted: function () {
                // 初始化MDUI组件
                mdui.mutation();
            }
        });
    </script>
@endsection