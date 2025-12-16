<!DOCTYPE html>
<html>

<head>
    <title>{{ config('app.name') }} API Documentation - Swagger UI</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/swagger-ui-dist@5/swagger-ui.css" />
    <style>
        html {
            box-sizing: border-box;
            overflow: -moz-scrollbars-vertical;
            overflow-y: scroll;
        }

        *,
        *:before,
        *:after {
            box-sizing: inherit;
        }

        body {
            margin: 0;
            background: #fafafa;
        }
        
        /* Custom styles for header */
        .swagger-ui .topbar {
            background-color: #2b3640;
        }
        
        .swagger-ui .topbar .download-url-wrapper input[type=text] {
            border: 2px solid #2b3640;
        }
        
        .swagger-ui .topbar .download-url-wrapper .select-label {
            color: #fff;
        }
    </style>
</head>

<body>
    <div id="swagger-ui"></div>

    <script src="https://cdn.jsdelivr.net/npm/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swagger-ui-dist@5/swagger-ui-standalone-preset.js"></script>
    <script>
        window.onload = function() {
            const ui = SwaggerUIBundle({
                url: "{{ secure_url(route('docs.openapi', [], false)) }}",
                dom_id: '#swagger-ui',
                deepLinking: false,
                persistAuthorization: true,
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                ],
                plugins: [
                    SwaggerUIBundle.plugins.DownloadUrl
                ],
                layout: "StandaloneLayout",
                requestInterceptor: function(request) {
                    // Set default headers
                    request.headers['Accept'] = 'application/json';
                    if (request.body) {
                        request.headers['Content-Type'] = 'application/json';
                    }
                    return request;
                }
            });

            // Add custom header with links
            const customHeader = document.createElement('div');
            customHeader.innerHTML = `
                <div style="background-color: #2b3640; padding: 15px; text-align: center; color: white;">
                    <h3 style="margin: 0;">{{ config('app.name') }} API Documentation</h3>
                    <p style="margin: 10px 0 0 0; font-size: 16px;">
                        <a href="{{ route('docs.scalar') }}" style="color: #61bff0; text-decoration: underline; font-weight: bold;">View in Scalar</a> | 
                        <a href="{{ route('docs.openapi') }}" style="color: #61bff0; text-decoration: underline; font-weight: bold;">Download OpenAPI Spec</a> |
                        <a href="{{ route('docs.postman') }}" style="color: #61bff0; text-decoration: underline; font-weight: bold;">Download Postman Collection</a>
                    </p>
                </div>
            `;
            
            // Insert the custom header before the Swagger UI container
            document.getElementById('swagger-ui').parentNode.insertBefore(customHeader, document.getElementById('swagger-ui'));

            window.ui = ui;
        };
    </script>
</body>

</html>