document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("#loginForm");
    const statusBox = document.querySelector("#formStatus");
    if (!form) return;


    function setStatus(type, msg) {
        if (!statusBox) return;
        if (!msg) {
            statusBox.className = "alert d-none";
            statusBox.textContent = "";
            return;
        }
        statusBox.className = `alert alert-${type}`;
        statusBox.textContent = msg;
    }
    async function doLogin() {
        const fd = new FormData(form);
        const res = await fetch("auth/login", {
            method: "POST",
            body: fd,
            headers: { "X-Requested-With": "XMLHttpRequest" },
        });

        if (!res.ok) throw new Error("Erreur serveur lors de la validation.");
        return res.json();
    }

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        try {
            const data = await doLogin();
            // applyServerResult(data);

            if (data.ok) {
                // setStatus("success", "Validation OK ✅ Envoi en cours...");
                window.location = '/index';
            } else {
                // spinner.setAttribute("class", "spinner");
                setStatus("danger", data.errors);
            }
        } catch (err) {
            // setStatus("warning", err.message || "Une erreur est survenue.");
        }
    });
});