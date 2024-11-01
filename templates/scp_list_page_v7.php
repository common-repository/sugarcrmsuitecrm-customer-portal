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

foreach ($list_result->records as $setAllCases) {
    $id = $setAllCases->id;
    if ($setAllCases->priority == 'P1') {
        $priority = 'High';
    } else if ($setAllCases->priority == 'P2') {
        $priority = 'Medium';
    } else if ($setAllCases->priority == 'P3') {
        $priority = 'Low';
    } else {
        $priority = $setAllCases->priority;
    }
    $html .= "<tr>
                <td>" . $setAllCases->case_number . "</td>
                <td>" . $setAllCases->name . "</td>
                <td>" . date('d-m-Y', strtotime($setAllCases->date_entered)) . "</td>
                <td>" . $priority . "</td>
                <td>" . $setAllCases->status . "</td>
                <td class='action edit'><a href='javascript:void(0);' onclick='scp_module_call_view(\"$module_name\",\"$id\",\"detail\",\"$current_url\");'><span class='fa fa-eye' title='View'></span></a></td>
            </tr>";
}
?>