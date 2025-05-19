<?php
define('CLI_SCRIPT', true);

require(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/gradelib.php');
//require_once($CFG->libdir . '/progresslib.php'); // Required for progress tracing

global $DB;

$courseids = [
    4338, 4339, 4340, 4341, 4342, 4343, 4344, 4345,
     4346, 4347, 4348, 4349, 4350, 4351, 4352, 4353
];

echo "📊 Starting grade recalculation for " . count($courseids) . " courses...\n\n";

foreach ($courseids as $courseid) {
    $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
    echo "🔄 Recalculating grades for course: {$course->fullname} (ID: {$courseid})\n";

    grade_force_full_regrading($courseid);
    // Create a CLI progress bar instance
    $progress = new \core\progress\display(
        null, // Indentation
        1,    // Update every N steps
        true  // Auto-finish
    );

    // Call the regrade function with the progress tracker
    $result = grade_regrade_final_grades($courseid, null, null, $progress);

    // Display any issues
    if ($result !== true) {
        echo "⚠️  Errors found while regrading course ID {$courseid}:\n";
        foreach ($result as $itemid => $message) {
            echo "   - Item {$itemid}: {$message}\n";
        }
    } else {
        echo "✅ Grades successfully recalculated for course ID {$courseid}\n";
    }

    echo str_repeat('-', 60) . "\n";
}

echo "\n🎉 Finished grade recalculation for all courses.\n";
