'use strict';

/**
 * @ngdoc function
 * @name t4LogsApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the t4LogsApp
 */
angular.module('t4LogsApp')
  .controller('MainCtrl', function ($scope, $http) {
    $http.get('http://127.0.0.1:5984/t4logs/_design/base/_view/all').success(function($data) {
      $scope.logs = $data.rows;
    });
  });
