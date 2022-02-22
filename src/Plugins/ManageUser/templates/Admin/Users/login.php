
<!DOCTYPE html>
<html class="h-100" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Quixlab - Bootstrap Admin Dashboard Template by Themefisher.com</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="../../assets/images/favicon.png">
    <!-- <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous"> -->
    <link href="/webroot/css/style.css" rel="stylesheet">

</head>

<body class="h-100">

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





<div class="login-form-bg h-100">
    <div class="container h-100">
        <div class="row justify-content-center h-100">
            <div class="col-xl-6">
                <div class="form-input-content">
                    <div class="card login-form mb-0">
                        <div class="card-body pt-5">
                            <a class="text-center" href="index.html"> <h4>Login</h4></a>

                            <div class="row">
                                <div class="col-md-12">
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
                                </div>
                            </div>

                            <div class="mt-5 mb-5 login-input">
                                <?php echo $this->Form->create(null, ['id' => 'admin-login-form']); ?>
                                <div class="form-group">
                                    <?php
                                    echo $this->Form->input('email', array(
                                        'type' =>'email',
                                        'class' =>'form-control',
                                        'id' =>'email',
                                        'label' =>false,
                                        'placeholder' =>'Email'
                                    ));
                                    ?>
                                </div>
                                <div class="form-group">
                                    <?php
                                    echo $this->Form->input('password', array(
                                        'type' =>'password',
                                        'class' =>'form-control',
                                        'label' =>false,
                                        'id' =>'password',
                                        'placeholder' =>'Password'
                                    ));

                                    ?>
                                </div>
                                <button class="btn login-form__btn submit w-100">Sign In</button>
                                <?php echo $this->Form->end() ?>
                            </div>
<!--                            <p class="mt-5 login-form__footer">Dont have account? <a href="page-register.html" class="text-primary">Sign Up</a> now</p>-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




<!--**********************************
    Scripts
***********************************-->

</body>
</html>





