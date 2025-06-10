@extends('home.layout.mdui')
@section('title', 'Point Details')
@section('content')
    <div id="vue">
        <div class="mdui-card">
            <div class="mdui-card-primary">
                <div class="mdui-card-primary-title">Point Details</div>
                <div class="mdui-card-primary-subtitle">Track your point usage history</div>
            </div>
            
            <div class="mdui-card-content">
                <div class="mdui-row mdui-m-b-2">
                    <div class="mdui-col-xs-8 mdui-col-sm-4">
                        <select class="mdui-select" v-model="search.act">
                            <option value="all">All Activities</option>
                            <option value="increase">Increase</option>
                            <option value="reduce">Reduce</option>
                            <option value="consume">Consume</option>
                        </select>
                    </div>
                    <div class="mdui-col-xs-4 mdui-col-sm-2">
                        <button class="mdui-btn mdui-btn-raised mdui-ripple mdui-color-theme" @click="getList(1)">
                            <i class="mdui-icon material-icons">search</i> Search
                        </button>
                    </div>
                </div>
                
                <div class="mdui-table-fluid">
                    <table class="mdui-table mdui-table-hoverable">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Action</th>
                            <th>Points</th>
                            <th>Remaining</th>
                            <th>Details</th>
                            <th>Time</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(row,i) in data.data" :key="i" 
                            :class="{'mdui-text-color-red-500':row.point<0,'mdui-text-color-green-500':row.point>0}">
                            <td>@{{ row.id }}</td>
                            <td>@{{ row.action }}</td>
                            <td>@{{ row.point }}</td>
                            <td>@{{ row.rest }}</td>
                            <td>@{{ row.remark }}</td>
                            <td>@{{ row.created_at }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination Component -->
                <div class="mdui-m-t-4 mdui-text-center" v-if="data.last_page > 1">
                    <div class="simple-pagination">
                        <!-- Previous Page -->
                        <a class="page-nav" 
                           @click="getList(search.page - 1)" 
                           :class="{'disabled': search.page === 1}">
                            <i class="mdui-icon material-icons">keyboard_arrow_left</i>
                        </a>
                        
                        <!-- Page Info -->
                        <span class="page-info">Page @{{ search.page }} of @{{ data.last_page }}</span>
                        
                        <!-- Next Page -->
                        <a class="page-nav" 
                           @click="getList(search.page + 1)" 
                           :class="{'disabled': search.page === data.last_page}">
                            <i class="mdui-icon material-icons">keyboard_arrow_right</i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <style>
    .custom-pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        margin-bottom: 12px;
    }
    
    .pagination-btn {
        min-width: 36px;
        height: 36px;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        background-color: transparent;
        border: 1px solid #e0e0e0;
        color: #424242;
        transition: all 0.2s ease;
        padding: 0 8px;
    }
    
    .pagination-btn.page-number {
        font-weight: 500;
    }
    
    .pagination-btn:hover:not(:disabled) {
        background-color: rgba(0, 0, 0, 0.04);
        border-color: #bdbdbd;
    }
    
    .pagination-btn.active {
        background-color: var(--mdui-color-theme, #2196f3);
        color: white;
        border-color: var(--mdui-color-theme, #2196f3);
    }
    
    .pagination-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .pagination-info {
        color: #757575;
        font-size: 14px;
        margin-top: 8px;
    }
    
    .simple-pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        margin: 20px 0;
    }
    
    .page-nav {
        width: 36px;
        height: 36px;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        background-color: #37474f;
        color: #ffffff;
        transition: all 0.2s ease;
    }
    
    .page-nav:hover:not(.disabled) {
        background-color: #455a64;
    }
    
    .page-nav.disabled {
        opacity: 0.5;
        cursor: not-allowed;
        background-color: #78909c;
    }
    
    .page-info {
        color: #9e9e9e;
        font-size: 14px;
        padding: 0 15px;
    }
    </style>
@endsection
@section('foot')
    <script>
        new Vue({
            el: '#vue',
            data: {
                search: {
                    page: 1, uid: $_GET('uid'), act: 'all'
                },
                data: {},
                storeInfo: {}
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
                    
                    if (current > 3) {
                        range.unshift(1);
                    }
                    
                    if (current < last - 2) {
                        range.push(last);
                    }
                    
                    return range;
                },
                getList: function (page) {
                    var vm = this;
                    vm.search.page = typeof page === 'undefined' ? vm.search.page : page;
                    this.$post("/home", vm.search, {action: 'pointRecord'})
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