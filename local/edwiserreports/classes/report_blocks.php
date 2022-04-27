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
 * Get Reports blocks to render in the dashboard
 *
 * @package     local_edwiserreports
 * @copyright   2020 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_edwiserreports;

use context_system;

/**
 * Class to serve the report blocks
 */
class report_blocks {
    /**
     * Reports block
     */
    protected $reportsblock;

    /**
     * Constructor to prepare all reports blocks
     */
    public function __construct($blocks) {
        global $CFG, $USER;

        // Rearrange blocks based on the saved preferences.
        \local_edwiserreports\utility::rearrange_block_with_preferences($blocks);
        $context = context_system::instance();

        // Prepare layout for each block.
        foreach ($blocks as $key => $block) {
            // If user dont have capability to see the block.
            if (!has_capability('report/edwiserreports_' . $block->classname . ':view', $context)) {
                continue;
            }

            // Check if class file exist.
            $classname = $block->classname;
            $filepath = $CFG->dirroot . '/local/edwiserreports/classes/blocks/' . $classname . '.php';
            if (!file_exists($filepath)) {
                debugging('Class file dosn\'t exist ' . $classname);
            }
            require_once($filepath);

            $classname = '\\local_edwiserreports\\' . $classname;
            $blockbase = new $classname();
            $layout = $blockbase->get_layout();

            if ($layout === false) {
                continue;
            }

            // Get block preferences.
            $pref = \local_edwiserreports\utility::get_reportsblock_preferences($block);

            if ($pref["hidden"] && !$USER->editing) {
                continue;
            } else if ($pref["hidden"]) {
                $layout->hiddenblock = true;
            }

            $blockbase->set_block_size($block->blockname);

            $this->reportsblock[] = $layout;
        }
    }

    /**
     * Functions to get report blocks
     */
    public function get_report_blocks() {
        return $this->reportsblock;
    }
}
