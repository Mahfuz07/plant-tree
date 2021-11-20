<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <!--    <title>Quixlab - Bootstrap Admin Dashboard Template by Themefisher.com</title>-->
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
    <!-- Custom Stylesheet -->
    <link href="/webroot/css/style.css" rel="stylesheet">

    <style>
        .message.alert-success {
            background: red;
        }

    </style>

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
                                        <label class="col-lg-4 col-form-label" for="val-Image">
                                        </label>
                                        <div class="col-lg-6">
                                            <div id="uploaded_image" >
                                                <img src="<?= $product['image'] ;?>" alt="Girl in a jacket" width="200" height="200"/>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label" for="val-Image">Image
<!--                                            <span class="text-danger">*</span>-->
                                        </label>
                                        <div class="col-lg-6">
                                            <div class="panel panel-default">
                                                <input type="file" name="upload_image" id="upload_image" data-toggle="modal" data-target="#basicModal"/>
                                            </div>
                                        </div>
                                    </div>
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
                                                'placeholder' =>'price',
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
                                    <!--                                    <div class="form-group row">-->
                                    <!--                                        <label class="col-lg-4 col-form-label" for="val-suggestions">Suggestions <span class="text-danger">*</span>-->
                                    <!--                                        </label>-->
                                    <!--                                        <div class="col-lg-6">-->
                                    <!--                                            <textarea class="form-control" id="val-suggestions" name="val-suggestions" rows="5" placeholder="What would you like to see?"></textarea>-->
                                    <!--                                        </div>-->
                                    <!--                                    </div>-->
                                    <!--                                    <div class="form-group row">-->
                                    <!--                                        <label class="col-lg-4 col-form-label" for="val-skill">Best Skill <span class="text-danger">*</span>-->
                                    <!--                                        </label>-->
                                    <!--                                        <div class="col-lg-6">-->
                                    <!--                                            <select class="form-control" id="val-skill" name="val-skill">-->
                                    <!--                                                <option value="">Please select</option>-->
                                    <!--                                                <option value="html">HTML</option>-->
                                    <!--                                                <option value="css">CSS</option>-->
                                    <!--                                                <option value="javascript">JavaScript</option>-->
                                    <!--                                                <option value="angular">Angular</option>-->
                                    <!--                                                <option value="angular">React</option>-->
                                    <!--                                                <option value="vuejs">Vue.js</option>-->
                                    <!--                                                <option value="ruby">Ruby</option>-->
                                    <!--                                                <option value="php">PHP</option>-->
                                    <!--                                                <option value="asp">ASP.NET</option>-->
                                    <!--                                                <option value="python">Python</option>-->
                                    <!--                                                <option value="mysql">MySQL</option>-->
                                    <!--                                            </select>-->
                                    <!--                                        </div>-->
                                    <!--                                    </div>-->
                                    <!--                                    <div class="form-group row">-->
                                    <!--                                        <label class="col-lg-4 col-form-label" for="val-currency">Currency <span class="text-danger">*</span>-->
                                    <!--                                        </label>-->
                                    <!--                                        <div class="col-lg-6">-->
                                    <!--                                            <input type="text" class="form-control" id="val-currency" name="val-currency" placeholder="$21.60">-->
                                    <!--                                        </div>-->
                                    <!--                                    </div>-->
                                    <!--                                    <div class="form-group row">-->
                                    <!--                                        <label class="col-lg-4 col-form-label" for="val-website">Website <span class="text-danger">*</span>-->
                                    <!--                                        </label>-->
                                    <!--                                        <div class="col-lg-6">-->
                                    <!--                                            <input type="text" class="form-control" id="val-website" name="val-website" placeholder="http://example.com">-->
                                    <!--                                        </div>-->
                                    <!--                                    </div>-->
                                    <!--                                    <div class="form-group row">-->
                                    <!--                                        <label class="col-lg-4 col-form-label" for="val-phoneus">Phone (US) <span class="text-danger">*</span>-->
                                    <!--                                        </label>-->
                                    <!--                                        <div class="col-lg-6">-->
                                    <!--                                            <input type="text" class="form-control" id="val-phoneus" name="val-phoneus" placeholder="212-999-0000">-->
                                    <!--                                        </div>-->
                                    <!--                                    </div>-->
                                    <!--                                    <div class="form-group row">-->
                                    <!--                                        <label class="col-lg-4 col-form-label" for="val-digits">Digits <span class="text-danger">*</span>-->
                                    <!--                                        </label>-->
                                    <!--                                        <div class="col-lg-6">-->
                                    <!--                                            <input type="text" class="form-control" id="val-digits" name="val-digits" placeholder="5">-->
                                    <!--                                        </div>-->
                                    <!--                                    </div>-->
                                    <!--                                    <div class="form-group row">-->
                                    <!--                                        <label class="col-lg-4 col-form-label" for="val-number">Number <span class="text-danger">*</span>-->
                                    <!--                                        </label>-->
                                    <!--                                        <div class="col-lg-6">-->
                                    <!--                                            <input type="text" class="form-control" id="val-number" name="val-number" placeholder="5.0">-->
                                    <!--                                        </div>-->
                                    <!--                                    </div>-->
                                    <!--                                    <div class="form-group row">-->
                                    <!--                                        <label class="col-lg-4 col-form-label" for="val-range">Range [1, 5] <span class="text-danger">*</span>-->
                                    <!--                                        </label>-->
                                    <!--                                        <div class="col-lg-6">-->
                                    <!--                                            <input type="text" class="form-control" id="val-range" name="val-range" placeholder="4">-->
                                    <!--                                        </div>-->
                                    <!--                                    </div>-->
                                    <!--                                    <div class="form-group row">-->
                                    <!--                                        <label class="col-lg-4 col-form-label"><a href="#">Terms &amp; Conditions</a>  <span class="text-danger">*</span>-->
                                    <!--                                        </label>-->
                                    <!--                                        <div class="col-lg-8">-->
                                    <!--                                            <label class="css-control css-control-primary css-checkbox" for="val-terms">-->
                                    <!--                                                <input type="checkbox" class="css-control-input" id="val-terms" name="val-terms" value="1"> <span class="css-control-indicator"></span> I agree to the terms</label>-->
                                    <!--                                        </div>-->
                                    <!--                                    </div>-->
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

</div>



</body>

</html>
<!--id="uploadimageModal"-->
<div class="modal fade" id="basicModal">
    <div class="modal-dialog">
        <div class="modal-content" style="width: 700px">
            <div class="modal-header">
                <h5 class="modal-title">Crop & Upload Image</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <div id="image_demo" style="margin-top:30px"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary crop_image" data-dismiss="modal">Save changes</button>
            </div>
        </div>
    </div>
</div>
