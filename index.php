<?php 
// ===============
//
//	FILE MANAGER EXAMPLE
//
// ===============
// @Author: Andy Normore
// https://github.com/AndyNormore/MoodleFileManager

// The point of this file is to demonstrate how to manage files within Moodle 2.3
// Why? Because file management is incredibly hard for some reason.
// This file is built to run as STANDALONE, no external files or strings. Just 100% easy to understand! (Noob friendly)






// --------------
//
// Standard Moodle Setup
//
// --------------
require_once( '../../config.php' );
global $CFG, $USER, $DB, $OUTPUT, $PAGE;

$PAGE->set_url('/local/filemanager/index.php');
require_login();

$PAGE->set_pagelayout( 'admin' );
// Choose the most appropriate context for your file manager - e.g. block, course, course module, this example uses
// the system context (as we are in a 'local' plugin without any other context)
$context = context_system::instance();
$PAGE->set_context( $context );

$PAGE->set_title( 'Page Title' );
$PAGE->set_heading( 'Page Heading' );

//DEFINITIONS
require_once($CFG->libdir.'/formslib.php');







// ===============
//
//
//	(LIBRARY) this would usually be in an: require('lib.php');
//
//
// ===============

class simplehtml_form extends moodleform {

    function definition() {

        $mform = $this->_form; // Don't forget the underscore!
        $filemanageropts = $this->_customdata['filemanageropts'];

        // FILE MANAGER
        $mform->addElement('filemanager', 'attachments', 'File Manager label', null, $filemanageropts);

        // Buttons
        $this->add_action_buttons();
    }
}











// ===============
//
//
// PAGE LOGIC
//
//
// ===============

$filemanageropts = array('subdirs' => 0, 'maxbytes' => '0', 'maxfiles' => 50, 'context' => $context);

$customdata = array('filemanageropts' => $filemanageropts);
$mform = new simplehtml_form(null, $customdata);



// CONFIGURE FILE MANAGER
// From http://docs.moodle.org/dev/Using_the_File_API_in_Moodle_forms#filemanager
$itemid = 0; // This is used to distinguish between multiple file areas, e.g. different student's assignment submissions, or attachments to different forum posts, in this case we use '0' as there is no relevant id to use
 
$draftitemid = file_get_submitted_draft_itemid('attachments');

// Copy all the files from the 'real' area, into the draft area
file_prepare_draft_area($draftitemid, $context->id, 'local_filemanager', 'attachment', $itemid, $filemanageropts);

// Prepare the data to pass into the form - normally we would load this from a database, but, here, we have no 'real' record to load
$entry = new stdClass();
$entry->attachments = $draftitemid; // Add the draftitemid to the form, so that 'file_get_submitted_draft_itemid' can retrieve it
 

// Set form data
$mform->set_data($entry);



// ===============
//
//
// PAGE OUTPUT
//
//
// ===============
echo $OUTPUT->header();


// ----------
// Form Submit Status
// ----------

if ($mform->is_cancelled()) {
    // CANCELLED
    echo '<h1>Cancelled</h1>';
    echo '<p>Handle form cancel operation, if cancel button is present on form<p>';
} else if ($data = $mform->get_data()) {
    // SUCCESS
    echo '<h1>Success!</h1>';
    echo '<p>In this case you process validated data. $mform->get_data() returns data posted in form.<p>';
    echo "<p><center><a href='$CFG->wwwroot/local/filemanager'>Click here to return and see your File Manager!</a></center><p>";

    // Save the files submitted
    file_save_draft_area_files($draftitemid, $context->id, 'local_filemanager', 'attachment', $itemid, $filemanageropts);

    // Just to show they are all there - output a list of submitted files
    $fs = get_file_storage();
    /** @var stored_file[] $files */
    $files = $fs->get_area_files($context->id, 'local_filemanager', 'attachment', $itemid);
    echo '<p>List of files:</p>';
    echo '<ul>';
    foreach ($files as $file) {
        $out = $file->get_filename();
        if ($file->is_directory()) {
            $out = $file->get_filepath();
        } else {
            $fileurl = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(),
                                                       $file->get_itemid(), $file->get_filepath(), $file->get_filename());
            $out = html_writer::link($fileurl, $out);
        }
        echo html_writer::tag('li', $out);
    }
    echo '</ul>';

} else {
    // FAIL / DEFAULT
    echo '<h1>Display form</h1>';
    echo '<p>This is the form first display OR "errors"<p>';
    $mform->display();
}




echo $OUTPUT->footer();

