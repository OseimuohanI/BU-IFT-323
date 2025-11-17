(function(){
  const root = document.documentElement;
  const storageKey = 'site-theme';
  const toggle = () => {
    const isDark = root.classList.toggle('dark');
    try { localStorage.setItem(storageKey, isDark ? 'dark' : 'light'); } catch(e){}
    updateToggleButton(isDark);
  };
  const updateToggleButton = (isDark) => {
    const btn = document.getElementById('theme-toggle');
    if (!btn) return;
    btn.textContent = isDark ? '🌙' : '☀️';
  };
  const applyFromStorage = () => {
    let theme = null;
    try { theme = localStorage.getItem(storageKey); } catch(e){}
    if (!theme) {
      // prefer system if no choice
      if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) theme = 'dark';
      else theme = 'light';
    }
    if (theme === 'dark') root.classList.add('dark');
    else root.classList.remove('dark');
    updateToggleButton(root.classList.contains('dark'));
  };
  document.addEventListener('DOMContentLoaded', function(){
    applyFromStorage();
    const btn = document.getElementById('theme-toggle');
    if (btn) btn.addEventListener('click', toggle);
  });
})();