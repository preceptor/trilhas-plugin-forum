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
 * @package    Forum_Plugin
 * @copyright  Copyright (C) 2005-2010  Preceptor Educação a Distância Ltda. <http://www.preceptoead.com.br>
 * @license    http://www.gnu.org/licenses/  GNU GPL
 */
class Forum_Plugin extends Tri_Plugin_Abstract
{
    protected $_name = "forum";
    
    protected function _createDb()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `forum` (
                  `id` bigint(20) NOT NULL AUTO_INCREMENT,
                  `user_id` bigint(20) NOT NULL,
                  `classroom_id` bigint(20) NOT NULL,
                  `title` varchar(255) DEFAULT NULL,
                  `description` text NOT NULL,
                  `begin` date NOT NULL,
                  `end` date DEFAULT NULL,
                  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  `status` varchar(255) NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `user_id` (`user_id`),
                  KEY `classroom_id` (`classroom_id`)
                );

                CREATE TABLE IF NOT EXISTS `forum_reply` (
                  `id` bigint(20) NOT NULL AUTO_INCREMENT,
                  `forum_id` bigint(20) DEFAULT NULL,
                  `user_id` bigint(20) NOT NULL,
                  `description` text NOT NULL,
                  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  KEY `forum_id` (`forum_id`),
                  KEY `user_id` (`user_id`)
                );

                CREATE TABLE IF NOT EXISTS `forum_access` (
                  `user_id` bigint(20) NOT NULL,
                  `forum_id` bigint(20) NOT NULL,
                  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY (`user_id`,`forum_id`),
                  KEY `forum_id` (`forum_id`)
                );

                CREATE TABLE IF NOT EXISTS `forum_subscribe` (
                  `user_id` bigint(20) NOT NULL,
                  `forum_id` bigint(20) NOT NULL,
                  PRIMARY KEY (`user_id`,`forum_id`)
                );";
        
        $this->_getDb()->query($sql);
    }

    public function install()
    {
        $this->_createDb();
    }

    public function activate()
    {
        $this->_addClassroomMenuItem('communication','forum','forum/index/index');
        $this->_addAclItem('forum/index/index','identified');
        $this->_addAclItem('forum/index/form','teacher, coordinator, institution');
        $this->_addAclItem('forum/index/save','teacher, coordinator, institution');
        $this->_addAclItem('forum/index/delete','teacher, coordinator, institution');
        $this->_addAclItem('forum/index/view','identified');
        $this->_addAclItem('forum/reply/index','identified');
        $this->_addAclItem('forum/reply/form','identified');
        $this->_addAclItem('forum/reply/save','identified');
        $this->_addAclItem('forum/reply/delete','identified');
        $this->_addAclItem('forum/reply/subscribe','identified');
    }

    public function desactivate()
    {
        $this->_removeClassroomMenuItem('communication','forum');
        $this->_removeAclItem('forum/index/index');
        $this->_removeAclItem('forum/index/form');
        $this->_removeAclItem('forum/index/save');
        $this->_removeAclItem('forum/index/delete');
        $this->_removeAclItem('forum/index/view');
        $this->_removeAclItem('forum/reply/index');
        $this->_removeAclItem('forum/reply/form');
        $this->_removeAclItem('forum/reply/save');
        $this->_removeAclItem('forum/reply/delete');
        $this->_removeAclItem('forum/reply/subscribe');
    }
}
