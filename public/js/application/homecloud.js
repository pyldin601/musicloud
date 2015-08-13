/**
 * Created by Roman on 27.07.2015.
 */

var homecloud = angular.module("HomeCloud", ["ngRoute", "ngCookies", "httpPostFix", "infinite-scroll"]);


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
        } else {
            document.title = "Music Library";
        }
    });

}]);

function groupGenres(coll) {
    var genres = coll.map(field("track_genre")).distinct();
    return (genres.length == 0) ? "-" :
           (genres.length == 1) ? genres[0] :
           (genres.length == 2) ? genres[0] + ", " + genres[1] :
           (genres.length == 3) ? genres[0] + ", " + genres[1] + " and " + genres[2] :
            genres[0] + ", " + genres[1] + " and " + (genres.length - 2) + " others";
}

function groupYears(coll) {
    var years = coll.map(field("track_year")).distinct().filter(isNumeric);
    return (years.length == 0) ? "" :
           (years.length == 1) ? years[0] :
           (years.length == 2) ? years.join(", ") :
           (years.min() + " - " + years.max())
}

function aggregateAlbumTitle(coll) {
    return coll.map(field("track_album")).reduce(or, "");
}

function aggregateDuration(coll) {
    return coll.map(field("length")).reduce(sum, 0);
}
