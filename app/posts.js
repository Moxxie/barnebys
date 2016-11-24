app.factory('posts', function($rootScope, $http, $httpParamSerializerJQLike, config, user){
  var posts = {};
  posts.error = false;

  posts.get = function (callback) {
    $http.get(config.apiUrl + '/posts').then(function successCallback(response) {
      if(response.data.result !== false){
        callback(response.data.result);
      }else{
        callback(false);
      }
    }, function errorCallback(response) {
      callback(false);
    });
  };

  posts.save = function(data, callback){
    if(!user.loggedIn) callback(false);

    data.user_id = user.id;
    $http.post(config.apiUrl + '/posts', data).then(function successCallback(response) {
      if(typeof response.data.error == 'undefined'){
        callback(true);
      }else{
        callback(response.data.error);
      }
    }, function errorCallback(response) {
      callback(false);
    });
  };

  return posts;
});