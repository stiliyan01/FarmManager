<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        /* DejaVu Sans е вграден в dompdf и поддържа кирилица. */
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #17201b; }
        h1 { font-size: 15px; margin: 0 0 4px; }
        .subtitle { color: #5b6660; margin-bottom: 12px; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; background: #ecefed; border: 1px solid #cbd3cd; padding: 5px; }
        td { border: 1px solid #e1e6e2; padding: 5px; vertical-align: top; }
        .footer { margin-top: 12px; color: #5b6660; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <div class="subtitle">{{ $subtitle }}</div>

    <table>
        <thead>
            <tr>
                @foreach ($headings as $heading)
                    <th>{{ $heading }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    @foreach ($row as $value)
                        <td>{{ $value }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headings) }}">Няма данни.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Изготвено на {{ now()->format('d.m.Y H:i') }} · Стадо</div>
</body>
</html>
