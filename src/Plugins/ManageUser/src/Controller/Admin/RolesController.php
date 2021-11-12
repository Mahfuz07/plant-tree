<?php
namespace ManageUser\Controller\Admin;

use ManageUser\Controller\AppController;

/**
 * Roles Controller
 *
 * @property \ManageUser\Model\Table\RolesTable $Roles
 *
 * @method \ManageUser\Model\Entity\Role[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class RolesController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $query = $this->Roles->find()->order('Roles.id');

        $this->set('roles', $this->paginate($query));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $role = $this->Roles->newEmptyEntity();
        if ($this->request->is('post')) {
            $role = $this->Roles->patchEntity($role, $this->request->getData());
            if ($this->Roles->save($role)) {
                $this->Flash->adminSuccess('The role has been saved.', ['key'=>'admin_success']);

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->adminError('The role could not be saved. Please, try again.', ['key'=>'admin_error']);
        }

        $form_title = 'Add';

        $this->set(compact('role', 'form_title'));

        $this->render('form');
    }

    /**
     * Edit method
     *
     * @param string|null $id Role id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $role = $this->Roles->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $role = $this->Roles->patchEntity($role, $this->request->getData());
            if ($this->Roles->save($role)) {
                $this->Flash->adminSuccess('The role has been saved.', ['key'=>'admin_success']);
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->adminError('The role could not be saved. Please, try again.', ['key'=>'admin_error']);
        }

        $form_title = 'Edit';

        $this->set(compact('role', 'form_title'));

        $this->render('form');

    }

    /**
     * Delete method
     *
     * @param string|null $id Role id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $role = $this->Roles->get($id);
        if ($this->Roles->delete($role)) {
            $this->Flash->adminSuccess('The role has been deleted.', ['key'=>'admin_success']);
        } else {
            $this->Flash->adminError('The role could not be deleted. Please, try again.', ['key'=>'admin_error']);
        }

        return $this->redirect(['action' => 'index']);
    }
}
