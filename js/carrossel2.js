        
            const slides2 = document.querySelectorAll('.slide2');
            const prevBtn2 = document.getElementById('prev2');
            const nextBtn2 = document.getElementById('next2');
            const bolinhasWrap2 = document.getElementById('bolinhas2');

            let indice2 = 0;
            let timer2 = null;
            const intervalo2 = 4000; // 4s

            // cria bolinhas
            slides2.forEach((_, i) => {
                const b2 = document.createElement('button');
                b2.setAttribute('aria-label', `Ir para slide ${i + 1}`);
                b2.addEventListener('click', () => irPara2(i));
                bolinhasWrap2.appendChild(b2);
            });

            function atualizarUI2() {
                slides2.forEach((s, i) => {
                    s.classList.toggle('ativo2', i === indice2);
                });
                const dots2 = bolinhasWrap2.querySelectorAll('button');
                dots2.forEach((d, i) => d.setAttribute('aria-selected', (i === indice2).toString()));
            }

            function irPara2(i) {
                indice2 = (i + slides2.length) % slides2.length;
                atualizarUI2();
            }

            function proximo2() {
                irPara2(indice2 + 1);
            }

            function anterior2() {
                irPara2(indice2 - 1);
            }

            function iniciar2() {
                parar2();
                timer2 = setInterval(proximo2, intervalo2);
            }

            function parar2() {
                if (timer2) clearInterval(timer2);
            }


            // pausa quando passa o mouse
            const viewport2 = document.getElementById('viewport2');
            viewport2.addEventListener('mouseenter', parar2);
            viewport2.addEventListener('mouseleave', iniciar2);

            // inicializa
            atualizarUI2();
            iniciar2();
        