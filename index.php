<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listify - Task Management System</title>
    <!-- Tailwind CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Custom CSS for enhanced front-end */
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        }
        .container {
            max-width: 1200px;
        }
        header h1 {
            font-size: 2.5rem;
            background: linear-gradient(to right, #4f46e5, #7c3aed);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.15);
        }
        .form-control {
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .form-control:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }
        .btn-primary {
            background: linear-gradient(to right, #4f46e5, #7c3aed);
            transition: background 0.3s ease, transform 0.2s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(to right, #4338ca, #6d28d9);
            transform: scale(1.05);
        }
        .btn-secondary {
            background: linear-gradient(to right, #6b7280, #9ca3af);
        }
        .btn-secondary:hover {
            background: linear-gradient(to right, #4b5563, #6b7280);
            transform: scale(1.05);
        }
        .btn-success {
            background: linear-gradient(to right, #10b981, #34d399);
        }
        .btn-success:hover {
            background: linear-gradient(to right, #059669, #10b981);
            transform: scale(1.05);
        }
        .progress-circle-bg {
            fill: none;
            stroke: #e5e7eb;
            stroke-width: 10;
        }
        .progress-circle {
            fill: none;
            stroke: url(#progressGradient);
            stroke-width: 10;
            stroke-linecap: round;
            transform: rotate(-90deg);
            transform-origin: center;
            transition: stroke-dashoffset 0.5s ease;
        }
        #progress-percentage {
            background: linear-gradient(to right, #4f46e5, #7c3aed);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        footer {
            background: #1f2937;
            color: #d1d5db;
            padding: 2rem 0;
            border-top: 4px solid #4f46e5;
        }
        @media (max-width: 640px) {
            header h1 {
                font-size: 2rem;
            }
            .card {
                padding: 1rem;
            }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <header class="mb-8 text-center">
            <h1 class="text-3xl font-bold">Listify</h1>
            <p class="text-gray-600 mt-2">Simple Task Management System</p>
        </header>
        
        <!-- Main Content -->
        <div class="flex flex-col md:flex-row gap-6">
            <!-- Sidebar: Form & Stats -->
            <div class="md:w-1/3">
                <!-- Task Form -->
                <div class="card p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4 text-gray-800">Add New Task</h2>
                    
                    <form id="task-form">
                        <!-- Task Title -->
                        <div class="mb-4">
                            <label for="task-input" class="block text-sm font-medium text-gray-700 mb-1">Task Title *</label>
                            <input type="text" id="task-input" class="form-control w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none" placeholder="Enter task title" required>
                        </div>
                        
                        <!-- Task Description -->
                        <div class="mb-4">
                            <label for="task-description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea id="task-description" class="form-control w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none" rows="3" placeholder="Enter task description"></textarea>
                        </div>
                        
                        <!-- Category -->
                        <div class="mb-4">
                            <label for="category-select" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <select id="category-select" class="form-control w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none">
                                <option value="">Select a category</option>
                                <option value="work">Work</option>
                                <option value="personal">Personal</option>
                                <option value="shopping">Shopping</option>
                                <option value="health">Health</option>
                            </select>
                        </div>
                        
                        <!-- Priority -->
                        <div class="mb-4">
                            <label for="priority-select" class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                            <select id="priority-select" class="form-control w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none">
                                <option value="">Select a priority</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        
                        <!-- Due Date -->
                        <div class="mb-4">
                            <label for="due-date" class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                            <input type="date" id="due-date" class="form-control w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none">
                        </div>
                        
                        <!-- Color -->
                        <div class="mb-6">
                            <label for="color-input" class="block text-sm font-medium text-gray-700 mb-1">Color Label</label>
                            <input type="color" id="color-input" class="form-control h-10 w-full" value="#6366F1">
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="flex space-x-2">
                            <button type="submit" id="add-task-btn" class="btn-primary text-white px-4 py-2 rounded-md w-full">Add Task</button>
                            <button type="button" id="update-task-btn" class="hidden btn-success text-white px-4 py-2 rounded-md flex-grow">Update Task</button>
                            <button type="button" id="cancel-edit-btn" class="hidden btn-secondary text-white px-4 py-2 rounded-md">Cancel</button>
                        </div>
                    </form>
                </div>
                
                <!-- Progress & Stats -->
                <div class="card p-6">
                    <h2 class="text-xl font-semibold mb-4 text-gray-800">Progress Dashboard</h2>
                    
                    <!-- Progress Circle -->
                    <div class="flex justify-center mb-6">
                        <div class="relative w-40 h-40">
                            <svg class="w-full h-full" viewBox="0 0 160 160">
                                <defs>
                                    <linearGradient id="progressGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" style="stop-color:#4f46e5;stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:#7c3aed;stop-opacity:1" />
                                    </linearGradient>
                                </defs>
                                <circle class="progress-circle-bg" cx="80" cy="80" r="60"></circle>
                                <circle id="progress-circle" class="progress-circle" cx="80" cy="80" r="60"></circle>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span id="progress-percentage" class="text-3xl font-bold">0%</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div class="w-full bg-gray-200 rounded-full h-2.5 mb-6">
                        <div id="progress-bar" class="bg-indigo-600 h-2.5 rounded-full" style="width: 0%"></div>
                    </div>
                    
                    <!-- Task Stats -->
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div class="bg-indigo-50 p-3 rounded-lg">
                            <p class="text-sm text-gray-600">Total Tasks</p>
                            <p id="total-tasks" class="text-xl font-bold text-indigo-600">0</p>
                        </div>
                        <div class="bg-green-50 p-3 rounded-lg">
                            <p class="text-sm text-gray-600">Completed</p>
                            <p id="completed-tasks" class="text-xl font-bold text-green-600">0</p>
                        </div>
                        <div class="bg-yellow-50 p-3 rounded-lg">
                            <p class="text-sm text-gray-600">Active</p>
                            <p id="active-tasks" class="text-xl font-bold text-yellow-600">0</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Main: Task List -->
            <div class="md:w-2/3">
                <div class="card p-6">
                    <h2 class="text-xl font-semibold mb-4 text-gray-800">My Tasks</h2>
                    
                    <!-- Filter & Sort Controls -->
                    <div class="flex flex-col sm:flex-row justify-between mb-6 space-y-3 sm:space-y-0">
                        <!-- Status Filter -->
                        <div class="flex space-x-2">
                            <select id="status-filter" class="form-control py-2 px-3 border border-gray-300 rounded-md focus:outline-none">
                                <option value="all">All Tasks</option>
                                <option value="active">Active Only</option>
                                <option value="completed">Completed Only</option>
                            </select>
                            
                            <!-- Category Filter -->
                            <select id="category-filter" class="form-control py-2 px-3 border border-gray-300 rounded-md focus:outline-none">
                                <option value="">All Categories</option>
                                <option value="work">Work</option>
                                <option value="personal">Personal</option>
                                <option value="shopping">Shopping</option>
                                <option value="health">Health</option>
                            </select>
                        </div>
                        
                        <!-- Sort Options -->
                        <div>
                            <select id="sort-select" class="form-control py-2 px-3 border border-gray-300 rounded-md focus:outline-none">
                                <option value="createdAt">Sort by Date Created</option>
                                <option value="dueDate">Sort by Due Date</option>
                                <option value="priority">Sort by Priority</option>
                                <option value="title">Sort by Title</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Tasks Container -->
                    <div id="tasks-container" class="space-y-3">
                        <div class="text-center py-8 text-gray-500">Loading tasks...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="text-center py-6">
        <p>© 2025 Listify - Task Management System</p>
    </footer>
    
    <!-- JavaScript -->
    <script src="js/app.js"></script>
</body>
</html>