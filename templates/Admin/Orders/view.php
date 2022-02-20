<?php

use Cake\Routing\Router;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
    <!-- Custom Stylesheet -->
    <link href="/webroot/plugins/tables/css/datatable/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="/webroot/css/style.css" rel="stylesheet">

</head>

<body>

<!--*******************
    Preloader start
********************-->
<div id="preloader">
    <div class="loader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="3" stroke-miterlimit="10" />
        </svg>
    </div>
</div>
<!--*******************
    Preloader end
********************-->


<!--**********************************
    Main wrapper start
***********************************-->
<div id="main-wrapper">

    <!--**********************************
        Nav header start
    ***********************************-->
    <div class="nav-header">
        <div class="brand-logo">
            <a href="index.html">
                <b class="logo-abbr"><img src="images/logo.png" alt=""> </b>
                <span class="logo-compact"><img src="./images/logo-compact.png" alt=""></span>
                <span class="brand-title">
                        <img src="images/logo-text.png" alt="">
                    </span>
            </a>
        </div>
    </div>
    <!--**********************************
        Nav header end
    ***********************************-->


    <!--**********************************
        Content body start
    ***********************************-->
    <div class="content-body">
        <div class="row page-titles mx-0">
            <div class="col p-md-0">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Orders</a></li>
                </ol>
            </div>
        </div>

        <div class="container-fluid">
            <?php
            $success = $this->Flash->render('success');
            $error = $this->Flash->render('error');
            if ($success) { ?>
                <div class="alert alert-success">
                    <?php echo $success; ?>
                </div>
            <?php }

            if ($error) { ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            <?php } ?>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">
                                <h4>Order Details</h4>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Order Id</th>
                                        <th>Order Stage</th>
                                        <th>Tran Id</th>
                                        <th>Order Total</th>
                                        <th>Customer Name</th>
                                        <th>Customer Email</th>
                                        <th>Customer Phone</th>
                                    </tr>
                                    </thead>
                                    <tbody>
<!--                                    --><?php //?>
                                        <tr>
                                            <td><?= $orders['id'] ?></td>
                                            <td><?= $orders['order_id'] ?></td>
                                            <td><?= $orders['order_stage'] ?></td>
                                            <td><?= $orders['tran_id'] ?></td>
                                            <td><?= $orders['order_total'] ?></td>
                                            <td><?= $orders['customer_name'] ?></td>
                                            <td><?= $orders['customer_email'] ?></td>
                                            <td><?= $orders['customer_phone'] ?></td>
                                        </tr>
<!--                                    --><?php // ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
<!--                --><?php //foreach ($order_product as $product) { ?>
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">
                                <h4>Product Details</h4>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Product Id</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Total Price</th>
                                        <th>Delivery Address</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($order_product as $product) { ?>
                                            <tr>
                                                <td><?= $product['id'] ?></td>
                                                <td><?= $product['product_id'] ?></td>
                                                <td><?= 'Image' ?></td>
                                                <td><?= $product['product_name'] ?></td>
                                                <td><?= $product['product_price'] ?></td>
                                                <td><?= $product['product_quantity'] ?></td>
                                                <td><?= $product['product_final_price'] ?></td>
                                                <?php foreach ($order_product_address as $address) {
                                                    if ($address['order_product_id'] == $product['id']) {?>

                                                    <td><?= $address['address'] ?></td>

                                                <?php }} ?>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- /# card -->
                </div>
<!--                --><?php //} ?>
            </div>

        </div>
        <!-- #/ container -->
    </div>
    <!--**********************************
        Content body end
    ***********************************-->

</div>

</body>

</html>
