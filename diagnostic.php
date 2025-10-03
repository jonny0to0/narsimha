<?php
echo "<h1>Narshimha Tattoo - Diagnostic Page</h1>";
echo "<p><strong>Server Status:</strong> ✅ PHP is working!</p>";
echo "<p><strong>Current Directory:</strong> " . getcwd() . "</p>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Current Time:</strong> " . date('Y-m-d H:i:s') . "</p>";

echo "<h2>File Status:</h2>";
$files = ['index.html', 'index_simple.html', 'index_no_images.html', 'index_modern_saas.html', 'script.js', 'images.js', 'test.html'];
foreach ($files as $file) {
    $status = file_exists($file) ? '✅ EXISTS' : '❌ MISSING';
    $size = file_exists($file) ? ' (' . round(filesize($file)/1024, 2) . ' KB)' : '';
    echo "<p><strong>$file:</strong> $status$size</p>";
}

echo "<h2>Quick Links:</h2>";
echo "<ul>";
echo "<li><a href='index.html' target='_blank'>🎨 Main Website (Full Featured)</a></li>";
echo "<li><a href='index_modern_saas.html' target='_blank'>🚀 Modern SaaS Design (NEW!)</a></li>";
echo "<li><a href='index_simple.html' target='_blank'>⚡ Simple Version (Lightweight)</a></li>";
echo "<li><a href='index_no_images.html' target='_blank'>🖼️ No External Images (Fastest)</a></li>";
echo "<li><a href='test.html' target='_blank'>🔧 Test Page</a></li>";
echo "<li><a href='admin/login.php' target='_blank'>🔐 Admin Login</a></li>";
echo "</ul>";

echo "<h2>Troubleshooting Steps:</h2>";
echo "<ol>";
echo "<li>Try the <a href='index_simple.html'>Simple Version</a> first</li>";
echo "<li>Check browser console for JavaScript errors (F12)</li>";
echo "<li>Ensure XAMPP Apache is running</li>";
echo "<li>Try accessing via <code>http://localhost/tatto/</code></li>";
echo "<li>Clear browser cache and reload</li>";
echo "</ol>";

echo "<style>
body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
h1, h2 { color: #ff073a; }
a { color: #ff073a; text-decoration: none; }
a:hover { text-decoration: underline; }
code { background: #f0f0f0; padding: 2px 4px; border-radius: 3px; }
</style>";
?>
