<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet"/>
    <script src="https://code.jquery.com/jquery-3.7.0.js"
            integrity="sha256-JlqSTELeR4TLqP0OG9dxM7yDPqX1ox/HfgiSLBj8+kM=" crossorigin="anonymous"></script>


</head>
<body class="antialiased">
@if(session('message'))
    <h1>{{ session('message') }}</h1>
@endif
<div
    class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white">
    <label>Files automatically deletes after converting</label><br>
    <label for="doc">Add doc file:</label>
    <input class='doc-to-convert' name="document" type="file">
    <button class="upload_button" disabled>Upload file</button>
    <label>Available variables:</label>
    <label class="vars"></label>
    <div>
        <form method="POST" action="{{ route('convert') }}">
            @csrf
            <label for="variable">Print your text for template:</label>
            <input name="variable" type="text">
            <input class="filename" name="filename" type="hidden" value="">
            <button class="convert_button" hidden>Convert file</button>
        </form>
    </div>
</div>
</body>
</html>
<script>
    $(document).ready(function () {
        $('.doc-to-convert').on('change', function () {
            $('.upload_button').prop('disabled', false);
        });

        $('.upload_button').on('click', function () {
            $('.convert_button').prop('hidden', false);
            // Get the selected file
            var files = $('.doc-to-convert')[0].files;
            if (files.length > 0) {
                var fd = new FormData();
                // Append data
                fd.append('file', files[0]);
                fd.append('_token', '{{ csrf_token() }}');
                // AJAX request
                $.ajax({
                    url: "{{ route('upload') }}",
                    method: 'post',
                    data: fd,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function (response) {
                        $('.vars').append(response.vars.toString());
                        $('.filename').val(response.filename);
                    },
                    error: function (response) {
                        console.log("error : " + JSON.stringify(response));
                    }
                });
            } else {
                alert("Please select a file.");
            }
        });
        $('.convert_button').on('click', function () {
            $('.doc-to-convert').val('');
            $('.vars').remove();
            $('.convert_button').prop('hidden', true);
            $('.upload_button').prop('disabled', true);
        });
    });
</script>
