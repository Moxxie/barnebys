var app = angular.module('BarnebysApp', []);

app.constant('config', {
    apiUrl: './api'
});

app.run(function($rootScope, user){
    user.init(function (status) {
    });
});

app.controller('IndexController', function($scope, $interval, user, posts) {
    $scope.user = user;

    $scope.posts = [];
    posts.get(function (data) {
        $scope.posts = data;
    });

    $scope.formData = {};
    $scope.error = false;

    $scope.post = function(){
        posts.save($scope.formData, function(error){
            if(error === false){
                posts.get(function (data) {
                    $scope.posts = data;
                });
            }else{
                $scope.error = error;
            }
        });
    };
});

app.controller('RegisterController', function($scope, $rootScope, $http, user) {
    $scope.formData = {};
    $scope.error = false;

    $scope.submit = function(){
        user.register($scope.formData, function(error){
            if(error === false){
                user.closePopup();
            }else{
                $scope.error = error;
            }
        });
    };
});
app.controller('LoginController', function($scope, $rootScope, $http, user) {
    $scope.formData = {};
    $scope.error = false;

    $scope.submit = function(){
        user.login($scope.formData, function(error){
            if(error === false){
                user.closePopup();
            }else{
                $scope.error = error;
            }
        });
    };
});