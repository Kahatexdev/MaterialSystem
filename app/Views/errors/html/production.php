<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= lang('Errors.whoops') ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-gradient-start: #3a3f72;
            --bg-gradient-end: #182848;
            --card-bg: rgba(255, 255, 255, 0.05);
            --accent-color: #ff6b6b;
            --text-main: #e0e0e0;
            --text-highlight: #ffffff;
            --detail-bg: rgba(255, 255, 255, 0.1);
            --detail-border: rgba(255, 255, 255, 0.2);
            --font-family: 'Roboto', Arial, sans-serif;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-family);
            color: var(--text-main);
            background: linear-gradient(135deg, var(--bg-gradient-start), var(--bg-gradient-end));
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            max-width: 700px;
            width: 100%;
            background: var(--card-bg);
            border: 1px solid var(--detail-border);
            border-radius: 12px;
            padding: 40px;
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            text-align: center;
            position: relative;
        }

        .icon {
            font-size: 48px;
            color: var(--accent-color);
            position: absolute;
            top: -24px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--bg-gradient-end);
            border-radius: 50%;
            padding: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .headline {
            font-size: 2.5rem;
            font-weight: 700;
            margin-top: 20px;
            margin-bottom: 10px;
            color: var(--text-highlight);
        }

        .lead {
            font-size: 1.25rem;
            margin-bottom: 30px;
        }

        .error-details {
            background: var(--detail-bg);
            border: 1px solid var(--detail-border);
            border-radius: 8px;
            text-align: left;
            padding: 20px;
            font-size: 0.95rem;
            color: var(--text-main);
            max-height: 300px;
            overflow-y: auto;
        }

        .error-details h3 {
            margin-bottom: 10px;
            color: var(--accent-color);
        }

        .error-details p {
            margin-bottom: 8px;
            line-height: 1.5;
        }

        .error-details code {
            display: inline-block;
            font-family: Consolas, monospace;
            background: rgba(255, 255, 255, 0.15);
            padding: 4px 6px;
            border-radius: 4px;
            word-break: break-all;
        }

        @media (max-width: 600px) {
            .container {
                padding: 20px;
            }

            .headline {
                font-size: 2rem;
            }

            .lead {
                font-size: 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="icon">❗️</div>
        <h1 class="headline"><?= lang('Errors.whoops') ?></h1>
        <p class="lead"><?= lang('Errors.weHitASnag') ?></p>

        <div class="error-details">
            <h3>Error Details:</h3>
            <p><strong>Message:</strong> <code><?= htmlspecialchars($errorMessage ?? 'No error message available') ?></code></p>
            <p><strong>Detailed:</strong> <code><?= esc($message) ?></code></p>
            <p><strong>File:</strong> <code><?= esc($file) ?></code></p>
            <p><strong>Line:</strong> <code><?= esc($line) ?></code></p>
        </div>
    </div>
</body>

</html>