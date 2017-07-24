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

const ACTIVE_TAB_CLASS = "active";

export default ["$location", "$route", ($location, $route) => ({
  scope: {
    activeTab: "@"
  },
  link: ($scope, $element, $attributes) => {
    $element.toggleClass(ACTIVE_TAB_CLASS, $location.url().match($scope.activeTab) !== null);

    $scope.$on("$routeChangeSuccess", function () {
      if ($route.current.special && $route.current.special.section) {
        $element.toggleClass(ACTIVE_TAB_CLASS, $route.current.special.section === $scope.activeTab);
      } else {
        $element.toggleClass(ACTIVE_TAB_CLASS, false);
      }
    });
  }
})];
