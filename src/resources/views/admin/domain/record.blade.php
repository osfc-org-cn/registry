@extends('admin.layout.index')
@section('title', '记录列表')
@section('content')
    <div id="vue" class="pt-3 pt-sm-0">
        <div class="card">
            <div class="card-header">
                记录列表
            </div>
            <div class="card-header">
                <div class="form-inline">
                    <input type="text" disabled="disabled" class="d-none">
                    <div class="form-group">
                        <select class="form-control" v-model="search.did">
                            <option value="0">所有</option>
                            @foreach(\App\Models\Domain::get() as $domain)
                                <option value="{{ $domain->did }}">{{ $domain->domain }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group ml-1">
                        <select class="form-control" v-model="search.type">
                            <option value="0">所有</option>
                            <option value="A">A记录</option>
                            <option value="AAAA">AAAA记录</option>
                            <option value="CNAME">CANME</option>
                            <option value="TXT">TXT记录</option>
                            <option value="MX">MX记录</option>
                        </select>
                    </div>
                    <div class="form-group ml-1">
                        <input type="text" placeholder="UID" class="form-control" v-model="search.uid">
                    </div>
                    <div class="form-group ml-1">
                        <input type="text" placeholder="主机记录" class="form-control" v-model="search.name">
                    </div>
                    <div class="form-group ml-1">
                        <input type="text" placeholder="记录值" class="form-control" v-model="search.value">
                    </div>
                    <a class="btn btn-info ml-1" @click="getList(1)"><i class="fa fa-search"></i> 搜索</a></div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>用户</th>
                            <th>域名</th>
                            <th>记录类型</th>
                            <th>线路</th>
                            <th>记录值</th>
                            <th>添加时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody v-cloak="">
                        <tr v-for="(row,i) in data.data" :key="i">
                            <td>@{{ row.id }}</td>
                            <td>@{{ row.user?row.user.username:'' }}[UID:@{{ row.uid }}]</td>
                            <td>
                                <a :href="'http://'+row.name+'.'+(row.domain?row.domain.domain:'')" target="_blank">
                                    @{{ row.name }}.@{{ row.domain?row.domain.domain:'' }}
                                </a>
                            </td>
                            <td>@{{ row.type }}</td>
                            <td>@{{ row.line }}</td>
                            <td>@{{ row.value }}</td>
                            <td>@{{ row.created_at }}</td>
                            <td>
                                <a class="btn btn-sm btn-danger" @click="del(row.id)">删除</a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer pb-0 text-center">
                @include('admin.layout.pagination')
            </div>
        </div>
        
        <!-- 删除确认模态框 -->
        <div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteConfirmModalLabel">确认删除</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>确定要删除此记录吗？</p>
                        <div class="form-group">
                            <label for="deleteReason">删除原因：</label>
                            <select class="form-control" id="deleteReason" v-model="deleteReason">
                                <option value="">-- 请选择删除原因 --</option>
                                <option value="包含政治、宗教等敏感内容">包含政治、宗教等敏感内容</option>
                                <option value="违反中华人民共和国法规">违反中华人民共和国法规</option>
                                <option value="无法访问">无法访问</option>
                                <option value="用户申请">用户申请</option>
                                <option value="域名解析不当">域名解析不当</option>
                                <option value="定期清理">定期清理</option>
                                <option value="custom">自定义原因...</option>
                            </select>
                        </div>
                        <div class="form-group" v-if="deleteReason === 'custom'">
                            <label for="customReason">自定义原因：</label>
                            <textarea class="form-control" id="customReason" v-model="customReasonText" rows="3" placeholder="请输入删除原因"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
                        <button type="button" class="btn btn-danger" @click="confirmDelete">确认删除</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('foot')
    <script>
        new Vue({
            el: '#vue',
            data: {
                search: {
                    page: 1, did: 0, name: '', type: 0, value: '', uid: $_GET('uid')
                },
                data: {},
                deleteId: null,
                deleteReason: '',
                customReasonText: '',
                deleteReasonMap: {
                    '包含政治、宗教等敏感内容': 'Contains sensitive political or religious content',
                    '违反中华人民共和国法规': 'Violates regulations of the People\'s Republic of China',
                    '无法访问': 'Inaccessible',
                    '用户申请': 'User request',
                    '域名解析不当': 'Improper domain resolution',
                    '定期清理': 'Routine cleanup'
                }
            },
            methods: {
                getList: function (page) {
                    var vm = this;
                    vm.search.page = typeof page === 'undefined' ? vm.search.page : page;
                    this.$post("/admin/domain/record", vm.search, {action: 'select'})
                        .then(function (data) {
                            if (data.status === 0) {
                                vm.data = data.data
                            } else {
                                vm.$message(data.message, 'error');
                            }
                        })
                },
                del: function (id) {
                    this.deleteId = id;
                    this.deleteReason = '';
                    this.customReasonText = '';
                    $('#deleteConfirmModal').modal('show');
                },
                confirmDelete: function() {
                    if (!this.deleteId) return;
                    
                    var vm = this;
                    var reason = this.deleteReason;
                    
                    // 如果是自定义原因，使用自定义文本
                    if (reason === 'custom') {
                        reason = this.customReasonText;
                    } else if (reason && this.deleteReasonMap[reason]) {
                        // 如果是预设原因，转换为英文（用于邮件）
                        reason = this.deleteReasonMap[reason];
                    }
                    
                    this.$post("/admin/domain/record", {action: 'delete', id: this.deleteId, reason: reason})
                        .then(function (data) {
                            if (data.status === 0) {
                                vm.getList();
                                vm.$message(data.message, 'success');
                                $('#deleteConfirmModal').modal('hide');
                            } else {
                                vm.$message(data.message, 'error');
                            }
                        });
                }
            },
            mounted: function () {
                this.getList();
            }
        });
    </script>
@endsection