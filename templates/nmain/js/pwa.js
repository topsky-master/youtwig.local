;(function(){
    if (!navigator.serviceWorker) {
        return;
    }

    class ProgressiveWebApp {
        constructor() {
            this.registration = null;
            this._registerServiceWorker();
        }

        _registerServiceWorker() {
            const _this = this;
            navigator.serviceWorker.getRegistration('/').then(function(registration) {
                if (registration) {
                    _this._registerServiceWorkerSuccess(registration);
                } else {
                    navigator.serviceWorker.register('/sw.js')
                        .then(_this._registerServiceWorkerSuccess.bind(_this))
                        .catch(_this._registerServiceWorkerFailed.bind(_this));
                }
            });
        }

        _registerServiceWorkerSuccess(registration) {
            this.registration = registration;
            this.registration.update();
        }

        _registerServiceWorkerFailed(err) {
            console.error('ServiceWorker registration failed: ', err);
        }
    }

    window.addEventListener('load', function() {
        new ProgressiveWebApp();
    });

    let deferredPrompt;
    window.addEventListener('beforeinstallprompt', (e) => {
        deferredPrompt = e;
    });
}());
