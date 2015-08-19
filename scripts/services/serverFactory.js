// TODO This factory is supposed to be customized once integrated
// into loggly. Currently it serves dummy data.

'use strict';

/**
 * @ngdoc service
 * @name peterbdotin.serverFactory
 * @description
 * # serverFactory
 * Factory in the peterbdotin.
 */
angular.module('peterbdotin')
  .factory('serverFactory', function ($http,util) {
	//private method
    return {
      checkusercredentials: function(email,password) {
        //hardwired for now
        var retval = false;
        if (email === "gapeterb@gmail.com" && password === "danielb07") {
          retval = true;
        }
        return retval;
      },
      getblogdetails : function(blogId,scope) {
        $http.get('services/pbrest.php/getpost/' + blogId).
          success(function(data, status, headers, config) {
            scope.blogDetails = data;
            scope.blogDetails_Backup = angular.copy(data);  //use this deep copy in a speccial case when a user cancels out all changes
            //now a HACK to manage ckeditor
            //default the blog to a p tag if ID = 0 (new blog)
            if (scope.blogDetails.ID === 0) {
              scope.blogDetails.Blog = "<p></p>";
            }
            //SELECTED CATEGORIES
            //now lets add the blog categories to the selected categories object array
            scope.selectedCategories = {ids: {}}; //clear out the current selected categories
            data.Categories.forEach (function(category) {
              scope.selectedCategories.ids[category.ID] = true;
            });
            //SELECTED TYPES
            //now lets add the blog types to the selected types object array
            scope.selectedTypes = {ids: {}}; //clear out the current selected types
            data.Types.forEach (function(blogType) {
              scope.selectedTypes.ids[blogType.ID] = true;
            });
          }).
          error(function(data, status, headers, config) {
            console.log(data);
            // called asynchronously if an error occurs
            // or server returns response with an error status.
          });        
      },
      
      getallblogs : function(scope) {
        $http.get('services/pbrest.php/getallposts').
          success(function(data, status, headers, config) {
            scope.bloglist = data;
            scope.setSelectedBlogId(scope.bloglist[0].ID); 
          }).
          error(function(data, status, headers, config) {
            console.log(data);
            // called asynchronously if an error occurs
            // or server returns response with an error status.
          });        
      },
      
      getcategorylist : function(scope) {
        $http.get('services/pbrest.php/getcategorylist').
          success(function(data, status, headers, config) {
            scope.categoryList = data;
          }).
          error(function(data, status, headers, config) {
            console.log(data);
            // called asynchronously if an error occurs
            // or server returns response with an error status.
          });        
      },

      gettypelist : function(scope) {
        $http.get('services/pbrest.php/getblogtypelist').
          success(function(data, status, headers, config) {
            scope.typeList = data;
            console.log(data);
          }).
          error(function(data, status, headers, config) {
            console.log(data);
            // called asynchronously if an error occurs
            // or server returns response with an error status.
          });        
      },
      
      publishblog : function (scope) {
        $http.get('services/testrest.php/testserver/').
          success(function(data, status, headers, config) {
            console.log('data');
          }).
          error(function(data, status, headers, config) {
            console.log(data);
            // called asynchronously if an error occurs
            // or server returns response with an error status.
          });        
      },
      
      saveblogdetails : function(scope) {
        var paramsObject = {blogObject:JSON.stringify(scope.blogDetails)};
        var httpPostParams = [];
        for (var key in paramsObject) {
          httpPostParams.push(key + '=' + encodeURIComponent(paramsObject[key]));
        }
        httpPostParams = httpPostParams.join('&');
        $http({
          method: 'POST',
          url: 'services/pbrest.php/savepost',
          data: httpPostParams,
          headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).
        success(function(data, status, headers, config) {
          //if all is good, then let's clean up
          scope.BlogIsDirty = false;
          scope.showdirtyalert = false;
          //scope.mode = 'readonly';
          scope.setSelectedBlogId(data.savedblogid); 
          //TODO:
          //and then refresh the blog list with the new / updated blog
          //this.getallblogs();
        }).
        error(function(data, status, headers, config) {
          console.log(data);
        });
        //util.httpPost(paramsObject,$http,);
      },
    };        
        
        
  });
