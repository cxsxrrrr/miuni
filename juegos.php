<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MiUni Kids Matemáticas | Juegos</title>
</head>
<body>
    <h1>Juegos de Matemáticas</h1>
    <p>¡Diviértete aprendiendo matemáticas con nuestros juegos interactivos!</p>

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