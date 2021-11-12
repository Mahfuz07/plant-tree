<div class="workspace-dashboard page page-ui-tables">
    <div class="col-md-12">
        <?php echo $this->Flash->render('admin_success'); ?>
        <?php echo $this->Flash->render('admin_error'); ?>
    </div>

    <div class="page-heading">
        <div class="flex-container">
            <div class="flex-item">
                <h4><?= __('Role') ?></h4>
            </div>
            <div class="flex-item">
                <a href="<?php echo $this->Url->build(['action' => 'add']) ?>" class="add-event-btn"
                   title="New <?= __('Role') ?>">
                    <span class='icon'>+</span> Add <?= __('Role') ?>
                </a>
            </div>
        </div>
    </div>

    <div class="event-listing">
        <div class="table-responsive table-part">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('name') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('slug') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('descriptions') ?></th>
                    <th scope="col" class="actions"><?= __('Actions') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($roles as $role): ?>
                    <tr>
                        <td><?= $this->Number->format($role->id) ?></td>
                        <td><?= h($role->name) ?></td>
                        <td><?= h($role->slug) ?></td>
                        <td><?= h($role->descriptions) ?></td>
                        <td class="actions">
                            <?= $this->Html->link(__('View'), ['action' => 'view', $role->id]) ?>
                            <?= $this->Html->link(__('Edit'), ['action' => 'edit', $role->id]) ?>
                            <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $role->id], ['confirm' =>
                                __('Are you sure you want to delete {0}?', $role->name)]) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

        </div>
        <div class="bottom-pagination">
            <div class="pagination-area flex-container">
                <div class="pagination-status-text">
                    <?php echo $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}}
                    total')]); ?>
                </div>
                <ul class="pagination">
                    <?= $this->Paginator->first('<< ' . __('first')) ?>
                    <?= $this->Paginator->prev('< ' . __('previous')) ?>
                    <?php echo $this->Paginator->numbers() ?>
                    <?= $this->Paginator->next(__('next') . ' >') ?>
                    <?= $this->Paginator->last(__('last') . ' >>') ?>
                </ul>
            </div>
        </div>

    </div>
</div>