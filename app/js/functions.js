const params = new Proxy(new URLSearchParams(window.location.search), {
    get: (searchParams, prop) => searchParams.get(prop),
});

const QUERY_ID = params.id;

function stringMaxAndPoint(string, len = 10) {
    if (string.length < len) return string;
    
    return string.substr(0, len) + '...';
}

function normalizeDateSql(date) {
    let result = date.split(' ');
    result = `${result[2]}-${monthShort.indexOf(result[1]) + 1}-${result[0]}`;

    return result;
}

function throttle(func, ms) {
    let locked = false;

    return function () {
        if (locked) return

        const context = this;
        const args = arguments;

        locked = true;

        setTimeout(() => {
            func.apply(context, args);
            locked = false;
        }, ms)
    }
}

function removeClass(elem, classHtml) {
    if (!elem.classList.contains(classHtml)) return;

    elem.classList.remove(classHtml)
}

function wordLast(string) {
    string = string.split(',');
    string = string[string.length - 1];
    string = string.trim();
    return string;
}

function api(method, params, success, error)
{
    if(typeof(params) == "function"){
        error = success;
        success = params;
        params = {};
    }
    $.post(BACKEND_URL_API + '/' + method, params, (res) => {
        if(res.error && typeof(error) == "function"){
            error.call(this, res.error);
        }else if(typeof(success) == "function"){
            success.call(this, res.response);
        }
    }).fail( (a, b, c) => {
        if(typeof(error) == "function"){
            error.call(this, c);
        }
    });
}