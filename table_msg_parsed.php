<?php

/*********** Using Switch to request data from Ajax  ************** */
//************* Switching inside "acc" key ************* //
if (isset($_POST["acc"])) {
    $output = ""; //Storage data 

    switch ($_POST["acc"]) {
            /**Selecting data nmatching id with the ticket idm */
        case "init":
            $conexion = pg_connect("host=localhost dbname=<databasename> user=postgres password=<password>");

            $query =   "SELECT * FROM messages JOIN ticket ON messages.id = ticket.idm ORDER BY messages.id asc";
            $result = pg_query($conexion, $query);
            $row_table = pg_num_rows($result);
            if ($row_table > 0) {
                while ($data_msg = pg_fetch_array($result)) {
                    $data_msg_ticket = $data_msg['id'];
                    $output .= "<tr>
                                    <td>
                                        <button class='btn btn-primary' data-bs-toggle='modal' name='details_message' id='details_message' value='$data_msg_ticket' data-bs-target='#exampleModal'>" . $data_msg_ticket . "</button>
                                    </td>
                                    <td>" . $data_msg['message_from'] . "</td>
                                    <td>" . $data_msg['message_to'] . "</td>
                                    <td>" . $data_msg['message_subject'] . "</td>
                                    <td>" . $data_msg['message_date'] . "</td>
                                    <td>
                                        <button class='btn btn-primary' data-bs-toggle='modal' name='update_message' id='update_message' value='" . $data_msg_ticket . "' data-bs-target='#exampleModal1'>Update</button>
                                        <a class='btn btn-secondary' type='button' href='remove_ms_table.php?t=$data_msg_ticket'>Remove</a></td>
                        </tr>";
                }
            } else {
                $output .= "Not data my friend";
            }
            echo $output;
            pg_close($conexion);
            break;

            /** Searching message for sender (message_from) */
        case "search":

            $conexion = pg_connect("host=localhost dbname=<databasename> user=postgres password=<password>");
            $search_box_mail = $_POST['search_box_mail'];
            $query = "SELECT * FROM messages JOIN ticket ON messages.id = ticket.idm WHERE message_from LIKE '%" . $search_box_mail . "%' ";  
            $result = pg_query($conexion, $query);                                 
            $row_table = pg_num_rows($result);
            if ($row_table > 0) {
                while ($data_msg = pg_fetch_array($result)) {
                    $data_msg_ticket = $data_msg['idm'];                   
                    $output .= "<tr>
                                    <td>
                                        <button class='btn btn-primary' data-bs-toggle='modal' name='details_message' id='details_message' value='$data_msg_ticket' data-bs-target='#exampleModal'>" . $data_msg_ticket . "</button>
                                    </td>
                                    <td>" . $data_msg['message_from'] . "</td>
                                    <td>" . $data_msg['message_to'] . "</td>
                                    <td>" . $data_msg['message_subject'] . "</td>
                                    <td>" . $data_msg['message_date'] . "</td>
                                    <td>
                                        <button class='btn btn-primary' data-bs-toggle='modal' name='update_message' id='update_message' value='" . $data_msg_ticket . "' data-bs-target='#exampleModal1'>Update</button>
                                        <a class='btn btn-secondary' type='button' href='remove_ms_table.php?t=$data_msg_ticket'>Remove</a></td>
                                    </tr>";
                }
            } else {
                $output .= "Not data coincidence";
            }
            echo $output;
            pg_close($conexion);                                               
            break;

            /** Searching message for date (message_date) */
        case "form_date":

            $conexion = pg_connect("host=localhost dbname=<databasename> user=postgres password=<password>");                                                                        
            $date1 = date('Y-m-d H:i:s', strtotime($_POST['start_date']));
            $date2 = date('Y-m-d H:i:s', strtotime($_POST['end_date']));
            $query = "SELECT * FROM messages JOIN ticket ON messages.id = ticket.idm WHERE message_date BETWEEN '$date1'  AND '$date2' ";  
            $result = pg_query($conexion, $query);                                    
            $row_table = pg_num_rows($result);
            if ($row_table > 0) {
                while ($data_msg = pg_fetch_array($result)) {
                    $data_msg_ticket = $data_msg['idm'];                       
                    $output .= "<tr>
                                    <td>
                                        <button class='btn btn-primary' data-bs-toggle='modal' name='details_message' id='details_message' value='$data_msg_ticket' data-bs-target='#exampleModal'>" . $data_msg_ticket . "</button>
                                    </td>
                                    <td>" . $data_msg['message_from'] . "</td>
                                    <td>" . $data_msg['message_to'] . "</td>
                                    <td>" . $data_msg['message_subject'] . "</td>
                                    <td>" . $data_msg['message_date'] . "</td>
                                    <td>
                                        <button class='btn btn-primary' data-bs-toggle='modal' name='update_message' id='update_message' value='" . $data_msg_ticket . "' data-bs-target='#exampleModal1'>Update</button>
                                        <a class='btn btn-secondary' type='button' href='remove_ms_table.php?t=$data_msg_ticket'>Remove</a></td>
                                 </tr>";
                }
            } else {
                $output .= 'Not messages in those dates';
            }
            echo $output;
            pg_close($conexion);                                                
            break;

            /** Showing message details with Bootstrap-Modal */
        case "message":

            $conexion = pg_connect("host=localhost dbname=<databasename> user=postgres password=<password>");
            $query =   "SELECT * FROM  messages JOIN ticket ON messages.id = ticket.idm WHERE ticket.idm = " . $_POST['details_message'];                            
            $result = pg_query($conexion, $query);                                       
            // echo pg_last_error($conexion);
            $row_table = pg_num_rows($result);
            if ($row_table > 0) {
                while ($detalles_message = pg_fetch_array($result)) {                           
                    $output .= "
                                <div class='modal-body'>Mensaje de : " . $detalles_message['message_from'] . "
                                <br>Mensaje a : " . $detalles_message['message_to'] . "</div>
                                <br>Mensaje a : " . $detalles_message['id'] . "</div>
                                <div>Mensaje fecha : " . $detalles_message['message_date'] . "</div>";
                }
            } else {
                $output .= "no hay datos en la base de datos";
            }

            echo $output;
            pg_close($conexion);
            break;


            /**  To Update message informations
             * Showing message details with Bootstrap-Modal-1 
             * */
        case "update_data":

            $conexion = pg_connect("host=localhost dbname=<databasename> user=postgres password=<password>");
            $query =   "SELECT * FROM  messages JOIN ticket ON messages.id = ticket.idm WHERE ticket.idm =" . $_POST["update_message"];
            $result = pg_query($conexion, $query);
            $row_table = pg_num_rows($result);
            if ($row_table > 0) {
                while ($detalles_message = pg_fetch_array($result)) {

                    /**Variables to update in from Start */
                    $message_id = $detalles_message["id"];
                    $message_from = $detalles_message["message_from"];
                    $message_to = $detalles_message["message_to"];
                    $message_subject = $detalles_message["message_subject"];
                    $message_estado = $detalles_message["estado"];
                    $message_usuarios = $detalles_message["usuarios"];
                    $message_observations = $detalles_message["observations"];
                    /**Variables to update in form End*/

                    $output = "                     
                            <form autocomplete='off' name='Submit_update'data-bs-target='#Submit_update' id='Submit_update' method='POST'>
                                <div class='form-group mb-3'><label for='' class='col-form-label'>Mensaje de : </label>
                                    <input type='hidden' name='nticket' value='$message_id'>
                                    <input class='form-control' type='text' name='message_from' id='message_from' value='$message_from'>
                                </div> 
                                <div class='form-group mb-3'><label for='' class='col-form-label'>Mensaje a : </label>
                                    <input type='text' class='form-control' id='message_to' name='message_to' value='$message_to'>
                                </div> 
                                <div class='form-group'>
                                    <label for=''class='col-form-label'>Mensaje tema : </label>
                                        <input type='text' class='form-control' id='message_subject' name='message_subject' value='$message_subject'>
                                </div>
                                <div class='form-group'>
                                     <label for=''class='col-form-label'>Observaciones : </label>
                                        <input type='text' class='form-control' id='message_observations' name='message_observations' value='$message_observations'></div>
                                            <select id='value_select'  name='value_select'>
                                                <option value='no_asignado' " . (($message_estado == 'no_asignado') ? 'selected="selected"' : "") . "  >No asignado</option>
                                                <option value='en_resolucion' " . (($message_estado == 'en_resolucion') ? 'selected="selected"' : "") . ">En resolucion</option>
                                                <option value='resuelto' " . (($message_estado == 'resuelto') ? 'selected="selected"' : "") . ">Resuelto</option>
                                            </select> 
                                            
                                            <select id='usuario_select' name='usuario_select'>
                                                <option value='usuario1'  " . (($message_usuarios == 'usuario1') ? 'selected="selected"' : "") . " >Usuario1</option>
                                                <option value='usuario2' " . (($message_usuarios == 'usuario2') ? 'selected="selected"' : "") . ">usuario2</option>
                                                <option value='usuario3'" . (($message_usuarios == 'usuario3') ? 'selected="selected"' : "") . ">usuario3</option>
                                            </select> 
                                            <button class='btn btn-primary' name='update_data_submit' type='submit'>Submit</button>
                                </form> 
                            ";
                }
            } else {
                $output .= "IMposible to update this data";
            }
            echo $output;
            pg_close($conexion);
            break;
    }
    exit;
}

/********************** Updating data with PHP not using AJAX - Start**************************/
if (isset($_POST['update_data_submit'])) {

    $conexion = pg_connect("host=localhost dbname=<databasename> user=postgres password=<password>");
    $message_id = htmlspecialchars($_POST["nticket"]);
    $message_from = htmlspecialchars($_POST["message_from"]);
    $message_to = htmlspecialchars($_POST["message_to"]);
    $message_subject = htmlspecialchars($_POST["message_subject"]);
    $message_estado = htmlspecialchars($_POST["value_select"]);
    $usuario_select = htmlspecialchars($_POST["usuario_select"]);
    $usuario_observations = htmlspecialchars($_POST["message_observations"]);


    $query = "UPDATE  messages SET  message_from='$message_from', message_to='$message_to', message_subject='$message_subject', estado='$message_estado', usuarios='$usuario_select', observations='$usuario_observations' WHERE messages.id ='$message_id'";
    $result = pg_query($conexion, $query);
    pg_close($conexion);
}
/********************** Updating data with PHP not using AJAX - End**************************/

?>
<?php
include_once "header.php";
?>


<body>
    <h1 style="text-align: center;">Message Parsed - showing from database</h1>
    <nav class="navbar navbar-expand-lg bg-light">
        <div class="container-fluid d-flex justify-content-center col-md-12">
            <form method="POST" name="search_form" id="search_form" class="d-flex" action="tabla_parse.php">
                <input class="form-control me-2" name="search_box_mail" id="search_box" type="text"> 
                <button class="btn btn-outline-success" type="submit">Search</button> 
            </form>
        </div>
    </nav>

    <div class="col-md-6">
        <h3 class="text-primary">Filter by date</h3>
        <hr style="border-top:1px dotted #000;" />
        <form method="POST" id="form_date">
            <label>Fecha inicial:</label>
            <input type="date" class="form-control" placeholder="Start" name="start_date" id="start_date" />
            <label>To</label>
            <input type="date" class="form-control" placeholder="End" name="end_date" id="end_date" />
            <button class="btn btn-primary btn-block" id="dispara" type="submit">Search</button>
        </form>
    </div>

    <div>
        <table class="table table-bordered border-primary">
            <thead>
                <tr>

                    <th scope="col">Ticket</th>
                    <th scope="col">From</th>
                    <th scope="col">To</th>
                    <th scope="col">Subject</th>
                    <th scope="col">Date</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody id="container">
                <!-- Table data -->
            </tbody>
        </table>
    </div>


    <!-- Modal to Show details - Start-->
    <div class="modal fade" tabindex="-1" aria-labelledby="exampleModalLabel" id="exampleModal" ari0a-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Message details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="ticket_body">
                    <!-- Modal body -->

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal to Show details - End-->


    <!-- Modal1 to Update - Start-->
    <div class="modal fade" tabindex="-1" aria-labelledby="exampleModalLabel" id="exampleModal1" ari0a-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Message details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body1" id="ticket_body_update">
                    <!-- Modal body -->


                </div>

            </div>
        </div>
    </div>
    <!-- Modal1 to Update - End-->

    <script src=https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js integrity="sha384-u1OknCvxWvY5kfmNBILK2hRnQC3Pr17a+RTT6rIHI7NnikvbZlHgTPOOmMi466C8" crossorigin="anonymous"></script>
    <script>
        //Loading content inittilizing document
        $(document).ready(function() {
            load_html("table_msg_parsed", "acc=init", "container");
        });

        //searching by sender
        $("#search_form").off();
        $("#search_form").submit(function(e) {
            e.preventDefault();
            var search_box_mail = $("#search_box").val();
            load_html("table_msg_parsed", "acc=search&search_box_mail=" + search_box_mail, "container");
        });

        //Showing message details - Modal
        $("#exampleModal").on("shown.bs.modal", function(event) {
            var button = event.relatedTarget
            var details_message = button.getAttribute('value')
            load_html("table_msg_parsed", "acc=message&details_message=" + details_message, "ticket_body");
        });

        //Showing message details to update - Modal-1
        $("#exampleModal1").on("shown.bs.modal", function(event) {
            var button = event.relatedTarget
            var update_message = button.getAttribute('value')

            load_html("table_msg_parsed", "acc=update_data&update_message=" + update_message, "ticket_body_update");
        });

        //Searching messages by date
        $("#form_date").submit(function(e) {
            e.preventDefault();
            var fecha_incial = $("#start_date").val();
            var end_date = $("#end_date").val();
            load_html("table_msg_parsed", "acc=form_date&start_date=" + fecha_incial + "&end_date=" + end_date, "container");


        });

        //Function to send date with Ajax
        function load_html(url1, data1, div) {
            var currentTime = new Date();

            //Using Pace library when send message with Ajax
            Pace.track(function() {
                $.ajax({
                    type: "POST",
                    url: url1 + ".php",
                    data: data1 + "&timestamp=" + currentTime,
                    cache: false,
                    success: function(content) {
                        $("#" + div).html(content);
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        if (XMLHttpRequest.status == "404") {
                            load_html("table_msg_parsed", data1 + "&tabla=" + url1.replace(/_list/gi, ""), div);
                        } else {
                            $("#" + div).html('');
                            msg("Page: " + url1 + "<br/>status: " + XMLHttpRequest.status + "<br/>Error: " + errorThrown, "alert-error");
                        }
                    }
                });
            });


        }
    </script>
</body>

</html>
