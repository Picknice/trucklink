let canPlaySms = false;
let connected = false;
function renderMessage(item)
{
    if(item.user) {
        item.user.is_current = item.user.id === userId;
    }
    let t = document.createElement('div');
    t.style.display = 'none';
    t.innerText = item.text;
    item.text = t.innerHTML.replace("\n", '<br>');
    t.remove();
    return `
        <div data-message-id="`+item.id+`" `+(item.date ? ' data-date="'+item.date+'"' : '')+` class="chat__message__wrap `+(item.user_id === 0 ? 'chat__system' : 'chat__message' + (item.user && item.user.is_current ? ' _from-me': ''))+`">
            <div class="chat__message__time">`+(item.created && item.user_id !== 0 ? date('H:i', item.created) : '')+`</div>
            `+(item.user_id!==0 && !item.readed ? '<div class="chat__message__unread"></div>':'')+`
            <div class="chat__message__text">`+item.text+`</div>
        </div>
    `;
}
function renderJoinMessage(joinUser)
{
    return 'Trucklink: manager ' + joinUser.name + ' ' + joinUser.surname + ' join';
}
let usersList = {};
let lastMessageId = false;
let lastDay = false;
let lastPrevDay = false;
function getFormatDate(v)
{
    if(date('d.m.Y') == v){
        return 'Сегодня';
    }
    if(date('d.m.Y', timestamp() - 24 * 3600) == v){
        return 'Вчера';
    }
    return v;
}
function checkUnread(upperScroll, currentScrollPos)
{
    upperScroll = upperScroll === true;
    currentScrollPos = currentScrollPos||0;
    let messagesBlock = $('.chat__messages');
    let notifyUnread = $('.chat__messages__unread__notify');
    if ((messagesBlock.prop('scrollHeight') <= messagesBlock.height()) || (messagesBlock.get(0).scrollHeight - messagesBlock.height() - currentScrollPos < 20 && !upperScroll && notifyUnread.css('display') !== 'none')) {
        let messagesList = messagesBlock.find('[data-message-id]');
        $('.chat__messages__unread__notify').hide();
        api('chat_read_messages', {
            application_id: applicationId,
            message_id: $(messagesList[messagesList.length - 1]).attr('data-message-id')
        }, function () {
            messagesList.removeClass('chat__message__unread');
            notifyUnread.hide();
        });
    }
}
function getMessages(messageId)
{
    let messagesBlock = $('.chat__messages');
    let loaderBlock = $('.chat__loader');
    let params = {
        application_id: applicationId
    };
    if(messageId !== undefined){
        params.message_id = messageId;
    }
    let lastScrollPos = messagesBlock.scrollTop();
    messagesBlock.scroll(function (e) {
        let currentScrollPos = messagesBlock.scrollTop();
        let upperScroll = currentScrollPos < lastScrollPos;
        lastScrollPos = currentScrollPos;
        if (currentScrollPos < 5 && upperScroll && loaderBlock.css('display') === 'none') {
            loaderBlock.show();
            messagesBlock.scrollTo(0, 200, {
                complete: function () {
                    let messageBlock = loaderBlock.next();
                    if (messageBlock.length) {
                        getMessages(messageBlock.attr('data-message-id'));
                    } else {
                        loaderBlock.hide();
                    }
                }
            });
        }
        checkUnread(upperScroll, currentScrollPos);
    });
    api('chat_get_messages', params, function(res){
        if(!res.length){
            loaderBlock.hide();
            if(messageId === undefined){
                messagesBlock.append(renderMessage({id: 0, user_id: 0, text: 'Welcome to chat'}));
            }
        }else{
            let html = [];
            for(let i = 0; i < res.length; i++){
                let item = res[i];
                let currentDay = date('d.m.Y', item.created);
                if(messageId === undefined){
                    if(!lastDay){
                        lastDay = currentDay;
                        lastPrevDay = lastDay;
                    }else if(currentDay !== lastDay){
                        html.push(renderMessage({id: 0, user_id: 0, text: getFormatDate(lastDay), date: lastDay}));
                        lastDay = currentDay;
                        lastPrevDay = lastDay;
                    }
                }else{
                    if(!lastPrevDay){
                        lastPrevDay = currentDay;
                    }else if(currentDay !== lastPrevDay){
                        loaderBlock.after(renderMessage({id: 0, user_id: 0, text: getFormatDate(lastPrevDay), date: lastPrevDay}));
                        lastPrevDay = currentDay;
                    }
                }
                if(!lastMessageId && messageId === undefined){
                    lastMessageId = item.id;
                }
                if(item.user_id === 0){
                    if(item.text.indexOf('join #') !== -1){
                        let joinUserId = parseInt(item.text.substring(6))||0;
                        if(joinUserId && usersList[joinUserId] !== undefined){
                            let joinUser = usersList[joinUserId];
                            item.text = renderJoinMessage(joinUser);
                        }
                    }
                }
                let message = renderMessage(item);
                if(item.user) {
                    usersList[item.user.id] = item.user;
                }
                if(messageId !== undefined){
                    loaderBlock.after(message);
                    messagesBlock.scrollTo('.chat__message__wrap[data-message-id='+messageId+']');
                }else{
                    html.push(message);
                }
            }
            if(messageId === undefined){
                function showChat() {
                    loaderBlock.hide();
                    messagesBlock.append(html.reverse().join(""));
                    messagesBlock.scrollTo('max');
                }
                let tm = setInterval(function(){
                    if(connected){
                        clearInterval(tm);
                        showChat();
                    }
                }, 100);
            }else if( res.length < 10 ){
                loaderBlock.remove();
            }else{
                loaderBlock.hide();
            }
        }
    }, function(){
        loaderBlock.hide();
    });
}
function sendMessage()
{
    let messageInput = $(".chat__textarea");
    let message =  messageInput.val();
    if(!message.length){
        alert('Enter message');
        return false;
    }
    api('chat_send_message', {application_id: applicationId, message: message}, function(res){
        messageInput.val('').focus();
        $(".chat__messages").find('.chat__message__wrap').not('._from-me').find('.chat__message__unread').remove();
    }, function(res){
        alert(res);
    });
}
function addMessage(message)
{
    let messagesBlock = $('.chat__messages');
    if(!messagesBlock.length){
        return;
    }
    let audioElement = document.getElementById('sms');
    if(audioElement && canPlaySms && userId !== message.user_id) {
        audioElement.volume = 1;
        audioElement.currentTime = 0;
        audioElement.play();
    }
    messagesBlock.find('[data-message-id=0]').remove();
    messagesBlock.append(renderMessage(message));
    if(userId === message.user_id) {
        messagesBlock.scrollTo('max');
    }else{
        let countUnreadMessages = messagesBlock.find('[data-message-id]').filter( (i, e)  => parseInt(e.getAttribute('data-message-id')) > lastMessageId ).length;
        if (countUnreadMessages > 0 && messagesBlock.prop('scrollHeight') - messagesBlock.height() > messagesBlock.scrollTop()) {
            $('.chat__messages__unread__notify').text(countUnreadMessages > 1 ? 'Новые сообщения' : 'Новое сообщение').show();
        } else {
            $('.chat__messages__unread__notify').hide();
        }
    }
    messagesBlock.find('[data-date]').each(function(i, e){
        let el = $(e);
        el.text(getFormatDate(el.attr('data-date')));
    });
}
function startChat()
{
    const chat = new WebSocket(webSocketServer);
    chat.onopen = function()
    {
        chat.send(JSON.stringify({
            sessionToken: sessionToken
        }));
        connected = true;
    };
    chat.onmessage = function(msg)
    {
        if(msg !== undefined && msg.data.length){
            let data = JSON.parse(msg.data);
            function checkApplication()
            {
                if(applicationId===undefined){
                    return false;
                }
                if(!data.applicationId && data.applicationId !== applicationId){
                    return false;
                }
                checkUnread(true);
                return true;
            }
            for(let prop in data){
                switch(prop){
                    case 'joined' :
                        if(checkApplication()) {
                            let messageJoined = data.joined;
                            messageJoined.user_id = 0;
                            messageJoined.text = renderJoinMessage(messageJoined);
                            addMessage(messageJoined);
                        }
                        break;
                    case 'readed':
                        if(checkApplication()) {
                            let messages = data.readed;
                            for (let i = 0; i < messages.length; i++) {
                                $('.chat__messages').find('[data-message-id=' + messages[i] + ']').find('.chat__message__unread').remove();
                            }
                        }
                    break;
                    case 'message':
                        if(checkApplication()) {
                            let message = data.message;
                            addMessage(message);
                        }
                    break;
                    case 'notify_unread':
                        console.log(data, userId);
                        if(data.users.indexOf(userId) !== -1){
                            let unreadApplication = $('[data-application-unread='+data.applicationId+']');
                            if(unreadApplication.length){
                                unreadApplication.addClass('show');
                            }
                        }
                        break;
                }
            }
        }
    };
    chat.onclose = function()
    {
        setTimeout(function(){
            startChat();
        }, 5000);
    };
}
$(document).ready(function () {
    let inputMessage = $(".chat__textarea");
    inputMessage.on('keypress', function(e){
        if(e.keyCode === 13 && !e.shiftKey){
            e.preventDefault();
            e.stopPropagation();
            sendMessage();
            return false;
        }
    });
    if(inputMessage.length) {
        inputMessage.textareaAutoSize();
        inputMessage.on("input", function () {
            if (this.value.length > 500) {
                alert('The message is too long');
                this.value = this.value.substring(0, 500);
            }
        });
        $(".chat__send").click(function () {
            sendMessage();
        });
        let messageId;
        getMessages(messageId);
        $('.chat__messages__unread__notify').click(function(){
            $('.chat__messages').scrollTo('max');
        });
        $(document).click(function(){
            canPlaySms = true;
        });
    }
});
startChat();