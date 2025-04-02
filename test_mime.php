<?php
// Vérifier si la fonction mime_content_type est disponible
if (function_exists('mime_content_type')) {
    echo "La fonction mime_content_type est disponible.";
} else {
    echo "La fonction mime_content_type n'est PAS disponible.";
}
?>
