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
      checkusercredentials: function(scope,location) {
        $http.get('services/in.peterb.restapi.php/validateuser/' + scope.email + '/' + scope.password).
          success(function(data, status, headers, config) {
			  if (data.success) {
				  scope.$parent.validUser = true;
				  location.path( "/blogitem" );
			  }
			  else {
				  scope.$parent.validUser = false;
				  scope.invalid_user_cred = data.error;
			  }
          }).
          error(function(data, status, headers, config) {
			  scope.invalid_user_cred = data.error;
          });        
      },
      getblogdetails : function(blogId,scope) {
        $http.get('services/in.peterb.restapi.php/getpost/' + blogId).
          success(function(data, status, headers, config) {
			scope.blogDetails = data;
			console.log(scope.blogDetails);
			//now a HACK to manage ckeditor
			//default the blog to a p tag if id = 0 (new blog)
			if (scope.blogDetails.id === 0) {
			  scope.blogDetails.blog = "<p></p>";
			}
			//SELECTED CATEGORIES
			//now lets add the blog categories to the selected categories object array
			scope.selectedCategories = {ids: {}}; //clear out the current selected categories
			scope.blogDetails.blogcategory.forEach (function(category) {
			  scope.selectedCategories.ids[category.id] = true;
			});
			//SELECTED TYPES
			//now lets add the blog types to the selected types object array
			scope.selectedTypes = {ids: {}}; //clear out the current selected categories
			scope.blogDetails.blogtype.forEach (function(blogType) {
			  scope.selectedTypes.ids[blogType.id] = true;
			});
          }).
          error(function(data, status, headers, config) {
            console.log(data);
          });        
      },
      
      getallblogs : function(scope) {
		  /*
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
          * */
      },
      
      getcategorylist : function(scope) {
        $http.get('services/in.peterb.restapi.php/gettypelist/').
          success(function(data, status, headers, config) {
            scope.typeList = data.Items;
            //console.log(data.Items[0].name);
          }).
          error(function(data, status, headers, config) {
            console.log(data);
            // called asynchronously if an error occurs
            // or server returns response with an error status.
          });        
      },

      gettypelist : function(scope) {
        $http.get('services/in.peterb.restapi.php/getcategorylist/').
          success(function(data, status, headers, config) {
            scope.categoryList = data.Items;
            //console.log(data.Items[0].name);
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
          url: 'services/in.peterb.restapi.php/savepost',
          data: httpPostParams,
          headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).
        success(function(data, status, headers, config) {
			console.log(data.savedblogid);
			
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
