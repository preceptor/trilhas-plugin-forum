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
 * @package    Forum_Model
 * @copyright  Copyright (C) 2005-2011  Preceptor Educação a Distância Ltda. <http://www.preceptoead.com.br>
 *
 */
class Forum_Model_Access
{
    public static function verify($forumId, $userId)
    {
        $db  = Zend_Db_Table::getDefaultAdapter();
        $sql = $db->quoteInto("SELECT updated FROM forum_access WHERE forum_id = ?", $forumId);
        $sql = $db->quoteInto($sql . " AND user_id = ?", $userId);

        $updated = current($db->fetchCol($sql));
        if (!$updated) {
           self::create($forumId, $userId);
           return 0;
        }

        $sql = $db->quoteInto("SELECT COUNT(0) FROM forum_reply fr
                               JOIN forum_access fa ON fr.forum_id = fa.forum_id
                               WHERE fr.forum_id = ? AND fr.created > fa.updated", $forumId);
        $sql = $db->quoteInto($sql . " AND fa.user_id = ?", $userId);

        return current($db->fetchCol($sql));
    }

    public static function create($forumId, $userId)
    {
        $table = new Tri_Db_Table('forum_access');
        $row = $table->createRow(array('user_id' => $userId,
                                      'forum_id' => $forumId));
        $row->save();
        return $row;
    }

    public static function update($forumId, $userId)
    {
        $table = new Tri_Db_Table('forum_access');
        $row = $table->fetchRow(array('user_id = ?' => $userId,
                                      'forum_id = ?' => $forumId));

        if (!$row) {
            $row = self::create($forumId, $userId);
        }

        $row->updated = date('Y-m-d H:i:s');
        $row->save();
    }
}
