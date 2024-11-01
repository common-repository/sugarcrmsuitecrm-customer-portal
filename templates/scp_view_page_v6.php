<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

$priorityValue = isset($record_detail->entry_list[0]->name_value_list->priority->value) ? html_entity_decode($record_detail->entry_list[0]->name_value_list->priority->value) : '';
if ($priorityValue == 'P1') {
        $priority = 'High';
    } else if ($priorityValue == 'P2') {
        $priority = 'Medium';
    } else if ($priorityValue == 'P3') {
        $priority = 'Low';
    } else {
        $priority = $priorityValue;
    }
$status = isset($record_detail->entry_list[0]->name_value_list->status->value) ? $record_detail->entry_list[0]->name_value_list->status->value : '';
$type = isset($record_detail->entry_list[0]->name_value_list->type->value) ? $record_detail->entry_list[0]->name_value_list->type->value : '';
$name = isset($record_detail->entry_list[0]->name_value_list->name->value) ? html_entity_decode($record_detail->entry_list[0]->name_value_list->name->value) : '';
$description = isset($record_detail->entry_list[0]->name_value_list->description->value) ? html_entity_decode($record_detail->entry_list[0]->name_value_list->description->value) : '';
$resolution = isset($record_detail->entry_list[0]->name_value_list->resolution->value) ? html_entity_decode($record_detail->entry_list[0]->name_value_list->resolution->value) : '';

$html .= "<div class='scp-form-title scp-$module_name-font scp-default-font'>
          <h3>Details of " . $name . "</h3>
            <div class='scp-move-action-btn'><a id='clear_btn_id' onclick='scp_clear_search_txtbox(0,\"$module_name\",\"\",\"\",\"\",\"list\",\"$current_url\");' href='javascript:void(0);'  class='scp-$module_name scp-dtl-viewbtn' title='List'><span class='fa fa-list' ></span><span>LIST</span></a></div>
        </div>
        <div class='scp-form scp-form-2-col'>
            <div class='panel scp-dtl-panel'><div class='scp-col-12 panel-title'><span class='panel_name'>Case Information</span></div>
                <div class='scp-col-6 panel-left-label'>
                                        <label><b>Subject</b> *</label>
                                        <span class='data-view'>" . $name . "</span>
                </div>
                <div class='scp-col-6 panel-left-label'>
                                    <label><b>Type</b></label>
                                    <span class='data-view'>" . $type . "</span>
                </div>
                <div class='scp-col-6 panel-left-label'>
                                    <label><b>Priority</b></label>
                                    <span class='data-view'>" . $priority . "</span>
                </div>
                <div class='scp-col-6 panel-left-label'>
                                    <label><b>Status</b></label>
                                    <span class='data-view'>" . $status . "</span>
                </div>
                <div class='scp-col-6 panel-left-label'>
                                    <label><b>Description</b> *</label>
                                    <span class='data-view'>" . nl2br($description) . "</span>
                </div>
                <div class='scp-col-6 panel-left-label'>
                                    <label><b>Resolution</b></label>
                                    <span class='data-view'>" . nl2br($resolution) . "</span>
                </div>
            </div>
        </div>";
?>