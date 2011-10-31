<?php
/**
 * Trilhas - Learning Management System
 * Copyright (C) 2005-2010  Preceptor Educação a Distância Ltda. <http://www.preceptoead.com.br>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * @category   Forum
 * @package    Forum_Form_Reply
 * @copyright  Copyright (C) 2005-2010  Preceptor Educação a Distância Ltda. <http://www.preceptoead.com.br>
 * @license    http://www.gnu.org/licenses/  GNU GPL
 */
class Forum_Form_Reply extends Zend_Form
{
    /**
     * (non-PHPdoc)
     * @see Zend_Form#init()
     */
    public function init()
    {
        $table         = new Tri_Db_Table('forum_reply');
        $validators    = $table->getValidators();
        $filters       = $table->getFilters();

        $this->setAction('forum/reply/save')
             ->setMethod('post');

        $id = new Zend_Form_Element_Hidden('id');
        $id->addValidators($validators['id'])
           ->addFilters($filters['id'])
           ->removeDecorator('Label')
           ->removeDecorator('HtmlTag');

        $forumId = new Zend_Form_Element_Hidden('forum_id');
        $forumId->addValidators($validators['forum_id'])
                ->addFilters($filters['forum_id'])
                ->removeDecorator('Label')
                ->removeDecorator('HtmlTag');
        
        $forumReplyId = new Zend_Form_Element_Hidden('forum_reply_id');
        $forumReplyId->addValidators($validators['forum_reply_id'])
                     ->addFilters($filters['forum_reply_id'])
                     ->removeDecorator('Label')
                     ->removeDecorator('HtmlTag');

        $filters['description'][] = 'StripTags';
        $description = new Zend_Form_Element_Textarea('description');
        $description->setLabel('Comment')
                    ->addValidators($validators['description'])
                    ->addFilters($filters['description'])
                    ->setAttrib('rows', 6)
                    ->setAttrib('class', 'xxlarge')
                    ->setAllowEmpty(false);

        $this->addElement($id)
             ->addElement($forumId)
             ->addElement($forumReplyId)
             ->addElement($description)
             ->addElement('submit', 'Post', array('class' => 'btn primary'));
   }
}
