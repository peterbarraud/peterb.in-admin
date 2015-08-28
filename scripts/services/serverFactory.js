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
				  location.path( "/blogadmin" );
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
      //we are going to use scope callbacks to handle multiple directive calls to identical factory methods
      //the specific directive functionality should be handled in the directive itselt. not in the factory. (mostly thanks to Ed Seckler)
      getitem : function(itemid,itemtype,scope) {
        $http.get('services/in.peterb.restapi.php/getitem/' + itemtype + '/' + itemid).
          success(function(data, status, headers, config) {
			  scope.manageitem(data);
          }).
          error(function(data, status, headers, config) {
            console.log(data);
          });        
      },

      getitems : function(itemtype,scope,callback) {
        $http.get('services/in.peterb.restapi.php/getitems/' + itemtype).
          success(function(data, status, headers, config) {
			  var dyn_functions = [];
			  scope[callback](data);
          }).
          error(function(data, status, headers, config) {
            console.log(data);
          });        
      },
      getblogitems : function(scope,callback) {
        $http.get('services/in.peterb.restapi.php/getblogitems').
          success(function(data, status, headers, config) {
			  scope[callback](data);
          }).
          error(function(data, status, headers, config) {
            console.log(data);
          });        
      },
      
      

      saveitemdetails : function(scope,itemtype) {
        var paramsObject = {itemObject:JSON.stringify(scope.itemDetails)};
        var httpPostParams = [];
        for (var key in paramsObject) {
          httpPostParams.push(key + '=' + encodeURIComponent(paramsObject[key]));
        }
        httpPostParams = httpPostParams.join('&');
        $http({
          method: 'POST',
          url: 'services/in.peterb.restapi.php/saveitem/' + itemtype,
          data: httpPostParams,
          headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).
        success(function(data, status, headers, config) {
			scope.managesaveitem (data);
        }).
        error(function(data, status, headers, config) {
          console.log(data);
        });
        //util.httpPost(paramsObject,$http,);
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
      
    };        
        
        
  });
