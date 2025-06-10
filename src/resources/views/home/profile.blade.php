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
                    
                    <div class="mdui-divider mdui-m-y-2"></div>
                    <div class="mdui-typo-title mdui-m-b-2">Change Password</div>
                    
                    <div class="mdui-textfield">
                        <label class="mdui-textfield-label">Old Password</label>
                        <input class="mdui-textfield-input" type="password" name="old_password" placeholder="Enter your current password"/>
                    </div>
                    
                    <div class="mdui-textfield">
                        <label class="mdui-textfield-label">New Password</label>
                        <input class="mdui-textfield-input" type="password" name="new_password" placeholder="Enter your new password"/>
                    </div>
                </form>
            </div>
            <div class="mdui-card-actions">
                <button class="mdui-btn mdui-btn-raised mdui-ripple mdui-color-theme-accent" @click="form('profile')">Change Password</button>
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