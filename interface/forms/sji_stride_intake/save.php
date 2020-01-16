<?php
/**  Work/School Note Form created by Nikolai Vitsyn: 2004/02/13 and update 2005/03/30
 *   Copyright (C) Open Source Medical Software
 *
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


include_once("../../globals.php");
include_once("$srcdir/api.inc");
include_once("$srcdir/forms.inc");

/* 
 * name of the database table associated with this form
 */
$table_name = "form_sji_stride_intake";

if (!$pid) {
    $pid = $_SESSION['pid'];
}

function sji_extendedIntake($formid, $submission) {
    global $pid;

    // Look for the external values which will likely have a different $encounter id */
    $query = 
       'SELECT id FROM form_sji_intake '.
       'WHERE pid = ? '.
       'ORDER BY date DESC '.
       'LIMIT 1';

    $res = sqlStatement($query, array($pid));

    $row = sqlFetchArray($res);

    $intake_id = $row['id'];

    if (isset($intake_id) && isset($submission['supportive_people'])) {

       sqlStatement("delete from form_sji_intake_supportive_people where pid=?", array($intake_id));

       foreach ($submission['supportive_people'] as $person) {
          $sql = "insert into form_sji_intake_supportive_people(supportive_people, pid) values(?, ?)";
          sqlInsert($sql, array($person, $intake_id));
       } // foreach
    } 

    // Update pronouns if they have changed
    if (isset($submission['pronouns']) && isset($intake_id)) {
       $sql = 'UPDATE form_sji_intake_core_variables SET pronouns = ? where pid = ?';
       $res = sqlStatement($sql, array($submission['pronouns'], $pid));
    } 

    // Update taken hormones if changed
    if (isset($submission['taken_hormones']) && isset($intake_id)) {
       $sql = 'UPDATE form_sji_intake SET taken_hormones = ? where id = ?';
       error_log(__FUNCTION__ .'() taken_hormones sql: '. $sql .', taken_hormones: '. $submission['taken_hormones'] .', id: '. $intake_id);
       $res = sqlStatement($sql, array($submission['taken_hormones'], $intake_id));
    } 
}

$intake_columns = array(
   'why_are_you_here', 'hormone_duration', 'hormone_form_dosage',
   'hormone_program', 'why_stopped', 'why_continue', 
   'affect_expectations', 'effect_hopes', 'hormone_concerns',
   'who_out_to', 'financial_situation', 'safety_concerns',
   'useful_support', 'clinician_narrative'
);

$submission = array();
foreach ($intake_columns as $column) {
   if (isset($_POST[$column])) {
      $submission[$column] = $_POST[$column];
   }
}

if ($_GET["mode"] == "new") {
    $newid = formSubmit($table_name, $submission, '', $userauthorized);
    addForm($_SESSION["encounter"], "St. James Infirmary STRIDE Intake", $newid, "sji_stride_intake", $pid, $userauthorized);
    sji_extendedIntake($newid, $_POST);
} elseif ($_GET["mode"] == "update") {
    $success = formUpdate($table_name, $submission, $_GET["id"], $userauthorized);
    sji_extendedIntake($_GET["id"], $_POST);
}

formHeader("Redirecting....");
formJump();
formFooter();
