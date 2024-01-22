/* const inputTel = document.querySelectorAll('.input-tel');
if (inputTel) inputTel.forEach(elem => initFlag(elem));
*/
// Соответствие кодов стран и флага
const iso = new Map([
    [1, 'us'],
    [7, 'ru'],
]);
function initFlag()
{
    const input = $('.input-tel');
    const flag = $('.input-tel__flag');
    if(flag.length) {
        flag.attr('style', 'background-image: url(\'/view/static/img/flags-4x3/us.svg\')');
        flag.addClass('has-flag');
    }
    if(input.length) {
        input.addClass('has-flag');
        input.parent().parent().find('svg').show();
    }
}
initFlag();

function maskTell(input) {
    let keyCode;
    if(!input.value.length){
        input.value = '+1';
    }
    function mask(event) {
        const flag = document.querySelector(".input-tel__flag");
        input.classList.remove('has-flag');
        flag.classList.remove('has-flag');
        let svg = $(input).parent().parent().find('svg');
        if(svg){
            svg.show();
        }
        // Маска телефона
        event.keyCode && (keyCode = event.keyCode);
        let pos = this.selectionStart;

        if (pos < 1) event.preventDefault();

        let matrix = "+1 (___) ___ ____",
            i = 0,
            def = matrix.replace(/\D/g, ""),
            val = this.value.replace(/\D/g, ""),
            new_value = matrix.replace(/[_\d]/g, function (a) {
                return i < val.length ? val.charAt(i++) || def.charAt(i) : a
            });
        i = new_value.indexOf("_");
        if (i != -1) {
            i < 3 && (i = 1);
            new_value = new_value.slice(0, i)
        }

        let reg = matrix.substr(0, this.value.length).replace(/_+/g,
            function (a) {
                return "\\d{1," + a.length + "}"
            }).replace(/[+()]/g, "\\$&");

        reg = new RegExp("^" + reg + "$");

        if (!reg.test(this.value) || this.value.length < 2 || keyCode > 47 && keyCode < 58) this.value = new_value;
        if (event.type == "blur" && this.value.length < 4) this.value = "";


        const regex = /\+(.).*$/;
        const match = input.value.match(regex);

        if (match) {
            const countryCode = parseInt(match[1]);
            const xx = iso.get(countryCode);
            if (xx){
                flag.style.backgroundImage = `url('/view/static/img/flags-4x3/${xx}.svg')`;
                flag.classList.add('has-flag');
                input.classList.add('has-flag');
                if(svg){
                    svg.hide();
                }
            }else flag.style.backgroundImage = 'none';
        }
        else flag.style.backgroundImage = 'none';
    }
    // Вызываем эту функцию, чтобы обновить телефон, когда страница загружена
    function dispatch() {
        const event = new Event('input', {
            bubbles: true,
            cancelable: true,
        });
        input.dispatchEvent(event);
    }

    input.addEventListener("input", mask, false);
    input.addEventListener("mouseover", mask, false);
    addEventListener('DOMContentLoaded', dispatch);
};

