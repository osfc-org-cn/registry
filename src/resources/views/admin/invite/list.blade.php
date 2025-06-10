@extends('admin.layout.index')
@section('title', '邀请记录')
@section('content')
    <div id="vue" class="pt-3 pt-sm-0">
        <div class="card">
            <div class="card-header">
                邀请记录列表
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-2">
                        <select class="form-control" v-model="search.status">
                            <option value="">全部状态</option>
                            <option value="0">未验证</option>
                            <option value="1">已验证</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <input type="text" class="form-control" v-model="search.keyword" placeholder="输入UID或用户名">
                            <div class="input-group-append">
                                <button class="btn btn-primary" @click="getList(1)">搜索</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>邀请人</th>
                            <th>被邀请人</th>
                            <th>状态</th>
                            <th>邀请时间</th>
                            <th>验证时间</th>
                        </tr>
                        </thead>
                        <tbody v-cloak="">
                        <tr v-for="(row,i) in data.data" :key="i">
                            <td>@{{ row.id }}</td>
                            <td>@{{ row.inviter ? row.inviter.username + ' (UID: ' + row.inviter.uid + ')' : '未知' }}</td>
                            <td>@{{ row.invitee ? row.invitee.username + ' (UID: ' + row.invitee.uid + ')' : '未知' }}</td>
                            <td>
                                <span v-if="row.status == 1" class="badge badge-success">已验证</span>
                                <span v-else class="badge badge-warning">未验证</span>
                            </td>
                            <td>@{{ row.created_at }}</td>
                            <td>@{{ row.updated_at != row.created_at ? row.updated_at : '-' }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer pb-0 text-center">
                @include('admin.layout.pagination')
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
                    page: 1,
                    status: '',
                    keyword: '',
                    action: 'inviteList'
                },
                data: {}
            },
            methods: {
                getList: function (page) {
                    var vm = this;
                    vm.search.page = typeof page === 'undefined' ? vm.search.page : page;
                    this.$post("/admin/invite", vm.search)
                        .then(function (data) {
                            if (data.status === 0) {
                                vm.data = data.data
                            } else {
                                vm.$message(data.message, 'error');
                            }
                        })
                },
            },
            mounted: function () {
                this.getList();
            }
        });
    </script>
@endsection 