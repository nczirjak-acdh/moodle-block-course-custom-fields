<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * course_custom_fields block caps.
 *
 * @package    block_course_custom_fields
 * @copyright  Norbert Czirjak (czirjak.norbert@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_course_custom_fields extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_course_custom_fields');
    }
   
    public function applicable_formats() {
        
        return array(
                    'all'             => true,
                    'site'            => true,
                    'course'          => true,
                    'course-category' => true,
                    'mod'             => true,
                    'my'              => false,
                    'tag'             => false,
                    'admin'           => false,              
            );
    }
    
    public function instance_allow_multiple() {
          return true;
    }

    public function instance_can_be_docked() {
        return parent::instance_can_be_docked() && isset($this->config->title) && !empty($this->config->title);
    }
    
    function has_config() {return true;}
    
    function get_content() {
        
        global $CFG, $OUTPUT, $DB, $PAGE;
        $PAGE->requires->js('/blocks/course_custom_fields/lib.js');
        $contextID = $this->page->context->id;
                
        $blockID = $this->instance->id;
        //if the contextid is not system type we need to change it - this needed because only system type will be available in all page
        if($contextID != 1){            
            
            if ($DB->record_exists('block_instances', array('id' => $blockID, 'blockname'=> 'course_custom_fields'))) {
                $updData = new stdClass();    
                $updData->id = $blockID;
                $updData->parentcontextid = 1;                         
                $DB->update_record('block_instances', $updData);                             
            } 
        }
        
        if($_SERVER['HTTP_HOST'] == 'moodle-dev.eos.arz.oeaw.ac.at'){
            $searchUrl = 'https://clarin.oeaw.ac.at/moodle-dev/search/index.php';
        } else if ( $_SERVER['HTTP_HOST'] == "https://teach-dariah.hephaistos.arz.oeaw.ac.at/") {    
            $searchUrl = 'https://teach-dariah.hephaistos.arz.oeaw.ac.at/search/index.php';            
        } else {
            $searchUrl = 'https://teach.dariah.eu/search/index.php';            
        }
        
        $id = $this->page->course->id;
        
        $data = $DB->get_records_sql('
                        SELECT cif.name, cid.data, cif.datatype, cif.param1
                        FROM {custom_info_data} as cid                        
                        LEFT JOIN {custom_info_field} as cif on cid.fieldid = cif.id
                        WHERE cid.objectid = '.$id.' and cif.objectname = "course" ');

        if(empty($data))
        {
            if(!isset($this->content)) { 
                $this->content = new stdClass();
            }
            @$this->content->text = 'Course has no custom fields';
            return $this->content->text;
        }
        
        $data = json_decode(json_encode($data), True);

        if(!isset($this->content)) {
            $this->content = new stdClass();
            @$this->content->text = '';
        }
        if(!isset($this->content->text)) {
            $this->content->text = '';
        }
        $this->content->text = '<p class="text-center font-weight-bold"><a href="#" class="show_hide_ccf_block">Show All</a></p>';
        $this->content->text .= '<div style="display: table; width:100%; margin: 5px;" class="course_custom_fields_div">'; 
        foreach ($data as $key => $value) {
                        
            if($value["datatype"] == "datetime"){                
                $valueF = date('Y-m-d H:i:s', $value["data"]); 
            } elseif($value["datatype"] == "menu"){                
                $param = $value["data"];                
                $param1 = explode("\n", $value["param1"]);                
                $valueF = $param1[$param];
            }else{
                $valueF = $value["data"];
            }
            /*$this->content->text .= '<div style="display: table-row;" >';*/
            $this->content->text .= '<div class="custom-meta-key-field" >'
                    . '<p class="ccm-kfh">'.$key.' </p>';
                $this->content->text .= '<p class="ccm-kft">'.$valueF.'</p>';
            $this->content->text .= '</div>';
            
        }
        
        
        //$this->content->text .=' <br> <a href="'.$searchUrl.'"><b>Search</b></a>';
        $this->content->text .= '</div>';
        
        
        
        //return $this->content->text;
        
        
        return $this->content;
    }

    
}
