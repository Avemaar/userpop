<?php
require_once(__DIR__.'/../../config.php');
require_once($CFG->dirroot.'/local/userpop/classes/form/forms.php');

$PAGE->set_url(new moodle_url('/local/userpop/create-new-task.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title('title','Manage messages');
$PAGE->set_heading('Settings');




global $DB;
        
    $id = optional_param('id', null, PARAM_INT);
    
    
    $tasks = $DB->get_record('local_userpop_tasks',['id'=>$id],'*',MUST_EXIST);
    $userids = $DB->get_records('local_userpop_tasks_clerks',['task_id'=>$tasks->id]);
    
    $users[] = new stdClass();
    foreach($userids as $userid){
        $user = core_user::get_user($userid->id);
        $users[$userid->id] = $user->firstname.' '.$user->lastname;
        
        
    }
    
    $mform = new edit_task_form(null,['name'=>$tasks->name,'userids'=>$users]);
    //$mform->set_data($tasks);
    

    
    if($mform->is_cancelled()){
        redirect($CFG->wwwroot.'/local/userpop/edit-task.php','You cancelled');
    }else if($fromform = $mform->get_data()){
        
        
        $task_data = new stdClass();
        $task_data->name = $fromform->name;
        $task_data->id = $fromform->id;
        
        //$DB->update_record('local_userpop_task',$task_data);
        
        

        
        foreach($fromform->userids as $key=>$users){
            foreach($users as $user){
                
                $new_user = new stdClass();
                $new_user->season_section_id = $key;
                $new_user->user_id = intval($user);
                
                $check_record = $DB->get_record('local_userpop_tasks', ['id'=>$key,'user_id'=>intval($user)],'id');
                
                if($check_record){
                    $new_user->id = $check_record->id;
                    $DB->update_record('tdk_seasons_sections_secreteries', $new_user);
                }else{
                    $DB->insert_record('tdk_seasons_sections_secreteries', $new_user);
                }
                
            }
        }
        
        

        
        
        if(!empty($fromform->id)){
            $redirect_url = new moodle_url($CFG->wwwroot.'/local/tdk/edit-season.php',['id'=>$fromform->id]);
            redirect($redirect_url);
        }else{
            redirect($CFG->wwwroot.'/local/tdk/edit-season.php');
        }
    }
    
    
    echo $OUTPUT->header();
    $mform->display();
    
    echo $OUTPUT->footer();
?>
