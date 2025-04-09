<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>jsFiler Test</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.15/themes/default/style.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.15/jstree.min.js"></script>

    <script src="{{ env('APP_PATH') }}/js/jsfiler.js"></script>
    <link rel="stylesheet" href="{{ env('APP_PATH') }}/css/jsfiler.css" />
</head>
<body>

<div id="filer-demo"></div>

<script>
    $(document).ready(function () {
        console.log('jQuery:', typeof jQuery);
        console.log('jsTree:', typeof $.fn.jstree);
        console.log('jsFiler:', typeof $.fn.jsFiler);

        try {
            $('#filer-demo').jsFiler({
                url: '/get-folder-structure',
                allowContextMenu: true
            });
        } catch (e) {
            console.error('Error:', e);
        }
    });
</script>

</body>
</html>
