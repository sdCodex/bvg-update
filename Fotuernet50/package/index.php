<?php 
require_once __DIR__ . '/../config.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phone Pe Payment Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<style>
body {
    background-color:black;
}
</style>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class='col-12 my-2 d-flex justify-content-center'>
                <div class="spinner-border text-danger mx-2" role="status" style="width:1.8rem;height:1.8rem">
                  <span class="visually-hidden">Loading...</span>
                </div>
                <h4 class='text-center text-danger'> Phone Pe Payment Processing... </h4>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title text-center mb-4 text-success"><u>Order Payment</u></h3>
                        <form id="paymentForm" onsubmit="displayProcessingText()" action="<?= APP_URL ?>/request.php" method="POST">
                        
                        <div class='row'>
                            <!-- <div class="mb-3 col-md-12">
                                <label for="customer_number" class="form-label">Customer Phone Number<span class='text-danger'>*</span></label>
                                <input type="text" minlength="10" maxlength="10" class="form-control"  value=""  name="mobileNumber"  required>
                            </div> -->
                            <div class="mb-3 col-md-12">
                                <label for="order_id" class="form-label">Order ID <span class='text-danger'>*</span></label>
                                <input type="text" class="form-control" id="order_id" name="order_id" value="<?php echo 'order_' . uniqid(); ?>" readonly>
                            </div>
                            <div class="mb-3 col-md-12">
                                <label for="order_amount" class="form-label">Order Amount(Rs) <span class='text-danger'>*</span></label>
                                <input type="text" class="form-control"  value="100" id="order_amount" name="order_amount"  required>
                            </div>
                            
                            <div class="text-center">
                                <button id="submitButton" type="submit" class="btn btn-success">Make Payment</button>
                            </div>
                            
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script>
        function displayProcessingText() {
            document.getElementById('submitButton').innerText = 'Please Wait';
        }
    </script>
</body>
</html>