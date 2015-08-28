'use strict';

/**
 * @ngdoc directive
 * @name peterbdotin.directive:eventPod
 * @description
 * # eventPod
 */
angular.module('peterbdotin')
  .directive('navBar', function (util,serverFactory,$location) {
    return {
      templateUrl: 'views/nav-bar.html',
      restrict: 'E',
      replace:true,
      link: function postLink(scope,element, attrs) {
		  scope.navigateto = function(whereto,endsession) {
			  scope.$parent.validUser = !endsession;
			  $location.path( whereto );
		  }
      }
    };
  });
