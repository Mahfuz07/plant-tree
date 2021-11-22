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
                                            <td ><img src="<?= $product['image'] ;?>" alt="Girl in a jacket" width="60" height="60"/></td>
                                            <td><?= $product['description'] ?></td>
                                            <td><?= $product['price'] ?></td>
                                            <td><?= $product['published'] ?></td>
                                            <td><?= $this->Html->link('Edit',['controller' => 'Products' , 'action' => 'edit', $product['id']], ['style' => 'transition: all 0.4s ease-in-out; color: #7571f9;']) ?>
                                                &nbsp;&nbsp;&nbsp;
                                                <?= $this->Html->link('Delete',['controller' => 'Products' , 'action' => 'edit', $product['id']], ['style' => 'transition: all 0.4s ease-in-out; color: #7571f9;']) ?></td>
                                        </tr>
                                    <?php } ?>
<!--                                    <tr>-->
<!--                                        <td>Garrett Winters</td>-->
<!--                                        <td>Accountant</td>-->
<!--                                        <td>Tokyo</td>-->
<!--                                        <td>63</td>-->
<!--                                        <td>2011/07/25</td>-->
<!--                                        <td>$170,750</td>-->
<!--                                    </tr>-->
<!--                                    <tr>-->
<!--                                        <td>Ashton Cox</td>-->
<!--                                        <td>Junior Technical Author</td>-->
<!--                                        <td>San Francisco</td>-->
<!--                                        <td>66</td>-->
<!--                                        <td>2009/01/12</td>-->
<!--                                        <td>$86,000</td>-->
<!--                                    </tr>-->
<!--                                    <tr>-->
<!--                                        <td>Cedric Kelly</td>-->
<!--                                        <td>Senior Javascript Developer</td>-->
<!--                                        <td>Edinburgh</td>-->
<!--                                        <td>22</td>-->
<!--                                        <td>2012/03/29</td>-->
<!--                                        <td>$433,060</td>-->
<!--                                    </tr>-->
<!--                                    <tr>-->
<!--                                        <td>Airi Satou</td>-->
<!--                                        <td>Accountant</td>-->
<!--                                        <td>Tokyo</td>-->
<!--                                        <td>33</td>-->
<!--                                        <td>2008/11/28</td>-->
<!--                                        <td>$162,700</td>-->
<!--                                    </tr>-->
<!--                                    <tr>-->
<!--                                        <td>Brielle Williamson</td>-->
<!--                                        <td>Integration Specialist</td>-->
<!--                                        <td>New York</td>-->
<!--                                        <td>61</td>-->
<!--                                        <td>2012/12/02</td>-->
<!--                                        <td>$372,000</td>-->
<!--                                    </tr>-->
<!--                                    <tr>-->
<!--                                        <td>Herrod Chandler</td>-->
<!--                                        <td>Sales Assistant</td>-->
<!--                                        <td>San Francisco</td>-->
<!--                                        <td>59</td>-->
<!--                                        <td>2012/08/06</td>-->
<!--                                        <td>$137,500</td>-->
<!--                                    </tr>-->
<!--                                    <tr>-->
<!--                                        <td>Rhona Davidson</td>-->
<!--                                        <td>Integration Specialist</td>-->
<!--                                        <td>Tokyo</td>-->
<!--                                        <td>55</td>-->
<!--                                        <td>2010/10/14</td>-->
<!--                                        <td>$327,900</td>-->
<!--                                    </tr>-->


                                    </tbody>
                                    <tfoot>
<!--                                    <tr>-->
<!--                                        <th>Name</th>-->
<!--                                        <th>Position</th>-->
<!--                                        <th>Office</th>-->
<!--                                        <th>Age</th>-->
<!--                                        <th>Start date</th>-->
<!--                                        <th>Salary</th>-->
<!--                                    </tr>-->
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

</div>

</body>

</html>
