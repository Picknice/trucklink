$('.work-hours-mask').mask('00:00 - 00:00', {
    onComplete: function(val, e, el){
        let exp = val.split(' - ');
        let a = 0;
        let b = 0;
        let c = 0;
        let d = 0;
        if(exp.length == 2){
            let exp2 = exp[0].split(':');
            a = parseInt(exp2[0])||0;
            b = parseInt(exp2[1])||0;
            exp2 = exp[1].split(':');
            c = parseInt(exp2[0])||0;
            d = parseInt(exp2[1])||0;
        }
        if(a > 23){
            a = 23;
        }
        if(b > 59){
            b = 59;
        }
        if(c > 23){
            c = 23;
        }
        if(d > 59){
            d = 59;
        }
        a = a < 10 ? '0' + a : a;
        b = b < 10 ? '0' + b : b;
        c = c < 10 ? '0' + c : c;
        d = d < 10 ? '0' + d : d;
        $(el).val(a+':'+b+' - '+c+':'+d);
    },
    selectOnFocus: true
});
$('.card-mask').mask('0000 0000 0000 0000', {
    selectOnFocus: true
});
$('.card-date-mask').mask('00/00', {
    onComplete: function(val, e, el){
      let exp = val.split('/');
      let a = parseInt(exp[0])||1;
      let b = parseInt(exp[1])||1;
      if(a == 0){
          a = 1;
      }
      if(a > 12){
          a = 12;
      }
      let m = (new Date).getMonth() + 1;
      let c = (new Date).getFullYear() - 2000;
      if(b < c){
          b = c;
      }
      if(b == c){
          if(a < m){
              a = m;
          }
      }
      a = a < 10 ? '0' + a : a;
      b = b < 10 ? '0' + b : b;
      $(el).val(a+'/'+b);
    },
    selectOnFocus: true
});
$('.card-cvv-mask').mask('000', {
    selectOnFocus: true
});
$('.input-cargo-size-mask').mask('00X00X00', {
   selectOnFocus: true
});
$('.input-mask-number').mask('0#', {
    selectOnFocus: true
});
$('.phone-mask, .input-tel').on('keydown keyup', function(){
    let val = $(this).val();
    if(val.length < 3){
        $(this).val('+1 ');
    }
}).mask('+1 (000) 000-0000', {
    selectOnFocus: true
});