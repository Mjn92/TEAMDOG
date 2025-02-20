<html>
<head>
    <script>
        // Function to handle the login response
        function handleLoginResponse(response) {
            var data = JSON.parse(response); // Parse the JSON response

            if (data.success) {
                // If login is successful, redirect to home.php
                window.location.href = data.redirect;
            } else {
                // Display the error message
                document.getElementById("textResponse").innerHTML = data.error;
            }
        }

        // Function to send login request
        function sendLoginRequest(username, password) {
            var request = new XMLHttpRequest(); //Allows browser to make request to server without refreshing the page
            request.open("POST", "login.php", true); // Open a POST request to login.php
            request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); //The data is sent as key=value pairs
            
            request.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    handleLoginResponse(this.responseText); // Handle the response when received
                }
            };
            
            // Send username and password to login.php
            request.send("uname=" + username + "&pword=" + password);
        }
    </script>
</head>
<body>
    <h1>Login Page</h1>
    
    <!-- Display login response messages -->
    <div id="textResponse">Please Sign In</div>
    
    <!-- Login Form -->
    <form onsubmit="event.preventDefault(); sendLoginRequest(document.getElementById('username').value, document.getElementById('password').value);">
        <input type="text" id="username" placeholder="Username" required><br>
        <input type="password" id="password" placeholder="Password" required><br>
        <button type="submit">Login</button>
    </form>
</body>
</html>
    
    <!-- Display login response messages -->
    <div id="textResponse">Please Enter Name and Password</div>
    
    <!-- Login Form -->
    <form onsubmit="event.preventDefault(); sendLoginRequest(document.getElementById('username').value, document.getElementById('password').value);">
        <input type="text" id="username" placeholder="Enter Username" required><br>
        <input type="password" id="password" placeholder="Enter Password" required><br>
        <button type="submit">Login</button>
    </form>
</body>
</html>
