<?php
/**
 * Clear Session - Debug Tool
 */

session_start();

echo "<h2>🧹 Session Clear Tool</h2>";
echo "<hr>";

echo "<h3>Current Session:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

echo "<h3>Session Cleared!</h3>";
echo "<p>All session variables have been cleared.</p>";

echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li><a href='auth/register'>📝 Register a new account</a></li>";
echo "<li><a href='auth/login'>🔑 Login with your account</a></li>";
echo "<li><a href='debug_auth.php'>🔍 Check authentication status</a></li>";
echo "</ol>";

?>

<style>
body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
a { color: #007bff; text-decoration: none; margin: 5px; }
a:hover { text-decoration: underline; }
</style>
