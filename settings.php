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
 * @package local_filemanager
 * @author Andrew Normore<andrewnormore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2019 onwards Yorkville Education Company
 */

if (is_siteadmin()) {
    $settings = new admin_settingpage('local_filemanager', get_string('pluginname', 'local_filemanager'));
    $ADMIN->add('localplugins', $settings);

	// Dashboard Link
    $settings->add( new admin_setting_configempty('local_filemanager/local_filemanager',
            "File Manager Example",
            "<a target='_blank' href='".$CFG->wwwroot."/local/filemanager/index.php'>Open the File Manager Example</a>"
        )
    );

}