<?php
require(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/gradelib.php');

global $DB;

$courseids = [4338,
    4339,
    4340,
    4341,
    4342,
    4343,
    4344,
    4345,
    4346,
    4347,
    4348,
    4349,
    4350,
    4351,
    4352,
    4353]; // Replace with your list of Moodle course IDs

foreach ($courseids as $courseid) {
    $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
    echo "Recalculating grades for course: {$course->fullname} (ID: $courseid)\n";

    // Force gradebook recalculation
    grade_regrade_final_grades($course->id);
}

echo "Done.\n";

