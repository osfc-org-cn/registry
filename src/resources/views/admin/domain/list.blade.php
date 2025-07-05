@extends('admin.layout.index')
@section('title', '域名列表')
@section('content')
    <div id="vue" class="pt-3 pt-sm-0">
        <div class="card">
            <div class="card-header">
                域名列表
                <a href="#modal-add" data-toggle="modal" @click="storeInfo={}"
                   class="float-right btn btn-sm btn-primary">添加</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>编号</th>
                            <th>平台</th>
                            <th>DomainId</th>
                            <th>域名</th>
                            <th title="可使用此域名的用户组">用户组</th>
                            <th>消耗积分</th>
                            <th>介绍</th>
                            <th>添加时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody v-cloak="">
                        <tr v-for="(row,i) in data.data" :key="i">
                            <td>@{{ row.did }}</td>
                            <td>@{{ row.dns }}</td>
                            <td>@{{ row.domain_id }}</td>
                            <td>@{{ row.domain }}</td>
                            <td v-html="getDomainGroups(row.groups)"></td>
                            <td>@{{ row.point }}</td>
                            <td v-html="row.desc"></td>
                            <td>@{{ row.created_at }}</td>
                            <td>
                                <a href="#modal-update" class="btn btn-sm btn-info" data-toggle="modal"
                                   @click="storeInfo=Object.assign({},row)">修改
                                </a>
                                <a class="btn btn-sm btn-danger" @click="del(row.did)">删除</a>
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
        <div class="modal fade" id="modal-add">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">域名添加</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="form-add">
                            <input type="hidden" name="action" value="add">
                            <div class="form-group row">
                                <label for="staticEmail" class="col-sm-3 col-form-label">解析平台</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <select name="dns" class="form-control" v-model="dns">
                                            <option value="0">请选择域名解析平台</option>
                                            @foreach(\App\Models\DnsConfig::all() as $dns)
                                                <option value="{{ $dns->dns }}">{{ $dns->dns }}</option>
                                            @endforeach
                                        </select>
                                        <div class="input-group-append" @click="getDomainList">
                                            <span class="input-group-text btn btn-success">获取</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="staticEmail" class="col-sm-3 col-form-label">选择域名</label>
                                <div class="col-sm-9">
                                    <select name="domain" class="form-control">
                                        <option v-for="(domain,i) in domainList" :key="i"
                                                :value="domain.domain_id+','+domain.domain">
                                            @{{ domain.domain }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="staticEmail" class="col-sm-3 col-form-label">用户组</label>
                                <div class="col-sm-9">
                                    <div class="input_tips">
                                        <div class="form-check">
                                            <input type="checkbox" name="groups[]" value="0"
                                                   class="form-check-input" id="group-0">
                                            <label class="form-check-label"
                                                   for="group-0">所有组</label>
                                        </div>
                                        @foreach(\App\Models\UserGroup::where('gid','>',99)->get() as $group)
                                            <div class="form-check">
                                                <input type="checkbox" name="groups[]" value="{{ $group->gid }}"
                                                       class="form-check-input" id="group-{{ $group->gid }}">
                                                <label class="form-check-label"
                                                       for="group-{{ $group->gid }}">{{ $group->name }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="input_tips">选择可使用此域名的用户组</div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="staticEmail" class="col-sm-3 col-form-label">消耗积分</label>
                                <div class="col-sm-9">
                                    <input type="number" name="point" placeholder="输入用户添加每条解析消耗积分" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="staticEmail" class="col-sm-3 col-form-label">域名介绍</label>
                                <div class="col-sm-9">
                                    <textarea name="desc" rows="5" placeholder="输入域名介绍内容"
                                              class="form-control"></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>
                        <button type="button" class="btn btn-primary" @click="form('add')">添加</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-update">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">域名修改</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="form-update" v-if="storeInfo.did">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="did" :value="storeInfo.did">
                            <div class="form-group row">
                                <label for="staticEmail" class="col-sm-3 col-form-label">用户组</label>
                                <div class="col-sm-9">
                                    <div class="input_tips">
                                        <div class="form-check">
                                            <input type="checkbox" name="groups[]" value="0"
                                                   class="form-check-input" id="group-0"
                                                   :checked="storeInfo.groups==='0'">
                                            <label class="form-check-label"
                                                   for="group-0">所有组</label>
                                        </div>
                                        @foreach(\App\Models\UserGroup::where('gid','>',99)->get() as $group)
                                            <div class="form-check">
                                                <input type="checkbox" name="groups[]" value="{{ $group->gid }}"
                                                       class="form-check-input" id="group-{{ $group->gid }}"
                                                       :checked="(storeInfo.groups.split(',')).indexOf('{{ $group->gid }}')>-1">
                                                <label class="form-check-label"
                                                       for="group-{{ $group->gid }}">{{ $group->name }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="input_tips">选择可使用此域名的用户组</div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="staticEmail" class="col-sm-3 col-form-label">消耗积分</label>
                                <div class="col-sm-9">
                                    <input type="number" name="point" placeholder="输入用户添加每条解析消耗积分" class="form-control"
                                           :value="storeInfo.point">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="staticEmail" class="col-sm-3 col-form-label">域名介绍</label>
                                <div class="col-sm-9">
                                    <textarea name="desc" rows="5" placeholder="输入域名介绍内容" class="form-control"
                                              :value="storeInfo.desc"></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>
                        <button type="button" class="btn btn-primary" @click="form('update')">添加</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 删除确认模态框 -->
        <div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteConfirmModalLabel">确认删除域名</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>确定要删除此域名吗？<strong class="text-danger">此操作将删除该域名下的所有记录！</strong></p>
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
                    page: 1
                },
                data: {},
                storeInfo: {},
                dns: 0,
                domainList: [],
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
                getDomainGroups: function (groups) {
                    if (groups === '0') {
                        return ' <span class="badge badge-danger">所有组</span>';
                    }
                    var str = '';
                    groups = groups.split(',');
                    @foreach(\App\Models\UserGroup::where('gid','>',99)->get() as $group)
                    if (groups.indexOf('{{ $group->gid }}') > -1) {
                        str += ' <span class="badge badge-info">{{ $group->name }}</span>';
                    }
                    @endforeach
                        return str;
                },
                getDomainList: function () {
                    var vm = this;
                    if (!this.dns) {
                        vm.$message('请选择域名解析平台', 'error');
                        return;
                    }
                    this.$post("/admin/domain", {action: 'domainList', dns: this.dns})
                        .then(function (data) {
                            if (data.status === 0) {
                                vm.domainList = data.data
                            } else {
                                vm.$message(data.message, 'error');
                            }
                        })
                },
                getList: function (page) {
                    var vm = this;
                    vm.search.page = typeof page === 'undefined' ? vm.search.page : page;
                    this.$post("/admin/domain", vm.search, {action: 'select'})
                        .then(function (data) {
                            if (data.status === 0) {
                                vm.data = data.data
                            } else {
                                vm.$message(data.message, 'error');
                            }
                        })
                },
                form: function (id) {
                    var vm = this;
                    this.$post("/admin/domain", $("#form-" + id).serialize())
                        .then(function (data) {
                            if (data.status === 0) {
                                vm.getList();
                                $("#modal-" + id).modal('hide');
                                vm.$message(data.message, 'success');
                            } else {
                                vm.$message(data.message, 'error');
                            }
                        });
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
                    
                    this.$post("/admin/domain", {action: 'delete', id: this.deleteId, reason: reason})
                        .then(function (data) {
                            if (data.status === 0) {
                                vm.getList();
                                vm.$message(data.message, 'success');
                                $('#deleteConfirmModal').modal('hide');
                            } else {
                                vm.$message(data.message, 'error');
                            }
                        });
                },
            },
            mounted: function () {
                this.getList();
            }
        });
    </script>
@endsection