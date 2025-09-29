// 🔝 Força sempre abrir no topo
function resetScroll() {
  setTimeout(() => window.scrollTo(0, 0), 50);
}
document.addEventListener("DOMContentLoaded", resetScroll);
window.addEventListener("load", resetScroll);

document.addEventListener("DOMContentLoaded", () => {
  const overlayContainer = document.getElementById("overlay");
  if (!overlayContainer) return;

  // 1. Carrega o conteúdo externo do overlay
  fetch("./overlay.html")
    .then(res => res.text())
    .then(html => {
      overlayContainer.innerHTML = html;

      // 2. Preenche o texto sorteado
      const overlayText = overlayContainer.querySelector(".overlay-text");
      if (overlayText) {
        const frases = [
         "UPUP é mais que um projeto social: é uma família unida por um propósito — <strong>Amar e servir</strong>",
  "Quem ama, cuida. Quem serve, transforma. <strong>UPUP é vida em movimento</strong>",
  "Pequenos gestos de amor geram grandes milagres. <strong>UPUP acredita em você</strong>",
  "O cuidado nos une, a esperança nos guia. <strong>UPUP é família</strong>",
  "Mais que palavras, nosso propósito é ação. <strong>UPUP — Amar é verbo</strong>",
  "Quando cuidamos juntos, a vida floresce. <strong>UPUP — Unidos para servir</strong>",
  "UPUP é cuidado em cada detalhe: um gesto simples pode mudar vidas. <strong>Amor que transforma</strong>",
  "Servir é nossa missão. Cuidar é nossa alegria. <strong>UPUP é coração em ação</strong>",
  "Onde há dor, UPUP leva esperança. <strong>Amar é o nosso propósito</strong>",
  "Com carinho e dedicação, UPUP acolhe cada história. <strong>Família que cuida</strong>",
  "UPUP acredita que servir é a maior forma de amar. <strong>Juntos pela vida</strong>",
  "Cada sorriso que nasce é fruto do cuidado da UPUP. <strong>Amor em movimento</strong>",
  "UPUP é mais que palavras: é serviço, é entrega. <strong>Amor verdadeiro</strong>",
  "Quando alguém precisa, UPUP está presente. <strong>Cuidar é servir</strong>",
  "UPUP é abraço que aquece, palavra que conforta. <strong>Família de amor</strong>",
  "No caminho da esperança, UPUP anda ao seu lado. <strong>Servir é viver</strong>",
  "UPUP transforma cuidado em amor multiplicado. <strong>Unidos pelo servir</strong>",
  "Com cada ato de carinho, UPUP semeia esperança. <strong>Serviço que floresce</strong>",
  "UPUP é amor sem fronteiras, solidariedade sem limites. <strong>Família que acolhe</strong>",
  "No coração de cada vida, UPUP planta amor. <strong>Cuidar é servir</strong>",
  "UPUP é a mão estendida, a palavra amiga. <strong>Esperança em ação</strong>",
  "O amor se multiplica quando a UPUP cuida. <strong>Família de fé</strong>",
  "UPUP é carinho, é atenção, <strong>é amor que abraça</strong>",
  "No silêncio da dor, UPUP leva conforto. <strong>Servir é amar</strong>",
  "Com dedicação e amor, UPUP constrói futuros. <strong>Esperança compartilhada</strong>",
  "UPUP é vida que floresce em cada cuidado. <strong>Amor transformador</strong>",
  "Onde há necessidade, UPUP responde com amor. <strong>Serviço e esperança</strong>",
  "UPUP é presença constante, é cuidado diário. <strong>Família que serve</strong>",
  "Cada gesto de carinho é o jeito UPUP de servir. <strong>Amor sem limites</strong>",
  "UPUP acredita que amar é cuidar de todos. <strong>Família unida</strong>",
  "No sorriso de quem recebe, vive a missão da UPUP. <strong>Amor em serviço</strong>",
  "UPUP transforma realidades através do amor. <strong>Cuidar é servir</strong>",
  "Com UPUP, ninguém caminha sozinho. <strong>Família de esperança</strong>",
  "O cuidado da UPUP é luz em meio à escuridão. <strong>Amor que guia</strong>",
  "UPUP é ponte de amor, ligando corações. <strong>Serviço que une</strong>",
  "Em cada abraço da UPUP, há vida renovada. <strong>Amar é servir</strong>",
  "UPUP é amor que se doa, cuidado que não cansa. <strong>Serviço de coração</strong>",
  "Onde o mundo fecha portas, UPUP abre os braços. <strong>Amor acolhedor</strong>",
  "UPUP é chama de esperança que nunca se apaga. <strong>Servir é viver</strong>",
  "Com coragem e amor, UPUP enfrenta desafios. <strong>Família que apoia</strong>",
  "UPUP é vida compartilhada, é coração em família. <strong>Amar e servir</strong>",
  "O carinho da UPUP é bálsamo para a alma. <strong>Serviço que cura</strong>",
  "UPUP é amor que inspira, cuidado que fortalece. <strong>Família unida</strong>",
  "A cada passo com a UPUP, renasce a esperança. <strong>Amor que guia</strong>",
  "UPUP é entrega, é serviço, <strong>é vida dedicada ao próximo</strong>",
  "Com ternura e cuidado, UPUP constrói laços. <strong>Amor que permanece</strong>",
  "UPUP é amor presente em todos os detalhes. <strong>Família que serve</strong>",
  "Na jornada da vida, UPUP caminha ao seu lado. <strong>Esperança em ação</strong>",
  "UPUP é a certeza de que amar sempre vale a pena. <strong>Servir é amar</strong>",
  "Com cada mão estendida, UPUP fortalece corações. <strong>Amor solidário</strong>",
  "UPUP é fé que age, amor que serve, <strong>esperança que acolhe</strong>",
  "Mais que uma ONG, UPUP é família. <strong>Amar e servir sempre</strong>",
  "No coração da UPUP, cabe o mundo inteiro. <strong>Amor que acolhe</strong>",
  "UPUP é cuidado que abraça, amor que transforma. <strong>Família de esperança</strong>",
  "Com amor e serviço, UPUP constrói pontes. <strong>Unidos pelo próximo</strong>",
  "UPUP é sorriso, é carinho, <strong>é amor em cada detalhe</strong>",
  "Servir com amor é a missão da UPUP. <strong>Família que transforma</strong>"
          // 👉 pode expandir sua lista completa aqui
        ];
        overlayText.innerHTML = frases[Math.floor(Math.random() * frases.length)];
      }

      // 3. Inicia fade-out após 5s
      setTimeout(() => {
        overlayContainer.classList.add("fade-out");

        // 4. Remove do DOM depois da animação
        setTimeout(() => overlayContainer.remove(), 1000);
      }, 5000);
    })
    .catch(err => console.error("Erro ao carregar overlay:", err));
    window.addEventListener("pageshow", () => {
  setTimeout(() => window.scrollTo(0, 0), 50);
});

});
