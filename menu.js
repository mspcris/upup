// Aguarda o HTML carregar
document.addEventListener("DOMContentLoaded", () => {
  // Seletores principais do menu
  const drawer = document.getElementById("app-drawer");      // O menu lateral
  const overlay = document.getElementById("drawerOverlay");  // Fundo escuro
  const toggleBtn = document.getElementById("menuToggle");   // Botão hamburguer
  const closeBtn = document.getElementById("drawerClose");   // Botão fechar (X)
  const accordions = document.querySelectorAll(".upup-accordion"); // Botões que abrem submenus

  /**
   * Abre o menu lateral (drawer)
   */
  function openDrawer() {
    // Mostra menu e overlay
    drawer.hidden = false;
    overlay.hidden = false;

    // Marca menu como aberto
    drawer.setAttribute("data-open", "true");
    overlay.setAttribute("aria-hidden", "false");
    toggleBtn.setAttribute("aria-expanded", "true");

    // Evita scroll na página
    document.body.style.overflow = "hidden";

    // ANIMAÇÃO:
    // Primeiro aparece o splash da logo (2s no CSS).
    // Só depois os itens do menu começam a aparecer.
    const menuList = drawer.querySelector(".upup-list");
    menuList.classList.remove("show"); // Garante reset da animação

    setTimeout(() => {
      menuList.classList.add("show");  // Ativa animação dos links
    }, 2000); // Espera 2s = duração do splash
  }

  /**
   * Fecha o menu lateral (drawer)
   */
  function closeDrawer() {
    // Marca como fechado
    drawer.setAttribute("data-open", "false");
    overlay.setAttribute("aria-hidden", "true");
    toggleBtn.setAttribute("aria-expanded", "false");
    document.body.style.overflow = "";

    // Espera a transição terminar antes de esconder
    setTimeout(() => {
      drawer.hidden = true;
      overlay.hidden = true;
    }, 300); // tempo deve bater com o CSS transition do drawer
  }

  // === Eventos principais ===
  toggleBtn.addEventListener("click", () => {
    const isOpen = drawer.getAttribute("data-open") === "true";
    isOpen ? closeDrawer() : openDrawer();
  });

  closeBtn.addEventListener("click", closeDrawer);
  overlay.addEventListener("click", closeDrawer);

  // === Submenus (ex.: Cursos) ===
  accordions.forEach(btn => {
    const target = document.getElementById(btn.getAttribute("aria-controls"));

    btn.addEventListener("click", () => {
      const expanded = btn.getAttribute("aria-expanded") === "true";

      // Atualiza atributo de acessibilidade
      btn.setAttribute("aria-expanded", String(!expanded));

      // Alterna a classe que abre/fecha com animação CSS
      if (expanded) {
        target.classList.remove("open"); // fecha
      } else {
        target.classList.add("open");    // abre
      }
    });
  });
});
