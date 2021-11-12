<?php
    use  Acl\Libs\AclHelper;
?>

<div class="workspace-dashboard page page-ui-tables">
    <div class="page-heading">
        <div class="flex-container">
            <div class="flex-item"><h4>Roles</h4></div>
            <div class="flex-item">
                <div class="flex-container">
                    <?php
                    AclHelper::link($this,
                        '<span class="icon">+</span> New Roles',
                        ['plugin' => 'ManageUser', 'controller' => 'Roles', 'action' => 'add', 'prefix' => 'admin'],
                        ['class' => 'add-event-btn', 'escapeTitle' => false, 'title' => 'Add Roles']
                    );

                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <?php echo $this->Flash->render('admin_success'); ?>
        <?php echo $this->Flash->render('admin_error'); ?>
    </div>

    <!-- Basic Table -->
    <div class="event-listing">
        <div class="table-responsive table-part">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th><?php echo $this->Paginator->sort('id'); ?></th>
                    <th><?php echo $this->Paginator->sort('title'); ?></th>
                    <th><?php echo $this->Paginator->sort('alias'); ?></th>
                    <th class="actions"><?php echo __('Actions') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php if(!empty($roles)){

                    foreach ($roles as $v){
                        ?>
                        <tr>
                            <td><?php echo h($v->id); ?></td>
                            <td><?php echo h($v->title); ?></td>
                            <td><?php echo $v->alias; ?></td>

                            <td class="actions" style="width: 204px;">
                                <div class="dropdown action-button">
										<span class="dropdown-toggle event-action" type="button" data-toggle="dropdown" >
											<img src="/css/admin_styles/images/dashboard-settings-sm.png" alt="">
										</span>
                                    <ul class="dropdown-menu action-dropdown">
                                        <li>
                                            <?php
                                            AclHelper::link($this,
                                                '<span class="fa fa-pencil-square"></span> Edit',
                                                ['plugin' => 'ManageUser', 'controller' => 'Roles', 'action' => 'edit', $v->id],
                                                ['escapeTitle' => false, 'title' => 'Edit this item']
                                            );

                                            if(AclHelper::hasAccess('Roles', 'delete', 'admin')) {
                                                echo $this->Form->postLink(
                                                    '<span class="fa fa-trash"></span> Delete',
                                                    "/admin/roles/delete/" . $v->id,
                                                    ['escapeTitle' => false, 'title' => 'Delete Page', 'confirm' => __('Are you sure you want to delete # {0}?', $v->id)]
                                                );
                                            }
                                            ?>
                                        </li>
                                    </ul>
                                </div>

                            </td>
                        </tr>
                        <?php

                    }}; ?>


                </tbody>
            </table>
        </div>

        <?php echo $this->element('admin/pagination'); ?>

    </div>
</div>