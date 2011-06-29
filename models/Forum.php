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
class Forum_Model_Forum
{
    public static function closeExpired()
    {
        $table = new Tri_Db_Table('forum');
        $data = $table->fetchAll(array('end < NOW() AND end IS NOT NULL',
                                       'status <> ?' => 'inactive'));
        if(count($data)) {
            foreach($data as $row) {
                $row->status = 'inactive';
                $row->save();
            }
        }
    }
}
