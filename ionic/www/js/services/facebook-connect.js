/**
 * FacebookConnect for users (login)
 */
angular.module('starter').service('FacebookConnect', function ($cordovaOauth, $rootScope, $timeout, $window,
                                                               $pwaRequest, Customer, Dialog, SB, Loader) {
    var service = this;

    service.app_id = null;
    service.version = 'v3.0';
    service.is_initialized = false;
    service.is_logged_in = false;
    service.access_token = null;
    service.permissions = null;
    service.fb_login = null;

    service.login = function () {
        if ($rootScope.isNotAvailableInOverview()) {
            return;
        }

        var scope = (service.permissions) ? service.permissions.join(',') : '',
            redirectUri = encodeURIComponent(DOMAIN + '/' + APP_KEY + '?login_fb=true');

        var fbLocation = 'https://graph.facebook.com/oauth/authorize?client_id=' +
            service.app_id+'&scope=' + scope + '&response_type=token&redirect_uri=';

        if (DEVICE_TYPE === SB.DEVICE.TYPE_BROWSER) {
            $window.location = fbLocation + redirectUri;
        } else {
            Loader.show();

            // Test callback!
            var options = {
                redirect_uri: 'https://localhost/callback'
            };

            $cordovaOauth.facebook(service.app_id, service.permissions, options)
                .then(function (result) {
                    Customer.loginWithFacebook(result.access_token)
                        .then(function () {
                            Customer.login_modal.hide();
                        }).finally(function () {
                            Loader.hide();
                        });
                }, function (error) {
                    Dialog.alert('Login error', error, 'OK', -1)
                        .then(function () {
                            Customer.login_modal.hide();
                            Loader.hide();
                        });
                });
        }
    };

    service.logout = function () {
        service.is_logged_in = false;
        service.access_token = null;
    };

    $rootScope.$on(SB.EVENTS.AUTH.logoutSuccess, function () {
        $timeout(function () {
            service.logout();
        });
    });

    return service;
});
