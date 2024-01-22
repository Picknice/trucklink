// Payment Status
(function() {
  const blockDDL = document.querySelector('.payments_filter');
  const blockPrice = document.querySelector('.payments_filter-price');
  const inputFilter = blockDDL.querySelector('input');
  const goBack = blockPrice.querySelector('.price-close');
  const priceFrom = blockPrice.querySelector('.price-from');
  const priceTo = blockPrice.querySelector('.price-to');

  const selectPrice = document.querySelector('label[for=method_3]');

  selectPrice.addEventListener('click', () => {
    blockDDL.style.display = 'none';
    blockPrice.style.display = 'flex';
  });

  goBack.addEventListener('click', () => {
    blockDDL.style.display = 'flex';
    blockPrice.style.display = 'none';
    priceFrom.value = "";
    priceTo.value = "";
    inputFilter.value = "";
    $('input[name="method"]').filter((i, e) => e.checked).prop('checked', false);
  });

})()