/* Interacciones del navegador y registro del Service Worker de la PWA. */
document.addEventListener('DOMContentLoaded', () => {
    // Muestra u oculta la contraseña en la pantalla de acceso.
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('contrasena');
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', () => {
            const showing = passwordInput.type === 'text';
            passwordInput.type = showing ? 'password' : 'text';
            togglePassword.classList.toggle('is-visible', !showing);
            togglePassword.setAttribute('aria-label', showing ? 'Mostrar contraseña' : 'Ocultar contraseña');
        });
    }

    // Completa el formulario de acceso con una cuenta de demostración.
    document.querySelectorAll('[data-fill-demo]').forEach((button) => {
        button.addEventListener('click', () => {
            const email = document.getElementById('correo');
            const password = document.getElementById('contrasena');
            if (!email || !password) return;
            email.value = button.dataset.demoEmail || '';
            password.value = button.dataset.demoPassword || '';
            email.focus();
        });
    });

    // Solicita confirmación antes de ejecutar acciones destructivas.
    document.querySelectorAll('.confirm-action').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const message = form.dataset.confirm || '¿Deseas continuar?';
            if (!window.confirm(message)) event.preventDefault();
        });
    });

    // Registra el Service Worker; el sitio debe estar en HTTPS o localhost.
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            const appBase = document.body?.dataset.appUrl || '';
            navigator.serviceWorker.register(`${appBase}/service-worker.js`, {
                scope: `${appBase}/`
            }).catch((error) => {
                console.warn('No se pudo registrar el Service Worker:', error);
            });
        });
    }

    // Muestra el botón de instalación cuando el navegador lo permite.
    let deferredPrompt = null;
    const installButton = document.getElementById('installAppBtn');

    window.addEventListener('beforeinstallprompt', (event) => {
        event.preventDefault();
        deferredPrompt = event;
        if (installButton) installButton.hidden = false;
    });

    if (installButton) {
        installButton.addEventListener('click', async () => {
            if (!deferredPrompt) return;
            deferredPrompt.prompt();
            await deferredPrompt.userChoice;
            deferredPrompt = null;
            installButton.hidden = true;
        });
    }

    window.addEventListener('appinstalled', () => {
        if (installButton) installButton.hidden = true;
    });
});
