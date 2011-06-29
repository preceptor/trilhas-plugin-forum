<?php
class Forum_IndexController extends Tri_Controller_Action
{
    public function init()
    {
        parent::init();
        $this->view->title = "Forum";
    }

    public function indexAction()
    {
        $session  = new Zend_Session_Namespace('data');
        $table    = new Tri_Db_Table('forum');
        $page     = Zend_Filter::filterStatic($this->_getParam('page'), 'int');
        $query    = Zend_Filter::filterStatic($this->_getParam("query"), 'stripTags');
        $select   = $table->select();

        Forum_Model_Forum::closeExpired();

        $select->where('classroom_id = ?', $session->classroom_id)
               ->where('begin < NOW()')
               ->order(array('status', 'begin'));

        if ($query) {
            $select->where('UPPER(title) LIKE UPPER(?)', "%$query%");
        }

        $paginator = new Tri_Paginator($select, $page);
        $this->view->data = $paginator->getResult();
        $this->view->q = $query;
    }

    public function formAction()
    {
        $id   = Zend_Filter::filterStatic($this->_getParam('id'), 'int');
        $form = new Forum_Form_Forum();

        if ($id) {
            $table = new Tri_Db_Table('forum');
            $row   = $table->find($id)->current();

            if ($row) {
                $form->populate($row->toArray());
            }
        }

        $this->view->form = $form;
    }

    public function saveAction()
    {
        $form  = new Forum_Form_Forum();
        $table = new Tri_Db_Table('forum');
        $session = new Zend_Session_Namespace('data');
        $data  = $this->_getAllParams();

        if ($form->isValid($data)) {
            $data = $form->getValues();
            $data['user_id']      = Zend_Auth::getInstance()->getIdentity()->id;
            $data['classroom_id'] = $session->classroom_id;

            if (!$data['end']) {
                unset($data['end']);
            }

            if (isset($data['id']) && $data['id']) {
                $row = $table->find($data['id'])->current();
                $row->setFromArray($data);
                $id = $row->save();
            } else {
                unset($data['id']);
                $row = $table->createRow($data);
                $id = $row->save();
                Application_Model_Timeline::save('created a new forum', $data['title']);
            }

            $this->_helper->_flashMessenger->addMessage('Success');
            $this->_redirect('forum/index/index/id/'.$id);
        }

        $this->_helper->_flashMessenger->addMessage('Error');
        $this->view->form = $form;
        $this->render('form');
    }

    public function viewAction()
    {
        $id = Zend_Filter::filterStatic($this->_getParam('id'), 'int');
        $this->_redirect('/forum/reply/index/layout/box/id/'.$id);
    }
}