(function () {

    var lobby = angular.module("Lobby", ["httpPostFix"]);

    lobby.controller("LoginController", ["$scope", "AccountService", "$location",
        function ($scope, AccountService, $location) {
            $scope.login = "";
            $scope.password = "";
            $scope.status = "";
            $scope.submit = function () {
                $scope.status = "";
                AccountService.login({email: $scope.login, password: $scope.password})
                    .success(function () {
                        window.location.href = "/library/";
                    })
                    .error(function (error) {
                        $scope.status = error.message;
                    });
            }
        }
    ]);

    lobby.factory("AccountService", ["$http", function ($http) {
        return {
            login: function (data) {
                return $http.post("/api/login", data);
            },
            logout: function () {
                return $http.post("/api/logout");
            },
            init: function () {
                return $http.get("/api/self");
            }
        };
    }]);

})();