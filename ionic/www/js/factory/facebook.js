/**
 * Facebook
 *
 * @author Xtraball SAS
 */
angular.module('starter').factory('Facebook', function ($pwaRequest) {
    var factory = {
        valueId: null
    };

    /**
     *
     * @param valueId
     */
    factory.setValueId = function (valueId) {
        factory.valueId = valueId;
    };

    /**
     * Fetch page informations & first posts page
     */
    factory.find = function (refresh) {
        if (!this.valueId) {
            return $pwaRequest.reject('[Factory::Facebook.find] missing valueId');
        }

        return $pwaRequest.get('facebook/mobile_page/find', {
            urlParams: {
                value_id: this.valueId
            },
            refresh: refresh
        });
    };

    /**
     * Fetch data for Facebook Page
     */
    factory.posts = function (after, refresh) {
        if (!this.valueId) {
            return $pwaRequest.reject('[Factory::Facebook.posts] missing valueId');
        }

        return $pwaRequest.get('facebook/mobile_page/posts', {
            urlParams: {
                value_id: this.valueId,
                after: after
            },
            refresh: refresh
        });
    };

    return factory;
});
