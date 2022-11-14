sELECT 
  CONCAT(table1.firstname, ' ', table1.lastname) AS epimorfwths,
  table1.email,
  COUNT(*) AS plithos,
  (SELECT DISTINCT DATEDIFF(NOW(),FROM_UNIXTIME(mu1.lastaccess)) FROM mdl_user mu1 WHERE mu1.id = table1.id)  AS days,
  count(IF (mcs.section = 1,1,NULL)) as WEEK1,  
  count(IF (mcs.section = 2,1,NULL)) as WEEK2,  
  count(IF (mcs.section = 3,1,NULL)) as WEEK3,  
  count(IF (mcs.section = 4,1,NULL)) as WEEK4,  
  count(IF (mcs.section = 5,1,NULL)) as WEEK5,  
  count(IF (mcs.section = 6,1,NULL)) as WEEK6,  
  count(IF (mcs.section = 7,1,NULL)) as WEEK7,  
  count(IF (mcs.section = 8,1,NULL)) as WEEK8,  
  count(IF (mcs.section = 11,1,NULL)) as WEEK11,  
  count(IF (mcs.section = 12,1,NULL)) as WEEK12,  
  count(IF (mcs.section = 13,1,NULL)) as WEEK13,  
  count(IF (mcs.section = 14,1,NULL)) as WEEK14,  
  count(IF (mcs.section = 15,1,NULL)) as WEEK15,  
  count(IF (mcs.section = 16,1,NULL)) as WEEK16,  
  count(IF (mcs.section = 17,1,NULL)) as WEEK17, 
  mc.fullname,
  mg.name,
  sevw.fullname as syntonisths
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
  syntonisths_epimorfwth_vw sevw,
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
  WHERE a.roleid IN (3, 4)
    AND a.userid = b.id 
    AND a.contextid = c.id 
    AND mg.id = mgm.groupid 
    AND mgm.userid = a.userid 
    AND b.id IN (
			SELECT DISTINCT epimorfoumenos
			  FROM group_epimorfwth
			 WHERE 1=1
			   AND courseid != 4252 -- Επιμόρφωση Επιμορφωτών
			   AND courseid = mg.courseid
			   AND ((groupname != 'Συντονιστές Επιμορφωτών') OR (roleid = 1 AND groupname = 'Συντονιστές Επιμορφωτών')) -- Εξαιρώ το group Συντονιστές επιμορφωτών για όλους εκτός τους Manager
    )
    AND mg.courseid = c.instanceid
	AND mg.courseid = ?) table1 
WHERE mcc.name LIKE '2022-23'
  AND mc.category = mcc.id
  AND ma.course = mc.id 
  AND mas.assignment = ma.id 
  AND mas.status = 'submitted'
  AND mgm.userid = mas.userid 

  AND mg.courseid = mc.id 
  AND mg.id = mgm.groupid 
 
  AND table1.courseid = mc.id 
  AND table1.groupid = mg.id 
  
  and table1.id = sevw.userid  
  
/* Display week no */  
  AND mcs.course = mg.courseid
  AND mcs.course = mcm.course
  and mcm.section = mcs.id
  and mcm.module = 1
  and mcm.instance = ma.id 
    
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
  table1.email
 HAVING count(week1) > 10
 ORDER BY COUNT(*) DESC, mc.fullname