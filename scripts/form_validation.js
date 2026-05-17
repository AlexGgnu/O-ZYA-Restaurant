const form = document.querySelector("form");
const inputs = document.querySelectorAll("input[required], input#promotion__code, select[required], textarea[required]");
const ratingInput = document.getElementById("rating__input");
const ratingButtons = document.querySelectorAll(".rating__button");
const togglePasswordButtons = document.querySelectorAll(".toggle-password");
const submitButton = document.querySelector("button[type='submit']");

// MARK: - Validation functions
function isAllValid() {
    if (!submitButton) return;

    return Array.from(inputs).every(input => {
        if (ratingInput && input === ratingInput) {
            ratingValue = parseInt(ratingInput.getAttribute("value"));
            return ratingValue > 0 && ratingValue <= 5 && !isNaN(ratingValue);
        } else if (input.type === "radio") {
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
        } else if(input.tagName.toLowerCase() === 'textarea') {
            return input.value.trim().length >= 200;
        } else if (input.tagName.toLowerCase() === 'select') {
            return input.value !== "" && input.value !== null;
        } else {
            return input.value.trim() !== "" || input.id === "promotion__code"; // NOTE: Allow empty value for promotion code as it's optional
        }
    });
}

// MARK: - Input event listeners
inputs.forEach((input, index) => {
    input.addEventListener('input', () => {
        if(input.type === 'password' || input.tagName.toLowerCase() === 'textarea') {
            const counter = document.querySelector(`#${input.id}__counter span`);
            if (counter) counter.textContent = input.value.length;
        } else if (input.type === 'tel') {
            let value = input.value.replace(/\s/g, '');
            
            if(!/^[0-9+\s]*$/.test(value)) value = value.replace(/[^0-9+\s]/g, ''); // NOTE: Remove non-numeric characters except + and space
            if (value.length > 2) value = value.replace(/(\d{2})(?=\d)/g, '$1 '); // NOTE: Add space after every 2 digits
            if (value.length > 14) value = value.slice(0, -1); // NOTE: Limit to 14 characters (expl: 06 12 34 56 78)

            input.value = value;
        } else if (input.id === "promotion__code") {
            input.value = input.value.toUpperCase().replace(/\s/g, ''); // NOTE: Convert to uppercase and remove spaces
        }

        if (submitButton) submitButton.disabled = !isAllValid();
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

// MARK: - Rating system
if(ratingButtons && ratingInput) {
    ratingButtons.forEach(button => {
        button.addEventListener("click", () => {
            const rating = button.getAttribute("data-rating");
            
            ratingButtons.forEach(btn => btn.setAttribute("data-checked", "false"));
            ratingButtons.forEach(btn => {
                if (btn.getAttribute("data-rating") <= rating) btn.setAttribute("data-checked", "true");
            });

            ratingInput.setAttribute("value", rating);
            submitButton.disabled = !isAllValid();
        });
    });
}

// MARK: - Form submission
if (form) {
    form.addEventListener('submit', (event) => {
        if (!isAllValid()) {
            event.preventDefault();
            if(submitButton) submitButton.disabled = true;

            let p = document.createElement("p");
            p.classList.add("error");
            p.textContent = "Veuillez remplir correctement tous les champs requis";
            form.appendChild(p);
        }
    });
}

// MARK: - Toggle password visibility
if (togglePasswordButtons) {
    togglePasswordButtons.forEach(button => {
        button.addEventListener("click", () => {
            const input = document.getElementById(button.getAttribute("data-target"));
            if (input && (input.type === "password" || input.type === "text")) input.type = input.type === "password" ? "text" : "password";
        });
    });
}