const navToggle = document.querySelector("[data-nav-toggle]");
const navMenu = document.querySelector("[data-nav-menu]");
const header = document.querySelector("[data-header]");

if (navToggle && navMenu) {
    navToggle.addEventListener("click", () => {
        const isOpen = navMenu.classList.toggle("is-open");

        navToggle.classList.toggle("is-open", isOpen);
        navToggle.setAttribute("aria-expanded", String(isOpen));
        navToggle.setAttribute("aria-label", isOpen ? "Close navigation" : "Open navigation");
    });

    navMenu.querySelectorAll("a").forEach((link) => {
        link.addEventListener("click", () => {
            navMenu.classList.remove("is-open");
            navToggle.classList.remove("is-open");
            navToggle.setAttribute("aria-expanded", "false");
            navToggle.setAttribute("aria-label", "Open navigation");
        });
    });
}

document.querySelectorAll('a[href^="#"]').forEach((link) => {
    link.addEventListener("click", (event) => {
        const target = document.querySelector(link.getAttribute("href"));

        if (!target) {
            return;
        }

        event.preventDefault();
        target.scrollIntoView({ behavior: "smooth", block: "start" });
    });
});

const revealObserver = new IntersectionObserver(
    (entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add("is-visible");
                revealObserver.unobserve(entry.target);
            }
        });
    },
    { threshold: 0.16 }
);

document.querySelectorAll(".reveal").forEach((element) => {
    revealObserver.observe(element);
});

const countObserver = new IntersectionObserver(
    (entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) {
                return;
            }

            const counter = entry.target;
            const target = Number(counter.dataset.count || 0);
            const duration = 900;
            const start = performance.now();

            const tick = (now) => {
                const progress = Math.min((now - start) / duration, 1);
                counter.textContent = Math.floor(progress * target);

                if (progress < 1) {
                    requestAnimationFrame(tick);
                } else {
                    counter.textContent = target;
                }
            };

            requestAnimationFrame(tick);
            countObserver.unobserve(counter);
        });
    },
    { threshold: 0.8 }
);

document.querySelectorAll("[data-count]").forEach((counter) => {
    countObserver.observe(counter);
});

const loginForm = document.querySelector("[data-login-form]");

if (loginForm) {
    loginForm.addEventListener("submit", (event) => {
        event.preventDefault();
    });
}

const atmOpenButton = document.querySelector("[data-atm-open]");
const atmPanel = document.querySelector("[data-atm-panel]");

if (atmOpenButton && atmPanel) {
    atmOpenButton.addEventListener("click", () => {
        atmPanel.hidden = false;
        atmPanel.scrollIntoView({ behavior: "smooth", block: "center" });
    });
}

window.addEventListener("scroll", () => {
    if (!header) {
        return;
    }

    header.classList.toggle("is-scrolled", window.scrollY > 10);
});
