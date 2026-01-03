<!DOCTYPE html>
<html>

<head>
    <title>{{ config('app.name') }} API Documentation</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        body {
            margin: 0;
            padding: 0;
        }
    </style>
</head>

<body>
    <div style="background-color: #2b3640; padding: 15px; text-align: center; color: white;">
        <h3 style="margin: 0;">{{ config('app.name') }} API Documentation</h3>
        <p style="margin: 10px 0 0 0; font-size: 16px;">
            <a href="{{ route('docs.swagger') }}" style="color: #61bff0; text-decoration: underline; font-weight: bold;">View in Swagger</a> |
            <a href="{{ route('docs.postman') }}" style="color: #61bff0; text-decoration: underline; font-weight: bold;">Download Postman Collection</a>
        </p>
    </div>
    <script id="api-reference" data-url="{{ secure_url(route('docs.openapi', [], false)) }}"></script>
    <script>
        document.getElementById('api-reference').dataset.configuration = JSON.stringify({
            "theme": "purple",
            "layout": "modern",
            "showSidebar": true,
            "hideDownloadButton": false,
            "searchHotKey": "k",
            "persistAuth": true,
            "metaData": {
                "title": "{{ config('app.name') }} API Documentation",
                "description": "Complete API reference for {{ config('app.name') }}."
            },
            "defaultHttpClient": {
                "targetKey": "javascript",
                "clientKey": "fetch"
            },
            "clients": {
                "javascript": {
                    "fetch": {
                        "headers": {
                            "Accept": "application/json",
                            "Content-Type": "application/json"
                        }
                    }
                }
            },
            "httpClientConfig": {
                "baseURL": "{{ config('app.url') }}",
                "headers": {
                    "Accept": "application/json",
                    "Content-Type": "application/json"
                }
            },
            "defaultHeaders": {
                "Accept": "application/json",
                "Content-Type": "application/json"
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@scalar/api-reference"></script>
</body>

</html>