
(function () {

    var module = angular.module("MLContextMenu", []);

    module.directive("mlContextMenu", ["$parse", function ($parse) {
        return {
            restrict: "A",
            link: function (scope, elem, attrs) {
                var source = $parse(attrs.mlContextMenu)(scope);
                elem.bind("contextmenu", function (event) {
                    var $menu;
                    event.preventDefault();
                    event.stopPropagation();
                    $menu = showContextMenu(createMenu(source, false));
                    $menu.css({
                        top: event.pageY,
                        left: event.pageX - 13
                    });
                    return false;
                });
            }
        }
    }]);

    initMenu();

    function initMenu() {
        var $ = angular.element;
        $(document).bind("click", function (event) {
            if ($(".menu-context").length == 0) {
                removeContextMenu();
            }
        });
    }

    function showContextMenu($menu) {

        removeContextMenu();

        $("body").prepend($menu);

        return $menu;

    }

    function removeContextMenu() {

        var $ = angular.element,
            menu = $("body > .menu-context");

        if (menu.length > 0) {
            menu.remove();
        }

    }

    function createMenu(data, isSubMenu) {

        var item,
            sub,

            $ = angular.element,
            $menu = $("<ul>");

        $menu.addClass("menu-context");

        if (isSubMenu === true) {
            $menu.addClass("menu-context-sub")
        }

        for (var i = 0; i < data.length; i += 1) {
            item = data[i];
            switch (item.type) {
                case "divider":
                    $menu.append($("<li>").addClass("menu-divider"));
                    break;
                case "header":
                    $menu.append($("<li>").addClass("menu-header").text(item.text));
                    break;
                case "sub":
                    $menu.append($("<li>").addClass("menu-sub-menu").html(createMenu(item.data, true)));
                    break;
                case "item":
                    sub = $("<li>").addClass("menu-item").text(item.text);
                    if (typeof item.href == "string") {
                        sub.attr("href", item.href);
                    } else if (typeof item.action == "function") {
                        sub.bind("click", item.action);
                    }
                    $menu.append(sub);
                    break;
                default:
                    throw "Incorrect menu item"
            }

        }

        return $menu;

    }

})();