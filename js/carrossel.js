// Carrossel 1
(function () {
  const radios = Array.from(document.querySelectorAll('.carousel1 input[name="radio-btn-1"]'));
  if (!radios.length) return;
  let i = 0;
  setInterval(() => {
    i = (i + 1) % radios.length;
    radios[i].checked = true;
  }, 4000);
})();

// Carrossel 2
(function () {
  const radios = Array.from(document.querySelectorAll('.carousel2 input[name="radio-btn-2"]'));
  if (!radios.length) return;
  let i = 0;
  setInterval(() => {
    i = (i + 1) % radios.length;
    radios[i].checked = true;
  }, 4000);
})();

// Carrossel 3
(function () {
  const radios = Array.from(document.querySelectorAll('.carousel3 input[name="radio-btn-3"]'));
  if (!radios.length) return;
  let i = 0;
  setInterval(() => {
    i = (i + 1) % radios.length;
    radios[i].checked = true;
  }, 4000);
})();
