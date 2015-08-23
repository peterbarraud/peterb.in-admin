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
          
          
          if (scope.$parent.validUser === false)  { //route back to the login page
            $location.path( "/" );
          }
          

          scope.cancel = function () {
            //TODO - this should revert any changes. Right now it doesn't
            scope.ServerResponse.Type = null;
            scope.BlogIsDirty = false;
            scope.showdirtyalert = false;
            scope.blogDetails = angular.copy(scope.blogDetails_Backup);
            //serverFactory.getblogdetails(scope.selectedBlogId,scope);
          }
          scope.new = function () {
            scope.ServerResponse.Type = null;
            scope.setSelectedBlogId(-1);
            scope.selectedCategories = {ids: {}};
            scope.selectedTypes = {ids: {}};
            scope.BlogIsDirty = false;
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
            scope.BlogIsDirty = true;
          }
          scope.canPublish = function () {
			  var ret_val = true;
			  if (angular.isDefined(scope.blogDetails)) {
				  if (scope.blogDetails.subtitle == null && scope.blogDetails.blog == null) {
					  ret_val = false;
				  }
			  }
			  else {
				  ret_val = false;
			  }
			  
			  return ret_val;
		  }
          scope.save = function() {
            if (!util.isEmptyString(scope.blogDetails.title)) { //you'll need at least a title to save a blog
              //CATEGORIES
              //clear the current blog categories list
              scope.blogDetails.blogcategory = [];
              //iterate the list of selected categories and add them to the blogDetails object category array
              angular.forEach (scope.selectedCategories.ids, function(isaddcategory,selectedCategoryID){
                if (isaddcategory) {
                  scope.categoryList.forEach (function (category) {
                    if (category.id === selectedCategoryID) {
                      scope.blogDetails.blogcategory.push(category);
                    }
                  });
                }
              });
              //TYPES
              //clear the current blog type list
              scope.blogDetails.blogtype = [];
              //iterate the list of selected categories and add them to the blogDetails object blogtype array
              angular.forEach (scope.selectedTypes.ids, function(isaddtype,selectedTypeID){
                if (isaddtype) {
                  scope.typeList.forEach (function (blogType) {
                    if (blogType.id === selectedTypeID) {
                      scope.blogDetails.blogtype.push(blogType);
                    }
                  });
                }
              });
              serverFactory.saveblogdetails(scope);
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
          scope.$watch('blogDetails',function (new_blogDetails,old_blogDetails){
              //we are checking for a dirty blog
              //so only test the watcher as long as the id has not changed
            if (scope.ckeditorIsReady) {
              if (!angular.isUndefined(old_blogDetails) && !angular.isUndefined(new_blogDetails)) {
                if (!angular.isUndefined(old_blogDetails.id) && !angular.isUndefined(new_blogDetails.id)) {
                  if (old_blogDetails.id === new_blogDetails.id) {  //means we havent changed the blog being view but something else has changed
                    if (scope.frm.editor.$dirty) {
                      scope.BlogIsDirty = true;
                    }
                    else if (old_blogDetails.title !== new_blogDetails.title) {
                      scope.BlogIsDirty = true;
                    }
                    else if (old_blogDetails.subtitle !== new_blogDetails.subtitle) {
                      scope.BlogIsDirty = true;
                    }
                  }
                }
              }
            }
          },true);  //true arg is used to watch objects
          

        scope.$watch('selectedBlogId',function (new_selectedBlogId,old_selectedBlogId){
            //dont get here on load. is there a better way of stopping a watch on load?
            if (new_selectedBlogId !== 0) {
              serverFactory.getblogdetails(new_selectedBlogId,scope);
            }
          });
        }
    };
  });
