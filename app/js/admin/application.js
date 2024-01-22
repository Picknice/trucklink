let applicationStatuses = {};
let queryParams = {
    offset: 0,
    limit: 0
};
function getStatusIcon(status, id)
{
    status = status.trim();
    let html = `
        <svg class="status__icon" width="8" height="8" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect width="8" height="8" rx="4" transform="matrix(-1 0 0 1 8 0)" fill="url(#paint0_linear_849_3807_${id})"/>
            <defs>
                <linearGradient id="paint0_linear_849_3807_${id}" x1="4" y1="0" x2="4" y2="8" gradientUnits="userSpaceOnUse">
                <stop stop-color="#0C9248"/>
                <stop offset="1" stop-color="#24F792"/>
                </linearGradient>
            </defs>
        </svg>
    `;
    switch(status){
        case 'Awaiting payment':
            html = `
                <svg class="status__icon" width="22" height="24" viewBox="0 0 22 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16.2802 9.18932C14.0179 4.9808 7.98206 4.9808 5.71984 9.18932C3.5731 13.183 6.4659 18.0222 11 18.0222C15.5341 18.0222 18.4269 13.183 16.2802 9.18932Z" fill="url(#paint0_linear_849_3799_${id})" stroke="#B47700" stroke-width="0.6"/>
                    <path opacity="0.6" d="M6.42449 9.56809C8.38481 5.92121 13.6152 5.92121 15.5755 9.56809C17.4358 13.0288 14.929 17.2222 11 17.2222C7.07099 17.2222 4.56424 13.0288 6.42449 9.56809Z" stroke="white"/>
                    <rect x="11.8462" y="12.7227" width="1.69231" height="4.54416" rx="0.846154" transform="rotate(-180 11.8462 12.7227)" fill="#5A3C00"/>
                    <ellipse cx="11" cy="14.5404" rx="0.846154" ry="0.908832" transform="rotate(-180 11 14.5404)" fill="#5A3C00"/>
                    <defs>
                        <linearGradient id="paint0_linear_849_3799_${id}" x1="6.56716" y1="3.52681" x2="16.2725" y2="18.5492" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#FEE3AD"/>
                            <stop offset="1" stop-color="#F0A91C"/>
                        </linearGradient>
                    </defs>
                </svg>
            `;
        break;
        case 'Awaiting quote':
            html = `
                <svg class="status__icon" width="8" height="8" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="8" height="8" rx="4" transform="matrix(-1 0 0 1 8 0)" fill="url(#paint0_linear_849_3810_${id})"/>
                    <defs>
                        <linearGradient id="paint0_linear_849_3810_${id}" x1="4" y1="0" x2="4" y2="8" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#4159D8"/>
                            <stop offset="1" stop-color="#2478F7"/>
                        </linearGradient>
                    </defs>
                </svg>
            `;
        break;
        case 'In Search':
            html = `
                <svg class="status__icon" width="8" height="8" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="8" height="8" rx="4" transform="matrix(-1 0 0 1 8 0)" fill="url(#paint0_linear_849_3809_${id})"/>
                    <defs>
                        <linearGradient id="paint0_linear_849_3809_${id}" x1="4" y1="0" x2="4" y2="8" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#FCD925"/>
                            <stop offset="1" stop-color="#F7BC24"/>
                        </linearGradient>
                    </defs>
                </svg>
            `;
        break;
        case 'Booked':
            html = `
                <svg class="status__icon" width="8" height="8" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="8" height="8" rx="4" transform="matrix(-1 0 0 1 8 0)" fill="url(#paint0_linear_1014_3625_${id})"/>
                    <defs>
                        <linearGradient id="paint0_linear_1014_3625_${id}" x1="4" y1="0" x2="4" y2="8" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#D24215"/>
                            <stop offset="1" stop-color="#FA8315"/>
                        </linearGradient>
                    </defs>
                </svg>
            `;
        break;
        case 'On the way to pickup':
            html = `
                <svg class="status__icon" width="8" height="8" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="8" height="8" rx="4" transform="matrix(-1 0 0 1 8 0)" fill="url(#paint0_linear_1014_3627_${id})"/>
                    <defs>
                        <linearGradient id="paint0_linear_1014_3627_${id}" x1="4" y1="0" x2="4" y2="8" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#AAFC25"/>
                            <stop offset="1" stop-color="#CDF724"/>
                        </linearGradient>
                    </defs>
                </svg>
            `;
        break;
        case 'Carrier loading':
            html = `
                <svg class="status__icon" width="8" height="8" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="8" height="8" rx="4" transform="matrix(-1 0 0 1 8 0)" fill="url(#paint0_linear_1014_3628_${id})"/>
                    <defs>
                        <linearGradient id="paint0_linear_1014_3628_${id}" x1="4" y1="0" x2="4" y2="8" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#3625FC"/>
                            <stop offset="1" stop-color="#291EAC"/>
                        </linearGradient>
                    </defs>
                </svg>
            `;
        break;
        case 'On the way to destination':
            html = `
                <svg class="status__icon" width="8" height="8" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="8" height="8" rx="4" transform="matrix(-1 0 0 1 8 0)" fill="url(#paint0_linear_1014_3629_${id})"/>
                    <defs>
                        <linearGradient id="paint0_linear_1014_3629_${id}" x1="4" y1="0" x2="4" y2="8" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#9E17B4"/>
                            <stop offset="1" stop-color="#AA25E9"/>
                        </linearGradient>
                    </defs>
                </svg>
            `;
        break;
        case 'Uploading':
            html = `
                <svg class="status__icon" width="8" height="8" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="8" height="8" rx="4" transform="matrix(-1 0 0 1 8 0)" fill="url(#paint0_linear_1014_3630_${id})"/>
                    <defs>
                        <linearGradient id="paint0_linear_1014_3630_${id}" x1="4" y1="0" x2="4" y2="8" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#9C5603"/>
                            <stop offset="1" stop-color="#795309"/>
                        </linearGradient>
                    </defs>
                </svg>
            `;
        break;
        case 'Delivered':
            html = `
                <svg class="status__icon" width="8" height="8" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="8" height="8" rx="4" transform="matrix(-1 0 0 1 8 0)" fill="url(#paint0_linear_1014_3631_${id})"/>
                    <defs>
                        <linearGradient id="paint0_linear_1014_3631_${id}" x1="4" y1="0" x2="4" y2="8" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#3481F4"/>
                            <stop offset="1" stop-color="#2D97F9"/>
                        </linearGradient>
                    </defs>
                </svg>
            `;
        break;
        case 'Quoted':
            html = `
                <svg class="status__icon" width="8" height="8" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="8" height="8" rx="4" transform="matrix(-1 0 0 1 8 0)" fill="url(#paint0_linear_1014_3632_${id})"/>
                    <defs>
                        <linearGradient id="paint0_linear_1014_3632_${id}" x1="4" y1="0" x2="4" y2="8" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#F434B3"/>
                            <stop offset="1" stop-color="#E82DF9"/>
                        </linearGradient>
                    </defs>
                </svg>
            `;
        break;
        case 'Trash':
            html = `
                <svg class="status__icon" width="8" height="8" viewBox="0 0 8 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="8" height="8" rx="4" transform="matrix(-1 0 0 1 8 0)" fill="url(#paint0_linear_1014_3632__${id})"/>
                    <defs>
                        <linearGradient id="paint0_linear_1014_3632__${id}" x1="4" y1="0" x2="4" y2="8" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#B22222"/>
                            <stop offset="1" stop-color="#8B0000"/>
                        </linearGradient>
                    </defs>
                </svg>
            `;
            break;
    }
    return html;
}
function renderAdminApplicationStatus(elem)
{
    let html = `
        <div class="status">
            <div class="select">
                <div class="select__content">
                    <div class="select-block">
                        <input class="select__input select__input_filter" placeholder="Status" value="`+elem.status+`" type="text" readonly>
                        <div class="select-icon">
                            <svg width="15" height="8" viewBox="0 0 15 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M0.742422 0.276188C0.374922 0.632552 0.374922 1.2071 0.742422 1.56346L6.97492 7.6071C7.26742 7.89073 7.73992 7.89073 8.03242 7.6071L14.2649 1.56346C14.6324 1.2071 14.6324 0.632551 14.2649 0.276187C13.8974 -0.0801763 13.3049 -0.0801762 12.9374 0.276187L7.49992 5.54164L2.06242 0.268916C1.70242 -0.0801745 1.10242 -0.0801757 0.742422 0.276188Z" fill="#6E7B8B" />
                            </svg>
                        </div>
                    </div>
                    <ul class="select__options">`;
    for(let i in applicationStatuses){
        let applicationStatus = applicationStatuses[i];
        html += `
                        <li class="select__option application-filter_select__option status_option">
                            <label for="status_application_` + elem.application_id + `_` + i + `">
                                `+ getStatusIcon(applicationStatus.name, elem.application_id) + applicationStatus.name+`
                            </label>
                            <input type="radio" name="status" id="status_application_` + elem.application_id + `_` + i + `" value="`+i+`" hidden>
                        </li>
        `;
    }
    html += `
                    </ul>
                </div>
            </div>
        </div>
    `;
    return html;
}
function renderAdminApplication(elem)
{
    return `
        <div class="string_orders">
            <div class="details">
                <a class="application__payment_link" href="/application?id=${elem.application_id}">
                    Details
                    <span class="for-xs">
                        <svg width="22" height="17" viewBox="0 0 15 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0.625 5.5C0.625 5.5 3.125 0.5 7.5 0.5C11.875 0.5 14.375 5.5 14.375 5.5C14.375 5.5 11.875 10.5 7.5 10.5C3.125 10.5 0.625 5.5 0.625 5.5Z" stroke="#798293" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M7.5 7.375C8.53553 7.375 9.375 6.53553 9.375 5.5C9.375 4.46447 8.53553 3.625 7.5 3.625C6.46447 3.625 5.625 4.46447 5.625 5.5C5.625 6.53553 6.46447 7.375 7.5 7.375Z" stroke="#798293" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                        <svg width="22" height="17" viewBox="0 0 7 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6.47632 5.64117L1.12058 1.14572C0.885572 0.948891 0.504823 0.948891 0.269223 1.14572C0.0342164 1.34254 0.0342164 1.66224 0.269223 1.85906L5.20016 5.99782L0.269817 10.1366C0.0348105 10.3334 0.0348105 10.6531 0.269817 10.8504C0.504824 11.0473 0.886165 11.0473 1.12117 10.8504L6.47692 6.35501C6.70831 6.16022 6.70831 5.8355 6.47632 5.64117Z" fill="#798293"></path>
                        </svg>
                    </span>
                </a>
            </div>
            `+renderAdminApplicationStatus(elem)+`
            <div class="info_delivery">
                <div class="delivery_wrap">
                    <div class="delivery_from" title="${elem?.from}">
                        <div class="flag_from">
                            <img class="application__flag" src="/view/static/img/flags/${wordLast(elem?.from)}.png" alt="${wordLast(elem?.from)}">
                        </div>
                        <div class="city__delivery_from">
                            ${elem?.from}
                        </div>
                    </div>
                    <div class="info">from</div>
                    <div class="application_info"></div>
                    <span class="arrow">&#8594;</span>
                    <div class="delivery_to" title="${elem?.to}">
                        <div class="flag_from">
                            <img class="application__flag" src="/view/static/img/flags/${wordLast(elem?.to)}.png" alt="${wordLast(elem?.to)}">
                        </div>
                        <div class="city__delivery_from">
                            ${elem?.to}
                        </div>
                    </div>
                    <div class="info">to</div>
                </div>
            </div>
            <div class="info_date">
                <div class="info_date_xs">${elem?.date}</div>
            </div>
            <div class="info_car">
                ${elem?.transport_type}
            </div>
            <div class="info_pay_order">
                ID ${elem?.application_id}
            </div>
            <div class="info_payment">
                <div class="info_price">
                    ${elem?.price}
                </div>
                <a class="info_message" href="/chat?broker&application_id=${elem?.application_id}">
                    <span data-application-unread="`+elem.application_id+`" class="badge`+(elem.count_unread_messages > 0 ? ' show':'')+`"></span>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M22 6L12 13L2 6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </a>
            </div>
            <div class="info_last">
                <div class="info_gps">
                    <a class="application__flex justify-center" href="/application_edit?id=${elem?.application_id}">
                        <svg fill="rgba(0, 0, 0, 0)" stroke-width="1.5" stroke="#fff" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="1.8rem" height="1.8rem">
                            <path d="M 10.490234 2 C 10.011234 2 9.6017656 2.3385938 9.5097656 2.8085938 L 9.1757812 4.5234375 C 8.3550224 4.8338012 7.5961042 5.2674041 6.9296875 5.8144531 L 5.2851562 5.2480469 C 4.8321563 5.0920469 4.33375 5.2793594 4.09375 5.6933594 L 2.5859375 8.3066406 C 2.3469375 8.7216406 2.4339219 9.2485 2.7949219 9.5625 L 4.1132812 10.708984 C 4.0447181 11.130337 4 11.559284 4 12 C 4 12.440716 4.0447181 12.869663 4.1132812 13.291016 L 2.7949219 14.4375 C 2.4339219 14.7515 2.3469375 15.278359 2.5859375 15.693359 L 4.09375 18.306641 C 4.33275 18.721641 4.8321562 18.908906 5.2851562 18.753906 L 6.9296875 18.1875 C 7.5958842 18.734206 8.3553934 19.166339 9.1757812 19.476562 L 9.5097656 21.191406 C 9.6017656 21.661406 10.011234 22 10.490234 22 L 13.509766 22 C 13.988766 22 14.398234 21.661406 14.490234 21.191406 L 14.824219 19.476562 C 15.644978 19.166199 16.403896 18.732596 17.070312 18.185547 L 18.714844 18.751953 C 19.167844 18.907953 19.66625 18.721641 19.90625 18.306641 L 21.414062 15.691406 C 21.653063 15.276406 21.566078 14.7515 21.205078 14.4375 L 19.886719 13.291016 C 19.955282 12.869663 20 12.440716 20 12 C 20 11.559284 19.955282 11.130337 19.886719 10.708984 L 21.205078 9.5625 C 21.566078 9.2485 21.653063 8.7216406 21.414062 8.3066406 L 19.90625 5.6933594 C 19.66725 5.2783594 19.167844 5.0910937 18.714844 5.2460938 L 17.070312 5.8125 C 16.404116 5.2657937 15.644607 4.8336609 14.824219 4.5234375 L 14.490234 2.8085938 C 14.398234 2.3385937 13.988766 2 13.509766 2 L 10.490234 2 z M 12 8 C 14.209 8 16 9.791 16 12 C 16 14.209 14.209 16 12 16 C 9.791 16 8 14.209 8 12 C 8 9.791 9.791 8 12 8 z" />
                        </svg>
                    </a>
                </div>
                <div class="info_options" data-application-id="${elem?.application_id}">
                    <svg width="1.5rem" height="1.5rem" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="4.5" y="5.10059" width="15" height="18" rx="1.5" stroke="#F53D3D" stroke-width="1.5" />
                        <rect x="2.25" y="2.10059" width="19.5" height="3" rx="0.75" stroke="#F53D3D" stroke-width="1.5" stroke-linejoin="round" />
                        <path d="M9 0.899414L15 0.899414" stroke="#F53D3D" stroke-width="1.5" stroke-linecap="round" />
                        <path d="M12 9V19.5" stroke="#F53D3D" stroke-width="1.5" stroke-linecap="round" />
                        <path d="M15.75 9V19.5" stroke="#F53D3D" stroke-width="1.5" stroke-linecap="round" />
                        <path d="M8.25 9V19.5" stroke="#F53D3D" stroke-width="1.5" stroke-linecap="round" />
                    </svg>                                
                </div>
            </div>
        </div>
    `;
}
function getAdminApplications(offset, count) {
    let table = $('#admin_cargo .admin_orders_table');
    let params = {};
    for(let p in queryParams){
        params[p] = queryParams[p];
    }
    if(offset !== undefined){
        params.offset = parseInt(offset)||0;
    }
    if(count !== undefined){
        params.limit = parseInt(count)||10;
    }
    api('admin_application', params, function(res){
        applicationStatuses = res.statuses;
        res = res.applications;
        if(!res.length && queryParams.limit === 0){
            return table.html('<div class="string_orders empty">No applications</div>');
        }
        table.find('.string_orders.empty').remove();
        table.find('.more').remove();
        for(let i = 0; i < res.length; i++){
            let elem = res[i];
            table.append(renderAdminApplication(elem));
        }
        queryParams.limit = table.find('.string_orders').length;
        if(res.length >= 10){
            table.append('<div class="more button">More</div>');
            table.find('.more').click(function(){
                getAdminApplications(queryParams.limit, 10);
            });
        }
        table.find('.select').off('click').click(function(e){
            let el = $(this);
            if(!el.hasClass('_active')){
                e.stopPropagation();
                e.preventDefault();
                el.addClass('_active');
                if(el.parent().parent().hasClass('filterss')){
                    el.parent().addClass('__active');
                }
            }
        }).off('focus').focus(function(){
            let self = $(this);
            if(self.hasClass('_error')){
                self.removeClass('_error');
            }
        }).find('.select__option').off('click').click(function(e){
            let self = $(this);
            let info = self.find('input').attr('id').split('_');
            e.stopPropagation();
            e.preventDefault();
            self.parent().parent().parent().removeClass('_active').parent().parent().removeClass('__active');
            api('admin_application_status', {application_id: info[2], status_id: info[3]}, function() {
                self.parent().parent().parent().find('.select__input').val(self.text().trim());
            });
        });
        table.find('.info_options').off('click').click(function(){
            let self = $(this);
            const confirmRemove = confirm('Confirm deletion');
            if(confirmRemove){
                api('admin_application_remove', {application_id: self.attr('data-application-id')}, function(){
                    $('#admin_cargo .button__filter').click();
                }, function(res){
                    alert(res);
                });
            }
        })
    });
}
function getAdminApplicationsFilter() {
    const applicationFilterButton = document.querySelector('.button__filter');
    if (!applicationFilterButton) return;
    applicationFilterButton.onclick = () => {
        const from = document.getElementsByName('from')[0]?.value;
        const to = document.getElementsByName('to')[0]?.value;

        const status = [...document.getElementsByName('status')].filter(elem => elem.checked)[0]?.value;
        const transportType = [...document.getElementsByName('transport_type')].filter(elem => elem.checked)[0]?.value;
        const method = [...document.getElementsByName('method')].filter(elem => elem.checked)[0]?.value;

        const priceMin = document.querySelector('.price-from').value;
        const priceMax = document.querySelector('.price-to').value;

        const beginDate = $('input[name="begin_date"]').val();
        const endDate =  $('input[name="end_date"]').val();

        const searchId = $('input[name=search_id]').val();
        let tmpQueryParams = {
            from: from||'',
            to: to||'',
            status: status||'',
            transport_type: transportType||'',
            method: method||'',
            price_min: priceMin||'',
            price_max: priceMax||'',
            begin_date: beginDate||'',
            end_date: endDate||'',
            application_id: searchId||''
        };
        for(let i in tmpQueryParams){
            let v = '' + tmpQueryParams[i];
            if(v.length){
                queryParams[i] = tmpQueryParams[i];
            }else if(queryParams[i]){
                delete queryParams[i];
            }
        }
        $('#admin_cargo .admin_orders_table').html('');
        queryParams.offset = 0;
        queryParams.limit = 0;
        getAdminApplications();
    }
    getAdminApplications();
}
if ($('#admin_cargo .admin_orders_table').length) {
    getAdminApplicationsFilter();
}
let filtersAdminClear = $('#admin_cargo .filters_clear');
if(filtersAdminClear.length){
    $('#admin_cargo .filters-xs').click(function(){
        let self = $(this);
        let pos = self.hasClass('show');
        $('#admin_cargo .filters-xs, #admin_cargo .filterss').removeClass('show');
        if(pos){
            $('#admin_cargo .filters-xs, #admin_cargo .filterss').removeClass('show');
        }else{
            $('#admin_cargo .filters-xs, #admin_cargo .filterss').addClass('show');
        }
    });
    $(window).resize(function(){
        $('#admin_cargo .filters-xs, #admin_cargo .filterss').removeClass('show');
    });
    $(document).click(function(e){
        let a = $('#cargo .filters-xs, #cargo .filterss');
        if(!a.is(e.target) && a.has(e.target).length === 0){
            a.removeClass('show');
        }
    });
    filtersAdminClear.click(function(){
        $('#admin_cargo input[name="from"]').val('');
        $('#admin_cargo input[name="to"]').val('');
        $('#admin_cargo input[name="status"]').filter((i, e) => e.checked).prop('checked', false);
        $('#admin_cargo input[name="transport_type"]').filter((i, e) => e.checked).prop('checked', false);
        $('#admin_cargo input[name="method"]').filter((i, e) => e.checked).prop('checked', false);
        $('#admin_cargo ._price').attr("method_id", '');
        $('#admin_cargo .price-from').val('');
        $('#admin_cargo .price-to').val('');
        $('#admin_cargo input[name="begin_date"]').val('');
        $('#admin_cargo input[name="end_date"]').val('');
        $('#admin_cargo .payments_filter-price').hide();
        $('#admin_cargo .payments_filter').css({display: "flex"});
        $('#admin_cargo .filterss .select__input.select__input_filter').val('');
        $('#admin_cargo .filterss .calendar-badge').hide();
        $('#admin_cargo .filterss .calendar__day_item').removeClass('_active _range');
        $("#admin_cargo input[name=search_id]").val('');
        $('#admin_cargo .button__filter').click();
    });
}