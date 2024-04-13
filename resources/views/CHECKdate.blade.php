<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Convert English Date to Nepali Date</title>
</head>
<body>
    <h1>Convert English Date to Nepali Date</h1>
    
    <form method="POST" action="{{ route('convert') }}">
        @csrf
        <label for="english_date">Enter English Date:</label>
        <input type="date" id="english_date" name="english_date" required>
        <button type="submit">Convert</button>
    </form>

    @isset($nepali_date)
        <p>Nepali Date: {{ $nepali_date }}</p>
    @endisset

    phpinfo();

</body>

</html>
