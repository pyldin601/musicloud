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

export default [() => (key) => {
  const groups = [];
  const getGroup = function (k) {
    if (groups.length === 0 || groups[groups.length - 1].key !== k) {
      groups.push({ key: k, items: [] })
    }
    return groups[groups.length - 1].items;
  };

  console.log("Initialized groups with key " + key);
  return {
    addItems: function (coll) {
      console.log("Adding " + coll.length + " items into groups");
      for (var i = 0, length = coll.length; i < length; i += 1) {
        getGroup(coll[i][key]).push(coll[i]);
      }
    },
    removeItems: function (itemKey, coll) {
      for (var j = groups.length - 1; j >= 0; j--) {
        for (var i = groups[j].items.length - 1; i >= 0; i--) {
          for (var k = coll.length - 1; k >= 0; k--) {
            if (groups[j].items[i][itemKey] == coll[k]) {
              console.log("Removing " + coll[k] + " from group " + groups[j].key);
              groups[j].items.splice(i, 1);
              break;
            }
          }
        }
        if (groups[j].items.length == 0) {
          groups.splice(j, 1);
        }
      }
    },
    removeGroup: function (group) {
      for (var i = groups.length - 1; i >= 0; i--) {
        if (groups[i].key === group) {
          groups.splice(i, 1);
          break;
        }
      }
    },
    getGroups: function () {
      console.log("Requested groups collection");
      return groups;
    },
    clear: function () {
      console.log("Cleaning groups");
      while (groups.length) {
        groups.shift();
      }
    }
  };
}];
