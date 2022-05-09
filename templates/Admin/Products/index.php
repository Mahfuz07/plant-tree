<?php

use Cake\Routing\Router;

?>
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
                            <h4 class="card-title">Products</h4>
                            <div class="">
                                <?= $this->Html->link('Add Product',['controller' => 'Products' , 'action' => 'add'], ['class' => 'btn btn-primary']) ?>
                            </div>


                            <?php if (!empty($products)) {?>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Display Name</th>
                                            <th>image</th>
                                            <th>description</th>
                                            <th>price</th>
                                            <th>Published</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($products as $product) { ?>
                                        <tr>
                                            <td><?= $product['id'] ?></td>
                                            <td><?= $product['title'] ?></td>
                                            <td><?= $product['display_name'] ?></td>
                                            <td>
                                                <img src="<?= Router::fullBaseUrl() . '/' . 'img' . DS . 'product_images' . DS .  $product['image'] ;?>" alt="Product Image" width="90" height="60"/><br><br>
                                                <?= $this->Html->link('Upload Image',['controller' => 'Products' , 'action' => 'uploadImage', $product['id']], ['style' => 'transition: all 0.4s ease-in-out; color: #7571f9;']) ?>
                                            </td>
                                            <td><?= $product['description'] ?></td>
                                            <td><?= $product['price'] ?></td>
                                            <td><?= $product['published'] ?></td>
                                            <td><?= $this->Html->link('Edit',['controller' => 'Products' , 'action' => 'edit', $product['id']], ['style' => 'transition: all 0.4s ease-in-out; color: #7571f9;']) ?>
                                                &nbsp;&nbsp;&nbsp;
                                                <?= $this->Html->link('Delete',['controller' => 'Products' , 'action' => 'delete', $product['id']], ['style' => 'transition: all 0.4s ease-in-out; color: #7571f9;']) ?></td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                    <tfoot>
                                    </tfoot>
                                </table>
                            </div>
                            <?php } else { ?>

                                <h2> Product Empty</h2>
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
