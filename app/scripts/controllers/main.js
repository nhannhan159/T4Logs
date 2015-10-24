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

	  $scope.itemsByPage=5;
    $http.get('http://192.168.227.128:82/couchdb/_design/base/_view/all').success(function($data) {
      $scope.rowCollection = [];
      $.each($data.rows,function(key, value){
          $scope.rowCollection.push({
            id:value.value._id,
            host:value.value.host,
            facility:value.value.facility,
            severity:value.value.severity,
            program:value.value.program,
            msg:value.value.msg,
            lo:value.value.lo,
            });
      })
      $scope.displayedCollection = [].concat($scope.rowCollection);
    });
    
    $scope.getters={
        id: function (value) {
            //this will sort by the length of the first name string
            return value.id.length;
        }
    }
})