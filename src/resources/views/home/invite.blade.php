@extends('home.layout.mdui')
@section('title', 'Invitation')
@section('content')
    <div id="vue">
        <div class="mdui-card mdui-m-b-4">
            <div class="mdui-card-primary">
                <div class="mdui-card-primary-title">Your Invitation Link</div>
                <div class="mdui-card-primary-subtitle">Share this link with friends to get rewards</div>
            </div>
            <div class="mdui-card-content">
                <div class="mdui-textfield">
                    <label class="mdui-textfield-label">Invitation Link</label>
                    <input class="mdui-textfield-input" type="text" id="inviteLink" :value="inviteLink" readonly/>
                    <div class="mdui-textfield-helper">When someone registers using your link and verifies their email, both of you will receive points</div>
                </div>
                <div class="mdui-m-t-2">
                    <button class="mdui-btn mdui-btn-raised mdui-ripple mdui-color-theme" @click="copyInviteLink">
                        <i class="mdui-icon material-icons">content_copy</i> Copy Link
                    </button>
                </div>
                <div class="mdui-panel mdui-panel-gapless mdui-m-t-3" mdui-panel>
                    <div class="mdui-panel-item">
                        <div class="mdui-panel-item-header">
                            <div class="mdui-panel-item-title">Rewards Information</div>
                            <i class="mdui-panel-item-arrow mdui-icon material-icons">keyboard_arrow_down</i>
                        </div>
                        <div class="mdui-panel-item-body">
                            <p>When someone registers using your invitation link and verifies their email:</p>
                            <ul>
                                <li>You will receive <strong>{{ config('sys.invite.inviter_point', 0) }}</strong> points</li>
                                <li>They will receive <strong>{{ config('sys.invite.invitee_point', 0) }}</strong> extra points</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mdui-card">
            <div class="mdui-card-primary">
                <div class="mdui-card-primary-title">Your Invitations</div>
                <div class="mdui-card-primary-subtitle">List of users you've invited</div>
            </div>
            <div class="mdui-card-content">
                <div class="mdui-table-fluid">
                    <table class="mdui-table mdui-table-hoverable">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(row,i) in data.data" :key="i">
                            <td>@{{ row.id }}</td>
                            <td>@{{ row.invitee ? row.invitee.username : 'Unknown' }}</td>
                            <td>
                                <span v-if="row.status == 1" class="mdui-text-color-green">Verified (Rewarded)</span>
                                <span v-else class="mdui-text-color-orange">Pending Verification</span>
                            </td>
                            <td>@{{ row.created_at }}</td>
                        </tr>
                        <tr v-if="data.data && data.data.length === 0">
                            <td colspan="4" class="mdui-text-center">No invitations yet</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- 分页组件 -->
                <div class="mdui-m-t-2 mdui-text-center" v-if="data.last_page > 1">
                    <ul class="mdui-pagination">
                        <li class="mdui-pagination-item" :class="{'mdui-pagination-item-active': search.page === 1}">
                            <a href="javascript:;" class="mdui-ripple" @click="getList(1)">1</a>
                        </li>
                        <template v-if="data.current_page > 4">
                            <li class="mdui-pagination-item mdui-pagination-item-icon">
                                <a href="javascript:;" class="mdui-ripple">...</a>
                            </li>
                        </template>
                        <template v-for="i in getPages()">
                            <li class="mdui-pagination-item" :class="{'mdui-pagination-item-active': search.page === i}">
                                <a href="javascript:;" class="mdui-ripple" @click="getList(i)">@{{ i }}</a>
                            </li>
                        </template>
                        <template v-if="data.current_page < data.last_page - 3">
                            <li class="mdui-pagination-item mdui-pagination-item-icon">
                                <a href="javascript:;" class="mdui-ripple">...</a>
                            </li>
                            <li class="mdui-pagination-item" :class="{'mdui-pagination-item-active': search.page === data.last_page}">
                                <a href="javascript:;" class="mdui-ripple" @click="getList(data.last_page)">@{{ data.last_page }}</a>
                            </li>
                        </template>
                    </ul>
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
                    page: 1,
                    action: 'inviteList'
                },
                data: {
                    data: []
                },
                inviteLink: window.location.origin + '/register?invite={{ auth()->user()->uid }}'
            },
            methods: {
                getPages: function() {
                    if (!this.data.last_page) return [];
                    
                    let current = this.data.current_page;
                    let last = this.data.last_page;
                    let delta = 2;
                    let range = [];
                    
                    for (let i = Math.max(2, current - delta); i <= Math.min(last - 1, current + delta); i++) {
                        range.push(i);
                    }
                    
                    return range;
                },
                copyInviteLink: function() {
                    var copyText = document.getElementById("inviteLink");
                    copyText.select();
                    copyText.setSelectionRange(0, 99999);
                    document.execCommand("copy");
                    
                    mdui.snackbar({
                        message: 'Invitation link copied to clipboard!',
                        position: 'bottom',
                        timeout: 2000
                    });
                },
                getList: function (page) {
                    var vm = this;
                    vm.search.page = typeof page === 'undefined' ? vm.search.page : page;
                    this.$post("/home", vm.search)
                        .then(function (data) {
                            if (data.status === 0) {
                                vm.data = data.data
                            } else {
                                mdui.snackbar({
                                    message: data.message,
                                    position: 'bottom',
                                    timeout: 2000
                                });
                            }
                        })
                },
            },
            mounted: function () {
                this.getList();
                mdui.mutation();
            }
        });
    </script>
@endsection 