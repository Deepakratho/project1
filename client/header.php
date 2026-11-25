
<nav class="navbar navbar-expand-lg navbar-light bg-light">

    <div class="container-fluid">

        <a class="navbar-brand" href="./">
            <img src="./public/Screenshot 2026-09-10 140430.png" alt="">
        </a>

        <button class="navbar-toggler" type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarNav"
            aria-controls="navbarNav"
            aria-expanded="false"
            aria-label="Toggle navigation">

            <span class="navbar-toggler-icon"></span>

        </button>

        <div class="collapse navbar-collapse" id="navbarNav">

            <ul class="navbar-nav gap-4">

                <!-- Home -->
                <li class="nav-item">
                    <a class="nav-link active" href="./">Home</a>
                </li>

                <?php if (isset($_SESSION['datauser']['username'])): ?>

                    <!-- Logout -->
                    <li class="nav-item">
                        <a class="nav-link" href="?logout=true">
                            Logout
                        </a>
                    </li>

                    <!-- Ask Question -->
                    <li class="nav-item">
                        <a class="nav-link" href="?ask=true">
                            Ask Question
                        </a>
                    </li>

                    <!-- My Questions -->
                    <li class="nav-item">
                        <a class="nav-link" href="?my-questions=true">
                            My Questions 
                            <?php echo ucfirst($_SESSION['datauser']['username']) ?>
                        </a>
                    </li>

                <?php else: ?>

                    <!-- Login -->
                    <li class="nav-item">
                        <a class="nav-link" href="?login=true">
                            Login
                        </a>
                    </li>

                    <!-- Sign Up -->
                    <li class="nav-item">
                        <a class="nav-link" href="?signup=true">
                            Sign Up
                        </a>
                    </li>

                <?php endif; ?>

                <!-- Category -->
                <li class="nav-item">
                    <a class="nav-link" href="?Category=true">
                        Category
                    </a>
                </li>

                <!-- Latest Questions -->
                <li class="nav-item">
                    <a class="nav-link" href="?latestq=true">
                        Latest Questions
                    </a>
                </li>

            </ul>

        </div>
    </div>
</nav>
