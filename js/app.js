var app = angular.module('virtualmarket', ['ui.router', 'ngStorage', 'oitozero.ngSweetAlert', 'chart.js']);
app.controller("LineCtrl", function ($scope) {

  $scope.labels = ["January", "February", "March", "April", "May", "June", "July"];
  $scope.series = ['Series A', 'Series B'];
  $scope.data = [
    [65, 59, 80, 81, 56, 55, 40],
    [28, 48, 40, 19, 86, 27, 90]
  ];
  $scope.onClick = function (points, evt) {
    console.log(points, evt);
  };
  $scope.datasetOverride = [{ yAxisID: 'y-axis-1' }, { yAxisID: 'y-axis-2' }];
  $scope.options = {
    scales: {
      yAxes: [
        {
          id: 'y-axis-1',
          type: 'linear',
          display: true,
          position: 'left'
        },
        {
          id: 'y-axis-2',
          type: 'linear',
          display: true,
          position: 'right'
        }
      ]
    }
  };
});

app.directive('fdInput', [function () {
    return {
        link: function (scope, element, attrs) {
            element.on('change', function  (evt) {
                var files = evt.target.files;
                console.log(files[0].name);
                console.log(files[0].size);
            });
        }
    }
}]);
app.directive('fileModel', ['$parse', function ($parse) {
    return {
    restrict: 'A',
    link: function(scope, element, attrs) {
        var model = $parse(attrs.fileModel);
        var modelSetter = model.assign;

        element.bind('change', function(){
            scope.$apply(function(){
                modelSetter(scope, element[0].files[0]);
            });
        });
    }
   };
}])

app.service('fileUpload', ['$http', 'SweetAlert', '$localStorage', function ($http, SweetAlert, $localStorage) {

    this.uploadFileToUrl = function(image, uploadUrl, pack){
         var fd = new FormData();
         fd.append('image', image);
         fd.append('product_name', pack.product_name);
         fd.append('product_details', pack.product_details);
         fd.append('product_price', pack.product_price);
         fd.append('unit_of_measure', pack.unit_of_measure);
         fd.append('quantity', pack.quantity);
         fd.append('status', pack.status);
         fd.append('category_id', pack.category);
         fd.append('seller', $localStorage.user[0]._id);

         $http.post(uploadUrl, fd, {
             transformRequest: angular.identity,
             headers: {'Content-Type': undefined}
         })
         .then(function(response){
         	if (response.data.status == "success"){
         		SweetAlert.swal("Success!", "Select a File", "success");
         	}
         	else{
         		SweetAlert.swal("Error!", "Failed", "error");
         	}
         },function(error){
         		SweetAlert.swal("Error!", "Check Network", "error");
         });
     }
 }])