/**
 * Created by Roman on 27.07.2015.
 */

var homecloud = angular.module("HomeCloud", []);

homecloud.run(["AccountService", function (AccountService) {
    AccountService.init().then(function (data) {
        console.log(data);
    });
}]);