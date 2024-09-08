$(document).ready(function () {
    $('#password-generator-form').on('submit', function (e) {
        e.preventDefault(); // Empêche le rechargement de la page
        
        // Désactiver le bouton et afficher l'animation de chargement
        $('#generate-btn').prop('disabled', true);
        $('#loading-animation').show();

        $.ajax({
            url: '/',
            method: 'POST',
            data: $(this).serialize(), // Récupère les données du formulaire
            success: function (response) {
                $('#generated-passwords').html(response); // Met à jour la section des mots de passe
            },
            complete: function() {
                // Réactiver le bouton et cacher l'animation après la réponse
                $('#generate-btn').prop('disabled', false);
                $('#loading-animation').hide();
            }
        });
    });

    // Fonctionnalité de copie
    $(document).on('click', '.copy-btn', function(e) {
        e.preventDefault();
        var password = $(this).data('password');
        var tempInput = $('<input>');
        $('body').append(tempInput);
        tempInput.val(password).select();
        document.execCommand('copy');
        tempInput.remove();

        // Feedback visuel pour informer que le mot de passe a été copié
        $(this).html('<i class="fas fa-check"></i>'); // Change l'icône temporairement
        var button = $(this);
        setTimeout(function() {
            button.html('<i class="fas fa-copy"></i>'); // Remet l'icône de copie après 2 secondes
        }, 2000);
    });
});    

//Bouton pour remettre les paramètres par defaut du formulaire
document.addEventListener('DOMContentLoaded', function () {
    const loadDefaultsBtn = document.getElementById('load-defaults-btn');

    loadDefaultsBtn.addEventListener('click', function () {
        const defaultValues = {
            'nb_mots': 2,
            'longueur_minimale': 12,
            'separateur': 'random',
            'majuscule_debut': true,
            'majuscule_aleatoire': false,
            'longueur_nombre': 2,
            'caractere_special': 'random'
        };
        // Modifier les champs du formulaire avec les valeurs par défaut
        document.getElementById('password_generation_form_nb_mots').value = defaultValues['nb_mots'];
        document.getElementById('password_generation_form_longueur_minimale').value = defaultValues['longueur_minimale'];
        document.getElementById('password_generation_form_separateur').value = defaultValues['separateur'];
        document.getElementById('password_generation_form_majuscule_debut').checked = defaultValues['majuscule_debut'];
        document.getElementById('password_generation_form_majuscule_aleatoire').checked = defaultValues['majuscule_aleatoire'];
        document.getElementById('password_generation_form_longueur_nombre').value = defaultValues['longueur_nombre'];
        document.getElementById('password_generation_form_caractere_special').value = defaultValues['caractere_special'];
    });
});