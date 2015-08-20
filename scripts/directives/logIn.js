'use strict';

/**
 * @ngdoc directive
 * @name peterbdotin.directive:eventPod
 * @description
 * # eventPod
 */
angular.module('peterbdotin')
  .directive('logIn', function (util,serverFactory,$location) {
    return {
      templateUrl: 'views/log-in.html',
      restrict: 'E',
      replace:true,
      link: function postLink(scope,element, attrs) {
        scope.$parent.validUser = false;
        scope.checkusercredentials = function () {
          serverFactory.checkusercredentials(scope,$location);
        }
        scope.displayErrorMsg = function(){
          return util.isEmptyString(scope.invalid_user_cred) === false;
        }
      }
    };
  });
