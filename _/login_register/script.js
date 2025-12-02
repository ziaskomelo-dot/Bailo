document.addEventListener('DOMContentLoaded', () => {
    const loginBox = document.getElementById('login-box');
    const registerBox = document.getElementById('register-box');
    const forgotPasswordBox = document.getElementById('forgot-password-box');

    const showRegisterLink = document.getElementById('show-register');
    const showLoginLink = document.getElementById('show-login');
    const forgotPasswordLink = document.getElementById('forgot-password-link');
    const backToLoginLink = document.getElementById('back-to-login');

    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');
    const forgotPasswordForm = document.getElementById('forgot-password-form');

    // --- Fonctions de basculement de formulaire ---
    function showForm(formToShow) {
        [loginBox, registerBox, forgotPasswordBox].forEach(box => {
            box.classList.add('hidden');
        });
        formToShow.classList.remove('hidden');
    }

    showRegisterLink.addEventListener('click', (e) => {
        e.preventDefault();
        showForm(registerBox);
    });

    showLoginLink.addEventListener('click', (e) => {
        e.preventDefault();
        showForm(loginBox);
    });

    forgotPasswordLink.addEventListener('click', (e) => {
        e.preventDefault();
        showForm(forgotPasswordBox);
    });

    backToLoginLink.addEventListener('click', (e) => {
        e.preventDefault();
        showForm(loginBox);
    });

    // --- Gestion de la persistance du type d'utilisateur (Client/Artisan) ---
    const userTypeRadios = document.querySelectorAll('input[name="user-type"]');
    const registerUserTypeRadios = document.querySelectorAll('input[name="register-user-type"]');
    const rememberMeCheckbox = document.getElementById('remember-me');

    // Charger le type d'utilisateur mémorisé au chargement de la page
    const savedUserType = localStorage.getItem('lastUserType');
    if (savedUserType) {
        document.querySelector(`input[name="user-type"][value="${savedUserType}"]`).checked = true;
        document.querySelector(`input[name="register-user-type"][value="${savedUserType}"]`).checked = true;
    }

    // Mettre à jour le type d'utilisateur mémorisé lors de la sélection
    userTypeRadios.forEach(radio => {
        radio.addEventListener('change', (e) => {
            if (rememberMeCheckbox.checked) {
                localStorage.setItem('lastUserType', e.target.value);
            }
        });
    });

    registerUserTypeRadios.forEach(radio => {
        radio.addEventListener('change', (e) => {
            // Synchroniser la sélection entre les deux formulaires
            document.querySelector(`input[name="user-type"][value="${e.target.value}"]`).checked = true;
            if (rememberMeCheckbox.checked) {
                localStorage.setItem('lastUserType', e.target.value);
            }
        });
    });

    // Gérer la case "Se souvenir de moi"
    rememberMeCheckbox.addEventListener('change', (e) => {
        const selectedType = document.querySelector('input[name="user-type"]:checked').value;
        if (e.target.checked) {
            localStorage.setItem('lastUserType', selectedType);
            console.log(`Type d'utilisateur mémorisé: ${selectedType}`);
        } else {
            localStorage.removeItem('lastUserType');
            console.log("Type d'utilisateur oublié.");
        }
    });

    // --- Simulation de soumission de formulaires ---

    loginForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const email = document.getElementById('login-email').value;
        const password = document.getElementById('login-password').value;
        const userType = document.querySelector('input[name="user-type"]:checked').value;
        const rememberMe = rememberMeCheckbox.checked;

        // Logique de connexion simulée
        console.log(`Tentative de connexion en tant que ${userType}:`);
        console.log(`Email: ${email}`);
        console.log(`Mot de passe: ${password}`);
        console.log(`Se souvenir de moi: ${rememberMe}`);

        if (rememberMe) {
            localStorage.setItem('lastUserType', userType);
        } else {
            localStorage.removeItem('lastUserType');
        }

        alert(`Connexion réussie (simulée) en tant que ${userType} !`);
        // Redirection vers la page d'accueil ou le tableau de bord
        // window.location.href = '/dashboard.html';
    });

    registerForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const email = document.getElementById('register-email').value;
        const password = document.getElementById('register-password').value;
        const confirmPassword = document.getElementById('register-confirm-password').value;
        const userType = document.querySelector('input[name="register-user-type"]:checked').value;

        if (password !== confirmPassword) {
            alert("Les mots de passe ne correspondent pas.");
            return;
        }

        // Logique d'inscription simulée
        console.log(`Tentative d'inscription en tant que ${userType}:`);
        console.log(`Email: ${email}`);
        console.log(`Mot de passe: ${password}`);

        alert(`Inscription réussie (simulée) en tant que ${userType} ! Vous pouvez maintenant vous connecter.`);
        // Après l'inscription, basculer vers le formulaire de connexion
        showForm(loginBox);
    });

    forgotPasswordForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const email = document.getElementById('forgot-email').value;

        // Logique de récupération de mot de passe simulée
        // Dans un vrai scénario, cela enverrait un e-mail
        console.log(`Demande de récupération de mot de passe pour l'email: ${email}`);

        alert(`Un e-mail de réinitialisation de mot de passe a été envoyé (simulé) à ${email}.`);
        // Après l'envoi, basculer vers le formulaire de connexion
        showForm(loginBox);
    });
});
