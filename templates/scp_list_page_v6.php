<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

$html .= "<tr class='row main-col'>";
$html .= "<th style='width: 150px; vertical-align: middle;'>Case Number</th>
          <th style='vertical-align: middle;'>Name</th>
          <th style='vertical-align: middle;'>Date Created</th>
          <th style='vertical-align: middle;'>Priority</th>
          <th style='vertical-align: middle;'>Status</th>
          <th><a>Action</a></th>";
$html .= "</tr>";

foreach ($list_result->entry_list as $list_result_s) {
    $setAllCasesObj = $list_result_s->name_value_list;
    $id = $setAllCasesObj->id->value;
    if ($setAllCasesObj->priority->value == 'P1') {
        $priority = 'High';
    } else if ($setAllCasesObj->priority->value == 'P2') {
        $priority = 'Medium';
    } else if ($setAllCasesObj->priority->value == 'P3') {
        $priority = 'Low';
    } else {
        $priority = $setAllCasesObj->priority->value;
    }
    $html .= "<tr>
                <td>" . $setAllCasesObj->case_number->value . "</td>
                <td>" . $setAllCasesObj->name->value . "</td>
                <td>" . date('d-m-Y', strtotime($setAllCasesObj->date_entered->value)) . "</td>
                <td>" . $priority . "</td>
                <td>" . $setAllCasesObj->status->value . "</td>
                <td class='action edit'><a href='javascript:void(0);' onclick='scp_module_call_view(\"$module_name\",\"$id\",\"detail\",\"$current_url\");'><span class='fa fa-eye' title='View'></span></a></td>
            </tr>";
}
?>