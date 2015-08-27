
application = angular.module "TestApp", ["ui.scroll"]

application.controller "TestController", ($scope) ->
  $scope.dataSource = {
    get: (index, count, callback) ->
      return [] if index < 0
      items = new Array(count).map () -> Math.random()
      callback items
  }
