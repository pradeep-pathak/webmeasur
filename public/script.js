(function() {
    var startTime = new Date().getTime();
    var currentScript = document.currentScript;
    var isBot = /(bot|crawler|spider|googlebot|bingbot|yandexbot|duckduckbot|slurp|baiduspider)/i.test(navigator.userAgent);
    if (!isBot) {
        var scrollDepth = 0;
        window.addEventListener('scroll', () => {
            scrollDepth = Math.floor((window.scrollY / (document.body.scrollHeight - window.innerHeight)) * 100);
        });

        var sendData = function() {
            var endTime = new Date().getTime();
            var duration = endTime - startTime;

            window.navigator.sendBeacon('https://app.webmeasur.com/api/collect-pageview', JSON.stringify({
                trackingCode: currentScript.getAttribute('data-tracking-code'),
                path: window.location.pathname,
                title: document.title,
                referrer: document.referrer,
                duration: duration,
                scrollDepth: scrollDepth
            }));
        };

        window.addEventListener('beforeunload', sendData);
    }
})();
