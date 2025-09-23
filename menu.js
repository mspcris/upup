document.addEventListener("DOMContentLoaded", () => {
  const drawer = document.getElementById("app-drawer");
  const overlay = document.getElementById("drawerOverlay");
  const toggleBtn = document.getElementById("menuToggle");
  const closeBtn = document.getElementById("drawerClose");
  const accordions = document.querySelectorAll(".upup-accordion");

  function openDrawer() {
    drawer.hidden = false;
    overlay.hidden = false;
    drawer.setAttribute("data-open", "true");
    overlay.setAttribute("aria-hidden", "false");
    toggleBtn.setAttribute("aria-expanded", "true");
    document.body.style.overflow = "hidden";
  }

  function closeDrawer() {
    drawer.setAttribute("data-open", "false");
    overlay.setAttribute("aria-hidden", "true");
    toggleBtn.setAttribute("aria-expanded", "false");
    document.body.style.overflow = "";
    setTimeout(() => {
      drawer.hidden = true;
      overlay.hidden = true;
    }, 300);
  }

  toggleBtn.addEventListener("click", () => {
    const isOpen = drawer.getAttribute("data-open") === "true";
    isOpen ? closeDrawer() : openDrawer();
  });

  closeBtn.addEventListener("click", closeDrawer);
  overlay.addEventListener("click", closeDrawer);

  // Submenus (Cursos)
  accordions.forEach(btn => {
    const target = document.getElementById(btn.getAttribute("aria-controls"));
    btn.addEventListener("click", () => {
  const expanded = btn.getAttribute("aria-expanded") === "true";
  btn.setAttribute("aria-expanded", String(!expanded));

  if (expanded) {
    target.classList.remove("open"); // fecha com animação
  } else {
    target.classList.add("open"); // abre com animação
  }
});
    });
  });
