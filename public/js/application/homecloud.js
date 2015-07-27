/**
 * Created by Roman on 27.07.2015.
 */

var homecloud = angular.module("HomeCloud", ["ngRoute"]);

homecloud.run(["AccountService", "$rootScope", function (AccountService, $rootScope) {
    $rootScope.account = { authorized: false };
    AccountService.init().success(function (data) {
        $rootScope.account = { authorized: true, user: data };
    }).error(function () {
        window.location.href = "/";
    });
}]);

