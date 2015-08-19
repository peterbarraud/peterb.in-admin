'use strict';

/**
 * @ngdoc filter
 * @name peterbdotin.filter:percentage
 * @function
 * @description
 * # percentage
 * Filter in the peterbdotin.
 */
angular.module('peterbdotin')
  .filter('percentage', function () {
    return function (input) {
      return Math.floor(input * 100).toString() + '%';
    };
  });