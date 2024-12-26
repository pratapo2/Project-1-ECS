<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Welcome to our modern, responsive website with dynamic PHP content">
    <title>PHP-Powered Webpage</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }

        nav {
            background-color: #333;
            color: #fff;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 1rem;
        }

        nav ul li a {
            color: #fff;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
        }

        nav ul li a:hover {
            background-color: #575757;
        }

        .hero {
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: #fff;
            text-align: center;
            padding: 2rem;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }

        .hero a {
            text-decoration: none;
            background-color: #fff;
            color: #333;
            padding: 0.75rem 1.5rem;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: bold;
        }

        .hero a:hover {
            background-color: #eaeaea;
        }

        footer {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 1rem;
        }

        footer ul {
            list-style: none;
            display: flex;
            justify-content: center;
            gap: 1rem;
            padding: 1rem 0;
        }

        footer ul li a {
            color: #fff;
            text-decoration: none;
        }

        footer ul li a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <?php
    $welcomeMessage = "Welcome to Our Dynamic Website";
    $currentDate = date("l, F j, Y");
    ?>
    <nav>
        <h1>BrandLogo</h1>
        <ul>
            <li><a href="#home">Home</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#services">Services</a></li>
            <li><a href="#contact">Contact</a></li>
        </ul>
    </nav>

    <section class="hero" id="home">
        <h1><?php echo $welcomeMessage; ?></h1>
        <p>Today is <?php echo $currentDate; ?>.</p>
        <a href="#services">Learn More</a>
    </section>

    <footer>
        <ul>
            <li><a href="#privacy">Privacy Policy</a></li>
            <li><a href="#terms">Terms of Service</a></li>
            <li><a href="#contact">Contact Us</a></li>
        </ul>
        <p>&copy; <?php echo date("Y"); ?> BrandLogo. All rights reserved.</p>
    </footer>
</body>

</html>

