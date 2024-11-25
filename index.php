<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retrofee</title>

    <!--Style-->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/form.css">
    <link rel="stylesheet" href="css/popupmessage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
</head>
<body>
    <nav id="navbar">
        <label onclick="myFunction()" for="check" class="checkbtn">
            <i class="fas fa-bars"></i>
        </label>
        <a  id="cartBtn" class="btn-shop"><i class="fa fa-shopping-cart" aria-hidden="true"></i></a>
        <img src="image/Retrofee-main-logo.svg" class="logo" alt="Logo">
        <ul class="ul-nav" id="ul-nav">

            <label for="check" onclick="myFunction()" class="checkbtn escapebtn">
                <i class="fa fa-times" aria-hidden="true"></i>
            </label>
            <li><a href="#home" onclick="myFunction()"><span class="menu-icon"><i class="fa fa-home"></i></span>
                    Home</a></li>
            <li><a href="#about" onclick="myFunction()"><span class="menu-icon"><i class="fa fa-user"></i></span>
                    About Us</a></li>
            <li><a href="#contact" onclick="myFunction()"><span class="menu-icon"><i class="fa fa-briefcase"></i></span>
                    Contact</a></li>
            <li><a class="button-login" role="button"  id="loginBtn" onclick="myFunction()">Login</a></li>
            <li><a class="button-sign" role="button" id="signupBtn" onclick="myFunction()">Sign Up</a></li>
        </ul>
    </nav>
     <!-- Login Modal -->
     <div id="loginModal" class="modal">
        <div class="modal-content login">
            <span class="close" id="closeLogin">&times;</span>
            <h2>Login</h2>
            <form action="login.php" method="POST">
                <input type="text" name="username" placeholder="Username or Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" class="submit-btn">Login</button>
                
            </form>
            <p>Don't have an account?<a  id="signuppop" role="button">Sign Up</a></p>
        </div>
    </div>

    <!-- Signup Modal -->
    <div id="signupModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeSignup">&times;</span>
            <h2>Signup</h2>
            <form action="signup.php" method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="text" name="contact" placeholder="Contact Number" required>
                <input type="text" name="location" placeholder="Address" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <button type="submit" class="submit-btn">Signup</button>
            </form>
            
        </div>
    </div>
    <section id="home" style="background-image: url('image/bg-retroo.svg');">
        <div class="home-container">
            <h1>Retrofee</h1>
            <p>Bringing the best of freshly brewed flavors to your doorsteps.</p>
            <a class="btn-order" role="button" id="orderBtn">Order Now!</a>
        </div>
    </section>
    <?php
require 'conn.php';

// SQL query to get top 4 best-selling products
$sql = "
SELECT p.product_id, p.productname, p.image, COUNT(o.product_id) AS order_count
FROM tblorder o
JOIN tblproduct p ON o.product_id = p.product_id
GROUP BY o.product_id
ORDER BY order_count DESC
LIMIT 4
";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo '<div class="bestSell-conatiner">';
    echo '<h2>All Trendings</h2>';
    echo '<div class="slider">';

    // Fetch and display the products twice
    $products = []; // To store the products
    while ($row = $result->fetch_assoc()) {
        $products[] = $row; // Store each product in the array
    }

    // Display products twice
    foreach (array_merge($products, $products) as $product) { // Merge the array with itself
        echo '<div class="slide-item">';
        echo '<img src="' . $product['image'] . '" alt="' . $product['productname'] . '">';
        echo '<p class="product-name">' . $product['productname'] . '</p>';
        echo '</div>';
    }

    echo '</div>';
    echo '</div>';
}

$conn->close();
    
    ?>
    <section id="about">
        <div class="about-container">
            <div class="about-left">
                <img src="image/about-image.png" alt="">
            </div>
            <div class="about-right">
                <div class="about-right-text">
                    <h2>About Us</h2>
                    <p>Welcome to Retrofee, your relaxing the web coffee shop located in Putatan, Muntinlupa!

Retrofee focuses that we deliver more than simply coffee; we serve experiences. Inspired by the appeal of nostalgia and the rich scent of freshly brewed coffee, we provide handmade drinks and delectable sweets that make you feel at home.

</p><br><P>Founded as a tiny business with a large heart, we strive to combine the warmth of traditional coffee culture with modern conveniences. Retrofee is here to make your day unique, whether you need your daily coffee dose, a peaceful moment of indulgence, or a treat to lighten it up.


Thank you for your support during our journey. Let's create memories one cup at a time!

Sip. Savor. Smile. ☕</p>
            
                </div>
            </div>
        </div>
    </section>
    <section id="contact">
        <div class="contact-container">
            <div class="contact-left">
                <h2>Contact Us</h2>
                <p>Feel free to reach out to us with any questions, feedback, or inquiries. We’re here to help and would love to hear from you!</p>

                <div class="socmed">
                    <p>Social Media</p>
                    <ul>
                        <li><a href=""><i class="fab fa-facebook-f"></i></a></li>
                        <li><a href=""><i class="fas fa-envelope"></i></a></li>
                        <li><a href=""><i class="fab fa-instagram"></i></a></li>
                        <li><a href=""><i class="fab fa-tiktok"></i></a></li>
                    </ul>
                </div>
                <div class="contact-info">
                    <div class="info-left">
                        <h3>Visit Us</h3>
                        <p>Putatan Muntinlupa</p>
                    </div>
                    <div class="info-center">
                        <h3>Call Us</h3>
                        <p>+639876543</p>
                        <p>987-654-321</p>
                    </div>
                    <div class="info-right">
                        <h3>Resources</h3>
                        <a href="">Blog</a>
                        <a href="">Event</a>
                        <a href="">Support</a>
                    </div>
                </div>
            </div>
            <div class="contact-right">
                
<form id="contactForm" method="POST" action="">
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Ensure this points to the correct location of the PHPMailer files

if (isset($_POST['get-submit'])) {
    $firstName = htmlspecialchars(trim($_POST['first_name']));
    $lastName = htmlspecialchars(trim($_POST['last_name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $message = htmlspecialchars(trim($_POST['message']));

    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->SMTPDebug = 0;                       // Set to 2 to see more debug info
        $mail->isSMTP();                            // Use SMTP
        $mail->Host       = 'smtp.gmail.com';       // Set the SMTP server (e.g., Gmail)
        $mail->SMTPAuth   = true;                   // Enable SMTP authentication
        $mail->Username   = 'retrofee7@gmail.com'; // Your email address
        $mail->Password   = 'brtr mlme shtv qtne';  // Your email password or App Password (recommended)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Use STARTTLS for encryption
        $mail->Port       = 587;                    // TCP port to connect to

        // Recipients
        $mail->setFrom('retrofee7@gmail.com', 'Retrofee'); // Sender's email and name
        $mail->addAddress('retrofee7@gmail.com');           // Recipient's email

        // Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = "New Message from $firstName $lastName";
        $mail->Body    = "
            <h2>Contact Form Submission</h2>
            <p><strong>First Name:</strong> $firstName</p>
            <p><strong>Last Name:</strong> $lastName</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Phone Number:</strong> $phone</p>
            <p><strong>Message:</strong></p>
            <p>$message</p>
        ";

        $mail->send();
        echo '<div id="sentMessage"><p>Sent successfully!</p></div>';
        echo '<script>
        setTimeout(function() {
            var checkMessage = document.getElementById("sentMessage");
            if (checkMessage) {
                checkMessage.style.display = "none";
            }
        }, 5000);
      </script>';
    } catch (Exception $e) {
        echo "There was a problem sending the message. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
    <div class="getintouch">
        <h3>Get in Touch</h3>
        <p>We're available for any questions or feedback.</p>
        <div class="name-group">
            <input type="text" class="name" name="first_name" placeholder="First Name" required>
            <input type="text" class="name" name="last_name" placeholder="Last Name" required>
        </div>
        <input type="email" name="email" placeholder="Email" required>
        <input type="number" name="phone" placeholder="Phone Number" required>
        <textarea class="helping" name="message" placeholder="How can we help you?" required></textarea>
        <button type="submit" name="get-submit">Submit</button>
    </div>
</form>
            </div>
        </div>
    </section>
    <section id="footer">
        <p>Created by CharlesA 2024.</p>
    </section>
    <script src="js/formmodal.js"></script>
    <script src="js/popupmessage.js"></script>
    <script src="js/click.js"></script>
    <script src="js/scroll.js"></script>
    <script src="js/slider.js"></script>
</body>
</html>