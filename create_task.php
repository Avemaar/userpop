<?php
require_once(__DIR__.'/../../config.php');
require_once($CFG->dirroot.'/local/userpop/classes/form/forms.php');

$PAGE->set_url(new moodle_url('/local/userpop/create-new-task.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('title','Manage messages');
$PAGE->set_heading('Settings');

global $DB;
        
    $mform = new create_task_form(null);

    if($mform->is_cancelled()){
        redirect($CFG->wwwroot.'/local/userpop/create_task.php','You cancelled');
    }else if($fromform = $mform->get_data()){
        $task_data = new stdClass();
        $task_data->name = $fromform->name;
        $task = $DB->insert_record('local_userpop_tasks', $task_data);
        
        //$DB->update_record('local_userpop_task',$task_data);
        foreach($fromform->userids as $key=>$user){
            $clerk_data = new stdClass();
            $clerk_data->task_id = $task;
            $clerk_data->user_id = intval($user);
                
            $DB->insert_record('local_userpop_tasks_clerks', $clerk_data);

        }

        redirect($CFG->wwwroot.'/local/userpop/create_task.php');
    }
    
    
    echo $OUTPUT->header();
    $mform->display();
    
    echo $OUTPUT->footer();
?>
