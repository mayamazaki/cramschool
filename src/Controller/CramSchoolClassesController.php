<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Auth\DefaultPasswordHasher;

/**
 * CramSchoolClasses Controller
 *
 * @property \App\Model\Table\CramSchoolClassesTable $CramSchoolClasses
 *
 * @method \App\Model\Entity\CramSchoolClass[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CramSchoolClassesController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['CramSchools'],
            'conditions' => [
                'CramSchoolClasses.cram_school_id' => $this->Auth->user('id'),
                'CramSchoolClasses.is_valid' => 1 // 1.有効
            ],
            'order' => [
                'CramSchoolClasses.disp_no' => 'asc' // 表示順
            ]
        ];
        $cramSchoolClasses = $this->paginate($this->CramSchoolClasses);

        $this->set(compact('cramSchoolClasses'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $cramSchoolClass = $this->CramSchoolClasses->newEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $data['cram_school_id'] = $this->Auth->user('id');
            $data['host'] = $_SERVER['REMOTE_ADDR']; // IP
            $cramSchoolClass = $this->CramSchoolClasses->patchEntity($cramSchoolClass, $data);
            if ($this->CramSchoolClasses->save($cramSchoolClass)) {
                $this->Flash->success(__('クラスを登録しました。'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('入力項目にエラーがあります。'));
        }
        $cramSchools = $this->CramSchoolClasses->CramSchools->find('list', ['limit' => 200]);
        $this->set(compact('cramSchoolClass', 'cramSchools'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Cram School Class id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $cramSchoolClass = $this->CramSchoolClasses->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $data['cram_school_id'] = $this->Auth->user('id');
            $data['host'] = $_SERVER['REMOTE_ADDR']; // IP
            if (empty($data['password'])) {
                // パスワード未変更
                // パスワードのバリデーションを解除
                unset($data['password']);
                $this->CramSchoolClasses->getValidator('default')->offsetUnset('password');
            }
            $cramSchoolClass = $this->CramSchoolClasses->patchEntity($cramSchoolClass, $data);
            if ($this->CramSchoolClasses->save($cramSchoolClass)) {
                $this->Flash->success(__('クラスを更新しました。'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('入力項目にエラーがあります。'));
        }
        $cramSchools = $this->CramSchoolClasses->CramSchools->find('list', ['limit' => 200]);
        $this->set(compact('cramSchoolClass', 'cramSchools'));
    }

    /**
     * Invalid method
     *
     * @param string|null $id Cram School Class id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function invalid($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $cramSchoolClasse = $this->CramSchoolClasses->get($id);
        $data = [];
        $data['is_valid'] = 0; // 0.無効
        $cramSchoolClasse = $this->CramSchoolClasses->patchEntity($cramSchoolClasse, $data);
        if ($this->CramSchoolClasses->save($cramSchoolClasse)) {
            $this->Flash->success(__('クラスを削除しました。'));
        } else {
            $this->Flash->error(__('クラスの削除に失敗しました。'));
        }
        return $this->redirect(['action' => 'index']);
    }

}
