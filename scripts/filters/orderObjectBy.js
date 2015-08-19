'use strict';

/**
 * @ngdoc filter
 * @name peterbdotin.filter:orderObjectBy
 * @function
 * @description
 * # percentage
 * Filter in the peterbdotin.
 */
angular.module('peterbdotin')
  .filter('orderObjectBy', function () {
	return function(items, field, reverse) {
		var filtered = [];
		angular.forEach(items, function(item) {
		  filtered.push(item);
		});
		filtered.sort(function (a, b) {
		  return (a[field] > b[field] ? 1 : -1);
		});
		if(reverse) filtered.reverse();
		return filtered;
	  };
  });