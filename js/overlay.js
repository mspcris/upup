document.addEventListener("DOMContentLoaded", () => {
  const overlayContainer = document.getElementById("overlay");
  if (!overlayContainer) return;

  // 1. Carrega o conteúdo do overlay.html
  fetch("overlay.html")
    .then(res => res.text())
    .then(html => {
      overlayContainer.innerHTML = html;

      // 2. Depois de carregar, pega a área do texto
      const overlayText = overlayContainer.querySelector(".overlay-text");
      if (overlayText) {
        const frases = [
          `UPUP é mais que um projeto social: é uma família unida por um propósito — <strong>Amar e servir</strong>`,
          `Quem ama, cuida. Quem serve, transforma. <strong>UPUP é vida em movimento</strong>`,
          `Pequenos gestos de amor geram grandes milagres. <strong>UPUP acredita em você</strong>`,
          `O cuidado nos une, a esperança nos guia. <strong>UPUP é família</strong>`,
          `Mais que palavras, nosso propósito é ação. <strong>UPUP — Amar é verbo</strong>`
        ];
        overlayText.innerHTML = frases[Math.floor(Math.random() * frases.length)];
      }

      // 3. Inicia fade-out após 5s
      setTimeout(() => {
        overlayContainer.classList.add("fade-out");

        // 4. Remove do DOM depois da animação
        setTimeout(() => {
          overlayContainer.remove();
        }, 1000);
      }, 5000);
    })
    .catch(err => console.error("Erro ao carregar overlay:", err));
});
