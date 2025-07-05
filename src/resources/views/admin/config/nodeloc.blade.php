@extends('admin.layout.index')
@section('title', 'Nodeloc认证配置')
@section('content')
    <div id="vue" class="pt-3 pt-sm-0 row">
        <div class="col-12 mt-2">
            <div class="card">
                <div class="card-header">
                    Nodeloc认证配置
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <p>注意：请在 <a href="https://conn.nodeloc.cc/apps" target="_blank">Nodeloc OAuth Apps</a> 中创建应用，并使用以下回调地址：</p>
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" value="{{ url('/nodeloc/callback') }}" id="callback_url" readonly>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" onclick="copyCallbackUrl()">复制</button>
                            </div>
                        </div>
                    </div>
                    <form id="form-nodeloc">
                        <input type="hidden" name="action" value="config">
                        <div class="form-group row">
                            <label for="nodeloc_auth_enabled" class="col-sm-3 col-form-label">启用Nodeloc认证</label>
                            <div class="col-sm-9">
                                <select name="nodeloc_auth_enabled" class="form-control" value="{{ config('sys.nodeloc_auth_enabled', '0') }}">
                                    <option value="0">关闭</option>
                                    <option value="1">开启</option>
                                </select>
                                <div class="input_tips">是否启用Nodeloc账号登录和绑定功能</div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nodeloc_client_id" class="col-sm-3 col-form-label">Nodeloc Client ID</label>
                            <div class="col-sm-9">
                                <input type="text" name="nodeloc_client_id" class="form-control" placeholder="输入Nodeloc OAuth App的Client ID"
                                       value="{{ config('sys.nodeloc_client_id', '') }}">
                                <div class="input_tips">在 <a href="https://conn.nodeloc.cc/apps" target="_blank">https://conn.nodeloc.cc/apps</a> 创建应用获取</div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nodeloc_client_secret" class="col-sm-3 col-form-label">Nodeloc Client Secret</label>
                            <div class="col-sm-9">
                                <input type="text" name="nodeloc_client_secret" class="form-control" placeholder="输入Nodeloc OAuth App的Client Secret"
                                       value="{{ config('sys.nodeloc_client_secret', '') }}">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer">
                    <a class="btn btn-info text-white float-right" @click="form('nodeloc')">保存</a>
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
