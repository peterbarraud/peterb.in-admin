'use strict';

/**
 * @ngdoc directive
 * @name peterbdotin.directive:eventPod
 * @description
 * # eventPod
 */
angular.module('peterbdotin')
  .directive('categoryItem', function (util,serverFactory,$location) {
    return {
      templateUrl: 'views/category-item.html',
      restrict: 'E',
      replace:true,
      link: function postLink(scope,element, attrs) {
          scope.new = function () {
            scope.ServerResponse.Type = null;
          }
          //to hide the message, set the type to null
          scope.getresponsemessagetype = function() {
            if (scope.ServerResponse.Type) {
              return "alert alert-dismissible alert-" + scope.ServerResponse.Type;
            }
            else {
              return "hide";
            }
              }

          scope.publish = function() {
			  serverFactory.publishcategory(scope);

          }
          scope.save = function() {
          }
        //callback function to handle the item details object after the factory get 
        scope.manageitem = function(itemdetails) {
			scope.itemDetails = itemdetails;
		}

        scope.$watch('selectedItemId',function (new_selectedItemId,old_selectedItemId){
            //dont get here on load. is there a better way of stopping a watch on load?
            if (new_selectedItemId !== 0) {
              serverFactory.getitem(new_selectedItemId,'blogcategory',scope);
            }
          });
        }
    };
  });
