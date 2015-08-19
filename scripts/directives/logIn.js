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
          scope.$parent.validUser = serverFactory.checkusercredentials(scope.email,scope.password);
          if (scope.$parent.validUser) {
            //console.log(attrs.redirectTo); need to get the redirect from the login attributes. not sure why it's not happening now??
            $location.path( "/blogitem" );
          }
          else {
            scope.invalid_user_cred = 'Invalid username or password';
          }
        }
        scope.displayErrorMsg = function(){
          return util.isEmptyString(scope.invalid_user_cred) === false;
        }
      }
    };
  });