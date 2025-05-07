<?php
// Include database connection
require_once 'includes/db.php';

// Set header to JSON
header('Content-Type: application/json');

// Handle CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight request for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// Response array
$response = [
    'status' => false,
    'message' => '',
    'data' => null
];

// Get action from request
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Process based on action
switch ($action) {
    case 'create':
        // Check if request method is POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Get data from request
                $data = json_decode(file_get_contents('php://input'), true);
                
                // Validate required fields
                if (empty($data['title'])) {
                    $response['message'] = 'Title is required';
                    echo json_encode($response);
                    exit;
                }
                
                // Generate unique ID
                $id = uniqid('task_');
                
                // Prepare SQL statement
                $sql = "INSERT INTO Task (id, title, description, category, priority, dueDate, color) 
                        VALUES (:id, :title, :description, :category, :priority, :dueDate, :color)";
                
                // Prepare and execute statement
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':id' => $id,
                    ':title' => $data['title'],
                    ':description' => $data['description'] ?? '',
                    ':category' => $data['category'] ?? null,
                    ':priority' => $data['priority'] ?? null,
                    ':dueDate' => !empty($data['dueDate']) ? $data['dueDate'] : null,
                    ':color' => $data['color'] ?? 'gray'
                ]);
                
                // Fetch the newly created task
                $sql = "SELECT * FROM Task WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':id' => $id]);
                $task = $stmt->fetch();
                
                // Set success response
                $response['status'] = true;
                $response['message'] = 'Task created successfully';
                $response['data'] = $task;
            } catch (PDOException $e) {
                $response['message'] = 'Error creating task: ' . $e->getMessage();
            }
        } else {
            $response['message'] = 'Invalid request method';
        }
        break;
        
    case 'read':
        try {
            // Get sorting parameter
            $sort = isset($_GET['sort']) ? $_GET['sort'] : 'createdAt';
            $validSorts = ['createdAt', 'dueDate', 'priority', 'title'];
            $sort = in_array($sort, $validSorts) ? $sort : 'createdAt';
            
            // Get filter parameter
            $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
            $category = isset($_GET['category']) ? $_GET['category'] : '';
            
            // Build WHERE clause based on filter
            $where = '';
            $params = [];
            
            if ($filter === 'active') {
                $where = 'WHERE completed = :completed';
                $params[':completed'] = false;
            } elseif ($filter === 'completed') {
                $where = 'WHERE completed = :completed';
                $params[':completed'] = true;
            }
            
            // Add category filter if selected
            if (!empty($category)) {
                $where = $where ? $where . ' AND category = :category' : 'WHERE category = :category';
                $params[':category'] = $category;
            }
            
            // Build ORDER BY clause
            $order = 'ORDER BY ';
            if ($sort === 'title') {
                $order .= 'title ASC';
            } elseif ($sort === 'priority') {
                $order .= 'FIELD(priority, "high", "medium", "low") ASC';
            } elseif ($sort === 'dueDate') {
                $order .= 'dueDate IS NULL, dueDate ASC';
            } else {
                $order .= 'createdAt DESC';
            }
            
            // Build and execute SQL query
            $sql = "SELECT * FROM Task $where $order";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $tasks = $stmt->fetchAll();
            
            // Also fetch statistics
            $totalSql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN completed = true THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN completed = false THEN 1 ELSE 0 END) as active
                FROM Task";
            $statStmt = $pdo->query($totalSql);
            $stats = $statStmt->fetch();
            
            // Calculate progress percentage
            $progressPercentage = 0;
            if ($stats['total'] > 0) {
                $progressPercentage = round(($stats['completed'] / $stats['total']) * 100);
            }
            
            // Set success response
            $response['status'] = true;
            $response['message'] = 'Tasks retrieved successfully';
            $response['data'] = [
                'tasks' => $tasks,
                'stats' => [
                    'total' => (int)$stats['total'],
                    'completed' => (int)$stats['completed'],
                    'active' => (int)$stats['active'],
                    'progressPercentage' => $progressPercentage
                ]
            ];
        } catch (PDOException $e) {
            $response['message'] = 'Error retrieving tasks: ' . $e->getMessage();
        }
        break;
        
    case 'update':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Get data from request
                $data = json_decode(file_get_contents('php://input'), true);
                
                // Validate required fields
                if (empty($data['id'])) {
                    $response['message'] = 'Task ID is required';
                    echo json_encode($response);
                    exit;
                }
                
                // Check if this is a toggle completion request
                if (isset($data['toggleComplete']) && $data['toggleComplete']) {
                    // Update just the completion status
                    $sql = "UPDATE Task SET completed = NOT completed WHERE id = :id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([':id' => $data['id']]);
                } else {
                    // Full update of task
                    $sql = "UPDATE Task SET 
                            title = :title, 
                            description = :description, 
                            category = :category, 
                            priority = :priority, 
                            dueDate = :dueDate, 
                            color = :color,
                            completed = :completed
                            WHERE id = :id";
                    
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        ':id' => $data['id'],
                        ':title' => $data['title'],
                        ':description' => $data['description'] ?? '',
                        ':category' => $data['category'] ?? null,
                        ':priority' => $data['priority'] ?? null,
                        ':dueDate' => !empty($data['dueDate']) ? $data['dueDate'] : null,
                        ':color' => $data['color'] ?? 'gray',
                        ':completed' => isset($data['completed']) ? (bool)$data['completed'] : false
                    ]);
                }
                
                // Fetch the updated task
                $sql = "SELECT * FROM Task WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':id' => $data['id']]);
                $task = $stmt->fetch();
                
                // Set success response
                $response['status'] = true;
                $response['message'] = 'Task updated successfully';
                $response['data'] = $task;
            } catch (PDOException $e) {
                $response['message'] = 'Error updating task: ' . $e->getMessage();
            }
        } else {
            $response['message'] = 'Invalid request method';
        }
        break;
        
    case 'delete':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Get data from request
                $data = json_decode(file_get_contents('php://input'), true);
                
                // Validate required fields
                if (empty($data['id'])) {
                    $response['message'] = 'Task ID is required';
                    echo json_encode($response);
                    exit;
                }
                
                // Prepare SQL statement
                $sql = "DELETE FROM Task WHERE id = :id";
                
                // Prepare and execute statement
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':id' => $data['id']]);
                
                // Set success response
                $response['status'] = true;
                $response['message'] = 'Task deleted successfully';
                $response['data'] = ['id' => $data['id']];
            } catch (PDOException $e) {
                $response['message'] = 'Error deleting task: ' . $e->getMessage();
            }
        } else {
            $response['message'] = 'Invalid request method';
        }
        break;
        
    default:
        $response['message'] = 'Invalid action';
        break;
}

// Send JSON response
echo json_encode($response);