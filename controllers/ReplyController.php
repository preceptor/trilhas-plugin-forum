<?php
class Forum_ReplyController extends Tri_Controller_Action
{
    public function init()
    {
        parent::init();
        $this->view->title = "Forum";
    }

    public function indexAction()
    {
        $session   = new Zend_Session_Namespace('data');
        $table     = new Tri_Db_Table('forum_reply');
        $forum     = new Tri_Db_Table('forum');
        $subscribe = new Tri_Db_Table('forum_subscribe');
        $page      = Zend_Filter::filterStatic($this->_getParam('page', -1), 'int');
        $id        = Zend_Filter::filterStatic($this->_getParam('id'), 'int');
        $form      = new Forum_Form_Reply();
        $identity  = Zend_Auth::getInstance()->getIdentity();

        $select = $table->select(true)
                        ->setIntegrityCheck(false)
                        ->join('user', 'user.id = user_id', array('user.id as uid','user.name','user.image','user.role'))
                        ->where('forum_id = ?', $id)
                        ->where('forum_reply_id IS NULL')
                        ->order('id');

        $form->populate(array('forum_id' => $id));

        Forum_Model_Access::update($id, $identity->id);

        $this->view->data      = $table->fetchAll($select);
        $this->view->parent    = $forum->find($id)->current();
        $this->view->form      = $form;
        $this->view->page      = $page;
        $this->view->subscribe = $subscribe->fetchRow(array('forum_id = ?' => $id,
                                                            'user_id = ?' => $identity->id));
    }
    
    public function timeAction()
    {
        $session   = new Zend_Session_Namespace('data');
        $table     = new Tri_Db_Table('forum_reply');
        $forum     = new Tri_Db_Table('forum');
        $subscribe = new Tri_Db_Table('forum_subscribe');
        $page      = Zend_Filter::filterStatic($this->_getParam('page'), 'int');
        $id        = Zend_Filter::filterStatic($this->_getParam('id'), 'int');
        $quantity  = Zend_Filter::filterStatic($this->_getParam('quantity'), 'int');
        $order     = Zend_Filter::filterStatic($this->_getParam('order', 'ASC'), 'alpha');
        $form      = new Forum_Form_Reply();
        $identity  = Zend_Auth::getInstance()->getIdentity();

        if ($quantity == -1) {
            $page = 1;
            $quantity = 999999;
        }

        if ($page == -1) {
            $page = 999999;
        }
        
        $select = $table->select(true)
                        ->setIntegrityCheck(false)
                        ->join('user', 'user.id = user_id', array('user.id as uid','user.name','user.image','user.role'))
                        ->where('forum_id = ?', $id)
                        ->order('id ' . $order);

        $form->populate(array('forum_id' => $id));

        Forum_Model_Access::update($id, $identity->id);

        $paginator = new Tri_Paginator($select, $page, $quantity);
        $this->view->data      = $paginator->getResult();
        $this->view->parent    = $forum->find($id)->current();
        $this->view->form      = $form;
        $this->view->page      = $page;
        $this->view->quantity  = $quantity;
        $this->view->order     = $order;
        $this->view->subscribe = $subscribe->fetchRow(array('forum_id = ?' => $id,
                                                            'user_id = ?' => $identity->id));
    }
    
    public function findAction()
    {
        $session   = new Zend_Session_Namespace('data');
        $table     = new Tri_Db_Table('forum_reply');
        $identity  = Zend_Auth::getInstance()->getIdentity();
        $query     = Zend_Filter::filterStatic($this->_getParam("query"), 'stripTags');
        $page      = Zend_Filter::filterStatic($this->_getParam('page', 0), 'int');
        
        $select = $table->select(true)
                        ->setIntegrityCheck(false)
                        ->join('user', 'user.id = user_id', array('user.id as uid','user.name','user.image','user.role'))
                        ->join('forum', 'forum.id = forum_id', array())
                        ->where('forum.classroom_id = ?', $session->classroom_id)
                        ->where('(user.name  LIKE(?)', "%$query%")
                        ->orWhere('forum_reply.description LIKE(?))', "%$query%")
                        ->order('forum_reply.id DESC');

        $paginator = new Tri_Paginator($select, $page);
        $this->view->data = $paginator->getResult();
        $this->view->page = $page;
        $this->view->query = $query;
    }

    public function formAction()
    {
        $id      = Zend_Filter::filterStatic($this->_getParam('id'), 'int');
        $form    = new Forum_Form_Reply();

        if ($id) {
            $table = new Tri_Db_Table('forum_reply');
            $row   = $table->find($id)->current();

            if ($row) {
                $form->populate($row->toArray());
            }
        }

        $this->view->form = $form;
    }

    public function saveAction()
    {
        $form  = new Forum_Form_Reply();
        $table = new Tri_Db_Table('forum_reply');
        $session = new Zend_Session_Namespace('data');
        $data  = $this->_getAllParams();

        if ($form->isValid($data)) {
            $data = $form->getValues();
            $data['user_id'] = Zend_Auth::getInstance()->getIdentity()->id;

            if (!$data['forum_reply_id']) {
                unset($data['forum_reply_id']);
            }
            
            if (isset($data['id']) && $data['id']) {
                $row = $table->find($data['id'])->current();
                $row->updated = date('Y-m-d H:i:s');
                $row->setFromArray($data);
                $id = $row->save();
            } else {
                unset($data['id']);
                $row = $table->createRow($data);
                $id = $row->save();
                Forum_Model_Reply::sendMail($id);
            }

            $this->_helper->_flashMessenger->addMessage('Success');
            $this->_redirect('forum/reply/index/id/'.$data['forum_id']);
        }

        $this->_helper->_flashMessenger->addMessage('Error');
        $this->view->form = $form;
        $this->render('form');
    }

    public function deleteAction()
    {
        $table   = new Tri_Db_Table('forum_reply');
        $id      = Zend_Filter::filterStatic($this->_getParam('id'), 'int');
        $forumId = Zend_Filter::filterStatic($this->_getParam('forumId'), 'int');

        try {
            if ($id) {
                $table->delete(array('id = ?' => $id));
                $this->_helper->_flashMessenger->addMessage('Success');
            }
        } catch(Exception $e) {
            $this->_helper->_flashMessenger->addMessage('Failed to delete. Forum with dependents.');
        }
        
        $this->_redirect('forum/reply/index/id/'.$forumId);
    }
}