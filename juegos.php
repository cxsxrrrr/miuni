<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MiUni Kids Matemáticas | Juegos</title>
    <link rel="stylesheet" href="index.css">
</head>
<body class="min-h-screen bg-gradient-to-b from-sky-50 to-white">
    <main class="max-w-3xl mx-auto p-6">
      <h1 class="text-3xl font-extrabold text-sky-800 mb-4">Juegos de Matemáticas</h1>
      <p class="text-gray-700">¡Diviértete aprendiendo matemáticas con nuestros juegos interactivos!</p>
    </main>

    <script>
        (function() {
            var originalTitle = document.title;
            var titleChanged = false;

            function setLeaveTitle() {
                if (!titleChanged) {
                    try { document.title = '¡Regresa a Jugar Pronto!'; } catch (e) {}
                    titleChanged = true;
                }
            }

            function restoreTitle() {
                if (titleChanged) {
                    try { document.title = originalTitle; } catch (e) {}
                    titleChanged = false;
                }
            }


            window.addEventListener('beforeunload', function (e) {
                setLeaveTitle();
                var message = '¡Regresa a Jugar Pronto!';
                e.returnValue = message; 
                return message;
            });

            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    setLeaveTitle();
                } else {
                    restoreTitle();
                }
            });


            document.addEventListener('click', function(ev) {
                var a = ev.target.closest && ev.target.closest('a');
                if (!a) return;
                var href = a.getAttribute('href');
                if (href && href.indexOf('#') !== 0 && href.indexOf('javascript:') !== 0) {
                    setLeaveTitle();
                }
            });
        })();
    </script>
</body>
</html>