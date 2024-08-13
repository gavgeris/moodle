SELECT 
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
		  sevw.fullname AS syntonisths,
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
					   AND courseid != 4298
					   AND ((groupname != 'Συντονιστές Επιμορφωτών') OR (roleid = 1 AND groupname = 'Συντονιστές Επιμορφωτών')) -- Εξαιρώ το group Συντονιστές επιμορφωτών για όλους εκτός τους Manager
		    )
		    AND mg.courseid = c.instanceid) table1 
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
		  
		  AND table1.id = sevw.userid  
		  
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
		  table1.email
		 ORDER BY COUNT(*) DESC, mc.fullname;