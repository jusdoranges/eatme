<?php
App::uses('AppController', 'Controller');
App::import('Model', 'User');
/**
 * Questions Controller
 *
 * @property Question $Question
 */
class QuestionsController extends AppController {

	public function isAuthorized($user){
			
		if($this->action == "quizz"){
			if($this->Auth->loggedIn())
			return true;
		}

		
		return parent::isAuthorized($user);
	}


/**
 * quizz method
 * @author jaouad
 * @return void
 */
	public function quizz() {
		$rep = $questz = null;
		$score = 0;
		foreach ($this->Question->find('all', array('order' => 'rand()', 'limit' => 10)) as $v)
			$questz[$v['Question']['id']] = $v['Question'];

		if(isset($this->request->data['qu']))
			foreach($this->request->data['qu'] as $k => $z) {
				$rep[$k] = $z;
				if($questz[$k]['soluce'] == $z)
					$score++;
			}
		
		if($rep)
			$this->set('bien', $rep);

		$user = new User();
		//$me = $user->findAllById($this->Auth->user('id'));
		//$me = $me[0];

		$user->id = $this->Auth->user('id');
		$user->saveField('point', $score);

		$this->set('score', $score);
		$this->Session->write('lvl', $score);
		
		$this->Question->recursive = 0;
		$this->set('questions', $questz);	
		
	}
	
	
/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Question->recursive = 0;
		$this->set('questions', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Question->exists($id)) {
			throw new NotFoundException(__('Invalid question'));
		}
		$options = array('conditions' => array('Question.' . $this->Question->primaryKey => $id));
		$this->set('question', $this->Question->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Question->create();
			if ($this->Question->save($this->request->data)) {
				$this->Session->setFlash(__('The question has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The question could not be saved. Please, try again.'));
			}
		}
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->Question->exists($id)) {
			throw new NotFoundException(__('Invalid question'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Question->save($this->request->data)) {
				$this->Session->setFlash(__('The question has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The question could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Question.' . $this->Question->primaryKey => $id));
			$this->request->data = $this->Question->find('first', $options);
		}
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Question->id = $id;
		if (!$this->Question->exists()) {
			throw new NotFoundException(__('Invalid question'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->Question->delete()) {
			$this->Session->setFlash(__('Question deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Question was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
