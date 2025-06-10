@extends('admin.layout.index')

@section('title', '邮件模板测试')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        邮件模板测试
                    </div>
                    <div class="card-body">
                        <p class="mb-3">点击下方链接预览各种邮件模板的最终效果，方便调试和检查。</p>
                        
                        <div class="list-group mb-4">
                            <a href="{{ url('/admin/testview-email?template=verify') }}" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                账户激活邮件模板
                                <span class="badge badge-primary badge-pill">verify</span>
                            </a>
                            <a href="{{ url('/admin/testview-email?template=password') }}" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                密码重置邮件模板
                                <span class="badge badge-primary badge-pill">password</span>
                            </a>
                            <a href="{{ url('/admin/testview-email?template=test') }}" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                邮箱配置测试邮件模板
                                <span class="badge badge-primary badge-pill">test</span>
                            </a>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle mr-2"></i>
                            这些链接会显示邮件的原始HTML内容，便于预览实际效果。这些邮件模板已经过优化，采用表格布局和内联样式，确保在各种邮件客户端中都能正确显示。
                        </div>
                    </div>
                </div>
                
                <div class="card mt-4">
                    <div class="card-header">
                        邮件兼容性提示
                    </div>
                    <div class="card-body">
                        <h5>邮件模板高兼容性要点</h5>
                        <ul class="mt-3">
                            <li>使用表格布局作为基础结构</li>
                            <li>所有样式采用内联CSS，避免外部CSS文件</li>
                            <li>使用网页安全字体(Arial, Helvetica等)</li>
                            <li>避免使用复杂CSS特性(如flexbox, CSS变量等)</li>
                            <li>使用HTML属性而非CSS进行基本对齐(cellpadding, align等)</li>
                            <li>图片设置alt文本和明确尺寸</li>
                            <li>避免使用JavaScript</li>
                            <li>使用简单明了的HTML结构</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 