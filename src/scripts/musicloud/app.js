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
import services from './services';
import directives from './directives';
import controllers from './controllers';
import filters from './filters';
import components from './components';
import config from './config';

import player from '../player';

import angular from 'angular';

const app = angular.module('MusicLoud', ["ngRoute", "ngCookies", "httpPostFix",
  "infinite-scroll", "MLContextMenu", "indexedDB", player]);

app.run(["AccountService", "$rootScope", (AccountService, $rootScope) => {
  $rootScope.account = { authorized: false };

  AccountService.init().then(
    response => $rootScope.account = { authorized: true, user: response.data },
    () => window.location.href = "/",
  );

  $rootScope.$on("$routeChangeSuccess", (e, $route) => {
    if ($route.title) {
      document.title = `${$route.title  } - MusicLoud`;
    } else {
      document.title = "MusicLoud";
    }
  });

}]);

const parts = [
  services,
  directives,
  controllers,
  filters,
  components,
  config,
];

parts.forEach(apply => apply(app));

export default app;
