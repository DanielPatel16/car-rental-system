<?php
session_start();
include "includes/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Check Email
    $stmt = $conn->prepare("SELECT id, user_name, email, password, role, status FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 1) {

        $user = $result->fetch_assoc();

        // Verify Password
        if (password_verify($password, $user['password'])) {

            // Blocked accounts may not log in, regardless of correct credentials
            if (($user['status'] ?? 'Active') === 'Blocked') {
                echo "<script>
                        alert('Your account has been blocked. Please contact support for assistance.');
                        window.location='login.php';
                      </script>";
                $stmt->close();
                $conn->close();
                exit();
            }

            // Store Session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['user_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            // Prevent session fixation
            session_regenerate_id(true);

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: admin/dashboard.php");
                exit();
            } elseif ($user['role'] === 'user') {
                header("Location: index.php");
                exit();
            } else {
                // Unknown role
                session_destroy();
                echo "<script>
                        alert('Unauthorized user role.');
                        window.location='login.php';
                    </script>";
                exit();
            }

        } else {

            echo "<script>
                    alert('Incorrect Password');
                    window.location='login.php';
                  </script>";

        }

    } else {

        echo "<script>
                alert('Email not found');
                window.location='login.php';
              </script>";

    }

    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DriveEase</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "surface-container-lowest": "#ffffff",
                        "on-tertiary-container": "#3fd298",
                        "on-secondary-fixed": "#0d1c2d",
                        "primary": "#00288e",
                        "error": "#ba1a1a",
                        "surface-container-low": "#f2f3ff",
                        "on-primary": "#ffffff",
                        "secondary": "#516072",
                        "surface-tint": "#3755c3",
                        "primary-fixed-dim": "#b8c4ff",
                        "surface": "#faf8ff",
                        "secondary-fixed": "#d4e4fa",
                        "tertiary-fixed": "#6ffbbe",
                        "outline-variant": "#c4c5d5",
                        "tertiary": "#003d27",
                        "surface-container-highest": "#dae2fd",
                        "outline": "#757684",
                        "on-surface-variant": "#444653",
                        "on-secondary-container": "#556477",
                        "background": "#faf8ff",
                        "on-primary-fixed": "#001453",
                        "inverse-on-surface": "#eef0ff",
                        "tertiary-container": "#00563a",
                        "on-primary-fixed-variant": "#173bab",
                        "secondary-container": "#d2e1f7",
                        "primary-container": "#1e40af",
                        "surface-bright": "#faf8ff",
                        "surface-variant": "#dae2fd",
                        "tertiary-fixed-dim": "#4edea3",
                        "on-surface": "#131b2e",
                        "on-tertiary-fixed-variant": "#005236",
                        "on-error-container": "#93000a",
                        "surface-dim": "#d2d9f4",
                        "primary-fixed": "#dde1ff",
                        "on-secondary": "#ffffff",
                        "on-primary-container": "#a8b8ff",
                        "inverse-primary": "#b8c4ff",
                        "error-container": "#ffdad6",
                        "on-tertiary-fixed": "#002113",
                        "on-background": "#131b2e",
                        "on-error": "#ffffff",
                        "surface-container": "#eaedff",
                        "inverse-surface": "#283044",
                        "on-tertiary": "#ffffff",
                        "secondary-fixed-dim": "#b9c8de",
                        "on-secondary-fixed-variant": "#39485a",
                        "surface-container-high": "#e2e7ff"
                    },
                    borderRadius: {
                        DEFAULT: "0.25rem",
                        lg: "0.5rem",
                        xl: "0.75rem",
                        full: "9999px"
                    },
                    spacing: {
                        md: "16px",
                        "margin-desktop": "32px",
                        base: "4px",
                        "2xl": "48px",
                        gutter: "24px",
                        lg: "24px",
                        sm: "8px",
                        xl: "32px",
                        xs: "4px",
                        "max-width": "1440px",
                        "margin-mobile": "16px"
                    },
                    fontFamily: {
                        "body-lg": ["Inter"],
                        "headline-lg-mobile": ["Inter"],
                        "headline-md": ["Inter"],
                        "label-sm": ["Inter"],
                        "body-sm": ["Inter"],
                        "label-md": ["Inter"],
                        "headline-lg": ["Inter"],
                        "headline-sm": ["Inter"],
                        "body-md": ["Inter"]
                    },
                    fontSize: {
                        "body-lg": ["18px", { lineHeight: "28px", fontWeight: "400" }],
                        "headline-lg-mobile": ["24px", { lineHeight: "32px", letterSpacing: "-0.01em", fontWeight: "700" }],
                        "headline-md": ["24px", { lineHeight: "32px", letterSpacing: "-0.01em", fontWeight: "600" }],
                        "label-sm": ["12px", { lineHeight: "16px", fontWeight: "500" }],
                        "body-sm": ["14px", { lineHeight: "20px", fontWeight: "400" }],
                        "label-md": ["14px", { lineHeight: "16px", letterSpacing: "0.05em", fontWeight: "600" }],
                        "headline-lg": ["32px", { lineHeight: "40px", letterSpacing: "-0.02em", fontWeight: "700" }],
                        "headline-sm": ["20px", { lineHeight: "28px", fontWeight: "600" }],
                        "body-md": ["16px", { lineHeight: "24px", fontWeight: "400" }]
                    }
                }
            }
        };
    </script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .login-glass {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
    </style>
</head>
<body class="bg-background text-on-background min-h-screen">
    <main class="w-full min-h-screen flex flex-col md:flex-row">
        <section class="hidden md:flex md:w-1/2 lg:w-3/5 relative overflow-hidden bg-primary">
            <div class="absolute inset-0 z-0 overflow-hidden"
                 style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuBv42g8aVrzsl0HBkGTZJ8hCVwlZQMmxxsjMOFKxWNWoLhl1GtXWaoLsxrN5PgaeBKnd-fkzPJepjRq2FRMxqZVUHRnTJ-OCpLIrF3aUq09HI1TzMiPO8eMPawI3ihMPfZ5_ZVHjbDwiP34VgaOCz_gklZbLHhRy2Ouo1_JducmeXCM9PIE3X2Vmb649vfwqeZkBV4dNI0S5CdcpRKlXWK1qqfWRlRxoeKIMBs6hdPqaGuuOxSGRWuUS-foAUWsO8k3v1T-EaqhDnI')">
                <div class="absolute inset-0 bg-gradient-to-r from-primary/40 to-transparent"></div>
            </div>

            <div class="relative z-10 p-xl flex flex-col justify-between w-full">
                <div>
                    <h1 class="font-headline-lg text-headline-lg text-on-primary">DriveEase</h1>
                    <p class="font-body-lg text-body-lg text-on-primary/80 mt-sm">Seamless Fleet Management &amp; Luxury Rentals.</p>
                </div>

                <div class="max-w-md">
                    <div class="bg-white/10 backdrop-blur-md p-lg rounded-xl border border-white/20">
                        <span class="material-symbols-outlined text-tertiary-fixed-dim text-4xl mb-md">verified_user</span>
                        <h2 class="font-headline-sm text-headline-sm text-on-primary mb-xs">Enterprise-Grade Security</h2>
                        <p class="font-body-sm text-body-sm text-on-primary/70">
                            Your fleet and customer data are protected by industry-leading encryption and multi-factor authentication protocols.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section class="w-full md:w-1/2 lg:w-2/5 bg-surface flex items-center justify-center p-md md:p-xl">
            <div class="w-full max-w-md">
                <div class="md:hidden mb-xl text-center">
                    <h1 class="font-headline-lg-mobile text-headline-lg-mobile text-primary">DriveEase</h1>
                    <p class="font-body-sm text-body-sm text-on-surface-variant">Fleet Management Portal</p>
                </div>

                <header class="mb-xl">
                    <h2 class="font-headline-md text-headline-md text-on-surface mb-xs">Welcome Back</h2>
                    <p class="font-body-md text-body-md text-on-surface-variant">Please enter your credentials to access your dashboard.</p>
                </header>

                <form  method="POST" class="space-y-lg" >
                    <div class="space-y-xs">
                        <label class="font-label-md text-label-md text-on-surface-variant block" for="email">Email Address</label>
                        <input class="w-full px-md py-md bg-surface-container-lowest border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-all font-body-md text-body-md placeholder:text-outline" id="email" name="email" placeholder="name@company.com" required type="email">
                    </div>

                    <div class="space-y-xs">
                        <div class="flex justify-between items-center">
                            <label class="font-label-md text-label-md text-on-surface-variant block" for="password">Password</label>
                            <a class="font-label-sm text-label-sm text-primary hover:underline" href="#">Forgot password?</a>
                        </div>
                        <div class="relative">
                            <input class="w-full px-md py-md bg-surface-container-lowest border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-all font-body-md text-body-md placeholder:text-outline" id="password" name="password" placeholder="••••••••" required type="password">
                            <button class="absolute right-md top-1/2 -translate-y-1/2 text-outline hover:text-on-surface transition-colors" type="button" onclick="togglePassword()">
                                <span class="material-symbols-outlined" id="eye-icon">visibility</span>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center gap-sm">
                        <input class="w-5 h-5 rounded border-outline-variant text-primary focus:ring-primary focus:ring-offset-0" id="remember" name="remember" type="checkbox">
                        <label class="font-body-sm text-body-sm text-on-surface-variant cursor-pointer select-none" for="remember">Remember this device for 30 days</label>
                    </div>

                    <button type="submit" class="w-full bg-primary text-on-primary py-md rounded-lg font-label-md text-label-md hover:bg-primary-container hover:text-on-primary-container active:scale-[0.98] transition-all flex items-center justify-center gap-sm shadow-md">
                        <span>Login</span>
                        <span class="material-symbols-outlined text-md">arrow_forward</span>
                    </button>
                </form>

                <footer class="mt-2xl text-center">
                    <p class="font-body-sm text-body-sm text-on-surface-variant">
                        New to DriveEase?
                        <a class="text-primary font-label-md hover:underline" href="register.php">Create an account</a>
                    </p>
                </footer>
            </div>
        </section>
    </main>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.textContent = 'visibility_off';
            } else {
                passwordInput.type = 'password';
                eyeIcon.textContent = 'visibility';
            }
        }
    </script>
</body>
</html>