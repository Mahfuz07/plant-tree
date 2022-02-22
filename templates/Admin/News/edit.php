
    <div class="content-body">

        <div class="row page-titles mx-0">
            <div class="col p-md-0">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Customers</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Add Customer</a></li>
                </ol>
            </div>
        </div>
        <!-- row -->

        <div class="container-fluid">
            <?php
            $success = $this->Flash->render('success');
            if ($success) { ?>
            <div class="alert alert-success">
                 <?php echo $success; ?>
            </div>
            <?php } ?>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-validation">
                                <?php echo $this->Form->create($news, array('enctype'=>'multipart/form-data')); ?>
                                <div class="form-valide" action="#" method="post">
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label" for="val-Image">Image<span class="text-danger">*</span>
                                        </label>
                                        <div class="col-lg-6">
                                            <div class="panel panel-default">
                                                <!--                                                <input type="file" name="upload_image[]" multiple />-->
                                                <?php echo $this->Form->input('news_image', ['type' => 'file', 'label' => false, 'required' => true]); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label" for="val-username">Display Name <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-lg-6">
                                            <?php
                                            echo $this->Form->input('display_name', array(
                                                'type' =>'text',
                                                'class' =>'form-control',
                                                'id' =>'val-display-name',
                                                'label' =>false,
                                                'placeholder' =>'Enter a Display Name..',
                                                'required' => true
                                            ));
                                            ?>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label" for="val-email">Title <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-lg-6">

                                            <?php
                                            echo $this->Form->input('title', array(
                                                'type' =>'text',
                                                'class' =>'form-control',
                                                'id' =>'val-title',
                                                'label' =>false,
                                                'placeholder' =>'Enter a Title ..',
                                                'required' => true
                                            ));
                                            ?>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label" for="val-password">Content <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-lg-6">
                                            <?php
                                            echo $this->Form->input('content', array(
                                                'type' =>'textarea',
                                                'class' =>'click2edit summernote m-b-40',
                                                'id' =>'content',
//                                                'value' =>$content,
                                                'label' =>false,
//                                                'placeholder' =>'content..',
                                                'required' => true
                                            ));
                                            ?>
                                        </div>
                                    </div>


                                    <div class="form-group row">
                                        <div class="col-lg-8 ml-auto">
<!--                                            <button id="edit" class="btn btn-info btn-rounded" onclick="edit()" type="button">Edit</button>-->
                                            <button id="save" class="btn btn-primary" onclick="save()" type="submit">submit</button>
<!--                                            <button type="submit" id="submit" class="btn btn-primary" onclick="save()">Submit</button>-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="/webroot/plugins/summernote/dist/summernote.min.js"></script>
<script src="/webroot/plugins/summernote/dist/summernote-init.js"></script>

