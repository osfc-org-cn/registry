document.addEventListener('DOMContentLoaded', function() {
    // 获取用户偏好和存储的主题设置
    const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
    const storedTheme = localStorage.getItem('theme');
    
    // 获取主题切换按钮
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');
    
    // 初始化主题
    let currentTheme = storedTheme || (prefersDarkScheme.matches ? 'dark' : 'light');
    setTheme(currentTheme);
    
    // 监听系统主题变化，但只在用户未手动设置主题时生效
    prefersDarkScheme.addEventListener('change', (e) => {
        if (!storedTheme) {
            currentTheme = e.matches ? 'dark' : 'light';
            setTheme(currentTheme);
        }
    });
    
    // 主题切换按钮点击事件
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            // 切换主题
            currentTheme = currentTheme === 'light' ? 'dark' : 'light';
            setTheme(currentTheme);
            
            // 存储用户偏好
            localStorage.setItem('theme', currentTheme);
        });
    }
    
    // 设置主题并更新图标
    function setTheme(theme) {
        if (theme === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
            updateThemeIcon(true);
        } else {
            document.documentElement.setAttribute('data-theme', 'light');
            updateThemeIcon(false);
        }
    }
    
    // 更新主题图标
    function updateThemeIcon(isDarkMode) {
        if (!themeIcon) return;
        
        if (isDarkMode) {
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
        } else {
            themeIcon.classList.remove('fa-sun');
            themeIcon.classList.add('fa-moon');
        }
    }
}); 