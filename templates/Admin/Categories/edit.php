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
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Category</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Add Categories</a></li>
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
                                <?php echo $this->Form->create($category, ['id' => 'user-password-form']); ?>
                                <div class="form-valide" action="#" method="post">
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label" for="val-category_name">Title <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-lg-6">
<!--                                            <input type="text" class="form-control" id="val-display-name" name="val-display-name" placeholder="Enter a Display Name..">-->
                                            <?php
                                            echo $this->Form->input('title', array(
                                                'type' =>'text',
                                                'class' =>'form-control',
                                                'id' =>'val-category_name',
                                                'label' =>false,
                                                'placeholder' =>'Title',
                                                'required' => true
                                            ));
                                            ?>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label" for="val-display_name">Display Name <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-lg-6">
                                            <!--                                            <input type="text" class="form-control" id="val-display-name" name="val-display-name" placeholder="Enter a Display Name..">-->
                                            <?php
                                            echo $this->Form->input('display_name', array(
                                                'type' =>'text',
                                                'class' =>'form-control',
                                                'id' =>'val-display_name',
                                                'label' =>false,
                                                'placeholder' =>'Enter a Display Name..',
                                                'required' => true
                                            ));
                                            ?>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label" for="val-email">slug <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-lg-6">
<!--                                            <input type="text" class="form-control" id="val-email" name="val-email" placeholder="Your valid email..">-->
                                            <?php
                                            echo $this->Form->input('slug', array(
                                                'type' =>'text',
                                                'class' =>'form-control',
                                                'id' =>'val-slug',
                                                'label' =>false,
                                                'placeholder' =>'slug',
                                                'required' => true
                                            ));
                                            ?>
                                            <span class="registrationFormAlert" style="color:#dc0000;" id="Exist"></span>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label" for="val-description">description <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-lg-6">
                                            <?php
                                            echo $this->Form->input('description', array(
                                                'type' =>'textarea',
                                                'class' =>'form-control',
                                                'id' =>'val-description',
                                                'label' =>false,
                                                'placeholder' =>'write a description',
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
                                                <input type="checkbox" class="css-control-input" id="val-terms" name="published" value="1" required> <span class="css-control-indicator"></span>  checkbox</label>
<!--                                            --><?php
//                                            echo $this->Form->checkbox('published', array('type' => 'checkbox', 'checked'=>false, 'class' => 'form-control', 'label' => 'Published'));
//                                            ?>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-lg-8 ml-auto">
                                            <button type="submit" id="submit" class="btn btn-primary">Submit</button>
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
