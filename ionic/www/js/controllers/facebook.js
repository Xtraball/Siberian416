/**
 * Module Facebook
 */
angular.module('starter').controller('FacebookListController', function ($filter, $location, $q, $scope, $state,
                                                                         $stateParams, $timeout, $window, Facebook,
                                                                         Modal, Dialog) {
    angular.extend($scope, {
        is_loading: true,
        use_pull_refresh: true,
        value_id: $stateParams.value_id,
        card_design: false,
        modal: null,
        template_header: 'templates/facebook/l1/header.html'
    });

    Facebook.setValueId($stateParams.value_id);

    $scope.loadContent = function (refresh) {
        Facebook.find(refresh)
            .then(function (data) {
                $scope.page = data.facebook.page;
                Facebook.collection = data.facebook.posts.data;
                $scope.collection = Facebook.collection;
            })
            .finally(function () {
                $scope.is_loading = false;

                $timeout(function () {
                    Dialog.alert('Error', '(#10) To use \'Page Public Content Access\', your use of this endpoint must be reviewed and approved by Facebook. To submit this \'Page Public Content Access\' feature for review please read our documentation on reviewable features: https:\\/\\/developers.facebook.com\\/docs\\/apps\\/review.');
                }, 200);
            });
    };

    $scope.pullToRefresh = function () {
        $scope.loadContent(true);
    };

    $scope.showPost = function (post) {

        $scope.currentPost = post;

        Modal
            .fromTemplateUrl('templates/facebook/l1/view.html', {
                scope: $scope
            })
            .then(function (modal) {
                $scope.modal = modal;
                $scope.modal.show();
            });
    };

    $scope.closePost = function () {
        $scope.modal.hide();
    };

    $scope.loadContent(false);
});
