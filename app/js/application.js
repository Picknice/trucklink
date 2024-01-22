const inputBlockFile = document.querySelector('.input__block_file');
if (inputBlockFile) {
    const inputPhoto = inputBlockFile.querySelector('#photo');
    const inputBlockFileContentText = inputBlockFile.querySelector('.input__block_file_content_text');

    inputPhoto.onchange = () => {
        if (!inputPhoto.files[0]) return;

        const file = inputPhoto.files[0];
        inputBlockFileContentText.textContent = file.name;
        inputPhoto.title = "You can re-upload the file";
    }
}

const applicationDelete = document.querySelector('#cargo ._application-delete');

if (applicationDelete) {
    applicationDelete.onclick = () => {
        const confirmRemove = confirm('Confirm deletion');

        if (confirmRemove) {
            fetch(`${BACKEND_URL_API}/application?application_id=${params.id}`, {
                method: 'DELETE'
            })
                .then(res => {
                    if (res.status >= 200 && res.status < 300) return res.json();

                    throw res;
                })
                .then(() => window.history.go(-1))
                .catch(data => data.json())
                .then(res => console.log(res?.message));
        }
    }
}
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
function renderApplication(elem)
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
            <div class="order_status">
                <div class="status_word">
                    `+getStatusIcon(elem.status, elem.application_id)+` ${elem?.status}
                </div>
            </div>
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
            <div class="info_payment">
                <div class="info_price">
                    ${elem?.price}
                </div>
                <a class="info_message" href="/chat?application_id=${elem?.application_id}">
                    <span data-application-unread="`+elem.application_id+`" class="badge`+(elem.count_unread_messages > 0 ? ' show':'')+`"></span>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M22 6L12 13L2 6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </a>
            </div>
            <div class="info_last">
                <div class="info_pay_order">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M13.4 17.4201H10.89C9.25003 17.4201 7.92003 16.0401 7.92003 14.3401C7.92003 13.9301 8.26003 13.5901 8.67003 13.5901C9.08003 13.5901 9.42003 13.9301 9.42003 14.3401C9.42003 15.2101 10.08 15.9201 10.89 15.9201H13.4C14.05 15.9201 14.59 15.3401 14.59 14.6401C14.59 13.7701 14.28 13.6001 13.77 13.4201L9.74003 12.0001C8.96003 11.7301 7.91003 11.1501 7.91003 9.36008C7.91003 7.82008 9.12003 6.58008 10.6 6.58008H13.11C14.75 6.58008 16.08 7.96008 16.08 9.66008C16.08 10.0701 15.74 10.4101 15.33 10.4101C14.92 10.4101 14.58 10.0701 14.58 9.66008C14.58 8.79008 13.92 8.08008 13.11 8.08008H10.6C9.95003 8.08008 9.41003 8.66008 9.41003 9.36008C9.41003 10.2301 9.72003 10.4001 10.23 10.5801L14.26 12.0001C15.04 12.2701 16.09 12.8501 16.09 14.6401C16.08 16.1701 14.88 17.4201 13.4 17.4201Z" fill="white" />
                        <path d="M12 18.75C11.59 18.75 11.25 18.41 11.25 18V6C11.25 5.59 11.59 5.25 12 5.25C12.41 5.25 12.75 5.59 12.75 6V18C12.75 18.41 12.41 18.75 12 18.75Z" fill="white" />
                        <path d="M12 22.75C6.07 22.75 1.25 17.93 1.25 12C1.25 6.07 6.07 1.25 12 1.25C17.93 1.25 22.75 6.07 22.75 12C22.75 17.93 17.93 22.75 12 22.75ZM12 2.75C6.9 2.75 2.75 6.9 2.75 12C2.75 17.1 6.9 21.25 12 21.25C17.1 21.25 21.25 17.1 21.25 12C21.25 6.9 17.1 2.75 12 2.75Z" fill="white" />
                    </svg>
                    <div class="pay_order">`+(elem.pay_status == 2 ? 'Success' : (elem.pay_status == 1 ? 'Cancel' : (elem.status_id == 1 && elem.price_value > 0 ? '<a href="order?application_id='+elem.application_id+'">Pay order</a>' : 'Find price')))+`</div>
                </div>
                <div class="info_gps">
                    `+(elem.map_exists ? '<a href="/application?id='+elem.application_id+'&map=1">':'')+`
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 11L22 2L13 21L11 13L3 11Z" stroke="#65707A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    `+(elem.map_exists ? '</a>':'')+`
                </div>
                <a class="info_options" href="/application?id=${elem?.application_id}">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>                                
                </a>
            </div>
        </div>
    `;
}
function getApplications(offset, count) {
    let table = $('#cargo .orders_table');
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
    api('application', params, function(res){
        if(!res.length && queryParams.limit === 0){
            return table.html('<div class="string_orders empty">No applications</div>');
        }
        table.find('.string_orders.empty').remove();
        table.find('.more').remove();
        for(let i = 0; i < res.length; i++){
            let elem = res[i];
            table.append(renderApplication(elem));
        }
        queryParams.limit = table.find('.string_orders').length;
        if(res.length >= 10){
            table.append('<div class="more button">More</div>');
            table.find('.more').click(function(){
                getApplications(queryParams.limit, 10);
            });
        }
    });
}

function getApplicationsFilter() {
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
        $('#cargo .orders_table').html('');
        queryParams.offset = 0;
        queryParams.limit = 0;
        getApplications();
    }
    getApplications();
}
if ($('#cargo .orders_table').length) {
    getApplicationsFilter();
}
let filtersClear = $('#cargo .filters_clear');
if(filtersClear.length){
    $('#cargo .filters-xs').click(function(){
        let self = $(this);
        let pos = self.hasClass('show');
        $('#cargo .filters-xs, #cargo .filterss').removeClass('show');
        if(pos){
            $('#cargo .filters-xs, #cargo .filterss').removeClass('show');
        }else{
            $('#cargo .filters-xs, #cargo .filterss').addClass('show');
        }
    });
    $(window).resize(function(){
        $('#cargo .filters-xs, #cargo .filterss').removeClass('show');
    });
    $(document).click(function(e){
        let a = $('#cargo .filters-xs, #cargo .filterss');
        if(!a.is(e.target) && a.has(e.target).length === 0){
           a.removeClass('show');
        }
    });
    filtersClear.click(function(){
        $('#cargo input[name="from"]').val('');
        $('#cargo input[name="to"]').val('');
        $('#cargo input[name="status"]').filter((i, e) => e.checked).prop('checked', false);
        $('#cargo input[name="transport_type"]').filter((i, e) => e.checked).prop('checked', false);
        $('#cargo input[name="method"]').filter((i, e) => e.checked).prop('checked', false);
        $('#cargo ._price').attr("method_id", '');
        $('#cargo .price-from').val('');
        $('#cargo .price-to').val('');
        $('#cargo input[name="begin_date"]').val('');
        $('#cargo input[name="end_date"]').val('');
        $('#cargo .payments_filter-price').hide();
        $('#cargo .payments_filter').css({display: "flex"});
        $('#cargo .filterss .select__input.select__input_filter').val('');
        $('#cargo .filterss .calendar-badge').hide();
        $('#cargo .filterss .calendar__day_item').removeClass('_active _range');
        $('#cargo .button__filter').click();
    });
}
let infoList = $(".application-info__info.info__list");
if(infoList.length){
    infoList.find('.details-xs, .info-xs').click(function(){
        let self = $(this);
        if(self.hasClass('show')){
            self.removeClass('show');
            if(self.hasClass('details-xs')){
                infoList.find('.details-wrap').removeClass('show');
            }else{
                infoList.find('.info-wrap').removeClass('show');
            }
        }else{
            self.addClass('show');
            if(self.hasClass('details-xs')){
                infoList.find('.details-wrap').addClass('show');
            }else{
                infoList.find('.info-wrap').addClass('show');
            }
        }
    });
}