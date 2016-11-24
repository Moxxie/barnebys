angular.
module('goodifyApp').
config(['$locationProvider', '$routeProvider',
  function config($locationProvider, $routeProvider) {
    //$locationProvider.hashPrefix('!');
    //$locationProvider.html5Mode(true);
    $routeProvider.
    when('/', {
      templateUrl: 'app/views/index.html',
      controller: 'IndexController'
    }).
    when('/listing/:group/:category?', {
      templateUrl: 'app/views/listing.html',
      controller: 'ListingController'
    }).
    when('/c/:id', {
      templateUrl: 'app/views/company.html',
      controller: 'CompanyController'
    }).
    otherwise('/');
  }
]);