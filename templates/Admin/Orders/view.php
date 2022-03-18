<?php

use Cake\Routing\Router;

?>
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
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
<!--                                    --><?php //?>
                                        <tr>
                                            <td><?= $orders['id'] ?></td>
                                            <td><?= $orders['order_id'] ?></td>
                                            <td><?= $orders['order_stage'] ?></td>
                                            <td><?= $orders['tran_id'] ?></td>
                                            <td><?= number_format($orders['order_total'], 2) ?></td>
                                            <td><?= $orders['customer_name'] ?></td>
                                            <td><?= $orders['customer_email'] ?></td>
                                            <td><?= $orders['customer_phone'] ?></td>
                                            <td><?= $this->Html->link('Edit',['controller' => 'Orders' , 'action' => 'orderEdit', $orders['id']], ['style' => 'transition: all 0.4s ease-in-out; color: #7571f9;']) ?>
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
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($order_product as $product) { ?>
                                            <tr>
                                                <td><?= $product['id'] ?></td>
                                                <td><?= $product['product_id'] ?></td>
                                                <td><?= 'Image' ?></td>
                                                <td><?= $product['product_name'] ?></td>
                                                <td><?= number_format($product['product_price'], 2) ?></td>
                                                <td><?= $product['product_quantity'] ?></td>
                                                <td><?= number_format($product['product_final_price'], 2) ?></td>
                                                <?php foreach ($order_product_address as $address) {
                                                    if ($address['order_product_id'] == $product['id']) {?>

                                                    <td><?= $address['address'] ?></td>

                                                <?php }} ?>
                                                <td><?= $this->Html->link('Edit',['controller' => 'Orders' , 'action' => 'orderProductEdit', $product['id']], ['style' => 'transition: all 0.4s ease-in-out; color: #7571f9;']) ?>
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

