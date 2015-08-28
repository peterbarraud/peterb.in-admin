'use strict';

/**
 * @ngdoc function
 * @name peterbdotin.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the peterbdotin
 */
angular.module('peterbdotin').controller('categoryCtrl', function ($rootScope, $scope, serverFactory,util,$location) {
	$scope.selectedItemId = 0;
  $scope.showdirtyalert = false;
  $scope.ItemIsDirty = false;
  
  
  $scope.ServerResponse = {Message:'Server messages will display here.',Type:''};

  $scope.setSelectedItemId = function (selectedItemId) {
    var goAhead = false;
    if ($scope.ItemIsDirty) {
      //for now we'll just use a message. but later will invoke a modal dialog
      $scope.showdirtyalert = true;
    }
    else {
      goAhead = true;
    }
    if (goAhead) {
      $scope.selectedItemId = selectedItemId;
    }
  }
  
  //get the category list
  serverFactory.getcategorylist($scope);

});
