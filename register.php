<?php
session_start();
include "includes/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Default Role
    $role = "user";

    // Check if email already exists
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<script>alert('Email already exists!');window.location='register.php';</script>";
        exit();
    }

    // Encrypt Password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert User
    $stmt = $conn->prepare("INSERT INTO users (user_name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $hashedPassword, $role);

    if ($stmt->execute()) {

        $_SESSION['user'] = $username;

        header("Location: index.php");
        exit();

    } else {
        echo "Registration Failed.";
    }

    $stmt->close();
    $check->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - DriveEase</title>

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

        .form-input-focus:focus {
            box-shadow: 0 0 0 2px rgba(0, 40, 142, 0.1);
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(8px);
        }
    </style>
</head>
<body class="bg-surface text-on-surface min-h-screen">
    <main class="flex min-h-screen flex-col lg:flex-row">
        <section class="relative hidden lg:flex lg:w-1/2 bg-primary items-center justify-center overflow-hidden" style="background-position: 54.7344% 55.1654%;">
            <div class="relative z-10 p-xl max-w-lg text-on-primary">
                <div class="mb-xl">
                    <span class="text-headline-lg font-headline-lg font-bold tracking-tighter">DriveEase</span>
                </div>

                <h1 class="text-headline-lg font-headline-lg mb-md">Accelerate Your Fleet Management Today.</h1>
                <p class="text-body-lg font-body-lg opacity-90 mb-xl">
                    Join thousands of fleet managers optimizing their logistics with our professional, high-performance platform.
                </p>

                <div class="grid grid-cols-2 gap-md">
                    <div class="glass-effect p-md rounded-xl text-on-surface">
                        <span class="material-symbols-outlined text-primary mb-sm">speed</span>
                        <p class="text-label-md font-label-md font-bold">Fast Setup</p>
                        <p class="text-body-sm font-body-sm opacity-70">Register in under 2 minutes.</p>
                    </div>

                    <div class="glass-effect p-md rounded-xl text-on-surface">
                        <span class="material-symbols-outlined text-primary mb-sm">security</span>
                        <p class="text-label-md font-label-md font-bold">Secure Data</p>
                        <p class="text-body-sm font-body-sm opacity-70">Enterprise-grade encryption.</p>
                    </div>
                </div>
            </div>

            <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-primary-container rounded-full blur-3xl opacity-30 animate-pulse"></div>
        </section>

        <section class="flex-1 flex flex-col justify-center items-center p-md lg:p-2xl bg-surface">
            <div class="w-full max-w-[480px]">
                <div class="lg:hidden mb-xl">
                    <span class="text-headline-md font-headline-md font-bold text-primary">DriveEase</span>
                </div>

                <div class="mb-xl">
                    <h2 class="text-headline-md font-headline-md text-on-surface">Create your account</h2>
                    <p class="text-body-md font-body-md text-on-surface-variant">Start managing your fleet with precision.</p>
                </div>

                <form  method="POST" class="space-y-lg" >
                    <div class="space-y-xs">
                        <label class="text-label-sm font-label-sm text-on-surface-variant" for="full_name">Full Name</label>
                        <div class="relative">
                            <input class="w-full px-md py-md bg-surface-container-lowest border border-outline-variant rounded-lg text-body-md font-body-md outline-none focus:border-primary form-input-focus transition-all duration-200" id="full_name" name="username" placeholder="enter your name" type="text" required>
                        </div>
                    </div>

                    <div class="space-y-xs">
                        <label class="text-label-sm font-label-sm text-on-surface-variant" for="email">Email Address</label>
                        <div class="relative">
                            <input class="w-full px-md py-md bg-surface-container-lowest border border-outline-variant rounded-lg text-body-md font-body-md outline-none focus:border-primary form-input-focus transition-all duration-200" id="email" name="email" placeholder="name@company.com" type="email" required>
                        </div>
                    </div>

                    <div class="space-y-xs">
                        <label class="text-label-sm font-label-sm text-on-surface-variant" for="phone">Phone Number</label>
                        <div class="relative">
                            <input class="w-full px-md py-md bg-surface-container-lowest border border-outline-variant rounded-lg text-body-md font-body-md outline-none focus:border-primary form-input-focus transition-all duration-200" id="phone" name="phone"  placeholder="+1 (555) 000-0000" type="tel" required>
                        </div>
                    </div>

                    <div class="space-y-xs">
                        <label class="text-label-sm font-label-sm text-on-surface-variant" for="password">Password</label>
                        <div class="relative">
                            <input class="w-full px-md py-md bg-surface-container-lowest border border-outline-variant rounded-lg text-body-md font-body-md outline-none focus:border-primary form-input-focus transition-all duration-200" id="password" name="password" placeholder="••••••••" type="password" required>
                        </div>
                    </div>

                    <div class="flex items-start gap-sm py-sm">
                        <input class="mt-1 h-4 w-4 rounded border-outline-variant text-primary focus:ring-primary" id="terms" type="checkbox" value="" required>
                        <label class="text-body-sm font-body-sm text-on-surface-variant leading-tight" for="terms">
                            I agree to the <a class="text-primary font-medium hover:underline" href="#">Terms of Service</a> and <a class="text-primary font-medium hover:underline" href="#">Privacy Policy</a>.
                        </label>
                    </div>

                    <button type="submit" class="w-full bg-primary text-on-primary py-md px-xl rounded-lg font-label-md text-label-md shadow-lg hover:bg-primary-container transition-colors duration-200 flex justify-center items-center gap-sm group">
                        Register Account
                        <span class="material-symbols-outlined text-[20px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                    </button>

                    <div class="text-center pt-lg">
                        <p class="text-body-md font-body-md text-on-surface-variant">
                            Already have an account?
                            <a class="text-primary font-bold hover:underline" href="login.php">Log in</a>
                        </p>
                    </div>
                </form>

                <div class="mt-2xl text-center space-x-md">
                    <a class="text-label-sm font-label-sm text-outline hover:text-on-surface transition-colors" href="#">Help Center</a>
                    <a class="text-label-sm font-label-sm text-outline hover:text-on-surface transition-colors" href="#">Legal</a>
                    <a class="text-label-sm font-label-sm text-outline hover:text-on-surface transition-colors" href="#">Security</a>
                </div>
            </div>
        </section>
    </main>

    <script>
        const inputs = document.querySelectorAll('input');

        inputs.forEach((input) => {
            input.addEventListener('focus', () => {
                const icon = input.previousElementSibling;
                if (icon && icon.classList.contains('material-symbols-outlined')) {
                    icon.classList.add('text-primary');
                    icon.classList.remove('text-outline');
                }
            });

            input.addEventListener('blur', () => {
                const icon = input.previousElementSibling;
                if (icon && icon.classList.contains('material-symbols-outlined')) {
                    icon.classList.remove('text-primary');
                    icon.classList.add('text-outline');
                }
            });
        });

        const leftPanel = document.querySelector('section.bg-primary');
        if (leftPanel) {
            leftPanel.addEventListener('mousemove', (e) => {
                const { clientX, clientY } = e;
                const xPos = (clientX / window.innerWidth) * 10;
                const yPos = (clientY / window.innerHeight) * 10;
                leftPanel.style.backgroundPosition = `${50 + xPos}% ${50 + yPos}%`;
            });
        }
    </script>
</body>
</html>