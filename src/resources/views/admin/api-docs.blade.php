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
                        
                        <h4 class="mt-4">API认证</h4>
                        <div class="card mb-4">
                            <div class="card-header">
                                认证方式
                            </div>
                            <div class="card-body">
                                <p>所有API请求都需要通过API密钥进行认证。系统支持以下两种认证方式：</p>
                                
                                <h5 class="mt-3">1. 请求头认证（推荐）</h5>
                                <p>在请求头中添加以下内容：</p>
                                <pre class="bg-light p-3"><code>X-API-KEY: 您的API密钥</code></pre>
                                
                                <h5 class="mt-3">2. GET参数认证</h5>
                                <p>在URL中添加api_key参数：</p>
                                <pre class="bg-light p-3"><code>{{ url('/admin/api/get-all-domains') }}?api_key=您的API密钥</code></pre>
                                
                                <p class="mt-3">您可以在<a href="/admin/config/sys">系统配置</a>页面中设置API密钥。</p>
                                <div class="alert alert-warning">
                                    <i class="fa fa-exclamation-triangle mr-2"></i>
                                    请妥善保管您的API密钥，不要泄露给他人。如果您怀疑密钥已泄露，请立即重新生成一个新的密钥。
                                </div>
                            </div>
                        </div>
                        
                        <h4 class="mt-4">域名API</h4>
                        <div class="card mb-4">
                            <div class="card-header">
                                获取所有域名信息
                            </div>
                            <div class="card-body">
                                <p><strong>请求URL：</strong> <code>{{ url('/admin/api/get-all-domains') }}</code></p>
                                <p><strong>请求方式：</strong> <code>GET</code></p>
                                <p><strong>认证方式：</strong> 
                                    <code>X-API-KEY: 您的API密钥</code> (请求头) 或 
                                    <code>?api_key=您的API密钥</code> (GET参数)
                                </p>
                                <p><strong>返回格式：</strong> <code>JSON</code></p>
                                <p><strong>包含数据：</strong> 域名基本信息、关联的解析记录（包含完整域名）</p>
                                
                                <h5 class="mt-3">筛选参数</h5>
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>参数名</th>
                                            <th>说明</th>
                                            <th>示例</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><code>did</code></td>
                                            <td>按域名ID筛选</td>
                                            <td><code>?did=1</code></td>
                                        </tr>
                                        <tr>
                                            <td><code>type</code></td>
                                            <td>按记录类型筛选</td>
                                            <td><code>?type=A</code> 或 <code>?type=CNAME</code></td>
                                        </tr>
                                        <tr>
                                            <td><code>uid</code></td>
                                            <td>按用户ID筛选</td>
                                            <td><code>?uid=100</code></td>
                                        </tr>
                                        <tr>
                                            <td><code>record_id</code></td>
                                            <td>按记录ID筛选，返回单条记录</td>
                                            <td><code>?record_id=123</code></td>
                                        </tr>
                                    </tbody>
                                </table>
                                
                                <h5 class="mt-3">返回示例</h5>
                                <pre class="bg-light p-3"><code>{
  "status": 0,
  "message": "success",
  "data": [
    {
      "did": 100,
      "domain": "osfc.org.cn",
      "domain_id": "123456",
      "dns": "dnspod",
      "groups": "0",
      "point": 0,
      "desc": "示例域名",
      "created_at": "2023-01-01 00:00:00",
      "updated_at": "2023-01-01 00:00:00",
      "records": [
        {
          "id": 113,
          "did": 100,
          "uid": 100,
          "name": "test",
          "allname": "test.osfc.org.cn",
          "type": "A",
          "value": "192.168.1.1",
          "line": "默认",
          "created_at": "2023-01-01 00:00:00",
          "updated_at": "2023-01-01 00:00:00"
        }
      ]
    }
  ]
}</code></pre>
                                
                                <div class="mt-3">
                                    <button class="btn btn-primary" onclick="testAPI('{{ url('/admin/api/get-all-domains') }}')">测试API</button>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" id="useGetParam" onchange="updateApiTestMethod()">
                                        <label class="form-check-label" for="useGetParam">
                                            使用GET参数而不是请求头
                                        </label>
                                    </div>
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
                                <p><strong>认证方式：</strong> 
                                    <code>X-API-KEY: 您的API密钥</code> (请求头) 或 
                                    <code>?api_key=您的API密钥</code> (GET参数)
                                </p>
                                <p><strong>返回格式：</strong> <code>JSON</code></p>
                                <p><strong>包含数据：</strong> 用户基本信息、用户组信息</p>
                                
                                <h5 class="mt-3">筛选参数</h5>
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>参数名</th>
                                            <th>说明</th>
                                            <th>示例</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><code>uid</code></td>
                                            <td>按用户ID筛选，返回单个用户</td>
                                            <td><code>?uid=100</code></td>
                                        </tr>
                                        <tr>
                                            <td><code>gid</code></td>
                                            <td>按用户组ID筛选</td>
                                            <td><code>?gid=1</code></td>
                                        </tr>
                                        <tr>
                                            <td><code>status</code></td>
                                            <td>按用户状态筛选</td>
                                            <td><code>?status=1</code></td>
                                        </tr>
                                        <tr>
                                            <td><code>email</code></td>
                                            <td>按邮箱模糊搜索</td>
                                            <td><code>?email=example.com</code></td>
                                        </tr>
                                        <tr>
                                            <td><code>username</code></td>
                                            <td>按用户名模糊搜索</td>
                                            <td><code>?username=test</code></td>
                                        </tr>
                                    </tbody>
                                </table>
                                
                                <h5 class="mt-3">返回示例</h5>
                                <pre class="bg-light p-3"><code>{
  "status": 0,
  "message": "success",
  "data": [
    {
      "uid": 100,
      "username": "testuser",
      "email": "test@example.com",
      "gid": 1,
      "point": 100,
      "status": 1,
      "created_at": "2023-01-01 00:00:00",
      "updated_at": "2023-01-01 00:00:00",
      "group": {
        "gid": 1,
        "name": "普通用户"
      }
    }
  ]
}</code></pre>
                                
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
    // 是否使用GET参数进行API测试
    let useGetParam = false;
    
    function updateApiTestMethod() {
        useGetParam = document.getElementById('useGetParam').checked;
    }
    
    function testAPI(url) {
        const responseElement = document.getElementById('api-response');
        responseElement.innerHTML = '加载中...';
        
        // 获取API密钥（从系统配置中）
        const apiKey = '{{ config('sys.api.key') }}';
        
        // 根据选择的方法构建请求
        let requestUrl = url;
        let requestOptions = {};
        
        if (useGetParam) {
            // 使用GET参数
            requestUrl = `${url}?api_key=${apiKey}`;
            requestOptions = {};
        } else {
            // 使用请求头
            requestOptions = {
                headers: {
                    'X-API-KEY': apiKey
                }
            };
        }
        
        fetch(requestUrl, requestOptions)
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