'use strict';

/**
 * @ngdoc directive
 * @name peterbdotin.directive:eventPod
 * @description
 * # eventPod
 */
angular.module('peterbdotin')
  .directive('blogList', function (util,serverFactory,$location) {
    return {
      templateUrl: 'views/blog-list.html',
      restrict: 'E',
      replace:true,
      link: function postLink(scope,element, attrs) {
          scope.setActiveBlogItem = function(currentBlogID) {
            //scope.ServerResponse.Type = null;
            if (currentBlogID === scope.selectedBlogId)
              return "active";
          }
          scope.refreshList = function () {
            serverFactory.getallblogs(scope);
          }
        }
    };
  });
