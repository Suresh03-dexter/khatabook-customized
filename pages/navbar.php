<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);
?>

<style>
    .navbar {
        background-color: #2d3436;
        overflow: hidden;
        display: flex;
        align-items: center;
        padding: 0 15px;
        position: relative;
    }
    .navbar .logo {
        font-weight: bold;
        font-size: 20px;
        margin-right: auto;
        color: #00cec9;
    }
    .navbar a {
        color: white;
        padding: 14px 20px;
        text-decoration: none;
        display: block;
    }
    .navbar a:hover {
        background-color: #636e72;
    }
    .navbar .menu {
        display: flex;
        gap: 0;
    }
    .navbar .hamburger {
        display: none;
        flex-direction: column;
        cursor: pointer;
        margin-left: 10px;
    }
    .navbar .hamburger span {
        height: 3px;
        width: 25px;
        background: #fff;
        margin: 4px 0;
        border-radius: 2px;
        transition: 0.4s;
    }
    @media (max-width: 768px) {
        .navbar {
            flex-direction: column;
            align-items: flex-start;
            padding: 10px;
        }
        .navbar .logo {
            margin-bottom: 10px;
            font-size: 18px;
        }
        .navbar .menu {
            flex-direction: column;
            width: 100%;
            display: none;
        }
        .navbar .menu.active {
            display: flex;
        }
        .navbar a {
            padding: 10px 15px;
            width: 100%;
            box-sizing: border-box;
        }
        .navbar .hamburger {
            display: flex;
            position: absolute;
            right: 15px;
            top: 15px;
        }
    }
    @media (max-width: 480px) {
        .navbar {
            padding: 5px;
        }
        .navbar .logo {
            font-size: 16px;
        }
        .navbar a {
            padding: 8px 10px;
            font-size: 14px;
        }
    }
</style>

<div class="navbar">
    <div class="logo">Khatabook</div>
    <div class="hamburger" id="hamburger-menu" aria-label="Toggle navigation" tabindex="0">
        <span></span>
        <span></span>
        <span></span>
    </div>
    <div class="menu" id="navbar-menu">
        <?php if ($current_page != 'dashboard.php'): ?>
            <a href="dashboard.php">Dashboard</a>
        <?php endif; ?>

        <?php 
        if ($current_page != 'bussiness.php' && $current_page != 'suppliers.php'): ?>
            <a href="bussiness.php">Business</a>
        <?php endif; ?>

        <?php if ($current_page != 'expence.php'): ?>
            <a href="expence.php">Expenses</a>
        <?php endif; ?>

        <?php if ($current_page != 'purchase.php'): ?>
            <a href="purchase.php">Purchases</a>
        <?php endif; ?>

        <!-- Admin-only Links -->
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <?php if ($current_page != 'admin_khatabook_user.php'): ?>
                <a href="admin_khatabook_user.php">Users</a>
            <?php endif; ?>
            <?php if ($current_page != 'tracker.php'): ?>
                <a href="tracker.php">Tracker</a>
            <?php endif; ?>
            <?php if ($current_page != 'staffs.php'): ?>
                <a href="staffs.php">Staffs</a>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($current_page != 'logout.php'): ?>
            <a href="http://localhost/khatabook/logout.php">Logout</a>
        <?php endif; ?>
    </div>
</div>

<script>
    const hamburger = document.getElementById('hamburger-menu');
    const menu = document.getElementById('navbar-menu');

    function toggleMenu() {
        menu.classList.toggle('active');
    }

    hamburger.addEventListener('click', toggleMenu);
    hamburger.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            toggleMenu();
        }
    });
</script>
