@extends('home.layout.mdui')
@section('title', 'Record List')
@section('head')
    <!-- 引入 Bootstrap 仅用于模态框 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* 样式重置和覆盖，确保 Bootstrap 模态框样式正常 */
        .modal-open {
            overflow: hidden;
            padding-right: 0 !important;
        }
        
        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.5);
        }
        
        .modal-content {
            background-color: var(--card-color);
            color: var(--text-primary);
            border: none;
        }
        
        .modal-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            padding: 15px;
        }
        
        .modal-footer {
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding: 15px;
        }
        
        .modal-title {
            color: var(--text-primary);
        }
        
        .close {
            color: var(--text-primary);
            text-shadow: none;
            opacity: 0.5;
        }
        
        .close:hover {
            color: var(--text-primary);
            opacity: 0.8;
        }
        
        .form-control {
            background-color: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: var(--text-primary);
        }
        
        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: var(--primary-color);
            color: var(--text-primary);
            box-shadow: 0 0 0 0.2rem rgba(33, 150, 243, 0.25);
        }
        
        .form-control:disabled {
            background-color: rgba(255, 255, 255, 0.05);
            color: var(--text-secondary);
        }
        
        /* 增强下拉菜单选项的可读性 */
        select.form-control option {
            background-color: var(--card-color);
            color: var(--text-primary);
        }
        
        /* 针对 MDUI 下拉框的样式调整 */
        .mdui-select-menu {
            background-color: var(--card-color) !important;
        }
        
        .mdui-select-menu-item {
            color: var(--text-primary) !important;
        }
        
        /* 确保下拉选项在任何主题下都有足够的对比度 */
        option {
            background-color: var(--card-color);
            color: #ffffff !important;
            text-shadow: 0 0 0 #ffffff;
        }
        
        /* 针对Webkit浏览器（Chrome、Safari等）的下拉菜单样式 */
        select option {
            background-color: var(--card-color);
            color: #ffffff !important;
        }
        
        /* 修复MDUI下拉菜单文本颜色 */
        .mdui-select-menu-item {
            color: rgba(255, 255, 255, 0.87) !important;
        }
        
        /* 修复激活项目的颜色 */
        .mdui-select-menu-item-selected {
            color: rgba(255, 255, 255, 1) !important;
            font-weight: bold !important;
        }
        
        /* 修复下拉菜单的背景颜色 */
        .mdui-menu {
            background-color: #424242 !important;
        }
        
        /* 确保MDUI选择框中的所有文本都可见 */
        .mdui-select {
            color: #ffffff !important;
        }
        
        /* 增强Bootstrap下拉菜单中的文本可见性 */
        .dropdown-menu {
            background-color: #424242 !important;
        }
        
        .dropdown-item {
            color: #ffffff !important;
        }
        
        .dropdown-item:hover {
            background-color: #616161 !important;
        }
        
        /* 设置选择框激活状态的颜色 */
        .form-control:active, .form-control:focus {
            color: #ffffff !important;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-secondary {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.15);
            color: var(--text-primary);
        }
        
        .btn-secondary:hover {
            background-color: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.2);
            color: var(--text-primary);
        }
        
        .col-form-label {
            color: var(--text-primary);
        }
        
        /* 描述文本区域样式 */
        .description-box {
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: .25rem;
            padding: .375rem .75rem;
            margin-top: .25rem;
            font-size: 14px;
            color: var(--text-secondary);
            max-height: 150px;
            overflow-y: auto;
        }
        
        /* 加载中遮罩 */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1060;
            border-radius: .3rem;
        }
        
        .spinner {
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid var(--primary-color);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
@endsection

@section('content')
    @if(config('sys.html_home'))
        <div class="mdui-panel-item mdui-panel-item-open mdui-m-b-2">
            <div class="mdui-panel-item-body">
                {!! config('sys.html_home') !!}
            </div>
        </div>
    @endif
    <div id="vue">
        <div class="mdui-card">
            <div class="mdui-card-primary">
                <div class="mdui-card-primary-title">
                    Record List
                    <button class="mdui-btn mdui-btn-icon mdui-float-right mdui-color-theme-accent mdui-ripple" 
                            data-toggle="modal" data-target="#modal-store" 
                            @click="storeInfo={did:domainList.length>0?domainList[0].did:0,line_id:'default',type:'A'}">
                        <i class="mdui-icon material-icons">add</i>
                    </button>
                </div>
            </div>
            
            <div class="mdui-card-content">
                <div class="mdui-row mdui-m-b-2">
                    <div class="mdui-col-xs-6 mdui-col-sm-2">
                        <select class="mdui-select" v-model="search.did" style="color: #ffffff; font-weight: bold;">
                            <option value="0" style="color: #ffffff; font-weight: bold;">All Domains</option>
                            <option v-for="(domain,i) in domainList" :value="domain.did" style="color: #ffffff; font-weight: bold;">@{{ domain.domain }}</option>
                        </select>
                    </div>
                    <div class="mdui-col-xs-6 mdui-col-sm-2">
                        <select class="mdui-select" v-model="search.type" style="color: #ffffff; font-weight: bold;">
                            <option value="0" style="color: #ffffff; font-weight: bold;">All Types</option>
                            <option value="A" style="color: #ffffff; font-weight: bold;">A</option>
                            <option value="CNAME" style="color: #ffffff; font-weight: bold;">CNAME</option>
                            <option value="AAAA" style="color: #ffffff; font-weight: bold;">AAAA</option>
                            <option value="TXT" style="color: #ffffff; font-weight: bold;">TXT</option>
                            <option value="MX" style="color: #ffffff; font-weight: bold;">MX</option>
                            <option value="NS" v-if="nsEnabled" style="color: #ffffff; font-weight: bold;">NS</option>
                        </select>
                    </div>
                    <div class="mdui-col-xs-6 mdui-col-sm-3">
                        <div class="mdui-textfield mdui-textfield-floating-label">
                            <label class="mdui-textfield-label">Host Record</label>
                            <input class="mdui-textfield-input" type="text" v-model="search.name"/>
                        </div>
                    </div>
                    <div class="mdui-col-xs-6 mdui-col-sm-3">
                        <div class="mdui-textfield mdui-textfield-floating-label">
                            <label class="mdui-textfield-label">Record Value</label>
                            <input class="mdui-textfield-input" type="text" v-model="search.value"/>
                        </div>
                    </div>
                    <div class="mdui-col-xs-12 mdui-col-sm-2">
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
                            <th>Domain</th>
                            <th>Type</th>
                            <th>Value</th>
                            <th>Add Time</th>
                            <th>Operation</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(row,i) in data.data" :key="i">
                            <td>@{{ row.id }}</td>
                            <td>
                                <a :href="'http://'+row.name+'.'+(row.domain?row.domain.domain:'')" target="_blank" class="mdui-text-color-theme">
                                    @{{ row.name }}.@{{ row.domain?row.domain.domain:'' }}
                                </a>
                            </td>
                            <td><span class="mdui-chip-title">@{{ row.type }}</span></td>
                            <td>@{{ row.value }}</td>
                            <td>@{{ row.created_at }}</td>
                            <td>
                                <button class="mdui-btn mdui-btn-icon mdui-ripple mdui-color-theme" 
                                        data-toggle="modal" data-target="#modal-store"
                                        @click="editRecord(row)">
                                    <i class="mdui-icon material-icons">edit</i>
                                </button>
                                <button class="mdui-btn mdui-btn-icon mdui-ripple mdui-color-red" @click="del(row.id)">
                                    <i class="mdui-icon material-icons">delete</i>
                                </button>
                            </td>
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
        
        <!-- Bootstrap 风格的添加/修改记录对话框 -->
        <div class="modal fade" id="modal-store" tabindex="-1" role="dialog" aria-labelledby="recordModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="recordModalLabel">Record Add/Modify</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="position: relative;">
                        <div v-if="isSubmitting" class="loading-overlay">
                            <div class="spinner"></div>
                        </div>
                        <form id="form-store">
                            <input type="hidden" name="action" value="recordStore">
                            <input type="hidden" name="id" :value="storeInfo.id" v-if="storeInfo.id">
                            <input type="hidden" name="did" :value="storeInfo.did" v-if="storeInfo.id">
                            
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Host Record</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <input type="text" name="name" class="form-control" v-model="storeInfo.name">
                                        <select class="form-control" name="did" style="flex: none; width: 120px; color: #ffffff; font-weight: bold;"
                                                v-model="storeInfo.did" :disabled="storeInfo.id">
                                            <option v-for="(domain,i) in domainList" :value="domain.did" style="color: #ffffff; font-weight: bold;">
                                                @{{ domain.domain }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="description-box mt-2" v-if="!storeInfo.id && storeInfo.type !== 'SRV'">
                                        <div>You can add:</div>
                                        <div>1. A first-level domain: <b>example</b></div>
                                        <div>2. A second-level domain under your own domain: <b>subdomain.yourdomain</b><br>
                                        <small>(requires @{{ subdomainPoint }} additional points)</small></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Type</label>
                                <div class="col-sm-9">
                                    <select name="type" class="form-control" v-model="storeInfo.type" style="color: #ffffff; font-weight: bold;">
                                        <option value="A" style="color: #ffffff; font-weight: bold;">A</option>
                                        <option value="CNAME" style="color: #ffffff; font-weight: bold;">CNAME</option>
                                        <option value="AAAA" style="color: #ffffff; font-weight: bold;">AAAA</option>
                                        <option value="TXT" style="color: #ffffff; font-weight: bold;">TXT</option>
                                        <option value="MX" style="color: #ffffff; font-weight: bold;">MX</option>
                                        <option v-if="isNsAllowedForDomain(storeInfo.did)" value="NS" style="color: #ffffff; font-weight: bold;">NS</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div v-if="storeInfo.type === 'MX'" class="form-group row">
                                <label class="col-sm-3 col-form-label">Priority</label>
                                <div class="col-sm-9">
                                    <input type="number" name="mx_priority" class="form-control" placeholder="Enter MX Priority"
                                           v-model="storeInfo.mx_priority" min="1" max="65535">
                                    <small class="form-text text-muted">Lower values have higher priority (1-100 recommended)</small>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Record Value</label>
                                <div class="col-sm-9">
                                    <input type="text" name="value" class="form-control" :placeholder="getValuePlaceholder()"
                                           v-model="storeInfo.value">
                                    <div class="description-box mt-2" v-if="storeInfo.type === 'MX'">
                                        <div>Enter the mail server hostname (e.g. mail.example.com)</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- 隐藏Line选择，使用隐藏字段代替 -->
                            <input type="hidden" name="line_id" v-model="storeInfo.line_id">
                            
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Points</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" :value="getPointCost()+' Points'" disabled>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" @click="form('store')" :disabled="isSubmitting">Confirm</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('foot')
    <!-- 引入 Bootstrap JS 仅用于模态框 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script>
        new Vue({
            el: '#vue',
            data: {
                search: {
                    page: 1, did: 0, name: '', type: 0, value: ''
                },
                domainList: [],
                data: {},
                storeInfo: {
                    did: 0,
                    line_id: "default",  // 修改为字符串"default"
                    type: 'A',
                    mx_priority: 10
                },
                selectDid: 0,
                desc: '',
                isSubmitting: false,
                subdomainPoint: @json(config('sys.subdomain_point', 0)),
                nsEnabled: @json(config('sys.domain.ns_enabled', 0) ? true : false),
                nsEnabledDomains: @json(config('sys.domain.ns_enabled_domains', '')),
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
                getDomainPoint: function () {
                    var vm = this;
                    for (var i = 0; i < this.domainList.length; i++) {
                        if (this.domainList[i].did === this.storeInfo.did) {
                            vm.desc = this.domainList[i].desc;
                            return this.domainList[i].point;
                        }
                    }
                    return 0;
                },
                getPointCost: function () {
                    const domainPoint = this.getDomainPoint();
                    const isSubdomain = this.storeInfo.name && this.storeInfo.name.includes('.');
                    
                    if (isSubdomain) {
                        return domainPoint + this.subdomainPoint;
                    }
                    
                    return domainPoint;
                },
                // 检查当前选择的域名是否允许NS记录
                isNsAllowedForDomain: function(did) {
                    // 如果全局NS记录已启用，所有域名都允许
                    if (this.nsEnabled) {
                        return true;
                    }
                    
                    // 否则检查域名是否在允许列表中
                    const enabledDomains = this.nsEnabledDomains.split(',');
                    return enabledDomains.includes(did.toString());
                },
                getValuePlaceholder: function() {
                    switch(this.storeInfo.type) {
                        case 'A': return 'Enter IP address (e.g. 192.168.1.1)';
                        case 'AAAA': return 'Enter IPv6 address (e.g. 2001:db8::1)';
                        case 'CNAME': return 'Enter hostname (e.g. example.com)';
                        case 'TXT': return 'Enter text value';
                        case 'MX': return 'Enter mail server hostname';
                        case 'NS': return 'Enter nameserver hostname (e.g. ns1.example.com)';
                        default: return 'Enter Record Value';
                    }
                },
                getLineList: function () {
                    // 始终返回默认线路
                    return [{Name: '默认', Id: 'default'}];
                    
                    // 原始代码注释掉
                    /*
                    for (var i = 0; i < this.domainList.length; i++) {
                        if (this.domainList[i].did === this.storeInfo.did) {
                            if (this.selectDid != this.storeInfo.did) {
                                this.storeInfo.line_id = this.domainList[i].line[0].Id;
                                this.selectDid = this.storeInfo.did
                            }
                            return this.domainList[i].line;
                        }
                    }
                    return [{Name: 'Default', Id: 0}];
                    */
                },
                getList: function (page) {
                    var vm = this;
                    vm.search.page = typeof page === 'undefined' ? vm.search.page : page;
                    this.$post("/home", vm.search, {action: 'recordList'})
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
                getDomainList: function () {
                    var vm = this;
                    this.$post("/home", vm.search, {action: 'domainList'})
                        .then(function (data) {
                            if (data.status === 0) {
                                vm.domainList = data.data
                            } else {
                                mdui.snackbar({
                                    message: data.message,
                                    position: 'bottom',
                                    timeout: 2000
                                });
                            }
                        })
                },
                form: function (id) {
                    var vm = this;
                    vm.isSubmitting = true;
                    
                    // 确保line_id为"default"
                    vm.storeInfo.line_id = "default";
                    
                    // 格式化MX记录的特殊值
                    if (vm.storeInfo.type === 'MX' && vm.storeInfo.mx_priority) {
                        // 创建一个隐藏的表单元素，用于传递优先级
                        var priorityInput = document.createElement('input');
                        priorityInput.type = 'hidden';
                        priorityInput.name = 'mx_priority';
                        priorityInput.value = vm.storeInfo.mx_priority;
                        document.getElementById('form-' + id).appendChild(priorityInput);
                    }
                    
                    this.$post("/home", $("#form-" + id).serialize())
                        .then(function (data) {
                            vm.isSubmitting = false;
                            
                            if (data.status === 0) {
                                vm.getList();
                                $('#modal-' + id).modal('hide');
                                mdui.snackbar({
                                    message: data.message,
                                    position: 'bottom',
                                    timeout: 2000
                                });
                            } else {
                                mdui.snackbar({
                                    message: data.message,
                                    position: 'bottom',
                                    timeout: 2000
                                });
                                // 失败时不关闭对话框
                            }
                        })
                        .catch(function(error) {
                            vm.isSubmitting = false;
                            mdui.snackbar({
                                message: "An error occurred during the request.",
                                position: 'bottom',
                                timeout: 2000
                            });
                        });
                },
                del: function (id) {
                    mdui.confirm('Are you sure you want to delete this record?', 'Confirm Deletion', 
                        function(){
                            var vm = this;
                            this.$post("/home", {action: 'recordDelete', id: id})
                                .then(function (data) {
                                    if (data.status === 0) {
                                        vm.getList();
                                        mdui.snackbar({
                                            message: data.message,
                                            position: 'bottom',
                                            timeout: 2000
                                        });
                                    } else {
                                        mdui.snackbar({
                                            message: data.message,
                                            position: 'bottom',
                                            timeout: 2000
                                        });
                                    }
                                });
                        }.bind(this), 
                        function(){}, 
                        {
                            confirmText: 'Delete',
                            cancelText: 'Cancel'
                        }
                    );
                },
                editRecord: function(row) {
                    // 复制记录数据
                    this.storeInfo = Object.assign({}, row);
                    // 确保line_id设置为"default"
                    this.storeInfo.line_id = "default";
                },
            },
            mounted: function () {
                this.getDomainList();
                this.getList();
            }
        });
    </script>
@endsection