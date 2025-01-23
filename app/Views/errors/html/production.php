<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">
    <title><?= lang('Errors.whoops') ?></title>

    <style>
        body {
            font-family: Arial, sans-serif;
            color: #fff;
            background: linear-gradient(135deg, #4b6cb7, #182848);
            padding: 20px;
            margin: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            text-align: center;
            padding: 40px;
            border: 2px solid #f8d7da;
            /* Light red border */
            border-radius: 10px;
            background: rgba(0, 0, 0, 0.7);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
        }

        .headline {
            font-size: 36px;
            font-weight: bold;
            color: #f44336;
            /* Red headline */
            margin-bottom: 20px;
        }

        .lead {
            font-size: 20px;
            color: #fff;
            margin-bottom: 20px;
        }

        .error-details {
            margin-top: 30px;
            font-size: 16px;
            padding: 15px;
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
            border-radius: 5px;
            text-align: left;
            white-space: pre-wrap;
            /* Preserve formatting in error details */
        }

        .error-details code {
            font-family: Consolas, monospace;
            font-size: 14px;
            color: #333;
            background-color: #f0f0f0;
            padding: 5px;
            border-radius: 4px;
            word-wrap: break-word;
        }
    </style>
</head>

<body>

    <div class="container">
        <h1 class="headline"><?= lang('Errors.whoops') ?></h1>
        <p class="lead"><?= lang('Errors.weHitASnag') ?></p>

        <!-- Optionally show more error details -->
        <div class="error-details">
            <h3>Error Details:</h3>
            <!-- Display the error message -->
            <p><strong>Message:</strong> <code><?= htmlspecialchars($errorMessage ?? 'No error message available') ?></code></p>

            <!-- Display the detailed message -->
            <p><strong>Detailed Message:</strong> <code><?= esc($message) ?></code></p>
    
            <!-- Display the file and line where the error occurred -->
            <p><strong>File:</strong> <code><?= esc($file) ?></code></p>
            <p><strong>Line:</strong> <code><?= esc($line) ?></code></p>
        </div>
    </div>

</body>

</html>