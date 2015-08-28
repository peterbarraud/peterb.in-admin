'use strict';

/**
 * @ngdoc function
 * @name peterbdotin.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the peterbdotin
 */
angular.module('peterbdotin').controller('blogCtrl', function ($rootScope, $scope, serverFactory,util,$location) {
	$scope.selectedItemId = 0;
  $scope.showdirtyalert = false;
  $scope.ckeditorIsReady = true;
  $scope.ItemIsDirty = false;
  $scope.itemlist = null;
  $scope.itemDetailsLastKnwonGood = null;
  
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
  
  $scope.manageblogitems = function (data) {
	  $scope.itemlist = data.Items;
  }
  $scope.managecategoryitems = function (data) {
	  $scope.categoryList = data.Items;
  }
  $scope.manageblogtypeitems = function (data) {
	  $scope.typeList = data.Items;
  }
  
  
  //get all categories
  serverFactory.getitems('blogcategory',$scope,'managecategoryitems');
  //get all blog types
  serverFactory.getitems('blogtype',$scope,'manageblogtypeitems');
  //get all blogs
  serverFactory.getblogitems($scope,'manageblogitems');  

});
