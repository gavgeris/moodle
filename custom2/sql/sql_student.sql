SELECT CONCAT('<a target="_blank" href="',
		'http://seminars.etwinning.gr/mod/assign/view.php&quest;id=',(SELECT id FROM mdl_course_modules mcm WHERE instance = ma.id AND course = mc.id LIMIT 1),'">',
		CONCAT(CONCAT(LPAD(ROUND(DATEDIFF(FROM_UNIXTIME(allowsubmissionsfromdate), FROM_UNIXTIME(mc.startdate)) / 7) + 1,2,'0'), " - "), ma.name),
		'</a>'
	) AS assignment
FROM
  mdl_assign_submission mas,
  mdl_assign ma,
  mdl_course mc,
  mdl_course_categories mcc,
  mdl_groups mg,
  mdl_groups_members mgm
WHERE mcc.name LIKE '2022-23'
  AND mc.category = mcc.id
  AND ma.course = mc.id 
  AND mas.assignment = ma.id 
  AND mas.status = 'submitted'
  AND mas.latest = 1
  AND mgm.userid = mas.userid 

  AND mg.courseid = mc.id 
  AND mg.id = mgm.groupid 
 
  AND mc.id = ?
  AND mas.userid = ?
  AND ( EXISTS (SELECT 1
		    FROM mdl_assign_grades mag
		   WHERE mag.assignment = mas.assignment
		     AND userid = mas.userid)
	)
	AND (mas.timemodified < (SELECT MAX(mag.timemodified)
		    FROM mdl_assign_grades mag
		   WHERE mag.assignment = mas.assignment
		      AND userid = mas.userid
		      AND (grade != 2 OR grade IS NULL)
		      ))