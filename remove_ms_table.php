<?php
$conexion = pg_connect("host=localhost dbname=<databasename> user=postgres password=<password>");
$remove_message = $_GET["t"];
$query =   "SELECT * from messages WHERE id='$button'";
$result = pg_query($conexion, $query);

while ($data_table = pg_fetch_array($result)) {
    $dat = $data_table['status'];
    $dat1 = $data_table['users'];
    if ($dat == 'close' && $dat1 == 'user3') {
        $remove_message = $_GET["t"];
        $query =   "DELETE from messages WHERE id ='$remove_message'";
        $result = pg_query($conexion, $query);
        header('HTTP/1.1 200 OK');
        header("location:table_msg_parsed.php");
        pg_close($conexion);
    } else {
        echo "imposible to remove";
    }
}
pg_close($conexion);
header('HTTP/1.1 200 OK');
header("location:table_msg_parsed.php");
