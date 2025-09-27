<!DOCTYPE html>
<html>

<head>
    <title>CSRF Test</title>
</head>

<body>
    <h2>Simulate CSRF Attack</h2>
    <p>This form tries to delete user ID = 17 without a valid CSRF token.</p>

    <form method="POST" action="http://localhost:8080/delete_user.php">
        <input type="hidden" name="id" value="17">
        <input type="hidden" name="csrf_token" value="INVALID_TOKEN">
        <button type="submit">Send Fake Delete Request</button>
    </form>
</body>

</html>