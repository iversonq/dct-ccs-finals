<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Add custom-dashboard.css link here if needed -->
    <title><?php echo $pageTitle; ?></title>
</head>

<body>
    <!-- Header Section -->
    <header class="navbar sticky-top bg-dark flex-md-nowrap p-0 shadow" data-bs-theme="dark">
        <!-- Navbar Brand -->
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-6 text-white" href="#">Student Management System</a>

        <!-- Mobile Navbar Icons -->
        <ul class="navbar-nav flex-row d-md-none">
            <!-- Search Icon -->
            <li class="nav-item text-nowrap">
                <button class="nav-link px-3 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSearch" aria-controls="navbarSearch" aria-expanded="false" aria-label="Toggle search">
                    <svg class="bi">
                        <use xlink:href="#search" />
                    </svg>
                </button>
            </li>
            <!-- Sidebar Toggle Icon -->
            <li class="nav-item text-nowrap">
                <button class="nav-link px-3 text-white" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
                    <svg class="bi">
                        <use xlink:href="#list" />
                    </svg>
                </button>
            </li>
        </ul>

        <!-- Search Bar -->
<div id="navbarSearch" class="navbar-search w-100 collapse">
    <!-- Search Input Field -->
    <div class="input-group">
        <input class="form-control rounded-0 border-0" type="text" placeholder="Search" aria-label="Search" id="searchInput">
        <button class="btn btn-outline-secondary" type="button" id="searchButton" aria-label="Search Button">
            <i class="fas fa-search"></i> <!-- FontAwesome Search Icon -->
        </button>
    </div>
</div>

    <!-- Main Content Area -->
    <div class="container-fluid">
        <div class="row">
            <!-- Add additional content here -->
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-pzjw8f+ua7Kw1TIq0v8FqzHndXt0pAjzJlWy2F6slz63dCmQwR9dzH0VJd9P6yXq" crossorigin="anonymous"></script>
</body>

</html>