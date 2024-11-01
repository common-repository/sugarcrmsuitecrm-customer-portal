<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

$list_result_count_all = $objSCP->getRelationship('Contacts', $_SESSION['scp_user_id'], strtolower($module_name), 'id', array(), '', '', 'date_entered:DESC');
if (!empty($list_result_count_all->records)) {
    $countCases = count($list_result_count_all->records);
} else {
    $countCases = 0;
}

$html .= "<div class='scp-form-title scp-$module_name-font'>
<h3>Add New " . $module_name . "</h3>
    <div class='scp-move-action-btn'>
        <a id='clear_btn_id' onclick='scp_clear_search_txtbox(0,\"$module_name\",\"\",\"\",\"\",\"list\",\"$current_url\");' href='javascript:void(0);'  class='scp-$module_name scp-dtl-viewbtn'><span class='fa fa-list' ></span><span>LIST</span></a></div>
    </div>
    <div class='scp-form scp-form-2-col'>
                <form action='" . home_url() . "/wp-admin/admin-post.php' method='post' enctype='multipart/form-data' id='general_form_id'>
                <div class='scp-form-container'>
    <div class='panel scp-dtl-panel'><div class='scp-col-12 panel-title'><span class='panel_name'>Case Information</span></div>
    <div class='scp-col-6'>
        <div class='scp-form-group'>
                <label><b>Subject</b> *</label>
                <span><input class='input-text scp-form-control avia-datepicker-div' type='text' name='add-name' id='add-name' required /> </span>
        </div>
     </div>
    <div class='scp-col-6'>
        <div class='scp-form-group'>
                <label><b>Type</b></label>
                    <select class='input-text scp-form-control' title='' id='add-type' name='add-type'>
                        <option value='Administration' label='Administration'>Administration</option>
                        <option value='Product' label='Product'>Product</option>
                        <option value='User' label='User'>User</option>
                    </select>
        </div>
    </div>
    <div class='scp-col-6'>
            <div class='scp-form-group'>
                <label><b>Priority</b></label>
                    <select class='input-text scp-form-control' title='' id='add-priority' name='add-priority'>
                        <option value='P1' label='High'>High</option>
                        <option value='P2' label='Medium'>Medium</option>
                        <option value='P3' label='Low'>Low</option>
                    </select>
            </div>
     </div>
     <div class='scp-col-6'>
        <div class='scp-form-group'>
                <label><b>Status</b></label>
                    <select class='input-text scp-form-control' title='' id='add-status' name='add-status'>
                        <option value='New' label='New'>New</option>
                        <option value='Assigned' label='Assigned'>Assigned</option>
                        <option value='Closed' label='Closed'>Closed</option>
                        <option value='Pending Input' label='Pending Input'>Pending Input</option>
                        <option value='Rejected' label='Rejected'>Rejected</option>
                        <option value='Duplicate' label='Duplicate'>Duplicate</option>
                    </select>
        </div>
     </div>
     <div class='scp-col-6'>
        <div class='scp-form-group'>
                <label><b>Description</b> *</label>
                <span><textarea class='input-text scp-form-control' id='add-description' name='add-description' required></textarea></span>
        </div>
     </div>
     <div class='scp-col-6'>
        <div class='scp-form-group'>
                <label><b>Resolution</b></label>
                <span><textarea class='input-text scp-form-control' id='add-resolution' name='add-resolution'></textarea></span>
        </div>
     </div>
</div>
<div class='scp-form-actions'>
            <input type='hidden' name='action' value='scp_add_moduledata_call'>
            <input type='hidden' name='module_name' value='" . $module_name . "'>
            <input type='hidden' name='view' value='" . $view . "'>
            <input type='hidden' name='current_url' value=" . $current_url . ">
<span><input type='submit' value='Submit' class='hover active scp-button action-form-btn scp-$module_name' />&nbsp&nbsp<input type='button' value='Cancel' class='hover active scp-button action-form-btn' onclick=\"window.location='" . home_url() . "/portal-manage-page/?list-$module_name'\"/></span>
</div>
            </div>
            </form>
            </div>";