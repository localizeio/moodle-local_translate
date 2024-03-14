# connect-moodle-plugin

## Required Plugins			
- filter_multilang2

## Installation
- Install module: [Site administration -> Plugins -> Install plugins](http://localhost:8080/admin/tool/installaddon/index.php)
- In the plugin settings select the courses you want to translate: [Site administration -> Plugins -> Local plugins](http://localhost:8080/admin/category.php?category=localplugins)
- Enable “Enable Web Services”: [Site administration -> Advanced Features](http://localhost:8080/admin/settings.php?section=optionalsubsystems)
- Enable “REST Protocol”: [Site administration -> Plugins -> Web Services -> Manage protocols](http://localhost:8080/admin/settings.php?section=webserviceprotocols)
- Enable and move the “Multi-Language Content (v2)” filter to the first place, also in the “Apply to” field, select “Content and headings”: [Site administration -> Plugins -> Filters -> Manage filters](http://localhost:8080/admin/filters.php)
- Create a token, select “Local translate” Service when creating: [Site administration -> Server -> Manage tokens](http://localhost:8080/admin/webservice/tokens.php)
			
## Usage
Use the created token for the following functions:			
- **local_translate_get_courses** - function for receiving course content (timemodify - time change courses, cids[][cid] - courses)		
- **local_translate_get_list_courses** - function for getting course lists		
- **local_translate_set_course** - function for setting content in the course, PUT method		

## Types

| Type  | Field  | Description | Translatable? |
|-------|--------|-------------|---------------|
| **Choice** |
| | instance |
| | modname  |
| | name  | Choice name | ✅|
| | intro | Description | ✅|
| | options[][instance] |
| | options[][modname] |
| | options[][text] | Option 1  | ✅|
| **Topic** |
| | name | Section name | ✅|
| | summary | Summary | ✅|
| **Assignment** |
| | instance |	
| | modname	 |
| | name | Assignment name | ✅|
| | intro |	Description | ✅|
| **Chat** |
| | instance |
| | modname  |
| | name | Name of this chat room | ✅|
| | intro | Description | ✅|
| **Database** |
| | instance |
| | modname  |
| | name | Name | ✅|
| | intro | Description | ✅|
| **External tool** |
| | instance |
| | modname  |
| | name | Activity name | ✅|
| | intro | Activity description | ✅|
| |	| Custom parameters |
| | | Preconfigured tool |
| **Feedback** |
| | instance |	
| | modname  |
| | name | Name | ✅|
| | intro | Description | ✅|
| | page_after_submit | Completion message | ✅|
| **Folder** |
| | instance |	
| | modname  |
| | name | Name | ✅|
| | intro | Description | ✅|
| **Forum** |	
| | instance |	
| | modname  |
| | name | Forum name | ✅|
| | intro | Description | ✅|
| **Glossary** |		
| | instance |
| | modname  |
| | name | Name | ✅|
| | intro | Description | ✅|
| | entries[][instance]	|
| | entries[][modname]  |
| | entries[][concept] | Concept | ✅|
| | entries[][definition] | Definition | ✅|
| **Scorm** |	
| | instance |	
| | modname  |
| | name | Name | ✅|
| | intro | Description | ✅|
| **Survey** |	
| | instance |	
| | modname	 |
| | name | Name | ✅|
| | intro | Description | ✅|
| **Wiki** |
| | instance |	
| | modname	 |
| | name | Wiki name | ✅|
| | intro | Description | ✅|
| | pages[][instance] |
| | pages[][modname]  |
| | pages[][title] | First page name | ✅|
| | pages[][cachedcontent] | HTML format | ✅|
| **Workshop** |		
| | instance |	
| | modname	 |
| | name | Workshop name | ✅|
| | intro | Description | ✅|
| | instructauthors | Instructions for submission | ✅|
| | instructreviewers | Instructions for assessment | ✅|
| | conclusion | Conclusion | ✅|
| | accumulative[][instance] |
| | accumulative[][modname]	 |
| | accumulative[][description] | | ✅| 	
| **IMS content package** |	
| | instance |
| | modname	 |
| | name | Name | ✅|
| | intro | Description | ✅|
| **Label** |
| | instance |
| | modname	 |
| | intro | Label text | ✅|
| **Lesson** |	
| | instance |
| | modname	 |
| | name | Name | ✅|
| | intro | Description | ✅|
| | pages[][instance] |
| | pages[][modname]  |
| | pages[][title] | Page title | ✅|
| | pages[][contents] | Page contents | ✅|
| | answers[][instance] |
| | answers[][modname]  |
| | answers[][answer] | Description | ✅|
| | answers[][response] | Response | ✅|
| **Page** |	
| | instance |
| | modname	 |
| | name | Name | ✅|
| | intro | Description | ✅|
| | content | Page content | ✅|
| **Book** |	
| | instance |
| | modname	 |
| | name | Name | ✅|
| | intro | Description | ✅|
| | charters[][instance] |
| | charters[][modname]	 |
| | charters[][title] | Chapter title | ✅|
| | charters[][content] | Content | ✅|
| **File** |
| | instance |
| | modname	 |
| | name | Name | ✅|
| | intro | Description | ✅|
| **URL** |
| | instance |	
| | modname	 |
| | name | Name | ✅|
| | intro | Description | ✅|
| **Quiz** |	
| | instance |	
| | modname	 |
| | name | Name | ✅|
| | intro | Description | ✅|
| **"Multiple choice"**	|
| | | "Multiple choice" / Question name	|
| | questiontext | "Multiple choice" / Question text | ✅|
| | generalfeedback | "Multiple choice" / General feedback | ✅|
| | correctfeedback | "Multiple choice" / Combined feedback / For any correct response | ✅|
| | partiallycorrectfeedback | "Multiple choice" / Combined feedback / For any partially correct response | ✅|
| | incorrectfeedback | "Multiple choice" / Combined feedback / For any incorrect response\ | ✅|
| | answer | "Multiple choice" / Answers / Choice 1 | ✅|
| | feedback | "Multiple choice" / Answers / Feedback 1 | ✅|
| | hint | "Multiple choice" / Multiple tries / Hint 1 | ✅|
| **"True/False"**	|	
| | | "True/False" / Question name |
| | questiontext | "True/False" / Question text | ✅|
| | generalfeedback | "True/False" / General feedback | ✅|
| | feedback | "True/False" / Feedback for the response 'True' | ✅|
| | feedback | "True/False" / Feedback for the response 'False' | ✅|
| **"Matching"** |
| | | "Matching" / Question name |
| | questiontext | "Matching" / Question text | ✅|
| | generalfeedback | "Matching" / General feedback | ✅|
| | questiontext | "Matching" / Answers / Question 1 | ✅|
| | answertext | "Matching" / Answers / Answer1 | ✅|
| | correctfeedback | "Matching" / Combined feedback / For any correct response | ✅|
| | partiallycorrectfeedback | "Matching" / Combined feedback / For any partially correct response | ✅|
| | incorrectfeedback | "Matching" / Combined feedback / For any incorrect response | ✅|
| | | "Matching" / Multiple tries / Hint 1 |
| **"Numerical"** |
| | | "Numerical" / Question name |
| | questiontext | "Numerical" / Question text | ✅|
| | generalfeedback | "Numerical" / General feedback | ✅|
| | | "Numerical" / Answers / Feedback 1 |
| | hint | "Numerical" / Multiple tries / Hint 1 | ✅|
| | params | |
| **"Short answer"** | | *Doesn't work properly with translations* |	
| | | "Short answer" / Question name |	
| | questiontext | "Short answer" / Question text |
| | generalfeedback | "Short answer" / General feedback |
| | | "Short answer" / Answers / Answer 1 |
| | | "Short answer" / Answers / Feedback  1 |	
| | hint | "Short answer" / Multiple tries / Hint 1 |	
| **"Essay"** | | *Doesn't work properly with translations* |
| | | "Essay" / Question name |
| | questiontext | "Essay" / Question text |
| | generalfeedback | "Essay" / General feedback |
| | | "Essay" / Response Template / Response template |
| | graderinfo | "Essay" / Grader Information / Information for graders |
| | hints | |
| **"Calculated"** | | *Doesn't work properly with translations* |
| | | "Calculated" / Question name |
| | questiontext | "Calculated" / Question text |
| | generalfeedback | "Calculated" / General feedback |
| | | "Calculated" / Answers / Feedback |
| | hint | "Calculated" / Multiple tries / Hint 1 |
| **"Calculated multichoice"** | | *Doesn't work properly with translations* |
| | | "Calculated multichoice" / Question name |
| | questiontext | "Calculated multichoice" / Question text |
| | generalfeedback | "Calculated multichoice" / General feedback |
| | | "Calculated multichoice" / Answers / Feedback 1 |
| | | "Calculated multichoice" / Combined feedback / For any correct response |
| | | "Calculated multichoice" / Combined feedback / For any partially correct response |
| | | "Calculated multichoice" / Combined feedback / For any correct response |
| | hint | "Calculated multichoice" / Multiple tries / Hint 1 |
| | params | |
| **"Simple calculated"** | | *Doesn't work properly with translations* |	
| | | Simple calculated" / Question name |
| | questiontext | "Simple calculated" / Question text |
| | generalfeedback | "Simple calculated" / General feedback |
| | | "Simple calculated" / Answers / Feedback |
| | hint | "Simple calculated" / Multiple tries / Hint 1 |
| | params | |
| **"Drag and drop into text"** | | *Doesn't work properly with translations* |
| | | "Drag and drop into text" / Question name |
| | questiontext | "Drag and drop into text" / Question text |
| | generalfeedback | "Drag and drop into text" / General feedback |
| | | "Drag and drop into text" / Choices / Choice 1 |
| | | "Drag and drop into text" / Combined feedback / For any correct response |
| | | "Drag and drop into text" / Combined feedback / For any partially correct response |
| | | "Drag and drop into text" / Combined feedback / or any incorrect response |
| | hint | "Drag and drop into text" / Multiple tries /Hint 1 |
| | params | |
| **"Drag and drop markers"** | | *Doesn't work properly with translations* |
| | | "Drag and drop markers" / Question name |
| | questiontext | "Drag and drop markers" / Question text |
| | generalfeedback | "Drag and drop markers" / General feedback |
| | | "Drag and drop markers" / Markers/ Marker 1 |
| | correctfeedback | "Drag and drop markers" / Combined feedback / For any correct response |
| | partiallycorrectfeedback | "Drag and drop markers" / Combined feedback / For any partially correct response |
| | incorrectfeedback | "Drag and drop markers" / Combined feedback / For any incorrect response |
| | hint | "Drag and drop markers" / Multiple tries / Hint 1 |
| **"Drag and drop onto image"** | | *Doesn't work properly with translations* |
| | | "Drag and drop onto image" / Question name |
| | questiontext | "Drag and drop onto image" / Question text |
| | generalfeedback | "Drag and drop onto image" / General feedback |
| | | "Drag and drop onto image" / Draggable items / Draggable item text 1 |
| | | "Drag and drop onto image" / Drop zones / Text 1 |
| | | "Drag and drop onto image" / Combined feedback / For any correct response |
| | | "Drag and drop onto image" / Combined feedback / For any partially correct response |
| | | "Drag and drop onto image" / Combined feedback / For any incorrect response |
| | hint | "Drag and drop onto image" / Multiple tries / Hint 1 |
| | params | |
| **"Embedded answers (Cloze)"** | | *Doesn't work properly with translations* |
| | | "Embedded answers (Cloze)" / Question name |
| | questiontext | "Embedded answers (Cloze)" / Question text |
| | generalfeedback | "Embedded answers (Cloze)" / General feedback |
| | hint | "Embedded answers (Cloze)" / Multiple tries / Hint 1 |
| | params | |
| **"Random short-answer matching"** | | *Doesn't work properly with translations* |
| | | "Random short-answer matching" / Question name |
| | questiontext | "Random short-answer matching" / Question text |
| | generalfeedback | "Random short-answer matching" / General feedback |
| | | "Random short-answer matching" / Combined feedback / For any correct response |
| | | "Random short-answer matching" / Combined feedback /For any partially correct response |
| | | "Random short-answer matching" / Combined feedback / For any incorrect response |
| | hint | "Random short-answer matching" / Multiple tries / Hint 1 |
| | params | |
| **"Select missing words"** | | *Doesn't work properly with translations* |
| | | "Select missing words" / Question name |
| | questiontext | "Select missing words" / Question text |
| | generalfeedback | "Select missing words" / General feedback |
| | | "Select missing words" / Choices / Choice [[1]] |
| | | "Select missing words" / Combined feedback / For any correct response	|
| | | "Select missing words" / Combined feedback / For any partially correct response |
| | | "Select missing words" / Combined feedback / For any incorrect response |
| | hint | "Select missing words" / Multiple tries / Hint 1 |
| | params | |
| **"Description"** | | *Doesn't work properly with translations* |
| | | Description" / Question name |
| | questiontext | "Description" / Question text |
| | generalfeedback | "Description" / General feedback |
| | hints | |	
| | params | |	