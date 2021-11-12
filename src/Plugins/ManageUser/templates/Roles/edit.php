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
                                        <?= __('Edit Role') ?>
                                    </div>
                                    <div class="container-fluid panel-body">

                                        <?php
                                                                                    echo ' <div class="col-sm-6">
                                        <div class="inputs">';                                                    echo $this->Form->control('name', ['class' => 'form-control']);
                                            echo '
                                        </div>
                                    </div>
                                    ';                                            echo ' <div class="col-sm-6">
                                        <div class="inputs">';                                                    echo $this->Form->control('slug', ['class' => 'form-control']);
                                            echo '
                                        </div>
                                    </div>
                                    ';                                            echo ' <div class="col-sm-6">
                                        <div class="inputs">';                                                    echo $this->Form->control('descriptions', ['class' => 'form-control']);
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

    <footer class="footer event-create-control-section">
        <div class="flex-container">
            <?php echo $this->Html->link('Cancel', "/", ['class' => 'btn btn-default reset inline-btn
            btn-cancel']); ?>
            <div class="flex-item">
                <?php echo $this->Form->button(__('Submit'),['class'=>'btn mr5 waves-effect']); ?>
            </div>
        </div>
    </footer>

    <?php echo $this->Form->end(); ?>
</section>