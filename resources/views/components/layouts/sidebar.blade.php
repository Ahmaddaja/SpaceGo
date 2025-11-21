@props(['isOpen' => true])

    <!-- Sidebar -->
    <aside id="sidebar" class="bg-dark text-white p-3">
        <h4 class="mb-4">Menu</h4>
        <ul class="nav flex-column">
            <li class="nav-item mb-2">
                <a href="#" class="nav-link text-white">Dashboard</a>
            </li>
            <li class="nav-item mb-2">
                <a href="#" class="nav-link text-white">Option 1</a>
            </li>
            <li class="nav-item mb-2">
                <a href="#" class="nav-link text-white">Option 2</a>
            </li>
        </ul>
    </aside>


    <!-- Main Content -->
    <main class="flex-grow-1">
        <button class="btn btn-dark m-3" onclick="toggleSidebar()">
            <i class="bi bi-list"></i> Menu
        </button>


        <div class="p-4">
            {{ $slot }}
        </div>
    </main>



<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('collapsed');
    }
</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">