'use strict';

/**
 * @ngdoc service
 * @name peterbdotin.util
 * @description
 * # util
 * Factory in the peterbdotin.
 */
angular.module('peterbdotin')
  .factory('util', function () {
    return {
      count: function (arrOrObj) {
        return Array.isArray(arrOrObj) ? arrOrObj.length : Object.keys(arrOrObj).length;
      },
      isEmptyString: function(str) {
        return angular.isUndefined(str) || str ==='' || str === null;
      },
      httpPost: function(paramsObject,http,url) {
        var httpPostParams = [];
        for (var key in paramsObject) {
          httpPostParams.push(key + '=' + encodeURIComponent(paramsObject[key]));
        }
        httpPostParams = httpPostParams.join('&');
        http({
          method: 'POST',
          url: url,
          data: httpPostParams,
          headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).
        success(function(data, status, headers, config) {
          console.log(data);
        }).
        error(function(data, status, headers, config) {
          console.log(data);
        });
      },
    };
  });
