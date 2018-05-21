/* global
    angular, lazyLoadResolver, BASE_PATH
 */
angular.module('starter').config(function ($stateProvider) {
    $stateProvider
        .state('facebook-list', {
            url: BASE_PATH + '/social/mobile_facebook_list/index/value_id/:value_id',
            controller: 'FacebookListController',
            templateUrl: 'templates/facebook/l1/list.html',
            resolve: lazyLoadResolver('facebook'),
            cache: false
        });
});
