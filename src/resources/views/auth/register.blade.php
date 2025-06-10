<!-- 假设这是注册表单的一部分 -->
<form method="POST" action="{{ route('register') }}">
    @csrf
    
    <!-- 用户名字段 -->
    <div class="form-group">
        <label for="username">用户名</label>
        <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="username" autofocus>
        @error('username')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    
    <!-- 邮箱字段 -->
    <div class="form-group">
        <label for="email">邮箱地址</label>
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
        @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    
    <!-- 密码字段 -->
    <div class="form-group">
        <label for="password">密码</label>
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
        @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    
    <!-- 确认密码字段 -->
    <div class="form-group">
        <label for="password-confirm">确认密码</label>
        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
    </div>
    
    <!-- 邀请码隐藏字段 -->
    @if(isset($invite))
    <input type="hidden" name="invite" value="{{ $invite }}">
    <div class="form-group">
        <div class="alert alert-info">
            您正在使用邀请链接注册，验证邮箱后您将获得额外积分奖励！
        </div>
    </div>
    @endif
    
    <div class="form-group mb-0">
        <button type="submit" class="btn btn-primary">
            注册
        </button>
    </div>
</form> 