<form method="POST" action="../api/register_process.php">
    <input type="text" name="username" required placeholder="Name">
    <input type="email" name="email" required placeholder="Email">
    <input type="password" name="password" required placeholder="Password">
    <select name="role" required>
        <option value="subadmin">Sub Admin</option>
    </select>
    <button type="submit">Register</button>
</form>
