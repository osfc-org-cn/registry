@extends('admin.layout.index')
@section('title', '接口配置')
@section('content')
    <div id="vue" class="pt-3 pt-sm-0">
        <div class="card">
            <div class="card-header">
                域名解析平台接口配置
                <a href="#modal-store" data-toggle="modal" @click="storeInfo={dns:0}"
                   class="float-right btn btn-sm btn-primary">添加</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>平台</th>
                            <th>配置</th>
                            <th>添加时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody v-cloak="">
                        <tr v-for="(row,i) in data.data" :key="i">
                            <td>@{{ row.dns }}</td>
                            <td>@{{ row.config }}</td>
                            <td>@{{ row.created_at }}</td>
                            <td>
                                <a href="#modal-store" class="btn btn-sm btn-info" data-toggle="modal"
                                   @click="storeInfo=Object.assign({},row)">编辑
                                </a>
                                <a class="btn btn-sm btn-danger" @click="del(row.dns)">删除</a>
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
        <div class="modal fade" id="modal-store">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">用户组修改/添加</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="form-store">
                            <input type="hidden" name="action" value="store">
                            <div class="form-group row">
                                <label for="staticEmail" class="col-sm-3 col-form-label">解析平台</label>
                                <div class="col-sm-9">
                                    <select name="dns" class="form-control" v-model="storeInfo.dns">
                                        <option value="0">请选择域名解析平台</option>
                                        <option v-for="(config,dns) in dnsList" :value="dns" :disabled="isDisabled(dns)" :class="{'text-muted': isDisabled(dns)}">
                                            @{{ dns }} <span v-if="isDisabled(dns)" class="text-danger">(此版本不可用)</span>
                                        </option>
                                    </select>
                                    <small class="form-text text-muted" v-if="storeInfo.dns === 'Cloudflare'">
                                        Cloudflare在当前版本中不可用。如需启用，请在控制台执行: <code>window.enableCloudflare = true;</code>
                                    </small>
                                </div>
                            </div>
                            <template v-if="storeInfo.dns!=0 && !isDisabled(storeInfo.dns)">
                                <div class="form-group row" v-for="(config,i) in dnsList[storeInfo.dns]" :key="i">
                                    <label for="staticEmail" class="col-sm-3 col-form-label">@{{ config.name }}</label>
                                    <div class="col-sm-9">
                                        <input type="text" :placeholder="config.placeholder" class="form-control"
                                               :name="'config['+config.name+']'"
                                               :value="storeInfo.config?storeInfo.config[config.name]:''">
                                        <div class="input_tips" v-html="config.tips" v-if="config.tips"></div>
                                    </div>
                                </div>
                            </template>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>
                        <button type="button" class="btn btn-primary" @click="form('store')" :disabled="isDisabled(storeInfo.dns)">保存</button>
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
                dnsList: []
            },
            methods: {
                isDisabled: function(dns) {
                    // 默认禁用Cloudflare，除非通过控制台命令启用
                    if (dns === 'Cloudflare') {
                        return !window.enableCloudflare;
                    }
                    return false;
                },
                getList: function (page) {
                    var vm = this;
                    vm.search.page = typeof page === 'undefined' ? vm.search.page : page;
                    this.$post("/admin/config/dns", vm.search, {action: 'select'})
                        .then(function (data) {
                            if (data.status === 0) {
                                vm.data = data.data
                            } else {
                                vm.$message(data.message, 'error');
                            }
                        })
                },
                getAllDns: function () {
                    var vm = this;
                    this.$post("/admin/config/dns", vm.search, {action: 'all'})
                        .then(function (data) {
                            if (data.status === 0) {
                                vm.dnsList = data.data
                            } else {
                                vm.$message(data.message, 'error');
                            }
                        })
                },
                form: function (id) {
                    var vm = this;
                    // 如果是禁用的DNS服务，阻止提交
                    if (vm.isDisabled(vm.storeInfo.dns)) {
                        vm.$message('此DNS服务提供商当前版本不可用', 'error');
                        return;
                    }
                    
                    this.$post("/admin/config/dns", $("#form-" + id).serialize())
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
                    if (!confirm('确认删除？')) return;
                    var vm = this;
                    this.$post("/admin/config/dns", {action: 'delete', dns: id})
                        .then(function (data) {
                            if (data.status === 0) {
                                vm.getList();
                                vm.$message(data.message, 'success');
                            } else {
                                vm.$message(data.message, 'error');
                            }
                        });
                },
            },
            mounted: function () {
                this.getList();
                this.getAllDns();
                
                // 初始化Cloudflare启用状态
                if (typeof window.enableCloudflare === 'undefined') {
                    window.enableCloudflare = false;
                }
                
                // 监听控制台变量变化
                Object.defineProperty(window, 'enableCloudflare', {
                    set: function(newValue) {
                        this._enableCloudflare = newValue;
                        console.log('Cloudflare ' + (newValue ? '已启用' : '已禁用'));
                    },
                    get: function() {
                        return this._enableCloudflare;
                    }
                });
            }
        });
    </script>
@endsection