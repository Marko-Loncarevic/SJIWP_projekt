<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Navigation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4895ef;
            --dark-color: #1b263b;
            --light-color: #f8f9fa;
            --hover-color: #4cc9f0;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar-modern {
            background: linear-gradient(135deg, var(--dark-color), var(--primary-color));
            padding: 0.8rem 2rem;
            box-shadow: var(--shadow);
            position: relative;
            z-index: 1000;
        }

        .navbar-brand-modern {
            color: white;
            font-size: 1.8rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            transition: var(--transition);
        }

        .navbar-brand-modern:hover {
            color: var(--hover-color);
            transform: translateY(-2px);
        }

        .navbar-brand-modern i {
            margin-right: 10px;
            font-size: 1.5rem;
            color: var(--accent-color);
        }

        .nav-item-modern {
            margin: 0 0.5rem;
            position: relative;
        }

        .nav-link-modern {
            color: white !important;
            font-weight: 500;
            padding: 0.8rem 1rem !important;
            border-radius: 8px;
            transition: var(--transition);
            display: flex;
            align-items: center;
        }

        .nav-link-modern i {
            margin-right: 8px;
            font-size: 1.1rem;
        }

        .nav-link-modern:hover {
            color: var(--hover-color) !important;
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        .nav-link-modern.active {
            background: rgba(255, 255, 255, 0.2);
            font-weight: 600;
        }

        .nav-link-modern.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60%;
            height: 3px;
            background: var(--accent-color);
            border-radius: 3px;
        }

        .navbar-toggler-modern {
            border: none;
            outline: none;
            padding: 0.5rem;
        }

        .navbar-toggler-modern:focus {
            box-shadow: none;
        }

        .navbar-toggler-icon-modern {
            background-image: none;
            position: relative;
            width: 30px;
            height: 2px;
            background-color: white;
            display: block;
            transition: var(--transition);
        }

        .navbar-toggler-icon-modern::before,
        .navbar-toggler-icon-modern::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background-color: white;
            left: 0;
            transition: var(--transition);
        }

        .navbar-toggler-icon-modern::before {
            top: -8px;
        }

        .navbar-toggler-icon-modern::after {
            top: 8px;
        }

        .navbar-toggler-modern[aria-expanded="true"] .navbar-toggler-icon-modern {
            background-color: transparent;
        }

        .navbar-toggler-modern[aria-expanded="true"] .navbar-toggler-icon-modern::before {
            transform: rotate(45deg);
            top: 0;
        }

        .navbar-toggler-modern[aria-expanded="true"] .navbar-toggler-icon-modern::after {
            transform: rotate(-45deg);
            top: 0;
        }

        @media (max-width: 991.98px) {
            .navbar-modern {
                padding: 0.8rem 1.5rem;
            }

            .navbar-collapse-modern {
                background: linear-gradient(135deg, var(--dark-color), var(--primary-color));
                padding: 1.5rem;
                border-radius: 0 0 15px 15px;
                box-shadow: var(--shadow);
                margin-top: 10px;
            }

            .nav-item-modern {
                margin: 0.5rem 0;
            }

            .nav-link-modern {
                padding: 0.8rem !important;
            }

            .nav-link-modern.active::after {
                display: none;
            }
        }

        .user-dropdown {
            margin-left: 1rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, 0.3);
            transition: var(--transition);
        }

        .user-avatar:hover {
            border-color: var(--accent-color);
            transform: scale(1.1);
        }

        .dropdown-menu-modern {
            background-color: var(--dark-color);
            border: none;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--shadow);
            margin-top: 10px !important;
        }

        .dropdown-item-modern {
            color: white;
            padding: 0.7rem 1.5rem;
            transition: var(--transition);
        }

        .dropdown-item-modern:hover {
            background-color: var(--primary-color);
            color: white;
            padding-left: 1.8rem;
        }

        .dropdown-divider-modern {
            border-color: rgba(255, 255, 255, 0.1);
        }

        /* Sidebar mode */
        .sidebar-mode .navbar-modern {
            flex-direction: column;
            align-items: flex-start;
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 1rem;
            border-radius: 0;
        }

        .sidebar-mode .navbar-collapse-modern {
            display: flex !important;
            flex-direction: column;
            width: 100%;
            margin-top: 1rem;
        }

        .sidebar-mode .nav-item-modern {
            width: 100%;
        }

        .sidebar-mode .nav-link-modern {
            width: 100%;
            padding-left: 2rem !important;
        }

        body.sidebar-mode {
            padding-left: 250px;
            transition: var(--transition);
        }

        .sidebar-mode .navbar-toggler-modern,
        .sidebar-mode #toggleSidebar {
            display: none;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-modern">
        <div class="container-fluid">
            <a class="navbar-brand navbar-brand-modern" href="#">
                <i class="fas fa-car"></i> Rent-a-Car
            </a>

            <button class="navbar-toggler navbar-toggler-modern" type="button" data-bs-toggle="collapse" data-bs-target="#navbarModern" aria-controls="navbarModern" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon-modern"></span>
            </button>

            <div class="collapse navbar-collapse navbar-collapse-modern" id="navbarModern">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item nav-item-modern">
                        <a class="nav-link nav-link-modern active" href="index.php">
                            <i class="fas fa-home"></i> Početna
                        </a>
                    </li>
                    <li class="nav-item nav-item-modern">
                        <a class="nav-link nav-link-modern" href="korisnici.php">
                            <i class="fas fa-users"></i> Korisnici
                        </a>
                    </li>
                    <li class="nav-item nav-item-modern">
                        <a class="nav-link nav-link-modern" href="pregled_rezervacija.php">
                            <i class="fas fa-calendar-check"></i> Rezervacije
                        </a>
                    </li>
                    <li class="nav-item nav-item-modern">
                        <a class="nav-link nav-link-modern" href="pregled_vozila.php">
                            <i class="fas fa-car-side"></i> Vozila
                        </a>
                    </li>
                </ul>
            </div>

            <button id="toggleSidebar" class="btn btn-light ms-auto">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Aktivna stranica
        document.addEventListener('DOMContentLoaded', function () {
            const currentPage = window.location.pathname.split('/').pop() || 'index.php';
            const navLinks = document.querySelectorAll('.nav-link-modern');
            navLinks.forEach(link => {
                const linkHref = link.getAttribute('href');
                if (currentPage === linkHref) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });
        });

        // Sidebar toggle
        document.getElementById('toggleSidebar').addEventListener('click', function () {
            document.body.classList.toggle('sidebar-mode');
        });
    </script>
</body>
</html>
