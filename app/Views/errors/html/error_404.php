<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found</title>

    <style>
        body {
            height: 100%;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #4b6cb7, #182848);
            color: #fff;
            text-align: center;
        }

        .container {
            max-width: 600px;
            padding: 2rem;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
            animation: fadeIn 1.5s ease-in-out;
        }

        h1 {
            font-size: 6rem;
            margin: 0;
            animation: bounce 1.5s infinite alternate;
        }

        .illustration {
            width: 150px;
            margin: 1rem auto;
            animation: rotate 3s linear infinite;
        }

        p {
            font-size: 1.2rem;
            margin: 1.5rem 0;
            line-height: 1.5;
        }

        a {
            display: inline-block;
            margin-top: 1rem;
            padding: 0.8rem 2rem;
            font-size: 1rem;
            color: #4b6cb7;
            background: #fff;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        a:hover {
            transform: scale(1.1);
            background: #4b6cb7;
            color: #fff;
            box-shadow: 0 8px 15px rgba(75, 108, 183, 0.5);
        }

        pre {
            text-align: center;
            overflow-x: auto;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes bounce {
            0% {
                transform: translateY(0);
            }

            100% {
                transform: translateY(-15px);
            }
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <img src="<?= base_url('assets/img/crescent-297801_1280.png') ?>" alt="404" class="illustration">
        <h1>404</h1>
        <p>Oops! Halaman yang Anda cari tidak ditemukan.</p>
        <pre><?= esc($message) ?></pre>
        <a href=" /">Kembali ke Halaman Utama</a>
    </div>
</body>

</html>