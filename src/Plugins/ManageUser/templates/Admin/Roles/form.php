<section class="workspace">
    <?php echo $this->Form->create($role, ['class' => 'form-horizontal', 'id' => 'roles-form']); ?>
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
                                        <?php echo $form_title . " Role "; ?>
                                    </div>
                                    <div class="container-fluid panel-body">

                                        <div class="col-sm-6">
                                            <div class="inputs">
                                                <?php echo $this->Form->input('title', array('type' => 'text', 'div' => 'false', 'class' => 'form-control')); ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="inputs">
                                                <label for="Role_name">Alias</label>
                                                <?php echo $this->Form->input('alias', array('type' => 'text', 'div' => 'false', 'class' => 'form-control', 'label' => false)); ?>
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

        <footer class="footer event-create-control-section">
            <div class="flex-container">

                <?php echo $this->Html->link('Cancel', "/admin/roles", ['class' => 'btn btn-default reset inline-btn btn-cancel']); ?>

                <div class="flex-item">
                    <?php echo $this->Form->button(__('Save'), ['class' => 'btn mr5 waves-effect']); ?>
                </div>

            </div>
        </footer>
    </div>

    <?php echo $this->Form->end(); ?>
</section>

<script type="text/javascript">
    $("#roles-form").validate({
        rules: {
            "title": "required",
            "alias": "required"
        },
        messages: {
            "title": "Please select Title",
            "alias": "Please select Alias"
        }
    });

</script>



<style>
    .clear {
        clear: both;
        padding: 0;
        margin: 0;
    }
</style>