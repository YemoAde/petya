app.controller('mainController', function($scope, $rootScope,$localStorage, $http, SweetAlert, $timeout){
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
                $timeout(function() {
                    $state.go('/dashboard');
                }, 4000);

                
            }else{
                SweetAlert.swal("Error!", response.data.message, "error");
                console.log(response.data.message);
            }
        }, function(error){
            SweetAlert.swal("Error!", "Failed", "error");
        })

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


app.controller('registerController', function($scope, $rootScope, $http, $timeout, $location, $state, $localStorage, SweetAlert){

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

app.controller('productController', function($scope, $rootScope){


})