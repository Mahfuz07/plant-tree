<section class="workspace">
    <?= $this->Form->create($role) ?>
    <div class="workspace-body page page-ui-tables">

        <!-- Basic form -->
        <div class="main-container">
            <div class="content">
                <div class="page-wrap">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel mb20 panel-default panel-hovered panel-stacked">
                                    <div class="panel-heading">
                                        <?= __('View Role') ?>
                                    </div>
                                    <div class="container-fluid panel-body">

                                        <?php
                                                                                    echo ' <div class="col-sm-6">
                                        <div class="inputs">';                                                    echo $this->Form->control('name', ['class' => 'form-control', 'readonly'=>true]);
                                            echo '
                                        </div>
                                    </div>
                                    ';                                            echo ' <div class="col-sm-6">
                                        <div class="inputs">';                                                    echo $this->Form->control('slug', ['class' => 'form-control', 'readonly'=>true]);
                                            echo '
                                        </div>
                                    </div>
                                    ';                                            echo ' <div class="col-sm-6">
                                        <div class="inputs">';                                                    echo $this->Form->control('descriptions', ['class' => 'form-control', 'readonly'=>true]);
                                            echo '
                                        </div>
                                    </div>
                                    ';                                    ?>

                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php echo $this->Form->end(); ?>
</section>