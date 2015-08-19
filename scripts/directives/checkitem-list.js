'use strict';

/**
 * @ngdoc directive
 * @name peterbdotin.directive:checkitemList
 * @description
 * # eventPod
 */
angular.module('peterbdotin')
  .directive('checkitemList', function (util,serverFactory,$location) {
    return {
      templateUrl: 'views/checkitem-list.html',
      restrict: 'E',
      replace:true,
      link: function postLink(scope,element, attrs) {
        
      }
    };
  });