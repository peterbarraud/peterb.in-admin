'use strict';

/**
 * @ngdoc directive
 * @name peterbdotin.directive:eventPod
 * @description
 * # eventPod
 */
angular.module('peterbdotin')
  .directive('categoryList', function (util,serverFactory,$location) {
    return {
      templateUrl: 'views/category-list.html',
      restrict: 'E',
      replace:true,
      link: function postLink(scope,element, attrs) {
          scope.setActiveItem = function(currentItemID) {
            //scope.ServerResponse.Type = null;
            if (currentItemID === scope.selectedItemId)
              return "active";
          }
          scope.refreshList = function () {
            //serverFactory.getallblogs(scope);
          }
        }
    };
  });
