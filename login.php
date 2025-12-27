<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap - EkinCRM</title>
    <script src="theme.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .glass {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .dark .glass {
            background: rgba(9, 9, 11, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>

<body
    class="bg-slate-50 dark:bg-zinc-950 flex items-center justify-center min-h-screen transition-colors duration-300">
    <div class="w-full max-w-md p-8">
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold text-zinc-900 dark:text-white">EkinCRM</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-2">İşlerinizi yönetmeye hemen başlayın</p>
        </div>

        <div class="glass p-8 rounded-2xl shadow-xl transition-colors">
            <form action="login_process.php" method="POST" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Kullanıcı
                        Adı</label>
                    <input type="text" name="username" required
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none"
                        placeholder="kullaniciadi">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Şifre</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none"
                        placeholder="••••••••">
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input type="checkbox"
                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-slate-700 rounded dark:bg-zinc-800">
                        <label class="ml-2 block text-sm text-slate-600 dark:text-slate-400">Beni hatırla</label>
                    </div>
                    <a href="#"
                        class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500">Şifremi
                        unuttum</a>
                </div>

                <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-xl shadow-lg  dark:shadow-indigo-900/20 transition-all transform hover:-translate-y-0.5 active:translate-y-0">
                    Giriş Yap
                </button>
            </form>

            <div class="mt-8 text-center">
                <p class="text-sm text-slate-600 dark:text-slate-400">Hesabınız yok mu? <a href="#"
                        class="font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500">Kayıt Ol</a></p>
            </div>
        </div>
    </div>
</body>

</html>
