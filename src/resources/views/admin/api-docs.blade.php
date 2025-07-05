@extends('admin.layout.index')

@section('title', 'API文档')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        API文档
                    </div>
                    <div class="card-body">
                        <p class="mb-3">以下是系统提供的API接口，仅限管理员使用。所有API都需要管理员登录后才能访问。</p>
                        
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle mr-2"></i>
                            这些API接口返回JSON格式数据，可用于数据导出、第三方集成或自动化任务。
                        </div>
                        
                        <h4 class="mt-4">域名API</h4>
                        <div class="card mb-4">
                            <div class="card-header">
                                获取所有域名信息
                            </div>
                            <div class="card-body">
                                <p><strong>请求URL：</strong> <code>{{ url('/admin/api/get-all-domains') }}</code></p>
                                <p><strong>请求方式：</strong> <code>GET</code></p>
                                <p><strong>返回格式：</strong> <code>JSON</code></p>
                                <p><strong>包含数据：</strong> 域名基本信息、关联的解析记录</p>
                                <div class="mt-3">
                                    <button class="btn btn-primary" onclick="testAPI('{{ url('/admin/api/get-all-domains') }}')">测试API</button>
                                </div>
                            </div>
                        </div>
                        
                        <h4 class="mt-4">用户API</h4>
                        <div class="card mb-4">
                            <div class="card-header">
                                获取所有用户信息
                            </div>
                            <div class="card-body">
                                <p><strong>请求URL：</strong> <code>{{ url('/admin/api/get-all-users') }}</code></p>
                                <p><strong>请求方式：</strong> <code>GET</code></p>
                                <p><strong>返回格式：</strong> <code>JSON</code></p>
                                <p><strong>包含数据：</strong> 用户基本信息、用户组信息</p>
                                <div class="mt-3">
                                    <button class="btn btn-primary" onclick="testAPI('{{ url('/admin/api/get-all-users') }}')">测试API</button>
                                </div>
                            </div>
                        </div>
                        
                        <h4 class="mt-4">API响应示例</h4>
                        <div class="card">
                            <div class="card-body">
                                <pre id="api-response" class="bg-light p-3" style="max-height: 400px; overflow: auto;">点击上方的"测试API"按钮查看响应数据</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('foot')
<script>
    function testAPI(url) {
        const responseElement = document.getElementById('api-response');
        responseElement.innerHTML = '加载中...';
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                responseElement.innerHTML = JSON.stringify(data, null, 2);
            })
            .catch(error => {
                responseElement.innerHTML = '请求出错: ' + error;
            });
    }
</script>
@endsection 