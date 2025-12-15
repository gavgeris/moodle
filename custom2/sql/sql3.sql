SELECT DISTINCT DATEDIFF(NOW(),FROM_UNIXTIME(lastaccess)) AS days, concat(b.firstname, ' ', b.lastname) as fullname,  d.fullname as coursename, email
FROM mdl_role_assignments a, mdl_user b, mdl_context c, mdl_course d, mdl_groups mg, mdl_groups_members mgm, mdl_course_categories mcc
WHERE a.roleid = 4
  AND a.userid = b.id
  AND a.contextid = c.id
  AND mg.id = mgm.groupid
  AND mgm.userid = a.userid
  AND mg.courseid = d.id
  AND c.instanceid = d.id
  AND mcc.id = d.category
  AND mcc.name = '2024-25'
  AND b.id  IN (
			SELECT DISTINCT epimorfoumenos
			  FROM group_epimorfwth
			 WHERE courseid = ?
			   AND epimorfwths = ?
			   AND ((groupname != 'Συντονιστές Επιμορφωτών') OR (roleid = 1 AND groupname = 'Συντονιστές Επιμορφωτών')) -- Εξαιρώ το group Συντονιστές επιμορφωτών για όλους εκτός τους Manager
    )
  AND DATEDIFF(NOW(),FROM_UNIXTIME(lastaccess)) > 0
ORDER BY days DESC, fullname ASC, lastname;