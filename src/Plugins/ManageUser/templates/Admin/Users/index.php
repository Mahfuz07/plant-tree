<?php
    //use Acl\Libs\AclHelper;

    if($this->request->isAjax())    {
        echo $this->element('admin/users_table');
        exit;
    }
?>
<div class="workspace-dashboard page page-ui-tables">
    <div class="page-heading">
        <div class="flex-container">
            <div class="flex-item"><h4>Users</h4></div>
            <div class="flex-item">
                <a href="<?php echo $this->Url->build(['action' => 'add']); ?>" class="add-event-btn" title="New User">
                    <span class="icon">+</span> New User
                </a>

            </div>
        </div>
    </div>
    <div class="col-md-12">
        <?php echo $this->Flash->render('admin_success'); ?>
        <?php echo $this->Flash->render('admin_error'); ?>
    </div>

    <!-- Basic Table -->
    <div class="event-listing">

        <div class="event-listing-top flex-container status-function">
            <div class="status-area flex-container">
                <div class="event-src-box">
                    <?php echo $this->element('admin/search'); ?>
                </div>
            </div>
        </div>

        <div id="table-data-wrap">
            <?php echo $this->element('admin/users_table'); ?>
        </div>

    </div>
</div>