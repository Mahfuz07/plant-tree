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
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <h4 class="card-title">Order List</h4>
                            <!-- Nav tabs -->
                            <div class="default-tab">
                                <ul class="nav nav-tabs mb-3" role="tablist">
                                    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#all-order">All Orders</a>
                                    </li>
                                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#processing-order">Processing Order</a>
                                    </li>
                                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#complete-order">Complete Order</a>
                                    </li>
                                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#cancel-order">Cancel Order</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="all-order" role="tabpanel">
                                        <div class="p-t-15">
                                            <?php if (!empty($all_orders)) {?>
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-bordered zero-configuration">
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
                                                        <?php foreach ($all_orders as $order) { ?>
                                                            <tr>
                                                                <td><?= $order['id'] ?></td>
                                                                <td><?= $order['order_id'] ?></td>
                                                                <td><?= $order['order_stage'] ?></td>
                                                                <td><?= $order['tran_id'] ?></td>
                                                                <td><?= number_format($order['order_total'], 2) ?></td>
                                                                <td><?= $order['customer_name'] ?></td>
                                                                <td><?= $order['customer_email'] ?></td>
                                                                <td><?= $order['customer_phone'] ?></td>
                                                                <td><?= $this->Html->link('View',['controller' => 'Orders' , 'action' => 'view', $order['id']], ['style' => 'transition: all 0.4s ease-in-out; color: #7571f9;']) ?>
                                                                    &nbsp;&nbsp;&nbsp;
                                                                    <?php if ($order['order_stage'] == 'Processing') { ?>
                                                                        <?= $this->Html->link('Complete', "/admin/orders/complete/" . $order['id'], ['style' => 'transition: all 0.4s ease-in-out; color: #7571f9;', 'confirm' => __('Are you sure you want to complete it?')]); ?>
                                                                    <?php } ?>
                                                                    &nbsp;&nbsp;&nbsp;
                                                                    <?php if ($order['order_stage'] != 'Cancel') { ?>
                                                                        <?= $this->Html->link('Cancel', "/admin/orders/cancel/" . $order['id'], ['style' => 'transition: all 0.4s ease-in-out; color: #7571f9;', 'confirm' => __('Are you sure you want to cancel it?')]); ?>
                                                                    <?php } ?>

                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                        </tbody>
                                                        <tfoot>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            <?php } else { ?>

                                                <h2>Order Empty</h2>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="processing-order">
                                        <div class="p-t-15">
                                            <?php if (!empty($processing_orders)) {?>
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-bordered zero-configuration">
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
                                                        <?php foreach ($processing_orders as $order) { ?>
                                                            <tr>
                                                                <td><?= $order['id'] ?></td>
                                                                <td><?= $order['order_id'] ?></td>
                                                                <td><?= $order['order_stage'] ?></td>
                                                                <td><?= $order['tran_id'] ?></td>
                                                                <td><?= number_format($order['order_total'], 2) ?></td>
                                                                <td><?= $order['customer_name'] ?></td>
                                                                <td><?= $order['customer_email'] ?></td>
                                                                <td><?= $order['customer_phone'] ?></td>
                                                                <td><?= $this->Html->link('View',['controller' => 'Orders' , 'action' => 'view', $order['id']], ['style' => 'transition: all 0.4s ease-in-out; color: #7571f9;']) ?>
                                                                    &nbsp;&nbsp;&nbsp;
                                                                    <?php if ($order['order_stage'] == 'Processing') { ?>
                                                                        <?= $this->Html->link('Complete', "/admin/orders/complete/" . $order['id'], ['style' => 'transition: all 0.4s ease-in-out; color: #7571f9;', 'confirm' => __('Are you sure you want to complete it?')]); ?>
                                                                    <?php } ?>
                                                                    &nbsp;&nbsp;&nbsp;
                                                                    <?php if ($order['order_stage'] != 'Cancel') { ?>
                                                                        <?= $this->Html->link('Cancel', "/admin/orders/cancel/" . $order['id'], ['style' => 'transition: all 0.4s ease-in-out; color: #7571f9;', 'confirm' => __('Are you sure you want to cancel it?')]); ?>
                                                                    <?php } ?>

                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                        </tbody>
                                                        <tfoot>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            <?php } else { ?>

                                                <h2>Order Empty</h2>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="complete-order">
                                        <div class="p-t-15">
                                            <?php if (!empty($complete_orders)) {?>
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-bordered zero-configuration">
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
                                                        <?php foreach ($complete_orders as $order) { ?>
                                                            <tr>
                                                                <td><?= $order['id'] ?></td>
                                                                <td><?= $order['order_id'] ?></td>
                                                                <td><?= $order['order_stage'] ?></td>
                                                                <td><?= $order['tran_id'] ?></td>
                                                                <td><?= number_format($order['order_total'], 2) ?></td>
                                                                <td><?= $order['customer_name'] ?></td>
                                                                <td><?= $order['customer_email'] ?></td>
                                                                <td><?= $order['customer_phone'] ?></td>
                                                                <td><?= $this->Html->link('View',['controller' => 'Orders' , 'action' => 'view', $order['id']], ['style' => 'transition: all 0.4s ease-in-out; color: #7571f9;']) ?>
                                                            </tr>
                                                        <?php } ?>
                                                        </tbody>
                                                        <tfoot>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            <?php } else { ?>

                                                <h2>Order Empty</h2>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="cancel-order">
                                        <div class="p-t-15">
                                            <?php if (!empty($cancel_orders)) {?>
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-bordered zero-configuration">
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
                                                        <?php foreach ($cancel_orders as $order) { ?>
                                                            <tr>
                                                                <td><?= $order['id'] ?></td>
                                                                <td><?= $order['order_id'] ?></td>
                                                                <td><?= $order['order_stage'] ?></td>
                                                                <td><?= $order['tran_id'] ?></td>
                                                                <td><?= number_format($order['order_total'], 2) ?></td>
                                                                <td><?= $order['customer_name'] ?></td>
                                                                <td><?= $order['customer_email'] ?></td>
                                                                <td><?= $order['customer_phone'] ?></td>
                                                                <td><?= $this->Html->link('View',['controller' => 'Orders' , 'action' => 'view', $order['id']], ['style' => 'transition: all 0.4s ease-in-out; color: #7571f9;']) ?>
                                                            </tr>
                                                        <?php } ?>
                                                        </tbody>
                                                        <tfoot>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            <?php } else { ?>

                                                <h2>Order Empty</h2>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- #/ container -->
    </div>
