SELECT 	epimorfwths,
          email,
          SUM(plithos) AS plithos,
          days,
          SUM(WEEK0) AS week0,
          SUM(WEEK1) AS WEEK1,
          SUM(WEEK2) AS WEEK2,
          SUM(WEEK3) AS WEEK3,
          SUM(WEEK4) AS WEEK4,
          SUM(WEEK5) AS WEEK5,
          SUM(WEEK6) AS WEEK6,
          SUM(WEEK7) AS WEEK7,
          SUM(WEEK8) AS WEEK8,
          SUM(WEEK9) AS WEEK9,
          SUM(WEEK10) AS WEEK10,
          SUM(WEEK11) AS WEEK11,
          SUM(WEEK12) AS WEEK12,
          SUM(WEEK13) AS WEEK13,
          SUM(WEEK14) AS WEEK14,
          SUM(WEEK15) AS WEEK15,
          SUM(WEEK16) AS WEEK16,
          SUM(WEEK17) AS WEEK17,
          SUM(WEEK18) AS WEEK18,
          SUM(WEEK19) AS WEEK19,
          SUM(WEEK20) AS WEEK20,
          SUM(WEEK21) AS WEEK21,
          SUM(WEEK22) AS WEEK22,
          SUM(WEEK23) AS WEEK23,
          SUM(WEEK24) AS WEEK24,
          SUM(WEEK25) AS WEEK25,
          SUM(WEEK26) AS WEEK26,
          SUM(WEEK27) AS WEEK27,
          SUM(WEEK28) AS WEEK28,
          SUM(WEEK29) AS WEEK29,
          SUM(WEEK30) AS WEEK30,
          id as courseid,
          fullname,
          NAME,
          -- syntonisths,
          cur_week
FROM
    (
        (SELECT
             -- concat('<button type="button" name="email" onclick=''sendemail("',table1.email,'","', CONCAT(table1.firstname, ' ', table1.lastname)  ,'",',Count(*),');">email</button>') as emailbtn,
             CONCAT(table1.firstname, ' ', table1.lastname) AS epimorfwths,
             table1.email,
             COUNT(*) AS plithos,
             (SELECT DISTINCT DATEDIFF(NOW(),FROM_UNIXTIME(mu1.lastaccess)) FROM mdl_user mu1 WHERE mu1.id = table1.id)  AS days,
             COUNT(IF (mcs.section = 0,1,NULL)) AS WEEK0,
             COUNT(IF (mcs.section = 1,1,NULL)) AS WEEK1,
             COUNT(IF (mcs.section = 2,1,NULL)) AS WEEK2,
             COUNT(IF (mcs.section = 3,1,NULL)) AS WEEK3,
             COUNT(IF (mcs.section = 4,1,NULL)) AS WEEK4,
             COUNT(IF (mcs.section = 5,1,NULL)) AS WEEK5,
             COUNT(IF (mcs.section = 6,1,NULL)) AS WEEK6,
             COUNT(IF (mcs.section = 7,1,NULL)) AS WEEK7,
             COUNT(IF (mcs.section = 8,1,NULL)) AS WEEK8,
             COUNT(IF (mcs.section = 9,1,NULL)) AS WEEK9,
             COUNT(IF (mcs.section = 10,1,NULL)) AS WEEK10,
             COUNT(IF (mcs.section = 11,1,NULL)) AS WEEK11,
             COUNT(IF (mcs.section = 12,1,NULL)) AS WEEK12,
             COUNT(IF (mcs.section = 13,1,NULL)) AS WEEK13,
             COUNT(IF (mcs.section = 14,1,NULL)) AS WEEK14,
             COUNT(IF (mcs.section = 15,1,NULL)) AS WEEK15,
             COUNT(IF (mcs.section = 16,1,NULL)) AS WEEK16,
             COUNT(IF (mcs.section = 17,1,NULL)) AS WEEK17,
             COUNT(IF (mcs.section = 18,1,NULL)) AS WEEK18,
             COUNT(IF (mcs.section = 19,1,NULL)) AS WEEK19,
             COUNT(IF (mcs.section = 20,1,NULL)) AS WEEK20,
             COUNT(IF (mcs.section = 21,1,NULL)) AS WEEK21,
             COUNT(IF (mcs.section = 22,1,NULL)) AS WEEK22,
             COUNT(IF (mcs.section = 23,1,NULL)) AS WEEK23,
             COUNT(IF (mcs.section = 24,1,NULL)) AS WEEK24,
             COUNT(IF (mcs.section = 25,1,NULL)) AS WEEK25,
             COUNT(IF (mcs.section = 26,1,NULL)) AS WEEK26,
             COUNT(IF (mcs.section = 27,1,NULL)) AS WEEK27,
             COUNT(IF (mcs.section = 28,1,NULL)) AS WEEK28,
             COUNT(IF (mcs.section = 29,1,NULL)) AS WEEK29,
             COUNT(IF (mcs.section = 30,1,NULL)) AS WEEK30,
             mc.id,
             mc.fullname,
             mg.name,
             -- sevw.fullname AS syntonisths,
             DATEDIFF(DATE_ADD(NOW(),INTERVAL -1 DAY), FROM_UNIXTIME(mc.startdate)) DIV 7 + 1 AS cur_week
         FROM
             mdl_assign_submission mas,
             mdl_assign ma,
             mdl_course mc,
             mdl_course_categories mcc,
             mdl_groups mg,
             mdl_groups_members mgm,
             /* For display week */
             mdl_course_sections mcs,
             mdl_course_modules mcm,
             -- syntonisths_epimorfwth_vw sevw,
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
                  mdl_groups_members mgm,
                  group_epimorfwth ge
              WHERE a.roleid IN (3, 4)
                AND a.userid = b.id
                AND a.contextid = c.id
                AND mg.id = mgm.groupid
                AND mgm.userid = a.userid

                AND b.id = ge.epimorfoumenos
                and ge.courseid = 4354
                and ((ge.groupname != 'Συντονιστές Επιμορφωτών' aND ge.groupname like 'Ομάδα%') OR (ge.roleid = 1 AND ge.groupname = 'Συντονιστές Επιμορφωτών')) -- Εξαιρώ το group Συντονιστές επιμορφωτών για όλους εκτός τους Manager
                AND mg.courseid = c.instanceid) table1
         WHERE mcc.name LIKE '2024-25'
           AND mc.category = mcc.id
           AND ma.course = mc.id
           AND mas.assignment = ma.id
           AND mas.status = 'submitted'
           AND mgm.userid = mas.userid
           AND mg.courseid = mc.id
           AND mg.id = mgm.groupid

           AND table1.courseid = mc.id
           AND table1.groupid = mg.id

           -- AND table1.id = sevw.userid

             /* Display week no */
           AND mcs.course = mg.courseid
           AND mcs.course = mcm.course
           AND mcm.section = mcs.id
           AND mcm.module = 1
           AND mcm.instance = ma.id

           -- Έλεγχος για μη διορθωμένες εργασίες
           AND ( NOT EXISTS (SELECT 1
                             FROM mdl_assign_grades mag
                             WHERE mag.assignment = mas.assignment
                               AND userid = mas.userid)
             OR (mas.timemodified >= (SELECT MAX(mag.timemodified)
                                      FROM mdl_assign_grades mag
                                      WHERE mag.assignment = mas.assignment
                                        AND userid = mas.userid))
             )
         GROUP BY  mc.fullname,
                   mg.name,
                   CONCAT(table1.firstname, ' ', table1.lastname),
                   table1.email)
--         UNION
--         (SELECT
--              CONCAT(table1.firstname, ' ', table1.lastname) AS epimorfwths,
--              table1.email,
--              COUNT(DISTINCT mfd.id) AS plithos,
--              (SELECT DISTINCT DATEDIFF(NOW(),FROM_UNIXTIME(mu1.lastaccess)) FROM mdl_user mu1 WHERE mu1.id = table1.id)  AS days,
--              COUNT(IF (mcs.section = 0,1,NULL)) AS WEEK0,
--              COUNT(IF (mcs.section = 1,1,NULL)) AS WEEK1,
--              COUNT(IF (mcs.section = 2,1,NULL)) AS WEEK2,
--              COUNT(IF (mcs.section = 3,1,NULL)) AS WEEK3,
--              COUNT(IF (mcs.section = 4,1,NULL)) AS WEEK4,
--              COUNT(IF (mcs.section = 5,1,NULL)) AS WEEK5,
--              COUNT(IF (mcs.section = 6,1,NULL)) AS WEEK6,
--              COUNT(IF (mcs.section = 7,1,NULL)) AS WEEK7,
--              COUNT(IF (mcs.section = 8,1,NULL)) AS WEEK8,
--              COUNT(IF (mcs.section = 9,1,NULL)) AS WEEK9,
--              COUNT(IF (mcs.section = 10,1,NULL)) AS WEEK10,
--              COUNT(IF (mcs.section = 11,1,NULL)) AS WEEK11,
--              COUNT(IF (mcs.section = 12,1,NULL)) AS WEEK12,
--              COUNT(IF (mcs.section = 13,1,NULL)) AS WEEK13,
--              COUNT(IF (mcs.section = 14,1,NULL)) AS WEEK14,
--              COUNT(IF (mcs.section = 15,1,NULL)) AS WEEK15,
--              COUNT(IF (mcs.section = 16,1,NULL)) AS WEEK16,
--              COUNT(IF (mcs.section = 17,1,NULL)) AS WEEK17,
--              COUNT(IF (mcs.section = 18,1,NULL)) AS WEEK18,
--              COUNT(IF (mcs.section = 19,1,NULL)) AS WEEK19,
--              COUNT(IF (mcs.section = 20,1,NULL)) AS WEEK20,
--              COUNT(IF (mcs.section = 21,1,NULL)) AS WEEK21,
--              COUNT(IF (mcs.section = 22,1,NULL)) AS WEEK22,
--              COUNT(IF (mcs.section = 23,1,NULL)) AS WEEK23,
--              COUNT(IF (mcs.section = 24,1,NULL)) AS WEEK24,
--              COUNT(IF (mcs.section = 25,1,NULL)) AS WEEK25,
--              COUNT(IF (mcs.section = 26,1,NULL)) AS WEEK26,
--              COUNT(IF (mcs.section = 27,1,NULL)) AS WEEK27,
--              COUNT(IF (mcs.section = 28,1,NULL)) AS WEEK28,
--              COUNT(IF (mcs.section = 29,1,NULL)) AS WEEK29,
--              COUNT(IF (mcs.section = 30,1,NULL)) AS WEEK30,
--              mc.id,
--              mc.fullname,
--              mg.name,
--              -- sevw.fullname AS syntonisths,
--              DATEDIFF(DATE_ADD(NOW(),INTERVAL -1 DAY), FROM_UNIXTIME(mc.startdate)) DIV 7 + 1 AS cur_week
--
--          FROM
--              mdl_course mc,
--              mdl_course_categories mcc,
--              mdl_groups mg,
--              mdl_groups_members mgm,
--              mdl_forum mf,
--              mdl_forum_discussions mfd,
--              mdl_forum_posts mfp,
--              /* For display week */
--              mdl_course_sections mcs,
--              mdl_course_modules mcm,
--              -- syntonisths_epimorfwth_vw sevw,
--
--              (SELECT ge.courseid, groupid, epimorfoumenos AS id, epimorfwths, mg.name AS groupname, mu2.`firstname`, mu2.lastname, mu2.email
--               FROM group_epimorfwth ge, mdl_user mu1, mdl_user mu2, mdl_groups mg
--               WHERE epimorfoumenos = mu1.id
--                 AND epimorfwths = mu2.`id`
--                 AND mg.id = groupid
--                 AND ((groupname != 'Συντονιστές Επιμορφωτών' aND groupname like 'Ομάδα%') OR (roleid = 1 AND groupname = 'Συντονιστές Επιμορφωτών'))) table1
--          WHERE mcc.name LIKE '2024-25'
--            AND mc.category = mcc.id
--
--            AND mg.courseid = mc.id
--            AND mgm.groupid  = mg.id
--
--            AND mf.course = mg.courseid
--            AND mfd.forum = mf.id
--            AND mfd.id = mfp.discussion
--
--            AND mgm.userid = mfp.userid
--            AND mf.assessed != 0
--
--              /* Συντονιστης */
--            -- AND table1.epimorfwths = sevw.userid
--
--            AND mcs.course = mc.id
--            AND mc.id = mcm.course
--            AND mcm.section = mcs.id
--            AND mcm.module = 9
--            AND mcm.instance = mf.id  -- Join with forum id
--
--            AND table1.courseid = mc.id
--            AND table1.groupid = mg.id
--            AND table1.id = mgm.userid
--
--            AND mfp.userid NOT IN (
--              SELECT mgg.userid
--              FROM mdl_grade_items mgi, mdl_grade_grades mgg
--              WHERE courseid = mg.courseid
--                AND mgi.id = mgg.itemid
--                AND itemmodule = 'forum'
--                AND finalgrade IS NOT NULL
--                AND iteminstance = mf.id
--          )
--          GROUP BY  mc.fullname,
--              mg.name,
--              CONCAT(table1.firstname, ' ', table1.lastname),
--              table1.email)
        ) t1
GROUP BY  epimorfwths
ORDER BY plithos DESC