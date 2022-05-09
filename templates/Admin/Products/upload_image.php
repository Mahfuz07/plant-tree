<?php
use Cake\Routing\Router;
?>
    <!--**********************************
        Content body start
    ***********************************-->
    <div class="content-body">

        <div class="row page-titles mx-0">
            <div class="col p-md-0">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Products</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Add Product Image</a></li>
                </ol>
            </div>
        </div>
        <!-- row -->

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
                            <div class="form-validation">
                                <?php echo $this->Form->create(null, array('enctype'=>'multipart/form-data')); ?>
                                <div class="form-valide" action="#" method="post">
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label" for="val-Image">
                                        </label>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label" for="val-Image">Image
                                            <!--                                            <span class="text-danger">*</span>-->
                                        </label>
                                        <div class="col-lg-6">
                                            <div class="panel panel-default">
                                                <!--                                                <input type="file" name="upload_image[]" multiple />-->
                                                <?php echo $this->Form->input('upload_image', ['type' => 'file', 'label' => false]); ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-lg-8 ml-auto">
                                            <button type="submit" id="submit crop_image" class="btn btn-primary">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<!--        <div class="content-body">-->
<!--            <div class="row page-titles mx-0">-->
<!--                <div class="col p-md-0">-->
<!--                    <ol class="breadcrumb">-->
<!--                        <li class="breadcrumb-item"><a href="javascript:void(0)">Product Images</a></li>-->
<!--                    </ol>-->
<!--                </div>-->
<!--            </div>-->

            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Product Image</h4>

                                <?php if (!empty($images)) {?>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered zero-configuration">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Image</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach ($images as $image) { ?>
                                                <tr>
                                                    <td><?= $image['id'] ?></td>
                                                    <td >
                                                        <img src="<?= Router::fullBaseUrl() . '/' . 'img' . DS . 'product_images' . DS .  $image['image_path'] ;?>" alt="Product Image" width="60" height="60"/><br>
                                                    </td>

                                                    <td>
                                                        <?= $this->Html->link('Delete',['controller' => 'Products' , 'action' => 'imageDelete', $image['id']], ['style' => 'transition: all 0.4s ease-in-out; color: #7571f9;']) ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            </tbody>
                                            <tfoot>
                                            </tfoot>
                                        </table>
                                    </div>
                                <?php } else { ?>

                                    <h2> Product Image Empty</h2>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- #/ container -->
        <!-- #/ container -->
    </div>

