app.config(function($stateProvider, $urlRouterProvider) {
  // For any unmatched url, redirect to /registration
  $urlRouterProvider.otherwise("/registration");
  // Now set up the states
  $stateProvider
    .state('addproducts', {
      url: "/addproducts",
      templateUrl: "views/addproducts.html"
    })
    .state('checkout', {
      url: "/checkout",
      templateUrl: "views/checkout.html"
    })
    .state('dashboard', {
      url: "/dashboard",
      templateUrl: "views/dashboard.html"
    })
    .state('login', {
      url: "/login",
      templateUrl: "views/login.html"
    })
    .state('myaccount', {
      url: "/myaccount",
      templateUrl: "views/myaccount.html"
    })
    .state('myorders', {
      url: "/myorders",
      templateUrl: "views/myorders.html"
    })
    .state('myproducts', {
      url: "/myproducts",
      templateUrl: "views/myproducts.html"
    })
    .state('orders', {
      url: "/orders",
      templateUrl: "views/orders.html"
    })
    .state('registration', {
      url: "/registration",
      templateUrl: "views/registration.html"
    })
    .state('result', {
      url: "/result",
      templateUrl: "views/result.html"
    });
});