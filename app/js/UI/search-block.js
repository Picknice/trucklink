const inputSearchBlock = document.querySelector('.input-search__block');

if (inputSearchBlock) {
    const inputSearch = inputSearchBlock.querySelector('.input-search__value');
    const inputSearchList = inputSearchBlock.querySelector('.input-search__list');
    const inputSearchItem = inputSearchBlock.querySelectorAll('.input-search__item');
    $(inputSearchBlock).click(function(e){
       let elList = $(inputSearchList);
       if(elList.hasClass('___active')){
           elList.removeClass('___active');
       }else{
           elList.addClass('___active');
       }
        e.preventDefault();
    });
    $(inputSearchItem).click(function(){
       $(inputSearch).val($(this).find('label').text().trim());
    });
    $(document).click(function(e){
        let inputSearchA = $(inputSearch);
        let inputSearchB = $(inputSearchList);
        if(!inputSearchA.is(e.target) && inputSearchA.has(e.target).length === 0 && !inputSearchB.is(e.target) && inputSearchB.has(e.target).length === 0){
            inputSearchB.removeClass('___active');
        }
    });
}