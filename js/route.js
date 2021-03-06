app.config(function($stateProvider, $urlRouterProvider) {
  // For any unmatched url, redirect to /registration
  $urlRouterProvider.otherwise("/dashboard");
  // Now set up the states
  $stateProvider

    // login route
    .state('login', {
      url: "/login",
      templateUrl: "views/login.html"
    })

    // register route
    .state('register', {
      url: "/register",
      templateUrl: "views/registration.html"
    })

    .state('myproduct', {
      url: "/myproduct",
      templateUrl: "views/myproducts.html",
      controller: 'sellerController'
    })

    .state('order', {
      url: "/order",
      templateUrl: "views/myorders.html"
    })

    .state('addProduct', {
      url: "/addProduct",
      templateUrl: "views/addproducts.html"
    })

    .state('profile', {
      url: "/profile",
      templateUrl: "views/profile.html"
    })

    .state('single', {
      url: "/single/{ID}",
      templateUrl: "views/single.html",
      controller: 'productController'
    })

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
    
    .state('myaccount', {
      url: "/myaccount",
      templateUrl: "views/myaccount.html"
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