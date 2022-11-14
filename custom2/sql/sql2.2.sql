-- Φέρνει τις μη διορθωμένες εργασίες ανα σεμινάριο και ενότητα για κάθε επιμορφωτή
SELECT 
  CONCAT(table1.firstname, ' ', table1.lastname) AS epimorfwths,
  table1.email,
  Concat('Εβδομάδα ', mcs.section) as weekno,
  COUNT(distinct mgm.userid) AS plithos,
  (SELECT DISTINCT DATEDIFF(NOW(),FROM_UNIXTIME(mu1.lastaccess)) FROM mdl_user mu1 WHERE mu1.id = table1.userid)  AS days,
  mc.fullname,
  mg.name
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
  
  (SELECT 
    mg.courseid,
    b.firstname,
    b.lastname,
    mg.name AS groupname,
    mg.id AS groupid,
	b.id as userid,
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
    AND b.id IN (
			SELECT DISTINCT epimorfoumenos
			  FROM group_epimorfwth
			 WHERE courseid = ?
			   AND epimorfwths = ?
			   AND ((groupname != 'Συντονιστές Επιμορφωτών') OR (roleid = 1 AND groupname = 'Συντονιστές Επιμορφωτών')) -- Εξαιρώ το group Συντονιστές επιμορφωτών για όλους εκτός τους Manager
    )
    AND mg.courseid = c.instanceid) table1 
WHERE mcc.name LIKE '2022-23'
  AND mc.category = mcc.id
  AND ma.course = mc.id 
  AND mas.assignment = ma.id 
  AND mas.status = 'submitted'
  AND mgm.userid = mas.userid 
  AND (grade != 2 OR grade IS NULL)

  AND mg.courseid = mc.id 
  AND mg.id = mgm.groupid 

  /* Display week no */  
  AND mcs.course = mg.courseid
  AND mcs.course = mcm.course
  and mcm.section = mcs.id
  and mcm.module = 1
  and mcm.instance = ma.id 
 
  AND table1.courseid = mc.id 
  AND table1.groupid = mg.id 
  
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
  weekno,
  CONCAT(table1.firstname, ' ', table1.lastname),
  table1.email
 ORDER BY weekno, COUNT(*) DESC, fullname
    