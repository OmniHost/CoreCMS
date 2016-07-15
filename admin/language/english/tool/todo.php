<?php

$_['heading_title'] = 'To Do';

// Text
$_['text_success'] = 'Success: You have modified task!';
$_['text_list'] = 'To Do List';
$_['text_add'] = 'Add Task';
$_['text_edit'] = 'Edit Task';
$_['text_confirm'] = "All selected tasks will be marked as COMPLETED (if you are a contractor) or CLOSED (if you are an author). Warning! CLOSED means that a task will be DELETED later. Continue?";
$_['text_autocomplete_description'] = "Completed/closed in batch mode";
$_['text_email_subject'] = 'Please check the ToDo queue.';
$_['text_email_title'] = 'Dear <b>%s %s</b>,'; // firstname;lastname;
$_['text_email_signature'] = 'Sincerely,<br>the %s team'; // shopname;
$_['text_email_content'] = 'the <a href="%s">ToDo task</a> you are involved in has been created/changed:'; // url

$_['text_role_author'] = 'Author';
$_['text_role_contractor'] = 'Contractor';

$_['text_filter_queue_awaiting_decision'] = 'Awaiting Decision';
$_['text_filter_queue_awaiting_answer'] = 'Awaiting Answer';
$_['text_filter_queue_inwork'] = 'In Work';
$_['text_filter_queue_myqueue'] = 'My Queue';
$_['text_filter_queue_not_distributed'] = 'Not Distributed';
$_['text_filter_queue_finished'] = 'Finished';

$_['text_status_free'] = 'Free';
$_['text_status_inprogress'] = 'In Progress';
$_['text_status_assigned'] = 'Assigned';
$_['text_status_completed'] = 'Completed';
$_['text_status_archived'] = 'Archived';
$_['text_status_closed'] = 'Closed';
$_['text_status_asked'] = 'Asked';
$_['text_status_replied'] = 'Replied';

$_['text_action_inwork'] = 'In Work';
$_['text_action_ask'] = 'Ask';
$_['text_action_reply'] = 'Reply';
$_['text_action_complete'] = 'Complete';
$_['text_action_archive'] = 'Archive';
$_['text_action_close'] = 'Close';
$_['text_action_edit'] = 'Edit';

$_['text_priority_1'] = 'Urgent';
$_['text_priority_2'] = 'High';
$_['text_priority_3'] = 'Normal';
$_['text_priority_4'] = 'Low';

$_['text_log_title_add1'] = 'On %s the user %s created the task'; // date;author;
$_['text_log_title_add2'] = 'assigned to %s'; // contractor
$_['text_log_title_edit'] = 'On %s %s made the following changes'; // date;user
$_['text_log_contractor'] = 'Contractor';
$_['text_log_priority'] = 'Priority';
$_['text_log_queue'] = 'Queue';
$_['text_log_onhold'] = 'Suspended';
$_['text_log_deadline'] = 'Deadline';
$_['text_log_deadline_not_set'] = 'Not set';
$_['text_logview_header'] = 'The Task Log';

$_['text_description_default_archived'] = "Task has been closed and moved into archive";
$_['text_description_default_closed'] = "Task has been closed and will be deleted later";
$_['text_description_default_free'] = 'An author has marked a task as \'not distributed\'';
$_['text_description_default_assigned'] = 'An author has assigned the task to contractor';

// Column
$_['column_description'] = 'Description';
$_['column_author'] = 'Author';
$_['column_contractor'] = 'Contractor';
$_['column_priority'] = 'Priority';
$_['column_deadline'] = 'Deadline';
$_['column_queue'] = 'Queue';
$_['column_action'] = 'Action';

// Entry
$_['entry_description'] = 'Description';
$_['entry_priority'] = 'Priority';
$_['entry_queue'] = 'Queue';
$_['entry_deadline'] = 'Deadline';
$_['entry_author_id'] = 'Author';
$_['entry_contractor_id'] = 'Contractor';
$_['entry_action'] = 'Action';
$_['entry_role'] = 'My Role';
$_['entry_suspended'] = 'Suspended tasks only';
$_['entry_suspended_form'] = 'Suspended';

// Help
$_['help_description'] = 'Describe a task. Any details are welcome. Please note, you can\'t change submitted description, only write additional info.';
$_['help_contractor_id_a'] = 'Clean-up this field to move a task to the <b>Not Distributed</b> queue; Select a user to <b>assign</b> the task to him.';
$_['help_contractor_id_c'] = 'Clean-up this field to refuse a task. Set any user to delegate a task to him. If you wish to assign not distributed task to yourself you don\'t need to select your name from the list, system do that automatically when saves changes.';
$_['help_deadline'] = 'Last date the task should be completed. Only the author can edit this field';
$_['help_action_inwork_a'] = 'Redirect a task on execution <b>if</b> an author prepared an answer for the question <b>or</b> does not agree to close the completed work <b>or</b> decided to back a task from finished queue.';
$_['help_action_inwork_c'] = 'A contractor decided to contitue working with the task without waiting for the author answer.';
$_['help_action_accept'] = 'A contractor accepts the non-distributed task for execution.';
$_['help_action_ask'] = 'A contractor asks a question to the author and waits for the reply.';
$_['help_action_reply'] = 'An author has answered on the contractor\'s question';
$_['help_action_complete'] = 'A contractor has completed the task.';
$_['help_action_archive'] = 'The task is completed and can be closed. The content of a task can be used in the future, therefore it is protected from deleting.';
$_['help_action_close'] = 'The task is completed and can be closed. It will be automatically deleted later.';
$_['help_action_edit'] = 'Change a content of a task, priority, contractor or deadline. Execution of the task is to be continued.';
$_['help_suspended_form'] = 'Marking a task as suspended removes it from all conductor queues. To come it back just switch off the checkbox.';

// Buttons
$_['button_delete'] = 'Complete/Close';
$_['button_log'] = 'View log';

// Error
$_['error_permission'] = 'Warning: You do not have permission to modify todo module!';
$_['error_description'] = 'Description is empty or too short!';
$_['error_priority'] = 'Priority should be set!';
$_['error_deadline'] = 'Date should not point to the past!';
$_['error_contractor_id'] = 'Contractor should not be empty!';
