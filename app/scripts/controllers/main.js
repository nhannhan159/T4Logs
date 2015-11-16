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
        $scope.itemsByPage = 5;
        $scope.accessToken = 1;
        //$http.get('http://192.168.227.128:82/couchdb/t4logs/_design/base/_view/all').success(function ($data) {
        //    $scope.rowCollection = [];
        //    $.each($data.rows, function (key, value) {
        //        $scope.rowCollection.push({
        //            id: value.value._id,
        //            host: value.value.host,
        //            facility: value.value.facility,
        //            severity: value.value.severity,
        //            program: value.value.program,
        //            msg: value.value.msg,
        //            lo: value.value.lo,
        //        });
        //    })
        //    $scope.displayedCollection = [].concat($scope.rowCollection);
        //});

        //Login Handler
        $scope.loginEmail = "superadmin";
        $scope.loginPassword = "superadmin";
        $scope.logIn = function () {
            var data = {
                "username" : $scope.loginEmail,
                "password" : $scope.loginPassword
            };
            $http({
                url: "http://127.0.0.1:81/t4logs/auth/login/local",
                dataType: "json",
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                data: data
            }).success(function(data, status, headers, config){
                $scope.accessToken = headers("Access-Token");
                $scope.getLog();
            }).error(function(error){
                console.log(error)
            });
        }

        //Get Log
        $scope.getLog = function(){
            $http({
                url: "http://127.0.0.1:81/t4logs/logs",
                dataType: "json",
                method: "GET",
                headers: {
                    "Content-Type": "application/json",
                    "Access-Token" : $scope.accessToken
                },
            }).success(function(data, status, headers, config){
                $scope.accessToken = headers("Access-Token");
                $scope.bindData(data);
            }).error(function(error){
                console.log(error)
            });
        }

        //Bind Data to Table
        $scope.bindData = function($data){
            $scope.rowCollection = [];
            $.each($data.rows, function (key, value) {
                $scope.rowCollection.push({
                    id: value.value._id,
                    host: value.value.host,
                    facility: value.value.facility,
                    severity: value.value.severity,
                    program: value.value.program,
                    msg: value.value.msg,
                    lo: value.value.lo,
                });
            })
            $scope.displayedCollection = [].concat($scope.rowCollection);
        }

        $scope.getters = {
            id: function (value) {
                //this will sort by the length of the first name string
                return value.id.length;
            }
        }

        //Logout
        $scope.logOut = function(){
            $http({
                url: "http://127.0.0.1:81/t4logs/auth/logout",
                dataType: "json",
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Access-Token" : $scope.accessToken
                }
            }).success(function(data, status, headers, config){
                $scope.rowCollection = [];
                $scope.displayedCollection = [];
            }).error(function(error){
                console.log(error)
            });
        }
    })
    .directive('stRatio', function () {
        return {
            link: function (scope, element, attr) {
                var ratio = +(attr.stRatio);

                element.css('width', ratio + '%');

            }
        };
    });

