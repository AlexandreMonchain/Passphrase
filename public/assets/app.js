document.addEventListener('DOMContentLoaded', function () {

    // --- Soumission AJAX du formulaire ---
    const form        = document.getElementById('password-generator-form');
    const generateBtn = document.getElementById('password_generation_form_generate');
    const loading     = document.getElementById('loading-animation');
    const passwordsDiv = document.getElementById('generated-passwords');

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            if (generateBtn) generateBtn.disabled = true;
            if (loading) loading.classList.remove('d-none');

            fetch('/', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(form),
            })
                .then(function (r) { return r.text(); })
                .then(function (html) {
                    passwordsDiv.innerHTML = html;
                    const top = passwordsDiv.getBoundingClientRect().top + window.scrollY - 80;
                    window.scrollTo({ top: top, behavior: 'smooth' });
                })
                .finally(function () {
                    if (generateBtn) generateBtn.disabled = false;
                    if (loading) loading.classList.add('d-none');
                });
        });
    }

    // --- Slider longueur minimale ---
    const sliderInput     = document.getElementById('password_generation_form_longueur_minimale');
    const longueurDisplay = document.getElementById('longueur-display');
    if (sliderInput) {
        sliderInput.type = 'range';
        sliderInput.classList.remove('form-control');
        sliderInput.classList.add('form-range');
        if (longueurDisplay) {
            longueurDisplay.textContent = sliderInput.value;
            sliderInput.addEventListener('input', function () {
                longueurDisplay.textContent = this.value;
            });
        }
    }

    // --- Copie au clic sur la carte mot de passe ---
    document.addEventListener('click', function (e) {
        const card = e.target.closest('.password-card-compact');
        if (!card) return;

        const password = card.dataset.password;
        if (!password) return;

        navigator.clipboard.writeText(password).then(function () {
            const txt = card.querySelector('.password-value-compact');
            if (!txt) return;
            const original = txt.textContent;
            txt.textContent = '✓ Copié !';
            setTimeout(function () { txt.textContent = original; }, 1500);
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
                'password_generation_form_nb_resultats':      { value: '10' },
                'password_generation_form_majuscule_debut':      { checked: true },
                'password_generation_form_majuscule_aleatoire':  { checked: false },
                'password_generation_form_caracteres_accentues': { checked: true },
            };

            for (const [id, def] of Object.entries(fields)) {
                const el = document.getElementById(id);
                if (!el) continue;
                if ('checked' in def) {
                    el.checked = def.checked;
                } else {
                    el.value = def.value;
                    if (id === 'password_generation_form_longueur_minimale' && longueurDisplay) {
                        longueurDisplay.textContent = def.value;
                    }
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
