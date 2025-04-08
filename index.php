<?php
session_start(); // Start the session
include("config/conn.php");

// Initialize error variable
$login_error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    $username = mysqli_real_escape_string($c, $_POST['username']);
    $password = $_POST['password'];

    // Prepare SQL to prevent SQL injection
    $stmt = $c->prepare("SELECT user_id, username, password FROM userstbl WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Compare the password directly (not recommended for production)
        if ($password === $user['password']) {
            // Password is correct, create session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            
            // Redirect to dashboard
            header("Location: main.php");
            exit();
        } else {
            // Invalid password
            $login_error = "Invalid username or password";
        }
    } else {
        // User not found
        $login_error = "Invalid username or password";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Barangay Survey Dashboard</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" />

    <!-- Custom CSS -->
    <style>
    body {
        font-family: "Montserrat", sans-serif;
        min-height: 100vh;
        display: flex;
        align-items: center;
        background-color: #212529;
    }

    .login-container {
        max-width: 450px;
        margin: 0 auto;
    }

    .login-icon {
        font-size: 1.5rem;
    }

    .card {
        border-radius: 10px;
    }
    </style>
</head>

<body>
    <div class="container login-container">
        <!-- Login Form Card -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow border-0">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <div class="d-flex justify-content-center mb-3">
                                <i class="fas fa-user login-icon"></i>
                            </div>
                            <h5 class="fw-bold">User Login</h5>
                            <p class="text-muted">Enter your credentials to continue</p>
                        </div>

                        <?php
                        if (!empty($login_error)) {
                            echo '<div class="alert alert-danger">' . $login_error . '</div>';
                        }
                        ?>

                        <form id="loginForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"
                            method="post">
                            <div class="mb-3">
                                <label for="username" class="form-label fw-bold">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-dark">
                                        <i class="fas fa-user text-primary"></i>
                                    </span>
                                    <input type="text" class="form-control" id="username" name="username"
                                        placeholder="Enter your username"
                                        value="<?php echo htmlspecialchars($username ?? ''); ?>" required />
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label fw-bold">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-dark">
                                        <i class="fas fa-lock text-primary"></i>
                                    </span>
                                    <input type="password" class="form-control" id="password" name="password"
                                        placeholder="Enter your password" required />
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye" id="eyeIcon"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-dark btn-lg">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Toggle password visibility
    document
        .getElementById("togglePassword")
        .addEventListener("click", function() {
            const passwordInput = document.getElementById("password");
            const eyeIcon = document.getElementById("eyeIcon");

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                eyeIcon.classList.remove("fa-eye");
                eyeIcon.classList.add("fa-eye-slash");
            } else {
                passwordInput.type = "password";
                eyeIcon.classList.remove("fa-eye-slash");
                eyeIcon.classList.add("fa-eye");
            }
        });
    </script>
</body>

</html>