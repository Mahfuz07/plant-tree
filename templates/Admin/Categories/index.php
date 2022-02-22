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
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Categories</a></li>
                </ol>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Categories</h4>
                            <div class="">
                                <?= $this->Html->link('Add Category',['controller' => 'Categories' , 'action' => 'add'], ['class' => 'btn btn-primary']) ?>
                            </div>


                            <?php if (!empty($categories)) {?>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered zero-configuration">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Display Name</th>
                                            <th>description</th>
                                            <th>Published</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($categories as $category) { ?>
                                            <tr>
                                                <td><?= $category['id'] ?></td>
                                                <td><?= $category['title'] ?></td>
                                                <td><?= $category['display_name'] ?></td>
                                                <td><?= $category['description'] ?></td>
                                                <td><?= $category['published'] ?></td>
                                                <td><?= $this->Html->link('Edit',['controller' => 'Categories' , 'action' => 'edit', $category['id']], ['style' => 'transition: all 0.4s ease-in-out; color: #7571f9;']) ?>
                                                    &nbsp;&nbsp;&nbsp;
                                                    <?= $this->Html->link('Delete',['controller' => 'Categories' , 'action' => 'edit', $category['id']], ['style' => 'transition: all 0.4s ease-in-out; color: #7571f9;']) ?></td>
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

