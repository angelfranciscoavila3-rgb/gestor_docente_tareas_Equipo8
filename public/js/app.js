document.addEventListener('DOMContentLoaded', function () {
  // Theme Management (Light / Dark Mode)
  const themeToggleBtn = document.getElementById('theme-toggle');
  const storedTheme = localStorage.getItem('app-theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');

  function applyTheme(theme) {
    if (theme === 'dark') {
      document.documentElement.setAttribute('data-theme', 'dark');
      if (themeToggleBtn) {
        themeToggleBtn.innerHTML = `
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
          Modo Claro
        `;
      }
    } else {
      document.documentElement.removeAttribute('data-theme');
      if (themeToggleBtn) {
        themeToggleBtn.innerHTML = `
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
          Modo Oscuro
        `;
      }
    }
  }

  applyTheme(storedTheme);

  if (themeToggleBtn) {
    themeToggleBtn.addEventListener('click', function () {
      const currentTheme = document.documentElement.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
      const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
      localStorage.setItem('app-theme', newTheme);
      applyTheme(newTheme);
    });
  }

  // Client-side Validation for PDF File Upload (Max 2MB limit)
  const evidenciaFileInput = document.getElementById('archivo_evidencia');
  const fileErrorMsg = document.getElementById('file-error-msg');

  if (evidenciaFileInput) {
    evidenciaFileInput.addEventListener('change', function (e) {
      const file = e.target.files[0];
      if (fileErrorMsg) fileErrorMsg.style.display = 'none';

      if (file) {
        const isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
        const maxBytes = 2 * 1024 * 1024; // 2MB

        if (!isPdf) {
          if (fileErrorMsg) {
            fileErrorMsg.textContent = ' Error: Solo se permiten archivos en formato PDF.';
            fileErrorMsg.style.display = 'block';
          }
          evidenciaFileInput.value = '';
          return;
        }

        if (file.size > maxBytes) {
          if (fileErrorMsg) {
            fileErrorMsg.textContent = ' Error: El archivo excede el tamaño máximo permitido (2MB).';
            fileErrorMsg.style.display = 'block';
          }
          evidenciaFileInput.value = '';
          return;
        }
      }
    });
  }
});
