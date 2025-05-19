SELECT
    CONCAT('<a target="_blank" href="',
           'https://seminars.etwinning.gr/mod/assign/view.php&quest;id=',(SELECT id FROM mdl_course_modules mcm WHERE instance = ma.id AND course = mc.id AND module = 1 LIMIT 1),'&action=grading">',
           CONCAT(CONCAT(LPAD(ROUND(DATEDIFF(FROM_UNIXTIME(allowsubmissionsfromdate), FROM_UNIXTIME(mc.startdate)) / 7) + 1,2,'0'), " - "), ma.name),
           '</a>'
        ) AS assignment,
    COUNT(distinct mas.userid) AS plithos
FROM
    mdl_assign_submission mas,
    mdl_assign ma,
    mdl_course mc
WHERE ma.course = mc.id
  and mc.id = ?
  AND mas.assignment = ma.id
  AND mas.status = 'submitted'
  AND (grade != 2 OR grade IS NULL)
  AND datediff(from_unixtime(ma.cutoffdate), now()) <= 7 -- Εργασίες που κλειδώνουν σε 5 μέρες
  AND ( NOT EXISTS (SELECT 1
                    FROM mdl_assign_grades mag
                    WHERE mag.assignment = mas.assignment
                      AND userid = mas.userid)
    OR (mas.timemodified >= (SELECT MAX(mag.timemodified)
                             FROM mdl_assign_grades mag
                             WHERE mag.assignment = mas.assignment
                               AND userid = mas.userid
    ))
    )
GROUP BY assignment
UNION
SELECT
    CONCAT('<a target="_blank" href="',
           'http://seminars.etwinning.gr/mod/forum/view.php&quest;id=',(SELECT id FROM mdl_course_modules mcm WHERE instance = mf.id AND course = mc.id AND module = 9 LIMIT 1),'">',
           'Forum:', mf.name,
           '</a>'
        ) AS assignment,
    COUNT(distinct mfp.userid) AS plithos
FROM
    mdl_course mc,
    mdl_forum mf,
    mdl_forum_discussions mfd,
    mdl_forum_posts mfp

WHERE mc.id = ?
  AND mf.course = mc.id
  AND mfd.forum = mf.id
  AND mfd.id = mfp.discussion

  AND mf.assessed != 0

  AND datediff(from_unixtime(mf.cutoffdate), now()) <= 5 -- Εργασίες που κλειδώνουν σε 5 μέρες
  AND mfp.userid NOT IN (
    SELECT mgg.userid
    FROM mdl_grade_items mgi, mdl_grade_grades mgg
    WHERE courseid = mc.id
      AND mgi.id = mgg.itemid
      AND itemmodule = 'forum'
      AND finalgrade IS NOT NULL
      AND iteminstance = mf.id
)
GROUP BY assignment;
-- ORDER BY LPAD(ROUND(DATEDIFF(FROM_UNIXTIME(allowsubmissionsfromdate), FROM_UNIXTIME(mc.startdate)) / 7) + 1,2,'0'), plithos desc