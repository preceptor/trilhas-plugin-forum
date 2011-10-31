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
 * @package    Forum_Form_Forum
 * @copyright  Copyright (C) 2005-2010  Preceptor Educação a Distância Ltda. <http://www.preceptoead.com.br>
 * @license    http://www.gnu.org/licenses/  GNU GPL
 */
class Forum_Form_Forum extends Zend_Form
{
    /**
     * (non-PHPdoc)
     * @see Zend_Form#init()
     */
    public function init()
    {
        $table         = new Tri_Db_Table('forum');
        $validators    = $table->getValidators();
        $filters       = $table->getFilters();
        $statusOptions = array('active' => 'active', 'inactive' => 'inactive');

        $this->setAction('forum/index/save')
             ->setMethod('post');

        $id = new Zend_Form_Element_Hidden('id');
        $id->addValidators($validators['id'])
           ->addFilters($filters['id'])
           ->removeDecorator('Label')
           ->removeDecorator('HtmlTag');

        $filters['title'][] = 'StripTags';
        $title = new Zend_Form_Element_Text('title');
        $title->setLabel('Title')
              ->setAttrib('size', '55')
              ->addValidators($validators['title'])
              ->addFilters($filters['title']);

        $filters['description'][] = 'StripTags';
        $description = new Zend_Form_Element_Textarea('description');
        $description->setLabel('Description')
                    ->addValidators($validators['description'])
                    ->addFilters($filters['description'])
                    ->setAttrib('rows', 10)
                    ->setAttrib('class', 'xxlarge')
                    ->setAllowEmpty(false);

        $status  = new Zend_Form_Element_Select('status');
        $status->addMultiOptions($statusOptions)
               ->setRegisterInArrayValidator(false);

        $begin = new Zend_Form_Element_Text('begin');
        $begin->setLabel('Begin')
              ->setAttrib('class', 'date small')
              ->addFilters($filters['begin'])
              ->addValidators($validators['begin'])
              ->setAllowEmpty(false)
              ->getPluginLoader('filter')->addPrefixPath('Tri_Filter', 'Tri/Filter');

        $end = new Zend_Form_Element_Text('end');
        $end->setLabel('End')
            ->setAttrib('class', 'date small')
            ->addFilters($filters['end'])
            ->getPluginLoader('filter')->addPrefixPath('Tri_Filter', 'Tri/Filter');

        $status->setLabel('Status')
               ->addValidators($validators['status'])
               ->addFilters($filters['status']);
        
        $this->addElement($id)
             ->addElement($title)
             ->addElement($description)
             ->addElement($status)
             ->addElement($begin)
             ->addElement($end)
             ->addElement('submit', 'Save', array('class' => 'btn primary'));
   }
}
