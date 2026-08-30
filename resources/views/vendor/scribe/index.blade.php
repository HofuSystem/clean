<!DOCTYPE html>
<html>
  <head>
    <title>{{ config('app.name', 'CleanStation') }} API Documentation</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
      /* Customize Scalar to match CleanStation identity */
      :root {
        --scalar-color-1: #0ea5e9; /* Light blue / CleanStation primary */
        --scalar-color-2: #0284c7;
        --scalar-color-3: #0369a1;
        --scalar-color-accent: #0ea5e9;
      }
      .dark-mode {
        --scalar-color-1: #38bdf8;
        --scalar-color-accent: #38bdf8;
      }
      body {
        margin: 0;
        background-color: #f8fafc;
      }
    </style>
  </head>
  <body>
    <!-- Scalar OpenAPI Viewer -->
    <script
      id="api-reference"
      data-url="{{ url('/docs.openapi') }}"
      data-configuration='{
        "theme": "default",
        "layout": "modern",
        "hideModels": true,
        "showSidebar": true,
        "metaData": {
          "title": "CleanStation API"
        }
      }'></script>
    <script src="https://cdn.jsdelivr.net/npm/@scalar/api-reference"></script>
  </body>
</html>
