<html>
<head>
    <script>
        // Function to handle the login response
        function handleLoginResponse(response) {
            var data = JSON.parse(response); // Parse the JSON response

            if (data.returnCode === 'success') {
                // If login is successful, redirect to home.php
                window.location.href = 'home.php';
            } else {
                // Display the error message
                document.getElementById("textResponse").innerHTML = data.message;
            }
        }

        // Function to send login request to RabbitMQ
        function sendLoginRequest(username, password) {
            var request = new XMLHttpRequest(); // Create a new XMLHttpRequest
            request.open("POST", "login.php", true); // Open a POST request to login.php
            request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            request.onreadystatechange = function() {
                if (this.readyState === 4 && this.status === 200) {
                    handleLoginResponse(this.responseText); // Handle the response when received
                }
            };

            // Send the request data to RabbitMQ
            var requestData = {
                type: 'login',
                username: username,
                password: password
            };

            // Send the data as a JSON-encoded string
            request.send(JSON.stringify(requestData));
        }
    </script>
</head>
<body>
    <h1>Login Page</h1>
    
    <!-- Display login response messages -->
    <div id="textResponse">Please Sign In</div>
    
    <!-- Login Form -->
    <form onsubmit="event.preventDefault(); sendLoginRequest(document.getElementById('username').value, document.getElementById('password').value);">
        <input type="text" id="username" placeholder="Enter Username" required><br>
        <input type="password" id="password" placeholder="Enter Password" required><br>
        <button type="submit">Login</button>
    </form>
</body>
