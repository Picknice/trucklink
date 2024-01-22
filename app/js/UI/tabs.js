$(document).ready(function(){
    let tabs = $('.tabs');
    tabs.find('.buttons .tab').click(function(){
       let self = $(this);
       tabs.find('.tab-active').removeClass('tab-active');
       self.addClass('tab-active');
       $(tabs.find('.list .tab')[self.index()]).addClass('tab-active');
    });
});