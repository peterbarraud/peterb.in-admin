'use strict';

/**
 * @ngdoc directive
 * @name peterbdotin.directive:eventPod
 * @description
 * # eventPod
 */
angular.module('peterbdotin')
  .directive('blogItem', function (util,serverFactory,$location) {
    return {
      templateUrl: 'views/blog-item.html',
      restrict: 'E',
      replace:true,
      link: function postLink(scope,element, attrs) {
          scope.selectedCategories = {ids: {}};
          scope.selectedTypes = {ids: {}};
          scope.editorOptions = {
              uiColor: '#fff',
              toolbarLocation: 'bottom',
              enterMode:CKEDITOR.ENTER_BR,
          };
          scope.$on("ckeditor.ready", function( event ) {scope.ckeditorIsReady = true;});
          
          
          if (!scope.$parent.validUser === true)  { //route back to the login page
            $location.path( "/" );
          }
          

          scope.cancel = function () {
            //scope.itemDetails = angular.copy(scope.itemDetailsLastKnwonGood);
            scope.itemDetails.title = scope.itemDetailsLastKnwonGood.title;
            scope.itemDetails.subtitle = scope.itemDetailsLastKnwonGood.sutitle;
            scope.itemDetails.blog = scope.itemDetailsLastKnwonGood.blog;
            //TODO: handle if user changes categories and types and cancels
            
			scope.ItemIsDirty = false;
			scope.showdirtyalert = false;
			scope.frm.editor.$setPristine();
          }
          scope.new = function () {
            scope.ServerResponse.Type = null;
            scope.setSelectedItemId(-1);
            scope.selectedCategories = {ids: {}};
            scope.selectedTypes = {ids: {}};
            scope.ItemIsDirty = false;
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
			  serverFactory.publishblog(scope);

          }
          scope.checkListIem = function (listItemID) {
            scope.ItemIsDirty = true;
          }
          scope.canPublish = function () {
			  var ret_val = true;
			  if (angular.isDefined(scope.itemDetails)) {
				  if (scope.itemDetails.subtitle == null && scope.itemDetails.blog == null) {
					  ret_val = false;
				  }
			  }
			  else {
				  ret_val = false;
			  }
			  
			  return ret_val;
		  }
		  scope.manageitem = function (data) {
			scope.itemDetails = data;
			if (scope.itemDetails.id === 0) {
			  scope.itemDetails.blog = "<p></p>";
			}
			//SELECTED CATEGORIES
			//now lets add the blog categories to the selected categories object array
			scope.selectedCategories = {ids: {}}; //clear out the current selected categories
			scope.itemDetails.blogcategory.forEach (function(category) {
			  scope.selectedCategories.ids[category.id] = true;
			});
			//SELECTED TYPES
			//now lets add the blog types to the selected types object array
			scope.selectedTypes = {ids: {}}; //clear out the current selected categories
			scope.itemDetails.blogtype.forEach (function(blogType) {
			  scope.selectedTypes.ids[blogType.id] = true;
			});
			//finally put the blog details into the last known good object
			//we will use this to check for changes (not for dirty changes)
			//1. to check if the client changed the title (if yes, then refresh the item list)
			//2. to execute the cancel command. we will bring back the last known good
			scope.itemDetailsLastKnwonGood = angular.copy(scope.itemDetails);
		  }
		  
		  scope.managesaveitem = function (data) {
			scope.ItemIsDirty = false;
			scope.showdirtyalert = false;
			scope.frm.editor.$setPristine();
			//refresh the item list
			//scope.itemDetails
			//data.saveditem;
			//in the blog list, we will only show the title. so refresh this list only if the title is changed
			if (scope.itemDetails.title !== scope.itemDetailsLastKnwonGood.title) {
				serverFactory.getblogitems(scope,'manageblogitems');
			}
			scope.itemDetailsLastKnwonGood = angular.copy(scope.itemDetails);
		  }
		  
		  
		  
          scope.save = function() {
            if (!util.isEmptyString(scope.itemDetails.title)) { //you'll need at least a title to save a blog
              //CATEGORIES
              //clear the current blog categories list
              scope.itemDetails.blogcategory = [];
              //iterate the list of selected categories and add them to the itemDetails object category array
              angular.forEach (scope.selectedCategories.ids, function(isaddcategory,selectedCategoryID){
                if (isaddcategory) {
                  scope.categoryList.forEach (function (category) {
                    if (category.id === selectedCategoryID) {
                      scope.itemDetails.blogcategory.push(category);
                    }
                  });
                }
              });
              //TYPES
              //clear the current blog type list
              scope.itemDetails.blogtype = [];
              //iterate the list of selected categories and add them to the itemDetails object blogtype array
              angular.forEach (scope.selectedTypes.ids, function(isaddtype,selectedTypeID){
                if (isaddtype) {
                  scope.typeList.forEach (function (blogType) {
                    if (blogType.id === selectedTypeID) {
                      scope.itemDetails.blogtype.push(blogType);
                    }
                  });
                }
              });
              serverFactory.saveitemdetails(scope,'blog');
            }
            else {
              alert("The blog will at least need a title for me to save it.")
            }
          }

          //we're checking for dirty blog details
          //this watch will set the dirty to true if a user makes some changes to the blog details
          //this is useful in two obvious cases:
          //1. to enable the save button
          //2. to throw a warning if a user tries to navigate out of this screen
          scope.$watch('itemDetails',function (new_itemDetails,old_itemDetails){
              //we are checking for a dirty blog
              //so only test the watcher as long as the id has not changed
            if (scope.ckeditorIsReady) {
              if (!angular.isUndefined(old_itemDetails) && !angular.isUndefined(new_itemDetails)) {
                if (!angular.isUndefined(old_itemDetails.id) && !angular.isUndefined(new_itemDetails.id)) {
                  if (old_itemDetails.id === new_itemDetails.id) {  //means we havent changed the blog being viewed but something else has changed
                    if (scope.frm.editor.$dirty) {
                      scope.ItemIsDirty = true;
                    }
                    else if (old_itemDetails.title !== new_itemDetails.title) {
                      scope.ItemIsDirty = true;
                    }
                    else if (old_itemDetails.subtitle !== new_itemDetails.subtitle) {
                      scope.ItemIsDirty = true;
                    }
                  }
                }
              }
            }
          },true);  //true arg is used to watch objects
          

        scope.$watch('selectedItemId',function (new_selectedItemId,old_selectedItemId){
            //dont get here on load. is there a better way of stopping a watch on load?
            if (new_selectedItemId !== 0) {
              serverFactory.getitem(new_selectedItemId,'blog',scope);
            }
          });
        }
    };
  });
