  
        (function () {
            const radios = Array.from(document.querySelectorAll('.carousel2 input[name="radio-btn"]'));
            if (!radios.length) return;
            let i = radios.findIndex(r => r.checked);
            setInterval(() => {
                i = (i + 1) % radios.length;
                radios[i].checked = true; // isto dispara suas regras CSS (#radioX:checked ~ .slides ...)
            }, 4000); // 4s por slide
        })();
 