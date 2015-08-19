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
    when('/:blogitem', {
      template:'<div class=row><blog-list/></blog-list><blog-item></blog-item></div>',
      controller: 'blogCtrl'
    }).
    otherwise('/', {
      redirectTo:'/'
    });
    
  
});


