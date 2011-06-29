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
 * @copyright  Copyright (C) 2005-2010  Preceptor Educação a Distância Ltda. <http://www.preceptoead.com.br>
 *
 */
class Forum_Model_Reply
{
    public static function count($forumId)
    {
        $db  = Zend_Db_Table::getDefaultAdapter();
        $sql = $db->quoteInto("SELECT COUNT(0) FROM forum_reply WHERE forum_id = ?", $forumId);

        return current($db->fetchCol($sql));
    }

    public static function sendMail($id)
    {
        $table     = new Tri_Db_Table('forum_reply');
        $subscribe = new Tri_Db_Table('forum_subscribe');
        $userTable = new Tri_Db_Table('user');
        $data      = $table->find($id)->current();
        $user      = $userTable->find($data->user_id)->current();
        $select = $subscribe->select(true)
                            ->setIntegrityCheck(false)
                            ->join('user', 'user.id = forum_subscribe.user_id')
                            ->join('forum', 'forum.id = forum_subscribe.forum_id')
                            ->where("forum_id = ?", $data->forum_id);
        $result = $subscribe->fetchAll($select);

        $body = $user->name . "\n\n" . $data->description;

        if (count($result)) {
            $mail = new Zend_Mail('utf-8');
            $mail->setSubject($result->current()->title);
            $mail->setBodyHtml(nl2br($body));

            foreach ($result as $rs) {
                $mail->addBcc($rs->email);
            }

            try {
                $mail->send();
            }catch(Exception $e) {
                Zend_Registry::get('Zend_Log')->log(print_r($e,true), Zend_Log::ERR);
            }
        }
    }
}
