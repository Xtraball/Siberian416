cordova.define("com.xtraball.cordova.plugin.navigator.Navigator", function(require, exports, module) {
/**
 *
 * @type {{navigateByPosition: Navigator.navigateByPosition}}
 */
Navigator = {
    navigateByPosition: function (to) {

        try {
            if (!isNaN(to.lat) && !isNaN(to.lng)) {
                cordova.exec(
                    function () {},
                    function () {},
                    'Navigator',
                    'navigate',
                    [to.lat, to.lng]);
            } else {
                console.error("Latitude and longitude aren't numbers.");
            }
        } catch (e) {
            console.error("Error on navigate by position: " + e.message);
        }

    }

};
module.exports = Navigator;

});
