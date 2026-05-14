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