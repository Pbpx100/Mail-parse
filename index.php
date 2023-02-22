<?php
include_once "header.php";
?>

<body>
    <?php


    /****clean_data -> remove special characters to put in database and print*****/

    function clean_data($text)
    {
        $text = htmlspecialchars($text);
        $text = preg_replace("/=/", "=\"\"", $text);
        $text = preg_replace("/&quot;/", "&quot;\"", $text);
        $tags = "/&lt;(\/|)(\w*)(\ |)(\w*)([\\\=]*)(?|(\")\"&quot;\"|)(?|(.*)?&quot;(\")|)([\ ]?)(\/|)&gt;/i";
        $replacement = "<$1$2$3$4$5$6$7$8$9$10>";
        $text = preg_replace($tags, $replacement, $text);
        $text = preg_replace("/=\"\"/", "=", $text);

        return $text;
    }

    /*********************************************************/
    /********************Project beginning********************/

    //Getting mail files from directory
    $directory = opendir(__DIR__ . '/Directory/');

    while ($file = readdir($directory)) {
        if ($file == "." || $file == ".." || !$file) continue;
        //echo '<br>' . "I am a new message with the name " . $file . '<br>';

        //Parsing the mail files == $mp -> message parsed
        $mp = mailparse_msg_parse_file("Directory/" . $file);

        //Getting the structure of the mail files
        $structure = mailparse_msg_get_structure($mp);
        //var_dump($structure);

        foreach ($structure as $part_label) {
            //Labels of structure parsed
            $part = mailparse_msg_get_part($mp, $part_label); //Parsing each part of data
            $part_data = mailparse_msg_get_part_data($part); //Getting data parsed
            // var_dump($part_data); //Date parsed



            /****************************/
            /******** Mail data Parsed ********/

            /** Data from*/
            if (isset($part_data["headers"]["from"])) {
                $msg_from = clean_data($part_data["headers"]["from"]);
                //echo "From: " . $msg_from . "<br>" . PHP_EOL;
            }

            /** Data to*/
            if (isset($part_data["headers"]["to"])) {
                $msg_to = clean_data($part_data["headers"]["to"]);
                //echo "To: " . $msg_to . "<br>" . PHP_EOL;
            }

            /** Data subject*/
            if (isset($part_data["headers"]["subject"])) {
                $msg_subject =  clean_data($part_data["headers"]["subject"]);
                //echo "subjects: " . $msg_subject . "<br>" . PHP_EOL;
            }

            /** Data date*/
            if (isset($part_data["headers"]["date"])) {

                $msg_date = date("Y-m-d H:i:s", strtotime($part_data["headers"]["date"]));
                //echo "Fecha: " .  $msg_date . "<br>" . PHP_EOL;
            }

            /** Getting content type to print in htlm */
            $content_html = strpos($part_data['content-type'], 'text/html');

            if ($content_html !== false) {
                $contents = file_get_contents("Directory/" . $file);
                $content_html = mailparse_msg_extract_part($part, $contents, null); //extract the content nothing more
                echo $content_html;
            }

            /** Saving text clean msg to database */
            $content_text = null;
            if ($part_data['content-type']) {
                $content_text = strpos($part_data['content-type'], 'text/plain');
                if ($content_text !== false) {
                    $content_text = file_get_contents("Directory/" . $file);
                    $content = mailparse_msg_extract_part($part, $content_text, null); //extract the content nothing more
                    $content_text = clean_data($content); //extract the content nothing more

                }
            }

            /** Saving name attahcment msg in database */
            $attachment_name = null;
            if (isset($part_data['content-disposition'])) {
                $attachment_name  = strpos($part_data['content-disposition'], 'attachment');
                if ($attachment_name !== false) {
                    $attachment_name = clean_data($part_data["content-name"]);
                }
            }
        }
        $conexion = pg_connect("host=localhost dbname=<databasename> user=postgres password=<password>");

        $query =   "INSERT INTO messages(message_from, message_to, message_subject, message_date, message_archivo, message_body ) VALUES ('$msg_from', '$msg_to',  '$msg_subject', '$msg_date', Array['$attachment_name'], '$content_text') RETURNING messages.id, messages.message_archivo";
        $in_data = pg_query($conexion, $query);
        $data_return = pg_fetch_row($in_data);

        $data_id = $data_return['0'];
        $data_attachment_name = $data_return['1'];
        //Cleaning attachment name
        $data_attachment_name = str_replace(array('{', '}'), '', $data_attachment_name);



        /******** Saving attahcment in directory_attahcment and creating new links ********/

        if (isset($part_data['content-disposition'])) {
            $attachment  = strpos($part_data['content-disposition'], 'attachment');
            if ($data_attachment_name !== false && $attachment !== false) {
                $data_attachment_file = file_get_contents("Directory/" . $file);
                $content = mailparse_msg_extract_part($part, $data_attachment_file, null);

                //making the dir
                $dir = (__DIR__ . '/Attachments_msg/attachment_' . $data_id);

                if (!is_dir($dir)) {
                    mkdir($dir, 0777, false);
                    //Adding to dir the file name and the file
                    $dir .= '/' . $data_attachment_name;
                    $content = file_put_contents($dir, $content);
                    $link = 'Attachments_msg/attachment_' . $data_id . '/' . $data_attachment_name;
                }
            }
        }
    ?>
        <h2 class="text-center"><a href="table_msg_parsed.php">Link to see message analized </a></h2>
        <div class="container text-center">
            <div class="row">
                <div class="col-12">
                    <h1><?php echo '<br>' . "I am a new message with the name " . $file . '<br>'; ?></h1>
                    <ul class="list-group">
                        <li class="list-group-item"><?php echo "From: " . $msg_from . "<br>" . PHP_EOL ?></li>
                        <li class="list-group-item"><?php echo "To: " . $msg_to . "<br>" . PHP_EOL ?></li>
                        <li class="list-group-item"><?php echo "subjects: " . $msg_subject . "<br>" . PHP_EOL ?></li>
                        <li class="list-group-item"><?php echo "Date: " . $msg_date . "<br>" . PHP_EOL ?></li>
                        <?php if ($attachment_name) {
                        ?>
                            <li class="list-group-item">
                                <a href="<?php echo $link ?>"> <?php echo "Attachment: " . $data_attachment_name . "<br>" . PHP_EOL ?></a>
                            </li>
                        <?php                      }
                        ?>


                    </ul>

                </div>
            </div>




        <?php
        //Mandatory free mailparse_msg
        mailparse_msg_free($mp);
    }
        ?>



        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>