document.addEventListener("DOMContentLoaded", () => {
  const preloader = document.getElementById("preloader");

  // espera a frase terminar (3s), aí aplica fade-out
  setTimeout(() => {
    preloader.classList.add("fade-out");

    // depois de 1s (tempo do transition), remove de vez
    setTimeout(() => {
      preloader.style.display = "none";
    }, 1000);
  }, 3000);
});
