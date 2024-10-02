foreach ($subjectIDs as $id) {
            foreach ($waitingCourses as &$course) {
                echo '<pre>';
                print_r($course['SubjectID']);
                echo '</pre>';

            if (trim($id) !== trim($course['SubjectID'])) {
                
                // If a match is found, change CourseType to 'Major'
                $course['CourseType'] = 'Elective'; 
                break; // Exit the inner loop once a match is found
            }
        }
      
    }


    foreach ($subjectIDs as $id) {
        foreach ($waitingCourses as &$course) {
            
            if (trim($id) === trim($course['SubjectID'])) {
                
                // If a match is found, change CourseType to 'Elective'
                $course['CourseType'] = 'Elective'; 
            }
        }
    }