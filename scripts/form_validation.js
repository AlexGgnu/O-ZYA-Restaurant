const form = document.querySelector("form");
const inputs = document.querySelectorAll("input[required], textarea[required]");
const togglePasswordButtons = document.querySelectorAll(".toggle-password");
const submitButton = document.querySelector("button[type='submit']");

// MARK: - Validation functions
function isAllValid() {
    if (!submitButton) return;

    return Array.from(inputs).every(input => {
        if (input.type === "radio") {
            const radioGroup = form.querySelectorAll(`input[name="${input.name}"]`);
            return Array.from(radioGroup).some(radio => radio.checked);
        } else if (input.type === 'email') {
            const email_pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return email_pattern.test(input.value);
        } else if(input.type === 'password') {
            return input.value.length >= 8;
        } else if (input.type === 'tel') {
            const phone_pattern = /^[0-9+\s]+$/;
            return phone_pattern.test(input.value);
        } else {
            return input.value.trim() !== "";
        }
    });
}

inputs.forEach((input, index) => {
    input.addEventListener('input', () => {
        if(input.type === 'password') {
            const counter = document.querySelector(`#${input.id}__counter span`);
            if (counter) counter.textContent = input.value.length;
        } else if (input.type === 'tel') {
            let value = input.value.replace(/\s/g, '');
            
            if(!/^[0-9+\s]*$/.test(value)) value = value.replace(/[^0-9+\s]/g, ''); // NOTE: Remove non-numeric characters except + and space
            if (value.length > 2) value = value.replace(/(\d{2})(?=\d)/g, '$1 '); // NOTE: Add space after every 2 digits
            if (value.length > 14) value = value.slice(0, -1); // NOTE: Limit to 14 characters (expl: 06 12 34 56 78)

            input.value = value;
        }

        submitButton.disabled = !isAllValid();
    });

    input.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' || event.key === 'Tab') {
            event.preventDefault();
            if (input.value === "") return;

            if (input.type === 'email') {
                const email_pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!email_pattern.test(input.value)) {
                    input.value = "";
                    input.classList.add("error");
                    return;
                }
            } else if(input.type === 'password') {
                if (input.value.length < 8) {
                    input.value = "";
                    input.classList.add("error");
                    return;
                }
            } else if (input.type === 'tel') {
                const phone_pattern = /^[0-9+\s]+$/;
                if (!phone_pattern.test(input.value)) {
                    input.value = "";
                    input.classList.add("error");
                    return;
                }
            }

            input.classList.remove("error");
            const nextInput = inputs[index + 1];
            nextInput ? nextInput.focus() : input.blur();
        }
    });
});

form.addEventListener('submit', (event) => {
    if (!isAllValid()) {
        event.preventDefault();
        submitButton.disabled = true;

        let p = document.createElement("p");
        p.classList.add("error");
        p.textContent = "Veuillez remplir correctement tous les champs requis";
        form.appendChild(p);
    }
});

// MARK: - Toggle password visibility
togglePasswordButtons.forEach(button => {
    button.addEventListener("click", () => {
        const input = document.getElementById(button.getAttribute("data-target"));
        if (input && (input.type === "password" || input.type === "text")) input.type = input.type === "password" ? "text" : "password";
    });
});









// const registerForm = document.querySelector("form");

// if (registerForm) {

//     registerForm.addEventListener("submit", function(e) {

//         let errors = [];

//         const firstname = document.getElementById("firstname");

//         const lastname = document.getElementById("lastname");

//         const email = document.getElementById("email");

//         const password = document.getElementById("password");

//         const confirmPwd = document.getElementById("confirme-pwd");

//         const phone = document.getElementById("phone");

//         if (firstname && firstname.value.length < 2) {
//             errors.push("Prénom invalide");
//         }

//         if (lastname && lastname.value.length < 2) {
//             errors.push("Nom invalide");
//         }
        
//         if (email) {

//             const email_pattern =
//                 /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

//             if (!email_pattern.test(email.value)) {
//                 errors.push("Email invalide");
//             }
//         }

//         if (password && password.value.length < 8) {
//             errors.push("Le mot de passe doit faire 8 caractères");
//         }

//         if (password && confirmPwd && password.value !== confirmPwd.value) {
//             errors.push("Les mots de passe ne correspondent pas");
//         }

//         if (phone) {
//             const phone_pattern = /^[0-9+\s]+$/;

//             if (!phone_pattern.test(phone.value)) {
//                 errors.push("Téléphone invalide");
//             }
//         }

//         if (errors.length > 0) {

//             e.preventDefault();

//             alert(errors.join("\n"));
//         }
//     });
// }


// const togglePassword = document.getElementById("togglePassword");

// const passwordInput = document.getElementById("password");

// if (togglePassword && passwordInput) {

//     togglePassword.addEventListener("click", () => {

//         if (passwordInput.type === "password") {
//             passwordInput.type = "text";
//         }
//         else {
//             passwordInput.type = "password";
//         }
//     });
// }

// const counter = document.getElementById("passwordCounter");

// if (passwordInput && counter) {

//     passwordInput.addEventListener("input", () => {

//         counter.textContent = `${passwordInput.value.length} / 8`;
//     });
// }



// const toggleConfirmPassword = document.getElementById("toggleConfirmPassword");

// const confirmPasswordInput = document.getElementById("confirme-pwd");

// if (toggleConfirmPassword && confirmPasswordInput) {

//     toggleConfirmPassword.addEventListener("click", () => {

//         if (confirmPasswordInput.type === "password") {
//             confirmPasswordInput.type = "text";
//         }
//         else {
//             confirmPasswordInput.type = "password";
//         }
//     });
// }

// const confirmPasswordCounter = document.getElementById("confirmPassword__counter");

// if (confirmPasswordInput && confirmPasswordCounter
// ) {

//     confirmPasswordInput.addEventListener("input", () => {

//     confirmPasswordCounter.textContent = `${confirmPasswordInput.value.length} / 20`;
//     });
// }