<?php

class Forum_SubscribeController extends Tri_Controller_Action
{
    public function inAction()
    {
        $table   = new Tri_Db_Table('forum_subscribe');
        $forumId = Zend_Filter::filterStatic($this->_getParam('id'), 'int');
        $userId  = Zend_Auth::getInstance()->getIdentity()->id;

        $table->createRow(array('user_id' => $userId, 'forum_id' => $forumId))
              ->save();

        $this->_helper->_flashMessenger->addMessage('Success');
        $this->_redirect('forum/reply/index/id/' . $forumId);
    }

    public function outAction()
    {
        $table   = new Tri_Db_Table('forum_subscribe');
        $forumId = Zend_Filter::filterStatic($this->_getParam('id'), 'int');
        $userId  = Zend_Auth::getInstance()->getIdentity()->id;

        $table->delete(array('user_id = ?' => $userId, 'forum_id = ?' => $forumId));

        $this->_helper->_flashMessenger->addMessage('Success');
        $this->_redirect('forum/reply/index/id/' . $forumId);
    }
}