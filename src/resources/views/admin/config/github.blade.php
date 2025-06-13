@extends('admin.layout.index')
@section('title', 'GitHub认证配置')
@section('content')
    <div id="vue" class="pt-3 pt-sm-0 row">
        <div class="col-12 mt-2">
            <div class="card">
                <div class="card-header">
                    GitHub认证配置
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <p>注意：请在 <a href="https://github.com/settings/developers" target="_blank">GitHub OAuth Apps</a> 中创建应用，并使用以下回调地址：</p>
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" value="{{ url('/github/callback') }}" id="callback_url" readonly>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" onclick="copyCallbackUrl()">复制</button>
                            </div>
                        </div>
                    </div>
                    <form id="form-github">
                        <input type="hidden" name="action" value="config">
                        <div class="form-group row">
                            <label for="github_auth_enabled" class="col-sm-3 col-form-label">启用GitHub认证</label>
                            <div class="col-sm-9">
                                <select name="github_auth_enabled" class="form-control" value="{{ config('sys.github_auth_enabled', '0') }}">
                                    <option value="0">关闭</option>
                                    <option value="1">开启</option>
                                </select>
                                <div class="input_tips">如果启用，要添加NS、MX记录必须认证GITHUB账号才能添加，且账号注册时间大于指定天数</div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="github_client_id" class="col-sm-3 col-form-label">GitHub Client ID</label>
                            <div class="col-sm-9">
                                <input type="text" name="github_client_id" class="form-control" placeholder="输入GitHub OAuth App的Client ID"
                                       value="{{ config('sys.github_client_id', '') }}">
                                <div class="input_tips">在 <a href="https://github.com/settings/developers" target="_blank">https://github.com/settings/developers</a> 创建 OAuth App 获取</div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="github_client_secret" class="col-sm-3 col-form-label">GitHub Client Secret</label>
                            <div class="col-sm-9">
                                <input type="text" name="github_client_secret" class="form-control" placeholder="输入GitHub OAuth App的Client Secret"
                                       value="{{ config('sys.github_client_secret', '') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="github_auth_required_days" class="col-sm-3 col-form-label">GitHub账号最小注册天数</label>
                            <div class="col-sm-9">
                                <input type="number" name="github_auth_required_days" class="form-control" placeholder="输入GitHub账号最小注册天数"
                                       value="{{ config('sys.github_auth_required_days', '180') }}">
                                <div class="input_tips">添加NS、MX记录需要GitHub账号注册时间大于该天数</div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <a class="btn btn-info text-white float-right" @click="form('github')">保存</a>
                </div>
            </div>
        </div>
    </div>

    <script>
    function copyCallbackUrl() {
        var copyText = document.getElementById("callback_url");
        copyText.select();
        document.execCommand("copy");
        layer.msg('已复制到剪贴板', {icon: 1});
    }
    </script>
@endsection

@section('foot')
    <script>
        new Vue({
            el: '#vue',
            data: {},
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
            },
            mounted: function () {
            }
        });
    </script>
@endsection 