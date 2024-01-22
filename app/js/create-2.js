class InputAmount {
  constructor() {
    this.inputPrice = document.querySelector('input[name=price]');
    this.wrapperPrice = document.querySelector('.input__block__price');
    this.wrapperMethod = document.querySelector('.select__payment-method');
    this.selectPrice = document.querySelector('label[for=method_3]');
    this.goBack = this.wrapperPrice.querySelector('.go-back');
    // Bindings
    this.setPrice = this.setPrice.bind(this);
    this.chooseMethod = this.chooseMethod.bind(this);
    // Listeners
    this.selectPrice.addEventListener('click', this.setPrice);
    this.goBack.addEventListener('click', this.chooseMethod);
    if(this.wrapperMethod.style.display != 'none') {
      this.wrapperPrice.style.display = 'none';
    }
  }

  setPrice() {
    this.wrapperPrice.style.display = 'flex';
    this.wrapperMethod.style.display = 'none';
  }

  chooseMethod() {
    this.wrapperPrice.style.display = 'none';
    this.wrapperMethod.style.display = 'flex';
    this.inputPrice.value = "";
  }
}
class InputCargoSize {
  constructor() {
    this.inputCargoSize = document.querySelector('input[name=cargo_size]');
    this.wrapperCargoSize = document.querySelector('.input__block__cargo-size');
    this.wrapperMethod = document.querySelector('.select__cargo-size-method');
    this.selectCargoSize = document.querySelector('label[for=size_4]');
    this.goBack = this.wrapperCargoSize.querySelector('.go-back');
    this.setCargoSize = this.setCargoSize.bind(this);
    this.chooseMethod = this.chooseMethod.bind(this);
    // Listeners
    this.selectCargoSize.addEventListener('click', this.setCargoSize);
    this.goBack.addEventListener('click', this.chooseMethod);
    if(this.wrapperMethod.style.display != 'none') {
      this.wrapperCargoSize.style.display = 'none';
    }
  }

  setCargoSize() {

    this.wrapperCargoSize.style.display = 'flex';
    this.wrapperMethod.style.display = 'none';
  }

  chooseMethod() {
    this.wrapperCargoSize.style.display = 'none';
    this.wrapperMethod.style.display = 'flex';
    this.inputCargoSize.value = "";
  }
}

$(document).ready(function(){

  const mass = document.querySelector("#mass");
  const massMeassure = document.querySelector("#mass + .input-meassure");
  if(massMeassure) {
    massMeassure.addEventListener('click', () => focusInput(mass));
  }
  if(document.querySelector('input[name=price]')) {
    new InputAmount();
  }
  if(document.querySelector('input[name=cargo_size]')) {
    new InputCargoSize();
  }
  function focusInput(el) {
    el.focus();
  }

});