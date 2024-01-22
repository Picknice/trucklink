let getParams = {};
if(location.search.length){
    let tmpParams = location.search.substring(1).split('&');
    for(let i = 0; i < tmpParams.length; i++){
        let tmpParam = tmpParams[i];
        let pos = tmpParam.indexOf('=');
        let value = '';
        if(pos !== -1){
            value = tmpParam.substring(pos+1);
            tmpParam = tmpParam.substring(0, pos);
        }
        getParams[tmpParam] = value;
    }
}
location.get = function(name)
{
    if(getParams[name] !== undefined){
        return getParams[name];
    }
    return false;
}
function initContactMap() {
    let mapContainer = $('#contact_map');
    if (mapContainer.length) {
        let marker = new google.maps.Marker({
            position: new google.maps.LatLng(40.37260714456388, -75.08720505834613),
            title: "Trucklink"
        });
        let mapOptions = {
            zoom: 9,
            center: marker.getPosition()
        }
        let map = new google.maps.Map(mapContainer.get(0), mapOptions);
        marker.setMap(map);
    }
}
function initTrucksMap()
{
    let mapContainer = $('#map-trucks');
    if(mapContainer.length && window.trucks !== undefined){
        let map = new google.maps.Map(mapContainer.get(0));
        let bounds = new google.maps.LatLngBounds();
        for (let i = 0; i < window.trucks.length; i++) {
            let truck = window.trucks[i];
            let geo = truck.geo.split(',');
            let latitude = parseFloat(geo[0])||0;
            let longitude = parseFloat(geo[1])||0;
            let marker = new google.maps.Marker({
                position: new google.maps.LatLng(latitude, longitude),
                title: truck.name
            });
            marker.setMap(map);
            bounds.extend(marker.getPosition());
        }
        map.fitBounds(bounds);
    }
}
function initApplicationMap()
{
    let mapContainer = $('#application-route-map');
    if(mapContainer.length){
        let directionsService = new google.maps.DirectionsService();
        let directionsRenderer = new google.maps.DirectionsRenderer({suppressMarkers: true});
        let origin = mapContainer.attr('data-origin').split(',');
        let beginTransport = parseInt(mapContainer.attr('data-status')) === 5;
        if(origin.length === 2){
            origin[0] = parseFloat(origin[0])||0;
            origin[1] = parseFloat(origin[1])||0;
        }
        let destination = mapContainer.attr('data-destination').split(',');
        if(destination.length === 2){
            destination[0] = parseFloat(destination[0])||0;
            destination[1] = parseFloat(destination[1])||0;
        }
        let current = mapContainer.attr('data-current').split(',');
        if(current.length === 2){
            current[0] = parseFloat(current[0])||0;
            current[1] = parseFloat(current[1])||0;
        }else{
            current = false;
        }
        origin = new google.maps.LatLng(origin[0], origin[1]);
        destination = new google.maps.LatLng(destination[0], destination[1]);
        let request = {
            travelMode: 'DRIVING'
        };
        if(current){
            current = new google.maps.LatLng(current[0], current[1]);
            if(beginTransport){
                let tmp = origin;
                origin = current;
                current = tmp;
            }
            request.waypoints = [{
                location: current,
                stopover: false
            }];
        }
        request.origin = origin;
        request.destination = destination;
        let mapOptions = {
            zoom:7,
            center: beginTransport && current ? current : origin
        }
        let map = new google.maps.Map(mapContainer.get(0), mapOptions);
        directionsRenderer.setMap(map);
        directionsService.route(request, function(result, status) {
            if (status === 'OK') {
                directionsRenderer.setDirections(result);
                let legs = result.routes[0].legs[0];
                let beginMarker = new google.maps.Marker({
                    draggable: false,
                    label: 'A'
                });
                let endMarker = new google.maps.Marker({
                    draggable: false,
                    label: 'B'
                });
                let transportMarker = new google.maps.Marker({
                    draggable: false,
                    icon: {
                        path: "M432.958,222.262c-1.452-0.305-2.823-0.592-4.042-0.909c-13.821-3.594-20.129-5.564-24.793-14.569l-17.667-35.768  c-5.678-10.961-20.339-19.879-32.682-19.879h-31.453v-41.303c0-7.416-6.034-13.45-13.452-13.45l-219.07,0.22  c-7.218,0-12.661,5.736-12.661,13.343v12.208H21.018C9.429,122.156,0,131.584,0,143.174s9.429,21.018,21.018,21.018h56.119v20.145  H40.394c-11.589,0-21.018,9.429-21.018,21.018s9.429,21.018,21.018,21.018h36.743v20.145H59.77  c-11.589,0-21.018,9.429-21.018,21.018s9.429,21.018,21.018,21.018h17.367v21.07c0,7.416,6.034,13.45,13.45,13.45h22.788  c3.549,24.323,24.542,43.064,49.837,43.064c25.297,0,46.291-18.741,49.841-43.064h92.224c0.479,0,0.97-0.032,1.46-0.064  c3.522,24.354,24.528,43.128,49.845,43.128c25.297,0,46.291-18.741,49.841-43.064h32.732c12.885,0,23.368-10.482,23.368-23.366  v-39.648C462.522,228.465,444.73,224.732,432.958,222.262z M356.582,297.46c10.1,0,18.317,8.214,18.317,18.311  s-8.217,18.311-18.317,18.311c-10.096,0-18.31-8.214-18.31-18.311S346.486,297.46,356.582,297.46z M322.321,219.414v-48.77h24.036  c9.238,0,20.634,6.932,24.864,15.094l15.721,31.829c0.333,0.644,0.679,1.258,1.038,1.846H322.321z M181.529,315.77  c0,10.096-8.217,18.311-18.317,18.311c-10.096,0-18.309-8.214-18.309-18.311s8.213-18.311,18.309-18.311  C173.312,297.46,181.529,305.674,181.529,315.77z",
                        fillColor: '#ea4335',
                        fillOpacity: 1,
                        strokeWeight: 0,
                        anchor: new google.maps.Point(231, 185),
                        scale: 0.06
                    },
                    zIndex: 10000
                });
                let a = legs.start_location;
                let b = legs.end_location;
                let c = legs.via_waypoints.length ? new google.maps.LatLng(legs.via_waypoints[0].lat(), legs.via_waypoints[0].lng()) : false;
                a = new google.maps.LatLng(a.lat(), a.lng());
                b = new google.maps.LatLng(b.lat(), b.lng());
                beginMarker.setMap(map);
                endMarker.setMap(map);
                if(!c){
                    beginMarker.setPosition(a);
                    endMarker.setPosition(b);
                }else{
                    c = new google.maps.LatLng(c.lat(), c.lng());
                    transportMarker.setMap(map);
                    let heading;
                    if(beginTransport){
                        beginMarker.setPosition(c);
                        endMarker.setPosition(b);
                        transportMarker.setPosition(a);
                        heading = google.maps.geometry.spherical.computeHeading(a, c)
                    }else{
                        beginMarker.setPosition(a);
                        endMarker.setPosition(b);
                        transportMarker.setPosition(c);
                        heading = google.maps.geometry.spherical.computeHeading(c, b);
                    }
                    if(heading < 0){
                        transportMarker.getIcon().path = 'M -432.9580078125 222.26199340820312 C -431.5060119628906 221.95700073242188 -430.135009765625 221.6699981689453 -428.9159851074219 221.35299682617188 C -415.0950012207031 217.75900268554688 -408.7869873046875 215.78900146484375 -404.12298583984375 206.78399658203125 L -386.45599365234375 171.01600646972656 C -380.77801513671875 160.05499267578125 -366.11700439453125 151.13699340820312 -353.77398681640625 151.13699340820312 L -322.3210144042969 151.13699340820312 L -322.3210144042969 109.83399963378906 C -322.3210144042969 102.41799926757812 -316.2869873046875 96.38400268554688 -308.8689880371094 96.38400268554688 L -89.79901123046875 96.60399627685547 C -82.58100891113281 96.60399627685547 -77.13800811767578 102.33999633789062 -77.13800811767578 109.9469985961914 L -77.13800811767578 122.15499877929688 L -21.01800537109375 122.15499877929688 C -9.429004669189453 122.15599822998047 -0.0000048667038754501846 131.58399963378906 -0.0000048667038754501846 143.1739959716797 C -0.0000048667038754501846 154.76400756835938 -9.429004669189453 164.19200134277344 -21.01800537109375 164.19200134277344 L -77.13700866699219 164.19200134277344 L -77.13700866699219 184.33700561523438 L -40.394004821777344 184.33700561523438 C -28.80500602722168 184.33700561523438 -19.376005172729492 193.76600646972656 -19.376005172729492 205.35499572753906 C -19.376005172729492 216.94400024414062 -28.80500602722168 226.3730010986328 -40.394004821777344 226.3730010986328 L -77.13700866699219 226.3730010986328 L -77.13700866699219 246.51800537109375 L -59.77000427246094 246.51800537109375 C -48.18100357055664 246.51800537109375 -38.75200271606445 255.94700622558594 -38.75200271606445 267.5360107421875 C -38.75200271606445 279.125 -48.18100357055664 288.5539855957031 -59.77000427246094 288.5539855957031 L -77.13700866699219 288.5539855957031 L -77.13700866699219 309.6239929199219 C -77.13700866699219 317.0400085449219 -83.17100524902344 323.0740051269531 -90.58700561523438 323.0740051269531 L -113.37500762939453 323.0740051269531 C -116.92401123046875 347.3970031738281 -137.91700744628906 366.13800048828125 -163.21200561523438 366.13800048828125 C -188.50900268554688 366.13800048828125 -209.5030059814453 347.3970031738281 -213.05299377441406 323.0740051269531 L -305.2770080566406 323.0740051269531 C -305.7560119628906 323.0740051269531 -306.24700927734375 323.0419921875 -306.73699951171875 323.010009765625 C -310.2590026855469 347.364013671875 -331.2650146484375 366.13800048828125 -356.5820007324219 366.13800048828125 C -381.8789978027344 366.13800048828125 -402.87298583984375 347.3970031738281 -406.4230041503906 323.0740051269531 L -439.1549987792969 323.0740051269531 C -452.0400085449219 323.0740051269531 -462.52301025390625 312.5920104980469 -462.52301025390625 299.7080078125 L -462.52301025390625 260.05999755859375 C -462.5220031738281 228.46499633789062 -444.7300109863281 224.73199462890625 -432.9580078125 222.26199340820312 Z M -356.5820007324219 297.4599914550781 C -366.6820068359375 297.4599914550781 -374.89898681640625 305.67401123046875 -374.89898681640625 315.77099609375 C -374.89898681640625 325.8680114746094 -366.6820068359375 334.0820007324219 -356.5820007324219 334.0820007324219 C -346.4859924316406 334.0820007324219 -338.2720031738281 325.8680114746094 -338.2720031738281 315.77099609375 C -338.2720031738281 305.67401123046875 -346.4859924316406 297.4599914550781 -356.5820007324219 297.4599914550781 Z M -322.3210144042969 219.41400146484375 L -322.3210144042969 170.6439971923828 L -346.35699462890625 170.6439971923828 C -355.5950012207031 170.6439971923828 -366.9909973144531 177.5760040283203 -371.22100830078125 185.73800659179688 L -386.9419860839844 217.56700134277344 C -387.2749938964844 218.21099853515625 -387.6210021972656 218.8249969482422 -387.9800109863281 219.41299438476562 L -322.3210144042969 219.41299438476562 L -322.3210144042969 219.41400146484375 Z M -181.5290069580078 315.7699890136719 C -181.5290069580078 325.8659973144531 -173.31199645996094 334.08099365234375 -163.21200561523438 334.08099365234375 C -153.11599731445312 334.08099365234375 -144.9029998779297 325.86700439453125 -144.9029998779297 315.7699890136719 C -144.9029998779297 305.6730041503906 -153.11599731445312 297.4590148925781 -163.21200561523438 297.4590148925781 C -173.31199645996094 297.4599914550781 -181.5290069580078 305.67401123046875 -181.5290069580078 315.7699890136719 Z';
                        transportMarker.getIcon().anchor = new google.maps.Point(-231, 185);
                    }
                }
            }
        });
    }
}

const addresSearchFromText = document.querySelector('.addres-search._from_text');
const addresSearchFrom = document.querySelector('.addres-search._from');

const addresSearchToText = document.querySelector('.addres-search._to_text');
const addresSearchTo = document.querySelector('.addres-search._to');

function googleSearch(input, inputChecked) {
    if(!input){
        return false;
    }
    const autocomplete = new google.maps.places.Autocomplete(input);
    google.maps.event.addListener(autocomplete, 'place_changed', function () {
        const place = autocomplete.getPlace();
        inputChecked.value = place.name;

    });
}

function initAutocomplete() {
    window.addEventListener('load', function () {
        $('.input-search').each(function (i, e) {
            const el = $(e);
            const autocomplete = new google.maps.places.Autocomplete(e);
            google.maps.event.addListener(autocomplete, 'place_changed', function () {
                const place = autocomplete.getPlace();
                if (el.next().hasClass('input-search-checked')) {
                    el.next().val(place.name);
                } else {
                    el.val(place.name);
                }
            });
        });
        googleSearch(addresSearchFromText, addresSearchFrom);
        googleSearch(addresSearchToText, addresSearchTo);
        initApplicationMap();
        initTrucksMap();
        initContactMap();
    });
}

const userAvatar = document.querySelector('#user_avatar');

if (userAvatar) {
    const userInfoImage = document.querySelector('.user-info__image img')

    userAvatar.onchange = e => {
        const formData = new FormData();

        formData.append('avatar', e.target.files[0]);
        fetch(`${BACKEND_URL_API}/user_avatar`, {
            method: 'POST',
            body: formData
        })
        .then(res => {
            if (res.status >= 200 && res.status < 300) {
                return res.json();
            } 
            
            throw res;
        })
        .then(res => {
            userInfoImage.src = res.avatar
        })
        .catch(data => data.json())
    }
}
$('input._error').focus(function(){
    $(this).removeClass('_error');
});
$('.questions__item_question').click(function(){
    let self = $(this);
    if(self.hasClass('show')){
        self.removeClass('show');
        self.next().removeClass('show');
    }else{
        self.addClass('show');
        self.next().addClass('show');
    }
});
function hideMenu()
{
    $('.menu-block').stop(true, true).animate({opacity: 0, width: '0px'}, function(){
        $('.menu-block').css({display: 'none'});
    });
}
$('.menu-list .menu-item').click(function(){
    hideMenu();
})
$(document).click(function(e){
    let menuBtn = $('.header__menu');
    let menuContent = $('.menu-content');
    if(!menuBtn.is(e.target) && menuBtn.has(e.target).length === 0 && !menuContent.is(e.target) && menuContent.has(e.target).length === 0){
        hideMenu();
    }
});
$('.header__menu').click(function(){
   $('.menu-block').stop(true, true).css({display: 'block'}).animate({opacity: 1, width: '100%'});
});
$('#profile_verify').on('change', function(e){
    if(this.files.length) {
        $(this).prev().text(this.files[0].name);
    }
});