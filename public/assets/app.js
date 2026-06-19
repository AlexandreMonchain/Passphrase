document.addEventListener('DOMContentLoaded', function () {

    // --- Soumission AJAX du formulaire ---
    const form = document.getElementById('password-generator-form');
    if (form) {
        const generateBtn  = form.querySelector('[type="submit"]');
        const loading      = document.getElementById('loading-animation');
        const passwordsDiv = document.getElementById('generated-passwords');

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            generateBtn.disabled = true;
            loading.classList.remove('d-none');

            fetch('/', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(form),
            })
                .then(function (r) { return r.text(); })
                .then(function (html) {
                    passwordsDiv.innerHTML = html;
                })
                .finally(function () {
                    generateBtn.disabled = false;
                    loading.classList.add('d-none');
                });
        });
    }

    // --- Copie de mot de passe ---
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.copy-btn-round');
        if (!btn) return;
        e.preventDefault();

        const password = btn.dataset.password;
        if (!password) return;

        navigator.clipboard.writeText(password).then(function () {
            const original = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i>';
            setTimeout(function () { btn.innerHTML = original; }, 2000);
        });
    });

    // --- Bouton réinitialisation des paramètres ---
    const resetBtn = document.getElementById('load-defaults-btn');
    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            const fields = {
                'password_generation_form_nb_mots':           { value: '2' },
                'password_generation_form_longueur_minimale': { value: '12' },
                'password_generation_form_separateur':        { value: 'random' },
                'password_generation_form_longueur_nombre':   { value: '2' },
                'password_generation_form_caractere_special': { value: 'random' },
                'password_generation_form_majuscule_debut':        { checked: true },
                'password_generation_form_majuscule_aleatoire':    { checked: false },
                'password_generation_form_caracteres_accentues':   { checked: true },
            };

            for (const [id, def] of Object.entries(fields)) {
                const el = document.getElementById(id);
                if (!el) continue;
                if ('checked' in def) {
                    el.checked = def.checked;
                } else {
                    el.value = def.value;
                }
            }
        });
    }

    // --- Banner extension Chrome ---
    const popup = document.getElementById('extension-popup');
    if (popup) {
        if (localStorage.getItem('extensionPopupClosed')) {
            popup.style.display = 'none';
        }
        const closeBtn = document.getElementById('popup-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function () {
                popup.style.display = 'none';
                localStorage.setItem('extensionPopupClosed', 'true');
            });
        }
    }
});
