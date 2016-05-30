<?php
/*
    "Contact Form to Database" Copyright (C) 2011-2015 Michael Simpson  (email : michael.d.simpson@gmail.com)

    This file is part of Contact Form to Database.

    Contact Form to Database is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Contact Form to Database is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Contact Form to Database.
    If not, see <http://www.gnu.org/licenses/>.
*/

class DVOCIntegrationContactForm7 {

    /**
     * @var DVOCCF7Plugin
     */

    /**
     */
    function __construct() {
    }

    public function registerHooks() {
        add_action('wpcf7_before_send_mail', array(&$this, 'saveFormData'));
    }

    /**
     * Callback from Contact Form 7. CF7 passes an object with the posted data which is inserted into the database
     * by this function.
     * @param $cf7 WPCF7_ContactForm
     * @return bool
     */
    public function saveFormData($cf7) {
        try {
            $data = $this->convertData($cf7);
            return saveToDb($data);
        } catch (Exception $ex) {
            //$this->plugin->getErrorLog()->logException($ex);
        }
        return true;
    }


    /**
     * @param $cf7 WPCF7_ContactForm
     * @return object
     */
    public function convertData($cf7) {
        if (!isset($cf7->posted_data) && class_exists('WPCF7_Submission')) {
            // Contact Form 7 version 3.9 removed $cf7->posted_data and now
            // we have to retrieve it from an API
            $submission = WPCF7_Submission::get_instance();
            if ($submission) {
                $data = array();
                $data['title'] = $cf7->title();
                $data['posted_data'] = $submission->get_posted_data();
                $data['uploaded_files'] = $submission->uploaded_files();
                $data['WPCF7_ContactForm'] = $cf7;
                return (object) $data;
            }
        }
        return $cf7;
    }

    function saveToDb($data) {
        return true;
    }

}
