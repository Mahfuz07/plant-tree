    <!--**********************************
        Content body start
    ***********************************-->
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
                                <?php echo $this->Form->create($product , ['id' => 'user-password-form']); ?>
                                <div class="form-valide" action="#" method="post">
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label" for="val-category">Category<span class="text-danger">*</span>
                                        </label>
                                        <div class="col-lg-6">
                                            <!--                                            <input type="text" class="form-control" id="val-display-name" name="val-display-name" placeholder="Enter a Display Name..">-->
                                            <?php
                                                if (!empty($categoryList)) {
                                                    $vss = [];
                                                    foreach ($categoryList as $key=>$category) {
                                                        if (empty($category['title'])) {
                                                            continue;
                                                        }
                                                        $vss[$category['id']] = $category['title'];
                                                    }
                                                }

                                            echo $this->Form->control('category_id',array('empty'=>'Select Category','id'=>'category_id','class'=>"form-control",'type'=>'select','options'=> $vss,'label'=> false, 'value' => $product['category_id'], 'required' => true));
                                            ?>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label" for="val-title">Title <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-lg-6">
                                            <!--                                            <input type="text" class="form-control" id="val-display-name" name="val-display-name" placeholder="Enter a Display Name..">-->
                                            <?php
                                            echo $this->Form->input('title', array(
                                                'type' =>'text',
                                                'class' =>'form-control',
                                                'id' =>'val-title',
                                                'label' =>false,
                                                'placeholder' =>'Enter a Display Name..',
                                                'required' => true
                                            ));
                                            ?>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label" for="val-email">Display Name <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-lg-6">
                                            <!--                                            <input type="text" class="form-control" id="val-email" name="val-email" placeholder="Your valid email..">-->
                                            <?php
                                            echo $this->Form->input('display_name', array(
                                                'type' =>'text',
                                                'class' =>'form-control',
                                                'id' =>'val-display-name',
                                                'label' =>false,
                                                'placeholder' =>'Your valid display name..',
                                                'required' => true
                                            ));
                                            ?>
                                            <span class="registrationFormAlert" style="color:#dc0000;" id="Exist"></span>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label" for="val-slug">Slug <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-lg-6">
                                            <!--                                            <input type="password" class="form-control" id="val-password" name="val-password" placeholder="Choose a safe one..">-->
                                            <?php
                                            echo $this->Form->input('slug', array(
                                                'type' =>'text',
                                                'class' =>'form-control',
                                                'id' =>'val-slug',
                                                'label' =>false,
                                                'placeholder' =>'Slug',
                                                'required' => true
                                            ));
                                            ?>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label" for="val-description">Description <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-lg-6">
                                            <!--                                            <input type="password" class="form-control" id="val-confirm-password" name="val-confirm-password" placeholder="..and confirm it!">-->
                                            <?php
                                            echo $this->Form->input('description', array(
                                                'type' =>'textarea',
                                                'class' =>'form-control',
                                                'id' =>'val-description',
                                                'label' =>false,
                                                'placeholder' =>'description',
                                                'required' => true
                                            ));
                                            ?>
                                        </div>

                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label" for="val-number">price <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-lg-6">
                                            <!--                                            <input type="password" class="form-control" id="val-confirm-password" name="val-confirm-password" placeholder="..and confirm it!">-->
                                            <?php
                                            echo $this->Form->input('price', array(
                                                'type' =>'text',
                                                'class' =>'form-control',
                                                'id' =>'val-number',
                                                'label' =>false,
                                                'placeholder' =>'0.00',
                                                'required' => true
                                            ));
                                            ?>
                                        </div>

                                    </div>

                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label"><a href="#">Published</a>  <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-lg-8">
                                            <label class="css-control css-control-primary css-checkbox" for="val-terms">
<!--                                                <input type="checkbox" class="css-control-input" id="val-terms" name="published" value="--><?//= $product['published'] ?><!--" required> <span class="css-control-indicator"></span>  checkbox</label>-->
                                            <?php
                                            echo $this->Form->checkbox('published', array('type' => 'checkbox', 'checked'=>$product['published'], 'class' => 'css-control-input', 'label' => 'Published'));
                                            ?>
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
        <!-- #/ container -->
    </div>

