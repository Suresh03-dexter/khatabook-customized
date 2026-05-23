<?php
session_start();
session_destroy();
header("Location: http://localhost/khatabook/index.php");
exit();
