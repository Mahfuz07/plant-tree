<?php

use Cake\Routing\Router;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Quixlab - Bootstrap Admin Dashboard Template by Themefisher.com</title>
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
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Products</a></li>
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
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Customer Details</h4>

                            <?php if (!empty($customers)) {?>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered zero-configuration">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Display Name</th>
                                            <th>Email</th>
                                            <th>Phone No</th>
                                            <th>Address</th>
                                            <th>Image</th>
                                            <th>Bio</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($customers as $customer) { ?>
                                            <tr>
                                                <td><?= $customer['id'] ?></td>
                                                <td><?= $customer['display_name'] ?></td>
                                                <td><?= $customer['email'] ?></td>
                                                <td><?= $customer['phone_no'] ?></td>
                                                <td><?= $customer['address'] ?></td>
                                                <td>
                                                    <img src="<?= Router::fullBaseUrl() . '/' . $customer['image'] ;?>" alt="Customer Image" width="90" height="60"/><br><br>
                                                </td>

                                                <td><?= $customer['bio'] ?></td>
                                                <td><?= $this->Html->link('Delete',['controller' => 'Products' , 'action' => 'edit', $customer['id']], ['style' => 'transition: all 0.4s ease-in-out; color: #7571f9;']) ?></td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                        <tfoot>
                                        </tfoot>
                                    </table>
                                </div>
                            <?php } else { ?>

                                <h2>Customer Empty</h2>
                            <?php } ?>
                        </div>
                    </div>
                </div>
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
