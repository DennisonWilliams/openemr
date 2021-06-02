<?php
/**
 *   This program is free software; you can redistribute it and/or
 *   modify it under the terms of the GNU General Public License
 *   as published by the Free Software Foundation; either version 2
 *   of the License, or (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */
include_once('common.php');
include_once(dirname(__FILE__).'/../../globals.php');
include_once($GLOBALS["srcdir"]."/api.inc");

function sji_triage_report($pid, $encounter, $cols, $id)
{
    $count = 0;
    $table = '';
    $data = array_merge(
     formFetch("form_sji_triage", $id),
     sji_extendedTriage_formFetch($id)
    );
    if ($data) {
        $table .= "<table>";
        foreach ($data as $key => $value) {
            if ($key == "id" ||
                $key == "pid" ||
                $key == "user" ||
                $key == "groupname" ||
                $key == "authorized" ||
                $key == "activity" ||
                $key == "date" ||
                $key == "hipaa_allowemail" ||
                $key == "hipaa_allowsms" ||
                $key == "hipaa_message" ||
                $key == "hipaa_voice" ||
                $key == "email" ||
                $key == "phone_cell" ||
                $key == "phone_home" ||
                $key == "contact_preferences" ||
                $key == "bps" ||
                $key == "bpd" ||
                $value == "")
            {
                continue;
            }

            if ($key === 'temperature' && $value == 0) {
               continue;
            }

            if ($value == "on" || $value === 1) {
                $value = "yes";
            } else if ($value == "off" || $value === 0) {
                $value = "no";
            }

            if (is_array($value)) {
                $value = implode(', ', $value);
            } else if ( preg_match('/^0000/', $value) ) {
                continue;
            }

            $key=ucwords(str_replace("_", " ", $key));
         
    
	$table .= "<tr><td><span class=bold>" . xlt($key) . ": </span><span class=text>" . text($value) . "</span></td></tr>";
        //$table .= print_r($data, 1);
        }
    }
    $table .= "</table>\n";
    print $table;
}
