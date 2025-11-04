<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Feedback Baru</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Noto Sans', sans-serif; }
    </style>
</head>
<body>
    <h2>Feedback Baru</h2>
    <p><strong>Periode:</strong> {{ $feedback->period }}</p>
    <p><strong>User:</strong> {{ optional($feedback->user)->name }} ({{ optional($feedback->user)->email }})</p>
    <p><strong>Rating:</strong> {{ $feedback->rating }}/5</p>
    <p><strong>Pesan:</strong></p>
    <p style="white-space: pre-wrap;">{{ $feedback->message ?: '-' }}</p>
    <hr />
    <p>Dikirim: {{ $feedback->created_at }}</p>
</body>
<html>