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
                    <li class="breadcrumb-item"><a href="javascript:void(0)">News</a></li>
                </ol>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">News List</h4>
                            <div class="">
                                <?= $this->Html->link('Add News',['controller' => 'News' , 'action' => 'add'], ['class' => 'btn btn-primary']) ?>
                            </div>


                            <?php if (!empty($newsList)) {?>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Display Name</th>
                                            <th>Title</th>
                                            <th>Image</th>
                                            <th>Published</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($newsList as $news) { ?>
                                        <tr>
                                            <td><?= $news['id'] ?></td>
                                            <td><?= $news['title'] ?></td>
                                            <td><?= $news['display_name'] ?></td>
                                            <td ><img src="<?= Router::fullBaseUrl() . '/' . $news['news_image'] ;?>" alt="news image" width="60" height="60"/></td>
                                            <td>publish</td>
                                            <td><?= $this->Html->link('Edit',['controller' => 'News' , 'action' => 'edit', $news['id']], ['style' => 'transition: all 0.4s ease-in-out; color: #7571f9;']) ?>
                                                &nbsp;&nbsp;&nbsp;
                                                <?= $this->Html->link('Delete',['controller' => 'News' , 'action' => 'delete', $news['id']], ['style' => 'transition: all 0.4s ease-in-out; color: #7571f9;']) ?></td>
                                        </tr>
                                    <?php } ?>

                                    </tbody>
                                    <tfoot>
                                    </tfoot>
                                </table>
                            </div>
                            <?php } else { ?>

                                <h2> News Empty</h2>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- #/ container -->
    </div>

