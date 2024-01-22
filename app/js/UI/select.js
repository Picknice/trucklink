const select = document.querySelectorAll('.select');

if (select) {
    $(select).focus(function(){
        let self = $(this);
        if(self.hasClass('_error')){
            self.removeClass('_error');
        }
    });
    $(document).click(function(e){
        $('.__active').removeClass('__active');
        $('.select').removeClass('_active');
    });
    select.forEach(elem => {
        const selectOption = elem.querySelectorAll('.select__option');
        const selectInput = elem.querySelector('.select__input');
        const selectBlock = elem.querySelector('.select-block');

        selectOption.forEach(option => {
            option.onclick = (e) => {
                selectInput.value = option.textContent.trim();
                $(elem).removeClass('_active').parent().parent().removeClass('__active');
                e.stopPropagation();
            }
        })

        elem.onclick = e => {
            let el = $(elem);
            $('.calendar').removeClass('display-block');
            e.stopPropagation();
            e.preventDefault();
            let pos = el.hasClass('_active');
            let sl = $(select);
            sl.removeClass('_active');
            if(sl.parent().parent().hasClass('filterss')){
                sl.parent().removeClass('__active');
            }
            if(!pos){
                el.addClass('_active');
                if(el.parent().parent().hasClass('filterss')){
                    el.parent().addClass('__active');
                }
            }
        }
    })
}