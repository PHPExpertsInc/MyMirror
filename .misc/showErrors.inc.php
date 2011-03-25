<?php
function showErrorMessage(Exception $e)
{
?>
    <div class="error_message">
        <h2>Oops! An error has occured:</h2>
        <p>Error: <?php echo $e->getMessage(); ?></p>
    </div>
<?php
}

