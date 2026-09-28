<?php

require_once __DIR__ . '/../includes/admin_header.php';

?>


<h1>Kopsavilkums</h1>

<p>
    Sveicināti,
    <?= htmlspecialchars($_SESSION['username']) ?>!
</p>


<?php

require_once __DIR__ . '/../includes/admin_footer.php';

?>