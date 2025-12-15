SELECT 
  SUBSTR(mc.shortname,1,10) AS coursename,
  mg.name,
  CONCAT(CONCAT(LPAD(ROUND(DATEDIFF(FROM_UNIXTIME(allowsubmissionsfromdate), FROM_UNIXTIME(mc.startdate)) / 7) + 1,2,'0'), " - "), ma.name) AS assignment,
  DATEDIFF(NOW(), FROM_UNIXTIME(mas.timemodified)) AS days,
  IF(DATEDIFF(NOW(), FROM_UNIXTIME(ma.duedate)) < 0,'-',DATEDIFF(NOW(), FROM_UNIXTIME(ma.duedate))) AS days2,
  CONCAT(table1.firstname, ' ', table1.lastname) AS epimorfwths,
  table1.email,
  (SELECT CONCAT(firstname, ' ', lastname) FROM mdl_user WHERE id = mas.userid) AS epimorfoumenos
FROM
  mdl_assign_submission mas,
  mdl_assign ma,
  mdl_course mc,
  mdl_course_categories mcc,
  mdl_groups mg,
  mdl_groups_members mgm,
  (SELECT 
    mg.courseid,
    b.firstname,
    b.lastname,
    mg.name AS groupname,
    mg.id AS groupid,
    email 
  FROM
    mdl_role_assignments a,
    mdl_user b,
    mdl_context c,
    mdl_groups mg,
    mdl_groups_members mgm 
  WHERE a.roleid IN (3, 4)
    AND a.userid = b.id 
    AND a.contextid = c.id 
    AND mg.id = mgm.groupid 
    AND mgm.userid = a.userid

    AND b.id = ge.epimorfoumenos
    and ge.courseid = ?
    AND ge.epimorfwths = ?
    AND ((ge.groupname != 'Συντονιστές Επιμορφωτών') OR (ge.roleid = 1 AND ge.groupname = 'Συντονιστές Επιμορφωτών')) -- Εξαιρώ το group Συντονιστές επιμορφωτών για όλους εκτός τους Manager
    AND mg.courseid = c.instanceid) table1
WHERE mcc.name LIKE '2024-25'
  AND mc.category = mcc.id
  AND ma.course = mc.id 
  AND mas.assignment = ma.id 
  AND mas.status = 'submitted'
  AND mas.latest = 1
  AND mgm.userid = mas.userid 
  AND (grade != 2 OR grade IS NULL)

  AND mg.courseid = mc.id 
  AND mg.id = mgm.groupid 
 
  AND table1.courseid = mc.id 
  AND table1.groupid = mg.id 
  
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
  
ORDER BY coursename ASC, lastname, days DESC, CONCAT(CONCAT(LPAD(ROUND(DATEDIFF(FROM_UNIXTIME(allowsubmissionsfromdate), FROM_UNIXTIME(mc.startdate)) / 7) + 1,2,'0'), " - "), ma.name)
    