<?php
/**
 * ownCloud
 *
 * @author Florian Rüchel
 * @copyright 2012 Florian Rüchel <florian.ruechel@gmail.com>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/**
 * Class to process requests made via GET or POST to return save variables
 */
class OC_Request
{
    public static function get($name, $default = null, $type = '', $datatype = null)
    {
        $data = null;
        $type = strtoupper($type);
        switch($type)
        {
            case 'GET':
                $data =& $_GET;
                break;
            case 'POST':
                $data =& $_POST;
                break;
            case 'REQUEST':
            case null:
                $data =& $_REQUEST;
                break;
        }
        
        return $data[$name];
    }
}


?>