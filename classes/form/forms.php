<?php


require_once(__DIR__.'/../../../../config.php');
require_once($CFG->libdir . '/formslib.php');

require_once($CFG->dirroot . '/user/selector/lib.php');

class create_task_form extends moodleform{

    protected  function definition(){
        global $DB;
        
        $mform = &$this->_form;
        $data = $this->_customdata;
        
        
        $mform->addElement('header', 'edit_task', get_string('edit_task', 'local_userpop'));
        
        
        $mform->addElement('text', 'name', get_string('name', 'local_userpop'));
        $mform->setType('name', PARAM_RAW);
        
        
        $options = array(
            'ajax' => 'core_user/form_user_selector',
            'valuehtmlcallback' => function($userid) {
            global $DB, $OUTPUT;
            $userid = 1;
            if ($userid){
                return false;
            }
            $context = \context_system::instance();
            $fields = \core_user\fields::for_name()->with_identity($context, false);
            $record = \core_user::get_user($userid, 'id' . $fields->get_sql()->selects, MUST_EXIST);
            
            $user = (object)[
                'id' => $record->id,
                'fullname' => fullname($record, has_capability('moodle/site:viewfullnames', $context)),
                'extrafields' => [],
            ];
            
            foreach ($fields->get_required_fields([\core_user\fields::PURPOSE_IDENTITY]) as $extrafield) {
                $user->extrafields[] = (object)[
                    'name' => $extrafield,
                    'value' => s($record->$extrafield)
                ];
            }
            
            return $OUTPUT->render_from_template('core_user/form_user_selector_suggestion', $user);
            },
            'multiple' => true
            );
        $mform->addElement('autocomplete', 'userids', get_string('users', 'local_userpop'), array(), $options);
        
        

        $this->add_action_buttons(true);
        
    }
    
}

class edit_task_form extends moodleform{
    
    protected  function definition(){
        global $DB;
        
        $mform = &$this->_form;
        $data = $this->_customdata;
        
        if(!empty($data->id)){
            $mform->addElement('hidden', 'id',$data->id);
            $mform->setType('id', PARAM_INT);
        }
        
        
        $mform->addElement('header', 'edit_tdk_season', get_string('edit_task', 'local_userpop'));
        
        $mform->addElement('text', 'name', get_string('name', 'local_userpop'));
        $mform->setType('name', PARAM_RAW);
        //$mform->setDefault('name', $data['name']);
        
        
        
        $options = array(
            'ajax' => 'core_user/form_user_selector',
            /*'valuehtmlcallback' => function($userid) {
            global $DB, $OUTPUT;
            
	    //test purpose to see how can user id excluded from new search	
	    $userid = 1;
            if ($userid){
                return false;
            }
            $context = \context_system::instance();
            $fields = \core_user\fields::for_name()->with_identity($context, false);
            $record = \core_user::get_user($userid, 'id' . $fields->get_sql()->selects, MUST_EXIST);
            
            $user = (object)[
                'id' => $record->id,
                'fullname' => fullname($record, has_capability('moodle/site:viewfullnames', $context)),
                'extrafields' => [],
            ];
            
            foreach ($fields->get_required_fields([\core_user\fields::PURPOSE_IDENTITY]) as $extrafield) {
                $user->extrafields[] = (object)[
                    'name' => $extrafield,
                    'value' => s($record->$extrafield)
                ];
            }
            
            return $OUTPUT->render_from_template('core_user/form_user_selector_suggestion', $user);
            },*/
            'valuehtmlcallback' => function($userid) {
            global $DB, $OUTPUT;
            
            $user = $DB->get_record('user', ['id' => (int) $userid], '*', IGNORE_MISSING);
            if (!$user) {
                return false;
            }
            
            if (class_exists('\core_user\fields')) {
                $extrafields = \core_user\fields::for_identity(\context_system::instance(),
                    false)->get_required_fields();
            } else {
                $extrafields = get_extra_user_fields(context_system::instance());
            }
            //return $OUTPUT->render_from_template('core_user/form_user_selector_suggestion', $user);
            //return $OUTPUT->render_from_template('core_user/form_user_selector_suggestion', \local_userpop\external\get_users::prepare_result_object($user, $extrafields));
            return $OUTPUT->render_from_template('report_customsql/form-user-selector-suggestion',\report_customsql\external\get_users::prepare_result_object($user, $extrafields));
            },
            'multiple' => true
            );
        
        
        
        $mform->addElement('autocomplete', 'userids', get_string('users', 'local_userpop'), array(), $options);

        //
        //just give a try to setup the saved values from the db
        //actually i have no idea, also does not know how to exclude from new search the previusly values
        //$mform->setDefault('userids', $data['userids'][1]['user_id']);
        
        
        $this->add_action_buttons(true);
        
    }
    
}






