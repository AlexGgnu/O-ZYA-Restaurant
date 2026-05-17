const body = document.querySelector("body");

function create_alert(title, message, type) {
    var alert = document.createElement("div");
    alert.classList.add("alert", "alert__" + type);

    var icon = document.createElement("div");
    icon.classList.add("alert__icon");
    alert.appendChild(icon);
    if(type === "success") {
        icon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor"><path d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"/></svg>';
    } else if(type === "error") {
        icon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M345 137c9.4-9.4 9.4-24.6 0-33.9s-24.6-9.4-33.9 0l-119 119L73 103c-9.4-9.4-24.6-9.4-33.9 0s-9.4 24.6 0 33.9l119 119L39 375c-9.4 9.4-9.4 24.6 0 33.9s24.6 9.4 33.9 0l119-119L311 409c9.4 9.4 24.6 9.4 33.9 0s9.4-24.6 0-33.9l-119-119L345 137z"/></svg>';
    }

    var content = document.createElement("div");
    content.classList.add("alert__content");
    alert.appendChild(content);
    content.innerHTML = '<h3>' + title + '</h3>';
    content.innerHTML += '<p>' + message + '</p>';

    return alert;
}

function show_alert(title, message, type) {
    var alert = create_alert(title, message, type);
    body.appendChild(alert);

    setTimeout(() => {
        alert.classList.add("exit");
        setTimeout(() => alert.remove(), 500);
    }, 3000);
    
}