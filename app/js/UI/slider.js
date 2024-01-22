const slider = document.querySelector('.header__slider');

if (slider) {
    const arrowLeft = slider.querySelector('.header__slider_arrow');
    const arrowRight = slider.querySelector('.header__slider_arrow._right');
    const headerSliderList = slider.querySelector('.header__slider_list');
    const countSliderItem = slider.querySelectorAll('.header__slider_item').length;

    let transformValue = 0;
    let incrementTransformValue = 33.3;
    let countSliderItemValue = (countSliderItem - 3) * incrementTransformValue;

    if (window.screen.width < 1024) {
        incrementTransformValue = 100;
        countSliderItemValue = (countSliderItem - 1) * incrementTransformValue;
    }

    arrowLeft.onclick = () => {
        if (0 <= transformValue) {
            return;
        }

        transformValue += incrementTransformValue;
        headerSliderList.style = `--transformValue: ${transformValue}%`;
    }

    arrowRight.onclick = () => {
        if (countSliderItemValue <= -transformValue) {
            return;
        }

        transformValue -= incrementTransformValue;
        headerSliderList.style = `--transformValue: ${transformValue}%`;
    }
}