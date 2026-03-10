<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PrimeForge Storage Test</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #1a1a2e; color: #e0e0e0; padding: 2rem; }
        .container { max-width: 800px; margin: 0 auto; }
        h1 { font-size: 2rem; margin-bottom: 0.5rem; color: #e94560; }
        .subtitle { color: #888; margin-bottom: 2rem; }
        .card { background: #16213e; border-radius: 8px; padding: 1.5rem; margin-bottom: 1.5rem; }
        .card h2 { font-size: 1.2rem; margin-bottom: 1rem; color: #0f3460; }
        .info { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem; }
        .info-item { background: #0f3460; border-radius: 6px; padding: 1rem; text-align: center; }
        .info-item .label { font-size: 0.75rem; color: #888; text-transform: uppercase; }
        .info-item .value { font-size: 1.1rem; font-weight: bold; color: #e94560; margin-top: 0.25rem; word-break: break-all; }
        form { display: flex; gap: 0.5rem; align-items: center; }
        input[type="file"] { flex: 1; padding: 0.5rem; background: #0f3460; border: 1px solid #333; border-radius: 4px; color: #e0e0e0; }
        button { padding: 0.5rem 1.5rem; background: #e94560; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        button:hover { background: #c73e54; }
        .alert { padding: 0.75rem 1rem; border-radius: 4px; margin-bottom: 1rem; }
        .alert-success { background: #1b4332; color: #95d5b2; }
        .alert-error { background: #4a1e1e; color: #f5a3a3; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 0.75rem; border-bottom: 1px solid #333; }
        th { color: #888; font-size: 0.8rem; text-transform: uppercase; }
        a { color: #e94560; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .empty { text-align: center; color: #666; padding: 2rem; font-style: italic; }
        .delete-form { display: inline; }
        .delete-btn { background: #4a1e1e; padding: 0.25rem 0.5rem; font-size: 0.8rem; }
    </style>
</head>
<body>
    <div class="container">
        <h1>PrimeForge Storage Test</h1>
        <p class="subtitle">S3-compatible file storage via MinIO</p>

        <div class="info">
            <div class="info-item">
                <div class="label">Filesystem Disk</div>
                <div class="value">{{ $disk }}</div>
            </div>
            <div class="info-item">
                <div class="label">S3 Endpoint</div>
                <div class="value">{{ $endpoint }}</div>
            </div>
            <div class="info-item">
                <div class="label">Bucket</div>
                <div class="value">{{ $bucket }}</div>
            </div>
        </div>

        @session('success')
            <div class="alert alert-success">{{ $value }}</div>
        @endsession

        @session('error')
            <div class="alert alert-error">{{ $value }}</div>
        @endsession

        <div class="card">
            <h2>Upload File</h2>
            <form method="POST" action="/upload" enctype="multipart/form-data" id="upload-form">
                @csrf
                <label for="file-input" style="display:inline-block;padding:0.5rem 1.5rem;background:#e94560;color:white;border:none;border-radius:4px;cursor:pointer;font-weight:bold;font-size:inherit;font-family:inherit;line-height:normal;">
                    Choose File
                    <input type="file" name="file" required id="file-input" style="position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0;">
                </label>
                <span id="file-name" style="flex:1;padding:0.5rem;color:#888;">No file chosen</span>
                <button type="submit">Upload</button>
            </form>
            <script>
                document.getElementById('file-input').addEventListener('change', function() {
                    document.getElementById('file-name').textContent = this.files.length ? this.files[0].name : 'No file chosen';
                });
            </script>
        </div>

        <div class="card">
            <h2>Files in Bucket</h2>
            @if(count($files) > 0)
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Size</th>
                            <th>URL</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($files as $file)
                            <tr>
                                <td>{{ $file['name'] }}</td>
                                <td>{{ number_format($file['size'] / 1024, 1) }} KB</td>
                                <td><a href="{{ $file['url'] }}" target="_blank">View</a></td>
                                <td>
                                    <form class="delete-form" method="POST" action="/files/{{ $file['name'] }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="delete-btn">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty">No files uploaded yet. Use the form above to upload a file.</div>
            @endif
        </div>
    </div>
@include('partials.env-footer')
</body>
</html>
