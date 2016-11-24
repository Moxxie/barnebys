app.factory('user', function($rootScope, $http, $httpParamSerializerJQLike, config){
  var user = {};
  user.loggedIn = false;
  user.id = false;
  user.token = false;
  user.error = false;
  user.hidePopup = true;
  user.showRegister = false;
  user.showLogin = false;

  user.init = function (callback) {
    if(typeof Cookies.get('userToken') != 'undefined'){
      var token = Cookies.get('userToken');
      $http.get(config.apiUrl + '/user/check/' + token).then(function successCallback(response) {
        if(response.data.user !== false){
          user.token = token;
          user.id = response.data.user.id;
          user.loggedIn = true;
          $rootScope.$emit('user.loggedin');
          callback(true);
        }else{
          callback(false);
        }
      }, function errorCallback(response) {
        callback(false);
      });
    }
  };

  user.register = function(data, callback){
    $http.post(config.apiUrl + '/user/register', data).then(function successCallback(response) {
      if(typeof response.data.error == 'undefined'){
        user.token = response.data.token;
        Cookies.set('userToken', user.token, {expires: 365});
        user.loggedIn = true;
        user.id = response.data.id;
        callback(false);
      }else{
        callback(response.data.error);
      }
    }, function errorCallback(response) {
      return false;
    });
  };

  user.login = function(data, callback){
    $http.post(config.apiUrl + '/user/login', data).then(function successCallback(response) {
      console.log(response.data);
      if(typeof response.data.error == 'undefined'){
        user.token = response.data.token;
        Cookies.set('userToken', user.token, {expires: 365});
        user.loggedIn = true;
        user.id = response.data.id;
        callback(false);
      }else{
        callback(response.data.error);
      }
    }, function errorCallback(response) {
      return false;
    });
  };

  user.logout = function(){
    user.loggedIn = false;
    user.token = false;
    Cookies.remove('userToken');
  };

  user.openPopup = function(type){
    if(type == 'login') user.showLogin = true;
    else user.showRegister = true;
    user.hidePopup = false;
  };
  user.closePopup = function(){
    user.showRegister = false;
    user.showLogin = false;
    user.hidePopup = true;
  };

  return user;
});