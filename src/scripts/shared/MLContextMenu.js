const MLContextMenu = angular.module('MLContextMenu', [])

MLContextMenu.directive('mlContextMenu', [
  '$compile',
  function ($compile) {
    return {
      restrict: 'A',
      link: function (scope, elem, attrs) {
        elem.bind('contextmenu', function (event) {
          var $menu,
            data = scope.$eval(attrs.mlContextMenu)

          event.preventDefault()
          event.stopPropagation()

          if (data === null) {
            return
          }

          $menu = showContextMenu(createMenu(data, false))
          $menu.css({
            top: event.pageY,
            left: event.pageX - 13,
          })

          $compile($menu)(scope)
        })
      },
    }
  },
])

initMenu()

function initMenu() {
  var $ = angular.element
  $(document).bind('click', () => {
    if ($('body > .menu-context').length > 0) {
      removeContextMenu()
    }
  })
}

function showContextMenu($menu) {
  removeContextMenu()

  $('body').prepend($menu)

  return $menu
}

function removeContextMenu() {
  var $ = angular.element,
    menu = $('body > .menu-context')

  if (menu.length > 0) {
    menu.remove()
  }
}

function createMenu(data, isSubMenu) {
  var item,
    sub,
    $ = angular.element,
    $menu = $('<ul>')

  $menu.addClass('menu-context')

  if (isSubMenu === true) {
    $menu.addClass('menu-context-sub')
  }

  for (var i = 0; i < data.length; i += 1) {
    item = data[i]
    switch (item.type) {
      case 'divider':
        $menu.append($('<li>').addClass('menu-divider'))
        break
      case 'header':
        $menu.append($('<li>').addClass('menu-header').html(item.text))
        break
      case 'sub':
        $menu.append(
          $('<li>')
            .addClass('menu-sub-menu')
            .append('<i class="fa fa-caret-right"></i>')
            .append(item.text)
            .append(createMenu(item.data, true)),
        )
        break
      case 'item':
        sub = $('<li>').addClass('menu-item')
        if (typeof item.href == 'string') {
          sub.append($('<a>').attr('ng-href', item.href).html(item.text))
        } else if (typeof item.action == 'function') {
          sub.bind('click', item.action).html(item.text)
        } else {
          sub.html(item.text)
        }
        $menu.append(sub)
        break
      default:
        throw 'Incorrect menu item'
    }
  }

  return $menu
}
