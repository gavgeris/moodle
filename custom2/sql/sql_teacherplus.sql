SELECT
    CONCAT('<a target="_blank" href="',
           'http://seminars.etwinning.gr/mod/assign/view.php&quest;id=',(SELECT id FROM mdl_course_modules mcm WHERE instance = ma.id AND course = mc.id AND module = 1 LIMIT 1),'&action=grading">',
           CONCAT(CONCAT(LPAD(ROUND(DATEDIFF(FROM_UNIXTIME(allowsubmissionsfromdate), FROM_UNIXTIME(mc.startdate)) / 7) + 1,2,'0'), " - "), ma.name),
           '</a>'
        ) AS assignment,
    COUNT(distinct mgm.userid) AS plithos
FROM
    mdl_assign_submission mas,
    mdl_assign ma,
    mdl_course mc,
    mdl_groups mg,
    mdl_groups_members mgm,
    (SELECT
         mg.courseid,
         b.firstname,
         b.lastname,
         b.id,
         mg.name AS groupname,
         mg.id AS groupid,
         email
     FROM
         mdl_role_assignments a,
         mdl_user b,
         mdl_context c,
         mdl_groups mg,
         mdl_groups_members mgm
     WHERE a.roleid IN (5)
       AND a.userid = b.id
       AND a.contextid = c.id
       AND mg.id = mgm.groupid
       AND mgm.userid = a.userid
       AND mg.courseid = c.instanceid
       AND mg.courseid = ?) table1
WHERE ma.course = mc.id
  AND mas.assignment = ma.id
  AND mas.status = 'submitted'
  AND mgm.userid = mas.userid
  AND (grade != 2 OR grade IS NULL)
  AND mas.userid != table1.id -- Εξαιρώ τις εργασίες που εχει υποβάλλει ο επιμορφωτής (αν ήταν πριν επιμορφούμενος)

  AND mg.courseid = mc.id
  AND mg.id = mgm.groupid

  AND table1.courseid = mc.id
  AND table1.groupid = mg.id
  AND datediff(from_unixtime(ma.cutoffdate), now()) <= 5 -- Εργασίες που κλειδώνουν σε 5 μέρες
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
    mdl_groups mg,
    mdl_groups_members mgm,
    mdl_forum mf,
    mdl_forum_discussions mfd,
    mdl_forum_posts mfp,
    (SELECT
         mg.courseid,
         b.firstname,
         b.lastname,
         b.id as userid,
         mg.name AS groupname,
         mg.id AS groupid,
         email
     FROM
         mdl_role_assignments a,
         mdl_user b,
         mdl_context c,
         mdl_groups mg,
         mdl_groups_members mgm
     WHERE a.roleid IN (5)
       AND a.userid = b.id
       AND a.contextid = c.id
       AND mg.id = mgm.groupid
       AND mgm.userid = a.userid

       AND mg.courseid = c.instanceid
       AND mg.courseid = ?) table1
WHERE mg.courseid = mc.id
  AND mgm.groupid  = mg.id

  AND mf.course = mg.courseid
  AND mfd.forum = mf.id
  AND mfd.id = mfp.discussion

  AND mgm.userid = mfp.userid
  AND mf.assessed != 0

  AND table1.courseid = mc.id
  AND table1.groupid = mg.id
  AND table1.userid = mfp.userid -- User in Teacher's Group

  AND datediff(from_unixtime(mf.cutoffdate), now()) <= 5 -- Εργασίες που κλειδώνουν σε 5 μέρες
  AND mfp.userid NOT IN (
    SELECT mgg.userid
    FROM mdl_grade_items mgi, mdl_grade_grades mgg
    WHERE courseid = mg.courseid
      AND mgi.id = mgg.itemid
      AND itemmodule = 'forum'
      AND finalgrade IS NOT NULL
      AND iteminstance = mf.id
)
GROUP BY assignment;
-- ORDER BY LPAD(ROUND(DATEDIFF(FROM_UNIXTIME(allowsubmissionsfromdate), FROM_UNIXTIME(mc.startdate)) / 7) + 1,2,'0'), plithos desc