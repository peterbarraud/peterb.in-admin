'use strict';

/**
 * @ngdoc overview
 * @name peterbdotin
 * @description
 * # peterbdotin
 *
 * Main module of the application.
 */
angular.module('peterbdotin', ['ui.bootstrap','ngRoute','ngCkeditor']).config(function($routeProvider){
  $routeProvider.
    when('/', {
      template:"<div class='row main'><div class='col-md-3 col-md-offset-4'><log-in redirectTo='/blogitem'></log-in></div></div>",
      controller: 'loginCtrl'
    }).
    when('/blogadmin', {
      template:'<nav-bar></nav-bar><div class=row><blog-list></blog-list><blog-item></blog-item></div>',
      controller: 'blogCtrl'
    }).
    when('/categoryadmin', {
      template:'<nav-bar></nav-bar><div class=row><category-list listname="categoryList"></category-list><category-item></category-item></div>',
      controller: 'categoryCtrl'
    }).
    when('/useradmin', {
      template:'<nav-bar></nav-bar><div class=row><user-list></user-list><user-item></user-item></div>',
      controller: 'userCtrl'
    });
    
  
});


