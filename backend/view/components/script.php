<? require_once __DIR__ . './../components/menu.php'; ?>
<script>
    const SESSION_ID = <?= $_SESSION['user']['user_id'] ? $_SESSION['user']['user_id'] : 'null' ?>;
    const webSocketServer = '<?=WS_PROXY_SERVER?>';
    const sessionToken = '<?=$_COOKIE['session_token']?>';
    let applicationId = window.applicationId ? window.applicationId : undefined;
    let userId = SESSION_ID;
</script>
<script src="https://www.google.com/recaptcha/api.js"></script>
<script src="/view/static/js/all.js?v=<?=version?>"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?=GOOGLE_MAP_API?>&libraries=geometry,places&language=en&callback=initAutocomplete"></script>
<script>
    window.intercomSettings = {
        api_base: "https://api-iam.intercom.io",
        app_id: "kv7wh0ac"
    };
</script>
<script>
    function gtag_report_conversion2(url) {
        var callback = function () {
            if (typeof(url) != 'undefined') {
                window.location = url;
            }
        };
        gtag('event', 'conversion', {
            'send_to': 'AW-536230408/qFD4CMP23oYYEIj02P8B',
            'event_callback': callback
        });
        return false;
    }
</script>
<script>
    // We pre-filled your app ID in the widget URL: 'https://widget.intercom.io/widget/kv7wh0ac'
    (function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic('reattach_activator');ic('update',w.intercomSettings);}else{var d=document;var i=function(){i.c(arguments);};i.q=[];i.c=function(args){i.q.push(args);};w.Intercom=i;var l=function(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://widget.intercom.io/widget/kv7wh0ac';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);};if(document.readyState==='complete'){l();}else if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})();
    Intercom('onShow', function() {
        gtag_report_conversion2();
    });
</script>
