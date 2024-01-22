class Calendar {
    constructor(calendarHtml, buttonShow, multiSelection = false) {
        this.calendarHtml = calendarHtml;
        this.monthHtml = calendarHtml.querySelector('.calendar__month_text');
        this.calendarMonthLeft = calendarHtml.querySelector('.calendar__month_left');
        this.calendarMonthRight = calendarHtml.querySelector('.calendar__month_right');
        this.daysHtml = calendarHtml.querySelectorAll('.calendar__day_item');
        this.inputHtml = buttonShow.querySelector('.input');
        this.fakeInputHtml = buttonShow.querySelector('.input + .input');
        this.clearBtn = calendarHtml.querySelector('.calendar__clear-btn');
        this.badge = buttonShow.querySelector('.calendar-badge');
        this.buttonShow = buttonShow;
        this.day = false;
        this.months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        this.month = (new Date()).getMonth();
        this.year = (new Date()).getFullYear();
        this.today = new Date(`${new Date().getMonth()} ${new Date().getDate()} ${new Date().getFullYear()}`)
        this.monthShort = [
            'Jan',
            'Feb',
            'Mar',
            'Apr',
            'May',
            'Jun',
            'Jul',
            'Aug',
            'Sep',
            'Oct',
            'Nov',
            'Dec'
        ];
        this.previousDays = false;
        this.isDisplayed = false;
        // Listeners:
        if(this.clearBtn) {
            this.clearBtn.addEventListener('click', () => this.clear());
        }
        this.beginRange = false;
        this.endRange = false;
        // Определяет, был ли клик внутри или снаружи клаендаря, закрывает,
        // если снаружи
        //this.handleClickOutside = this.handleClickOutside.bind(this);

        // Multiselection

    }

    setAnimation(elem, determinantProperty, showProperty = 'd-flex', hideProperty = '_hide', duration = 500) {
        if (elem.classList.contains(determinantProperty)) {
            return;
        }

        elem.classList.add(showProperty);
        elem.classList.add(hideProperty);

        setTimeout(() => {
            if (elem.classList.contains(determinantProperty)) {
                return;
            }

            elem.classList.remove(showProperty);
            elem.classList.remove(hideProperty);
        }, duration);
    }

    render() {
        this.monthHtml.textContent = this.months[this.month] + (this.year !== (new Date).getFullYear() ? ' ' + this.year : '');
        this.days();
    }

    decrementMonth() {
        this.calendarMonthLeft.addEventListener('click', () => {
            this.month -= 1;

            if (this.month < 0) {
                this.year -= 1
                this.month = 11;
            }

            this.render();
        });
    }

    incrementMonth() {
        this.calendarMonthRight.addEventListener('click', () => {
            this.month += 1;
            if (this.month > 11) {
                this.year += 1
                this.month = 0;
            }

            this.render();
        });
    }

    removeClass(elem, classCss) {
        if (elem.classList.contains(classCss)) {
            elem.classList.remove(classCss);
        }
    }

    setInput = (day, disabled) => {
        const { inputHtml, fakeInputHtml } = this;

        if (!disabled) return;
        this.day = day;
        if(day !== ''){
            inputHtml.value = `${day} ${this.monthShort[this.month]} ${this.year}`;
            if (fakeInputHtml)
                fakeInputHtml.value = `${day} ${this.monthShort[this.month]}`;
        }else{
            this.day = false;
            this.month = false;
            this.year = false;
            inputHtml.value = '';
            fakeInputHtml.value = '';
        }
        this.hide();
        this.render();
        if(this.badge) {
            this.badge.style.display = 'block';
        }
    }

    clear() {
        const { inputHtml, fakeInputHtml } = this;
        inputHtml.value = "";
        if (fakeInputHtml) fakeInputHtml.value = "";
        this.hide();
        this.badge.style.display = 'none';
        $(this.calendarHtml).find('.calendar__day_item').removeClass('_active').removeClass('_range');
        this.beginRange = false;
        this.endRange = false;
    }

    hide() {
        this.removeClass(this.calendarHtml, 'display-block');
    }

    ClickOutside(event) {
        if (!elem || elem.contains(event.target)) return;
        this.hide();
    }

    show() {
        return this.buttonShow.addEventListener('click', () => {
            // Логика, которая была до меня
            this.calendarHtml.classList.toggle('display-block');
            this.setAnimation(this.calendarHtml, 'display-block', 'd-block');
            // Если календарь через секунду видим
            /*
            setTimeout(() => {
              const cal = this.calendarHtml;
              const isVisible = cal.classList.contains("display-block");
              if (isVisible) document.addEventListener(
                'click', 
                this.handleClickOutside
              );
              else document.removeEventListener(
                'click', 
                this.handleClickOutside
              );
            }, 200); */
        });
    }

    handleClickOutside(e) {
      const cal = this.calendarHtml;
      const targetEl = e.target;
      const isOutside = !cal.contains(targetEl);
      // const isVisible = cal.classList.contains("display-block");
      if (isOutside) {
        this.hide();
        document.removeEventListener(
          'click', 
          this.handleClickOutside
        );
      }
      // this.hide();
      // if (isVisible && isOutside) console.log("works")
      // this.hide();
      // if ()
      // if (isOutside) this.hide();
    }

    setPreviousDays(value) {
        this.previousDays = value;
    }

    days() {
        let obj = this;
        let onlyOne = $(this.calendarHtml).parent().find('input[name="begin_date"]').length === 1 && $(this.calendarHtml).parent().find('input[name="end_date"]').length == 1 ? false : true;
        this.previousDays = !onlyOne;
        function parseDate(v)
        {
            let exp = v.split("-");
            let y = parseInt(exp[0])||0;
            let m = parseInt(exp[1])||0;
            let d = parseInt(exp[2])||0;
            m--;
            let dd = (new Date(y, m, d));
            let w = dd.getDay();
            if(w == 0){
                w = 6;
            }else{
                w--;
            }
            return {
                y: dd.getFullYear(),
                m: dd.getMonth(),
                d: dd.getDate(),
                w: w
            };
        }
        function toDate(v)
        {
            let y = v.y;
            let m = v.m + 1;
            let d = v.d;
            return y + '-' + (m < 10 ? '0' + m : m) + '-'+ (d < 10 ? '0'+d : d);
        }
        function intDate(v)
        {
            return v.y * 10000 + v.m * 100 + v.d;
        }
        let begin = this.beginRange;
        let end = this.endRange;
        function checkDate()
        {
            let badgeCounter = 0;
            $(obj.calendarHtml).find('.calendar__day_item._range').removeClass('_range');
            let begin = obj.beginRange;
            let end = obj.endRange;
            if(begin){
                badgeCounter++;
            }
            if(end){
                badgeCounter++;
            }
            if(begin && end){
                let left = intDate(begin);
                let right = intDate(end);
                if( left > right){
                    let tmp = begin;
                    begin = end;
                    end = tmp;
                    obj.beginRange = begin;
                    obj.endRange = end;
                }
                $(obj.calendarHtml).find('.calendar__day_item').not('._disabled').each(function(){
                   let self = $(this);
                   let xdate = parseDate(self.attr('id').substring(4));
                   if(intDate(xdate) > intDate(begin) && intDate(xdate) < intDate(end)){
                       self.addClass('_range');
                   }
                });
            }
            if(obj.badge){
                obj.badge.textContent = badgeCounter;
                obj.badge.style.display = badgeCounter > 0 ? 'block' : 'none';
            }
            let elBegin = $(obj.calendarHtml).parent().find('input[name="begin_date"]');
            let elEnd = $(obj.calendarHtml).parent().find('input[name="end_date"]');
            if(elBegin){
                elBegin.val(begin ? toDate(begin) : '');
            }
            if(elEnd){
                elEnd.val(end ? toDate(end) : '');
            }
        }
        if(!onlyOne) {
            checkDate();
        }
        if (!this.daysHtml) {
            return;
        }

        let disabled = true;
        let firstOne = true;
        $(this.calendarHtml).find('.calendar__day_item._active').removeClass('_active');
        $(this.calendarHtml).find('.calendar__day_item._range').removeClass('_range');
        for (let i = 0; i < this.daysHtml.length; i++) {
            let day = new Date(this.year, this.month, 1).getDay();

            if (day === 0) day += 7;

            let date = new Date(this.year, this.month, i - day + 2)

            this.daysHtml[i].textContent = date.getDate();

            removeClass(this.daysHtml[i], '_disabled');
            //removeClass(this.daysHtml[i], '_active');

            /* (if (this.day == this.daysHtml[i].textContent && this.month === new Date().getMonth() && this.year === new Date().getFullYear()) this.daysHtml[i].classList.add('_active'); */

            if (+this.daysHtml[i].textContent === 1) disabled = false;

            if (!disabled && firstOne && +this.daysHtml[i].textContent > 1) firstOne = false;

            if (this.daysHtml[i - 1] && +this.daysHtml[i - 1].textContent >= +this.daysHtml[i].textContent && !firstOne) disabled = true;

            if (disabled) this.daysHtml[i].classList.add('_disabled');
            else if (!this.previousDays) {
                if (this.today > new Date(`${this.month} ${this.daysHtml[i].textContent} ${this.year}`)) this.daysHtml[i].classList.add('_disabled');
            }
            if(!disabled) {
                if(!onlyOne){
                    let currentDate = {
                        y: date.getFullYear(),
                        m: date.getMonth(),
                        d: date.getDate()
                    };
                    this.daysHtml[i].setAttribute('id', 'day_' + toDate(currentDate));
                    if(begin){
                        if(begin.y == date.getFullYear() && begin.m == date.getMonth() && begin.d == date.getDate()){
                            this.daysHtml[i].classList.add('_active');
                        }
                    }
                    if(end){
                        if(end.y == date.getFullYear() && end.m == date.getMonth() && end.d == date.getDate()){
                            this.daysHtml[i].classList.add('_active');
                        }
                    }
                    if(begin && end){
                        if(intDate(currentDate) > intDate(begin) && intDate(currentDate) < intDate(end)){
                            this.daysHtml[i].classList.add('_range');
                        }
                    }
                }else{
                    if(this.day == date.getDate()){
                        this.daysHtml[i].classList.add('_active');
                    }
                }
            }
            /*
            this.daysHtml[i].onclick = () => {
                this.setInput(this.daysHtml[i].textContent, !this.daysHtml[i].classList.contains('_disabled'));
                this.setAnimation(this.calendarHtml, 'display-block', 'd-block');
            } */
        }
        let days = $(this.calendarHtml).find('.calendar__day_item').not('._disabled');
        days.off('click').click(function(){
            let self = $(this);
            let d = parseInt(self.text())||0;
            let currentDate = {
                y: obj.year,
                m: obj.month,
                d: d
            };
            let begin = obj.beginRange;
            let end = obj.endRange;
            if(self.hasClass('_active')){
                self.removeClass('_active');
                if(!onlyOne) {
                    if (begin && begin.y == obj.year && begin.m == obj.month && begin.d == d) {
                        obj.beginRange = false;
                    }
                    if (end && end.y == obj.year && end.m == obj.month && end.d == d) {
                        obj.endRange = false;
                    }
                    checkDate();
                }else{
                    obj.setInput('', true);
                }
                return true;
            }
            if(!onlyOne) {
                if (begin && end) {
                    if (Math.abs(intDate(currentDate) - intDate(begin)) > Math.abs(intDate(currentDate) - intDate(end))) {
                        $("#day_" + toDate(end)).removeClass('_active');
                        end = currentDate;
                    } else {
                        $("#day_" + toDate(begin)).removeClass('_active');
                        begin = currentDate;
                    }
                    self.removeClass('_range').addClass('_active');
                } else {
                    self.removeClass('_range').addClass('_active');
                    if (begin) {
                        end = currentDate;
                    } else {
                        begin = currentDate;
                    }
                }
                obj.beginRange = begin;
                obj.endRange = end;
                checkDate();
            }else{
                obj.setInput(d, true);
            }
        });
    }

    start() {
        this.render();

        this.decrementMonth();
        this.incrementMonth();

        this.show();
    }
}

const calendarFirst = document.querySelector('.calendar');
const calendarFromActive = document.querySelector('.calendar-from__active');

if (calendarFirst) {
    const path = window.location.pathname;
    let calendarStart;

    switch (path) {
      case "/profile":
        calendarStart = new Calendar(calendarFirst, calendarFromActive, true);
        calendarStart.start();
        break;
      default:
        calendarStart = new Calendar(calendarFirst, calendarFromActive);
        calendarStart.start();
  }
    $(document).ready(function(){
        $(document).click(function(e){
            let calendarBtn = $(calendarFromActive);
            let calendarContent = $(calendarFirst);
            if(!calendarBtn.is(e.target) && calendarBtn.has(e.target).length === 0 && !calendarContent.is(e.target) && calendarContent.has(e.target).length === 0){
                calendarContent.removeClass('display-block');
            }
        });
    })
}