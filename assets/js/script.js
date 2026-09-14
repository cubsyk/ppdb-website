document.addEventListener("DOMContentLoaded", () => {
    const navToggle = document.querySelector("[data-nav-toggle]");
    const navMenu = document.querySelector("[data-nav-menu]");
    const form = document.querySelector(".registration-form");

    if (navToggle && navMenu) {
        navToggle.addEventListener("click", () => {
            const isOpen = navMenu.classList.toggle("is-open");
            navToggle.setAttribute("aria-expanded", String(isOpen));
        });

        navMenu.querySelectorAll("a").forEach((link) => {
            link.addEventListener("click", () => {
                navMenu.classList.remove("is-open");
                navToggle.setAttribute("aria-expanded", "false");
            });
        });
    }

    if (form) {
        form.addEventListener("submit", () => {
            const submitButton = form.querySelector(".form-submit");
            if (submitButton) {
                submitButton.textContent = "Mengirim...";
                submitButton.disabled = true;
            }
        });

        form.querySelectorAll("input, select, textarea").forEach((field) => {
            field.addEventListener("invalid", () => {
                field.classList.add("is-invalid");
            });

            field.addEventListener("input", () => {
                field.classList.remove("is-invalid");
            });
        });
    }

    const animatedItems = document.querySelectorAll(".card, .plain-box, .registration-form, .timeline-row, .table-wrap, .section-title");
    animatedItems.forEach((item) => item.classList.add("reveal"));

    if ("IntersectionObserver" in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("is-visible");
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });

        animatedItems.forEach((item) => observer.observe(item));
    } else {
        animatedItems.forEach((item) => item.classList.add("is-visible"));
    }
});