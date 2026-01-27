<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTMS</title>
    <!-- jQuery file -->
    <script src="includes/jquery_latest.js"></script>
    <!-- Bootstrap files -->
    <link rel="stylesheet" type="text/css" href="booystrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.18.0/font/bootstrap-icons.css">
    <script src="bootstrap/js/bootstrap.min.js"></script>

    <link rel="stylesheet" type="text/css" href="css/style.css">
    <style>
        .logo-image {
            width: 100px;

            height: auto;

            margin-right: 10px;

            vertical-align: middle;

        }

        .contact-info {
            display: none;
            margin-top: 20px;
            padding: 10px;
            background-color: rgba(255, 255, 255, 0.25);

        }

        .contact-link,
        .feedback-link {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            background-color: rgba(0, 0, 255, 0.5);
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .feedback-link {
            margin-bottom: 60px;
        }

        .contact-link:hover,
        .feedback-link:hover {
            background-color: #0056b3;
        }

        .img {
            vertical-align: middle;
            margin-right: 7px;
        }

        .contact-info img {
            vertical-align: middle;
            margin-right: 7px;
        }

        .feedback-form {
            display: none;
            position: fixed;
            bottom: 100px;
            right: 20px;
            z-index: 1000;
            background-color: rgba(255, 255, 255, 0.5);
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 300px;
        }

        .modal-body {
            display: flex;
            flex-direction: column;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
    </style>
    <script>
        function toggleContact() {
            var contactInfo = document.getElementById('contact-info');
            if (contactInfo.style.display === 'none') {
                contactInfo.style.display = 'block';
            } else {
                contactInfo.style.display = 'none';
            }
        }

        function toggleFeedback() {
            var feedbackForm = document.getElementById('feedback-form');
            if (feedbackForm.style.display === 'none') {
                feedbackForm.style.display = 'block';
            } else {
                feedbackForm.style.display = 'none';
            }
        }

        function submitFeedback(event) {
            event.preventDefault();
            var feedbackData = {
                type: document.getElementById('feedbackType').value,
                description: document.getElementById('feedbackDescription').value,
                priority: document.getElementById('feedbackPriority').value,
                contact: document.getElementById('feedbackContact').value,
            };

            // Example: Sending feedback data to the server using jQuery AJAX
            $.ajax({
                url: 'submit_feedback.php', // Your server-side script to handle feedback
                method: 'POST',
                data: feedbackData,
                success: function(response) {
                    alert('Thank you for your feedback!');
                    var feedbackModal = bootstrap.Modal.getInstance(document.getElementById('feedbackModal'));
                    feedbackModal.hide();
                },
                error: function(error) {
                    alert('Error submitting feedback. Please try again later.');
                }
            });
        }
    </script>

</head>

<body>
    <h1>
        <img src="/OTMS PROJECT/OTMS PROJECT/logo/logo33.png" alt="Logo" class="logo-image">
        ONLINE TASK MANAGEMENT SYSTEM
    </h1>
    <div class="container center-align">
        <div class="row">
            <div class="col-md-12 text-center">
                <h3>CHOOSE LOGIN ROLE!</h3><br>
            </div>
        </div>
        <form action="config.php" method="post">
            <div class="row login-options-row">
                <div class="col-md-4 text-center">
                    <a href="user_login.php" class="btn login-button login-button-success">User Login</a><br><br><br>
                </div>
                <div class="col-md-4 text-center">
                    <a href="register.php" class="btn login-button login-button-warning">User Registration</a><br><br><br>
                </div>
                <div class="col-md-4 text-center">
                    <a href="admin/admin_login.php" class="btn login-button login-button-info">Admin Login</a><br>
                </div>
            </div>

        </form>
    </div>

    <a href="#" onclick="toggleContact();" class="contact-link">CONTACT FOR QUESTIONS</a>
    <a href="#" onclick="toggleFeedback();" class="feedback-link">FEEDBACK</a>

    <div id="contact-info" class="container center-align contact-info">
        <b>
            <h2>CONTACT INFORMATION</h2>
        </b>
        <p><img src="envelope.svg" alt="Envelope Icon" height="32" width="32" class="img"> Email: Jessemallya@gmail.com</p>
        <p><img src="telephone.svg" alt="Telephone Icon" height="32" width="32" class="img"> Phone: +255653294241</p>
        <p><img src="instagram.svg" alt="Instagram Icon" height="32" width="32" class="img"> Instagram: _.firminjr03</p>
    </div>

    <div id="feedback-form" class="feedback-form">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="feedbackModalLabel">Submit Feedback</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="feedbackForm" onsubmit="submitFeedback(event);">
                        <div class="form-group">
                            <label for="feedbackType">Type of Feedback</label>
                            <select class="form-select" id="feedbackType" required>
                                <option value="Bug Report">Bug Report</option>
                                <option value="Feature Request">Feature Request</option>
                                <option value="General Feedback">General Feedback</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="feedbackDescription">Description</label>
                            <textarea class="form-control" id="feedbackDescription" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="feedbackPriority">Priority</label>
                            <select class="form-select" id="feedbackPriority" required>
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="feedbackContact">Contact Information (optional)</label>
                            <input type="text" class="form-control" id="feedbackContact" placeholder="Email or Phone">
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Feedback</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


</body>

</html>