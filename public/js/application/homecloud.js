/**
 * Created by Roman on 27.07.2015.
 */

var homecloud = angular.module("HomeCloud", ["ngRoute", "httpPostFix", "infinite-scroll"]);

homecloud.run(["AccountService", "$rootScope", function (AccountService, $rootScope) {
    $rootScope.title = "My Library";
    $rootScope.account = { authorized: false };
    AccountService.init().success(function (data) {
        $rootScope.account = { authorized: true, user: data };
    }).error(function () {
        window.location.href = "/";
    });

    $rootScope.$on("$routeChangeSuccess", function (e, $route) {
        if ($route.title) {
            document.title = $route.title + " - Music Library";
        }
    });
}]);

