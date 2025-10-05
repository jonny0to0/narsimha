<?php
    require_once 'auth.php';
    
    // Only allow access if already logged in as super admin
    if (!AdminAuth::isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
    
    // Check if current user is super admin (role_id = 1)
    $database = new Database();
    $conn = $database->getConnection();
    if ($conn) {
        $stmt = $conn->prepare("SELECT role_id FROM admin_users WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['admin_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if ($user['role_id'] != 1) {
                header('Location: dashboard.php');
                exit();
            }
        }
        $stmt->close();
        $conn->close();
    }

$database = new Database();
$conn = $database->getConnection();

$errors = [];
$success = null;

// Fetch roles for dropdown
$roles = [];
$roles_result = $conn->query("SELECT id, name FROM user_roles ORDER BY id ASC");
if ($roles_result) {
    while ($row = $roles_result->fetch_assoc()) {
        $roles[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_admin'])) {
    $username = sanitizeInput($_POST['username'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role_id = intval($_POST['role_id'] ?? 0);

    if (strlen($username) < 3) {
        $errors[] = 'Username must be at least 3 characters.';
    }
    if (!validateEmail($email)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }
    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match.';
    }
    if ($role_id <= 0) {
        $errors[] = 'Please select a valid role.';
    }

    // Ensure selected role exists
    if ($role_id > 0) {
        $role_check = $conn->prepare("SELECT id FROM user_roles WHERE id = ?");
        $role_check->bind_param("i", $role_id);
        $role_check->execute();
        $role_exists = $role_check->get_result()->num_rows > 0;
        $role_check->close();
        if (!$role_exists) {
            $errors[] = 'Selected role does not exist.';
        }
    }

    // Check duplicates
    if (empty($errors)) {
        $dup_stmt = $conn->prepare("SELECT id FROM admin_users WHERE username = ? OR email = ? LIMIT 1");
        $dup_stmt->bind_param("ss", $username, $email);
        $dup_stmt->execute();
        $dup_exists = $dup_stmt->get_result()->num_rows > 0;
        $dup_stmt->close();

        if ($dup_exists) {
            $errors[] = 'Username or email already exists.';
        }
    }

    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO admin_users (username, email, password_hash, role_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $username, $email, $password_hash, $role_id);
        if ($stmt->execute()) {
            $success = 'Admin user created successfully.';
            // Clear form values after success
            $username = '';
            $email = '';
        } else {
            $errors[] = 'Failed to create admin user. Please try again.';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin - Narshimha Tattoo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&family=Dancing+Script:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Inter', 'sans-serif'],
                        'script': ['Dancing Script', 'cursive'],
                    },
                    colors: {
                        'neon-red': '#ff073a',
                        'blood-red': '#8b0000',
                        'dark-gray': '#1a1a1a',
                        'darker-gray': '#0d0d0d',
                    }
                }
            }
        }
    </script>
    <style>
        :root { --neon-red: #ff073a; --blood-red: #8b0000; --dark-gray: #1a1a1a; --darker-gray: #0d0d0d; }
        .hero-bg { background: linear-gradient(135deg, rgba(13, 13, 13, 0.95) 0%, rgba(26, 26, 26, 0.9) 50%, rgba(13, 13, 13, 0.95) 100%), url('../img/hero-bg.png') center/cover; filter: brightness(0.7); }
        .neon-glow { box-shadow: 0 0 20px rgba(255, 7, 58, 0.5), 0 0 40px rgba(255, 7, 58, 0.3), 0 0 60px rgba(255, 7, 58, 0.1); }
        .glass-morphism { background: rgba(26, 26, 26, 0.25); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .gradient-text { background: linear-gradient(45deg, var(--neon-red), var(--blood-red)); background-size: 300% 300%; -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; animation: gradient-shift 4s ease infinite; }
        @keyframes gradient-shift { 0%, 100% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } }
    </style>
<?php /* Simple inline nonce for Tailwind is fine in this context */ ?>
</head>
<body class="min-h-screen hero-bg">
    <nav class="fixed top-0 w-full z-50 bg-black/20 backdrop-blur-sm border-b border-neon-red/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <a href="dashboard.php" class="nav-link text-white hover:text-neon-red transition-all duration-300 font-medium px-3 py-2 rounded-md relative overflow-hidden group">
                    <span class="relative z-10"><i class="fas fa-arrow-left mr-2"></i>Back to Dashboard</span>
                </a>
                <a href="auth.php?logout=1" class="bg-gradient-to-r from-neon-red to-red-600 hover:from-red-600 hover:to-neon-red text-white px-4 py-2 rounded-lg text-sm font-medium transition-all hover:scale-105">
                    <i class="fas fa-sign-out-alt mr-1"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="min-h-screen flex items-center justify-center py-20 px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="max-w-xl w-full space-y-8">
            <div class="text-center">
                <div class="mx-auto h-24 w-24 flex items-center justify-center rounded-full bg-gradient-to-r from-neon-red to-red-600 neon-glow">
                    <i class="fas fa-user-plus text-white text-3xl"></i>
                </div>
                <h2 class="mt-6 text-center text-4xl font-extrabold gradient-text">Create Admin User</h2>
                <p class="mt-2 text-center text-lg text-gray-300">Add a new admin or moderator account</p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="bg-red-900 border border-red-700 text-red-100 px-4 py-3 rounded-lg">
                    <ul class="list-disc list-inside">
                        <?php foreach ($errors as $err): ?>
                            <li><?php echo htmlspecialchars($err); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-900 border border-green-700 text-green-100 px-4 py-3 rounded-lg">
                    <i class="fas fa-check-circle mr-2"></i><?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="modern-card p-8 rounded-2xl glass-morphism">
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Username</label>
                        <input type="text" name="username" required value="<?php echo htmlspecialchars($username ?? ''); ?>" class="block w-full px-4 py-3 border border-gray-600 placeholder-gray-400 text-white bg-gray-800/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-neon-red focus:border-transparent" placeholder="e.g. admin2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Email</label>
                        <input type="email" name="email" required value="<?php echo htmlspecialchars($email ?? ''); ?>" class="block w-full px-4 py-3 border border-gray-600 placeholder-gray-400 text-white bg-gray-800/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-neon-red focus:border-transparent" placeholder="admin@example.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Role</label>
                        <select name="role_id" class="block w-full px-4 py-3 border border-gray-600 text-white bg-gray-800/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-neon-red focus:border-transparent">
                            <option value="0">Select a role</option>
                            <?php foreach ($roles as $r): ?>
                                <option value="<?php echo $r['id']; ?>"><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $r['name']))); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Password</label>
                            <input type="password" name="password" required class="block w-full px-4 py-3 border border-gray-600 placeholder-gray-400 text-white bg-gray-800/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-neon-red focus:border-transparent" placeholder="At least 8 characters">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Confirm Password</label>
                            <input type="password" name="confirm_password" required class="block w-full px-4 py-3 border border-gray-600 placeholder-gray-400 text-white bg-gray-800/50 rounded-lg focus:outline-none focus:ring-2 focus:ring-neon-red focus:border-transparent" placeholder="Re-enter password">
                        </div>
                    </div>
                </div>
                <div class="mt-8">
                    <button type="submit" name="create_admin" class="group relative w-full flex justify-center py-4 px-6 border border-transparent text-lg font-bold rounded-xl text-white bg-gradient-to-r from-neon-red to-red-600 hover:from-red-600 hover:to-neon-red focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-neon-red transition-all duration-300 transform hover:scale-105">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-4">
                            <i class="fas fa-user-plus text-red-200 group-hover:text-white transition-colors"></i>
                        </span>
                        <span>Create Admin</span>
                    </button>
                </div>
            </form>

            <div class="glass-morphism rounded-2xl p-6">
                <h3 class="text-white font-semibold mb-4 flex items-center"><i class="fas fa-users-cog mr-2 text-neon-red"></i>Existing Admins</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead class="bg-gray-800/50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Username</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Email</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Role</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Active</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Created</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            <?php
                            $list = $conn->query("SELECT au.username, au.email, au.is_active, au.created_at, ur.name as role_name FROM admin_users au LEFT JOIN user_roles ur ON au.role_id = ur.id ORDER BY au.created_at DESC LIMIT 20");
                            if ($list) {
                                while ($row = $list->fetch_assoc()) {
                                    echo '<tr class="hover:bg-gray-800/30">';
                                    echo '<td class="px-4 py-2 text-gray-200">' . htmlspecialchars($row['username']) . '</td>';
                                    echo '<td class="px-4 py-2 text-gray-300">' . htmlspecialchars($row['email']) . '</td>';
                                    echo '<td class="px-4 py-2 text-gray-300">' . htmlspecialchars(ucwords(str_replace('_', ' ', $row['role_name'] ?? ''))) . '</td>';
                                    echo '<td class="px-4 py-2">' . ((int)$row['is_active'] === 1 ? '<span class="text-green-400">Yes</span>' : '<span class="text-red-400">No</span>') . '</td>';
                                    echo '<td class="px-4 py-2 text-gray-400">' . htmlspecialchars($row['created_at']) . '</td>';
                                    echo '</tr>';
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>


