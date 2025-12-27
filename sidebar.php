<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside
    class="w-64 bg-white dark:bg-zinc-900 border-r border-slate-200 dark:border-zinc-800 flex flex-col transition-colors duration-300">
    <div class="p-6 flex items-center justify-between">
        <h2 class="text-xl font-bold text-indigo-600 dark:text-indigo-400">EkinCRM</h2>
        <button onclick="toggleTheme()"
            class="p-2 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors rounded-lg hover:bg-slate-100 dark:hover:bg-zinc-800">
            <i data-lucide="sun" class="w-5 h-5 hidden dark:block"></i>
            <i data-lucide="moon" class="w-5 h-5 block dark:hidden"></i>
        </button>
    </div>
    <nav class="flex-1 px-4 space-y-1">
        <a href="index.php"
            class="flex items-center px-4 py-3 text-sm font-medium <?php echo $current_page === 'index.php' ? 'text-indigo-600 bg-indigo-50 dark:bg-indigo-900/20 dark:text-indigo-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-zinc-800' ?> rounded-xl transition-colors">
            <i data-lucide="layout-dashboard" class="w-5 h-5 mr-3"></i>
            Dashboard
        </a>
        <a href="clients.php"
            class="flex items-center px-4 py-3 text-sm font-medium <?php echo $current_page === 'clients.php' ? 'text-indigo-600 bg-indigo-50 dark:bg-indigo-900/20 dark:text-indigo-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-zinc-800' ?> rounded-xl transition-colors">
            <i data-lucide="users" class="w-5 h-5 mr-3"></i>
            Müşteriler
        </a>
        <a href="projects.php"
            class="flex items-center px-4 py-3 text-sm font-medium <?php echo $current_page === 'projects.php' ? 'text-indigo-600 bg-indigo-50 dark:bg-indigo-900/20 dark:text-indigo-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-zinc-800' ?> rounded-xl transition-colors">
            <i data-lucide="briefcase" class="w-5 h-5 mr-3"></i>
            Projeler
        </a>
        <a href="kanban.php"
            class="flex items-center px-4 py-3 text-sm font-medium <?php echo $current_page === 'kanban.php' ? 'text-indigo-600 bg-indigo-50 dark:bg-indigo-900/20 dark:text-indigo-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-zinc-800' ?> rounded-xl transition-colors">
            <i data-lucide="layout" class="w-5 h-5 mr-3"></i>
            Kanban
        </a>
        <a href="payments.php"
            class="flex items-center px-4 py-3 text-sm font-medium <?php echo $current_page === 'payments.php' ? 'text-indigo-600 bg-indigo-50 dark:bg-indigo-900/20 dark:text-indigo-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-zinc-800' ?> rounded-xl transition-colors">
            <i data-lucide="credit-card" class="w-5 h-5 mr-3"></i>
            Ödemeler
        </a>
        <a href="accounting.php"
            class="flex items-center px-4 py-3 text-sm font-medium <?php echo $current_page === 'accounting.php' ? 'text-indigo-600 bg-indigo-50 dark:bg-indigo-900/20 dark:text-indigo-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-zinc-800' ?> rounded-xl transition-colors">
            <i data-lucide="pie-chart" class="w-5 h-5 mr-3"></i>
            Muhasebe
        </a>
        <a href="settings.php"
            class="flex items-center px-4 py-3 text-sm font-medium <?php echo $current_page === 'settings.php' ? 'text-indigo-600 bg-indigo-50 dark:bg-indigo-900/20 dark:text-indigo-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-zinc-800' ?> rounded-xl transition-colors">
            <i data-lucide="settings" class="w-5 h-5 mr-3"></i>
            Ayarlar
        </a>
    </nav>
    <div class="p-4 border-t border-slate-100 dark:border-zinc-800">
        <a href="logout.php"
            class="flex items-center px-4 py-3 text-sm font-medium text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition-colors">
            <i data-lucide="log-out" class="w-5 h-5 mr-3"></i>
            Çıkış Yap
        </a>
    </div>
</aside>
