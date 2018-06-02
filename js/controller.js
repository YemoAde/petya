app.controller('mainController', function($scope, $rootScope,$localStorage, $http, SweetAlert, $timeout, $state){
	if($localStorage.user){
		$rootScope.logged = true
	}
	$scope.logout = function(){

        $http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";
        $http.get('http://localhost/petya/v1/logout')
        .then(function(response){
            if(response.data.status === "info"){
                SweetAlert.swal("Success!", "Avast! Redirecting...", "success");
                console.log(response.data);
                delete $localStorage.user;
                $rootScope.logged = false;
                $rootScope.buyer = true;
                $timeout(function() {
                    $state.go('dashboard');
                }, 4000);

                
            }else{
                SweetAlert.swal("Error!", response.data.message, "error");
                console.log(response.data.message);
            }
        }, function(error){
            SweetAlert.swal("Error!", "Failed", "error");
        })

    }
    $scope.query ='';

    $scope.search = function(query) {
    	$http.get('http://localhost/petya/v1/search/'+ query)
        .then(function(response){
            if(response.data.status == "success"){
                $scope.result = response.data.products
            }else{
                SweetAlert.swal("Error!", response.data.message, "error");
                console.log(response.data.message);
            }
        }, function(error){
            SweetAlert.swal("Error!", "Failed", "error");
        })
    }
    $rootScope.cart = []
    $scope.addToCart = function(item){
    	console.log("jj")
    	$rootScope.cart.push(item)
    	SweetAlert.swal("Success!", "Added to Cart", "success");
    }

})


app.controller('loginController', function($scope, $rootScope, $http, $timeout, $location, $state, $localStorage, SweetAlert, $state){
	if($rootScope.logged){
		$state.go('dashboard')
	}
	$scope.login = function(user){
		$http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";
		$http.post('http://localhost/petya/v1/login', user).then(function(response){
			if(response.data.status == "success"){
				SweetAlert.swal("Success!", response.data.message, "success");
				$localStorage.user = response.data.user;
				$rootScope.logged = true;
				if(response.data.user[0].type  == 1){
					$rootScope.buyer = true;
				}
				$timeout(function(){
					$state.go('dashboard');
				}, 2000)
			}else{
				SweetAlert.swal("Error!", response.data.message, "error");
			}
		}, function(error){
			SweetAlert.swal("Error!", "Check Network", "error");
		})
	}
})

app.controller('registerController', function($scope, $rootScope, $http, $timeout, $location, $state, $localStorage, SweetAlert, fileUpload){

	$scope.register = function(user){
		$http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";
		$http.post('http://localhost/petya/v1/register', user).then(function(response){
			if(response.data.status == "success"){
				SweetAlert.swal("Success!", response.data.message, "success");
				$scope.user = {}
				$timeout(function(){
					$state.go('login');
				}, 2000)
			}else{
				SweetAlert.swal("Error!", response.data.message, "error");
			}
		}, function(error){
			SweetAlert.swal("Error!", "Check Network", "error");
		})
	}
})


app.controller('productController', function($scope, $rootScope, $http, $timeout, $location, $state, $localStorage, SweetAlert, fileUpload){
	$scope.move = function(id){
		$location.path('#/single/1');
	}	
	$scope.viewAll = function(){
		$http.get('http://localhost/petya/v1/viewProducts').then(function(response){
			if(response.data.status == "success"){
				$rootScope.allProducts = response.data.products
			}else{
				SweetAlert.swal("Error!", "Could not get List", "error");
			}
		}, function(error){
			SweetAlert.swal("Error!", "Could not get List", "error");
		})
	}

	$scope.viewAll();

	$scope.add = function(item){
		
		if(!$scope.myFile){
			SweetAlert.swal("Error!", "Select a File", "error");
		}
		else{
			var image = $scope.myFile;
	        console.log('file is ' );
	        console.dir(image);
	        
	        
	        var uploadUrl = "http://localhost/petya/v1/addProduct";
	        
	         fileUpload.uploadFileToUrl(image, uploadUrl, item);
	         $scope.item = {};
		}
	}

	$scope.viewAll()

})

app.controller('sellerController', function($scope, $rootScope, $http, $timeout, $location, $state, $localStorage, SweetAlert, fileUpload){	
	$scope.viewMyProduct = function(){
		$http.get('http://localhost/petya/v1/myProduct/'+ $localStorage.user[0]._id).then(function(response){
			if(response.data.status == "success"){
				$scope.myProduct = response.data.products
			}else{
				SweetAlert.swal("Error!", "Could not get List", "error");
			}
		}, function(error){
			SweetAlert.swal("Error!", "Could not get List", "error");
		})
	}

	$scope.viewMyProduct();

})
app.controller('cartController', function($scope, $rootScope, $http, $timeout, $location, $state, $localStorage, SweetAlert, fileUpload){	
	$scope.cart = $rootScope.cart
	console.log($scope.cart)

	if (!($rootScope.cart.length > 0)){
		$state.go('dashboard')
	}

	$scope.remove = function(item){
		$rootScope.cart.splice($rootScope.cart.indexOf(item), 1);
		SweetAlert.swal("Success!", "Removed", "success");
		$scope.cart = $rootScope.cart
	}

})
