# connect-moodle-plugin

## Required Plugins:			
-	filter_multilang2		
			
## Steps:
-	Install module: [Site administration -> Plugins -> Install plugins](http://localhost:8080/admin/tool/installaddon/index.php)
-	In the plugin settings select the courses you want to translate: [Site administration -> Plugins -> Local plugins](http://localhost:8080/admin/category.php?category=localplugins)
-	Enable “Enable Web Services”: [Site administration -> Advanced Features](http://localhost:8080/admin/settings.php?section=optionalsubsystems)
-	Enable “REST Protocol”: [Site administration -> Plugins -> Web Services -> Manage protocols](http://localhost:8080/admin/settings.php?section=webserviceprotocols)
-	Enable and move the “Multi-Language Content (v2)” filter to the first place, also in the “Apply to” field, select “Content and headings”: [Site administration -> Plugins -> Filters -> Manage filters](http://localhost:8080/admin/filters.php)
-	Create a key, select “Local translate” Service when creating: [Site administration -> Plugins -> Web Services -> Manage tokens](http://localhost:8080/admin/settings.php?section=webservicetokens)
			
Use the created key for the following functions:			
-	**local_translate_get_courses** - function for receiving course content (timemodify - time change courses, cids[][cid] - courses)		
-	**local_translate_get_list_courses** - function for getting course lists		
-	**local_translate_set_course** - function for setting content in the course, PUT method		
