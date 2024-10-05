<?php
include('db_connect.php');

// Define the number of students and teachers to add
$num_students = 10;
$num_teachers = 5;
$classes = ['9/A', '9/B', '10/A', '10/B', '11/A', '11/B'];

// Helper function to generate random usernames
function generate_username($prefix) {
    return $prefix . rand(1000, 9999);
}

// Add random students
for ($i = 0; $i < $num_students; $i++) {
    $username = generate_username('student');
    $password = 'testpassword'; // In a real environment, use hashed passwords
    $class = $classes[array_rand($classes)];
    
    $stmt = $conn->prepare("INSERT INTO users (username, password, role, class) VALUES (?, ?, 'student', ?)");
    $stmt->bind_param("sss", $username, $password, $class);
    $stmt->execute();
    echo "Added student: $username in class $class\n";
}

// Add random teachers
for ($i = 0; $i < $num_teachers; $i++) {
    $username = generate_username('teacher');
    $password = 'testpassword'; // In a real environment, use hashed passwords
    
    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'teacher')");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    echo "Added teacher: $username\n";
}

// Add random assignments from teachers to students
$num_assignments = 15;
$assignment_titles = ['Math Homework', 'Physics Project', 'History Essay', 'Chemistry Lab Report', 'Art Assignment'];
$assignment_descriptions = [
    'Complete the exercises on page 10.',
    'Write a report on the French Revolution.',
    'Solve the physics problems in chapter 3.',
    'Prepare a chemistry lab report on experiment 5.',
    'Create a painting based on nature.'
];

for ($i = 0; $i < $num_assignments; $i++) {
    $title = $assignment_titles[array_rand($assignment_titles)];
    $description = $assignment_descriptions[array_rand($assignment_descriptions)];
    $due_date = date('Y-m-d', strtotime("+".rand(1, 30)." days")); // Random future date within 30 days
    $class = $classes[array_rand($classes)];
    $created_by = 'teacher'.rand(1000, 1000 + $num_teachers - 1);
    
    $stmt = $conn->prepare("INSERT INTO assignments (title, description, due_date, class, created_by) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $title, $description, $due_date, $class, $created_by);
    $stmt->execute();
    echo "Added assignment: $title for class $class by $created_by\n";
}

// Add some broadcasts for students
$num_broadcasts = 5;
$broadcasts = [
    'School will be closed next Monday for a holiday.',
    'The school library will be open from 9 AM to 3 PM.',
    'There will be a school-wide science fair next month.',
    'New extracurricular activities will begin next week.',
    'Midterm exams will take place in two weeks.'
];

for ($i = 0; $i < $num_students; $i++) {
    $username = 'student'.rand(1000, 1000 + $num_students - 1);
    $broadcast = $broadcasts[array_rand($broadcasts)];
    
    $stmt = $conn->prepare("INSERT INTO student_portfolio (username, broadcasts) VALUES (?, ?) ON DUPLICATE KEY UPDATE broadcasts = VALUES(broadcasts)");
    $stmt->bind_param("ss", $username, $broadcast);
    $stmt->execute();
    echo "Added broadcast for $username: $broadcast\n";
}

echo "Test data added successfully!";
?>
