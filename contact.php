<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | Online Book Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Cormorant+Garamond:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #5c4d3c;    /* Earthy brown */
            --secondary: #8b7355;   /* Warm tan */
            --accent: #a67c52;     /* Coffee accent */
            --light: #f8f4ef;      /* Cream paper */
            --dark: #2a2118;       /* Dark chocolate */
            --border-radius: 4px;
            --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light);
            color: var(--dark);
            line-height: 1.6;
            background-image: url('https://images.unsplash.com/photo-1507842217343-583bb7270b66?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(249, 244, 234, 0.92);
            z-index: 0;
        }

        .contact-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 3rem 2rem;
            position: relative;
            z-index: 1;
        }

        .contact-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .contact-header h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.8rem;
            color: var(--dark);
            margin-bottom: 1rem;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .contact-header p {
            color: var(--dark);
            opacity: 0.8;
            max-width: 600px;
            margin: 0 auto;
            font-size: 1.1rem;
        }

        .contact-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .contact-card {
            background: white;
            padding: 2.5rem 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            text-align: center;
            transition: var(--transition);
            border-top: 4px solid var(--secondary);
        }

        .contact-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }

        .card-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 1.5rem;
            background-color: rgba(139, 115, 85, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--secondary);
            font-size: 1.8rem;
        }

        .contact-card h2 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.6rem;
            margin-bottom: 1rem;
            color: var(--dark);
            font-weight: 600;
        }

        .contact-card p, 
        .contact-card a {
            color: var(--dark);
            opacity: 0.9;
            line-height: 1.7;
            text-decoration: none;
            transition: var(--transition);
            display: block;
            margin-bottom: 0.5rem;
        }

        .contact-card a:hover {
            color: var(--accent);
            text-decoration: underline;
        }

        .hours-list {
            list-style: none;
            margin-top: 1rem;
        }

        .hours-list li {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.3rem;
        }

        .hours-list span:first-child {
            font-weight: 500;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .contact-container {
                padding: 2rem 1.5rem;
            }

            .contact-header h1 {
                font-size: 2.2rem;
            }

            .contact-cards {
                grid-template-columns: 1fr;
                max-width: 500px;
                margin: 2rem auto 0;
            }
        }

        @media (max-width: 480px) {
            .contact-container {
                padding: 1.5rem 1rem;
            }

            .contact-header h1 {
                font-size: 2rem;
            }

            .contact-header p {
                font-size: 1rem;
            }

            .contact-card {
                padding: 2rem 1.5rem;
            }

            .card-icon {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="contact-container">
        <div class="contact-header">
            <h1>Visit Book Store</h1>
            <p>We'd love to hear from you! Reach out through any of these channels or stop by our cozy bookstore.</p>
        </div>

        <div class="contact-cards">
            <div class="contact-card">
                <div class="card-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <h2>Our Location</h2>
                <a href="https://maps.google.com?q=123+Book+Street,+Literary+District,+New+York,+NY+10001" target="_blank">
                    123 Book Street<br>
                    Literary District<br>
                    New York, NY 10001
                </a>
            </div>

            <div class="contact-card">
                <div class="card-icon">
                    <i class="fas fa-phone-alt"></i>
                </div>
                <h2>Call Us</h2>
                <a href="tel:+18005551234">+1 (800) 555-1234</a>
                <p>Mon-Fri: 9am-8pm</p>
            </div>

            <div class="contact-card">
                <div class="card-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <h2>Email Us</h2>
                <a href="mailto:hello@bookhaven.com">hello@bookhaven.com</a>
                <p>Response within 24 hours</p>
            </div>

            <div class="contact-card">
                <div class="card-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <h2>Store Hours</h2>
                <ul class="hours-list">
                    <li><span>Mon-Fri</span> <span>9am - 8pm</span></li>
                    <li><span>Saturday</span> <span>10am - 6pm</span></li>
                    <li><span>Sunday</span> <span>12pm - 5pm</span></li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>