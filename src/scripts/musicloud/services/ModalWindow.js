/*
 * Copyright (c) 2017 Roman Lakhtadyr
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
import angular from 'angular'

export default [
  '$templateRequest',
  '$controller',
  '$rootScope',
  '$compile',
  ($templateRequest, $controller, $rootScope, $compile) => {
    const defaults = {
      controller: null,
      closeOnEscape: true,
      closeOnClick: true,
      data: {},
      scope: null,
    }

    return (opts) => {
      const options = angular.copy(defaults)

      angular.extend(options, opts)

      $templateRequest(options.template).then((template) => {
        const newScope = angular.isObject(options.scope) ? options.scope.$new() : $rootScope.$new()
        const body = angular.element('body')
        const $modal = angular.element(template)

        const onEscapeEvent = (event) => {
          if (event.which === 27) {
            newScope.closeThisWindow()
          }
        }

        const onMouseClickEvent = (event) => {
          if (angular.element(event.target).parents($modal).length === 0) {
            newScope.closeThisWindow()
          }
        }

        const compile = () => {
          $compile($modal.contents())(newScope)
        }

        newScope.closeThisWindow = () => {
          $modal.remove()
          newScope.$destroy()
        }

        newScope.$on('$destroy', () => {
          body.off('keyup', onEscapeEvent)
          body.off('click', onMouseClickEvent)
        })

        for (const k in options.data)
          if (options.data.hasOwnProperty(k)) {
            newScope[k] = options.data[k]
          }

        if (options.closeOnEscape) {
          body.bind('keyup', onEscapeEvent)
        }

        if (options.closeOnClick) {
          body.bind('click', onMouseClickEvent)
        }

        $modal.appendTo(body)

        if (options.controller) {
          const controllerInstance = $controller(options.controller, {
            $scope: newScope,
            $element: $modal,
          })
          $modal.data('$modalWindowController', controllerInstance)
        }

        if (newScope.$$phase) {
          newScope.$applyAsync(compile)
        } else {
          newScope.$apply(compile)
        }
      })
    }
  },
]
