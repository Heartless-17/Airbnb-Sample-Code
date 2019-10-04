<?php
function processMail($id_actividad_selected, $recipients)
  {

    require('db_vars.php');
    require_once 'mail/swiftmailer-5.x/lib/swift_required.php';

    $dbh = new PDO("mysql:host=$hostname;dbname=cea_toolbox", $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET utf8"));

    $queryact = "SELECT * FROM actividades WHERE id_actividad=:id_actividad";
    $stmtact = $dbh->prepare($queryact);
    $stmtact->execute(array(':id_actividad' => $id_actividad_selected));
    $rowact = $stmtact->fetch(PDO::FETCH_ASSOC);
    $Activ_name = $rowact['name_cert'];


    $query = "SELECT * FROM matricula WHERE id_actividad=:id_actividad and asistio='si' or 'Si' ORDER BY nombre_completo ASC";
    $stmt = $dbh->prepare($query);
    $stmt->execute(array(':id_actividad' => $id_actividad_selected));
    $row = $stmt->fetchAll();


    $data = $row;

    foreach ($data as $row) {
      $query2 = "SELECT * FROM personas WHERE id_persona=:id_persona";
      $stmt2  = $dbh->prepare($query2);
      $stmt2->execute(array(':id_persona' => $row['id_persona']));
      $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);

      $personas[] = $row2['correo_electronico']; //Saves the emails of the people that attended the activity
    }

    $query3 = "SELECT evaluacionID FROM evaluaciones WHERE actividadID=:actividadID AND tipo_eval LIKE '%Espanol'";
    $stmt3 = $dbh->prepare($query3);
    $stmt3->execute(array(':actividadID' => $id_actividad_selected));
    $row3 = $stmt3->fetch(PDO::FETCH_ASSOC);

    $Espa = $row3['evaluacionID']; //Id of the evaluation in spanish

    $query4 = "SELECT evaluacionID FROM evaluaciones WHERE actividadID=:actividadID AND tipo_eval LIKE '%Ingles'";
    $stmt4 = $dbh->prepare($query4);
    $stmt4->execute(array(':actividadID' => $id_actividad_selected));
    $row4 = $stmt4->fetch(PDO::FETCH_ASSOC);

    $Ingl = $row4['evaluacionID']; //Id of the evaluation in english
    //$attachment = Swift_Attachment::fromPath('/images/logo.png');


    $subject = "Evaluación: " . $rowact['name_cert']; //assigns the name of the activity as the subject
    $from = array('actividades@cea.uprrp.edu' => 'Centro para la Excelencia Académica');
    $replyto = array('cea.upr@upr.edu' => 'Centro para la Excelencia Académica');

    $transport = Swift_SmtpTransport::newInstance('mail.cea.uprrp.edu', 25); //mailserver
    $transport->setUsername('actividades@cea.uprrp.edu');
    $transport->setPassword('sutsfmFo+yQw');

    $swift = Swift_Mailer::newInstance($transport);
    //$to = 'gabriel.algarin@upr.edu';
    $message = new Swift_Message($subject);
    $message->setFrom($from);
    //Variable that holds the email message in html format
    $html = '<html> 
                <body>
                    <table cellspacing="0" cellpadding="0" border="0">
                      <tr>
                        <td>Saludos,</td>
                      </tr>
                      <tr>
                        <td>Agradecemos su reciente participación en las actividades del CEA. Su opinión es importante para nosotros. Por favor tome unos minutos para evaluar la siguiente actividad:</td>
                      </tr>
                      <p></p>
                      <tr>
                        <td><strong>' . $Activ_name . '</strong></td>
                      </tr>
                      <p></p>
                      <tr>
                        <td>Versión en español:</td>
                      </tr>
                      <p></p>
                      <tr>
                        <td><a href="http://toolboxcea2017.cea.uprrp.edu/evaluacion.php?id=' . $Espa . '">Evaluación en Español</a></td>
                      </tr>
                      <hr>
                      <tr>
                      <td>We thank you for your recent participation in the activities of the Center for Academic Excellence.</td>
                      </tr>
                      <p></p>
                      <tr>
                      <td>Your opinion is important to us. We appreciate that you take a moment to evaluate this activity through the following link:</td>
                      </tr>
                      <p></p>
                      <tr>
                        <td><strong>' . $Activ_name . '</strong></td>
                      </tr>
                      <p></p>
                      <tr>
                        <td>English version:</td>
                      </tr>
                      <tr>
                      <td><a href="http://toolboxcea2017.cea.uprrp.edu/evaluation.php?id=' . $Ingl . '">English Evaluation</a></td>
                      </tr>
                      <tr>
                        <td><br />Gracias por apoyar las actividades del Centro para la Excelencia Acad&eacute;mica.</td>
                      </tr>
                      <p></p>
                      <tr>
                        <td>Atentamente,</td>
                      </tr>
                      <tr>
                        <td>Gabriel Andrés Algarín Ballesteros,  Developer</td>
                      </tr>
                      <tr>
                        <td>Marc Anthony De Jesús Ellsworth, Developer</td>
                      </tr>
                      <p></p>
                      <tr>
                        <td><img src="http://toolboxcea2017.cea.uprrp.edu/images/logo.png"></td>
                      </tr>
                      <p></p>
                      <tr>
                        <td>Centro para la Excelencia Académica</td>
                      </tr>
                      <tr>
                        <td>Decanato de Asuntos Académicos</td>
                      </tr>
                      <tr>
                        <td>Universidad de Puerto Rico</td>
                      </tr>
                      <tr>
                        <td>Recinto de Río Piedras</td>
                      </tr>
                      <tr>
                        <td>tel. (787) 764-0000 ext. 83236, 83243, 83244</td>
                      </tr>
                      <tr>
                        <td>fax (787) 772-1429</td>
                      </tr>
                      <p></p>
                      <tr>
                        <td>¡Síguenos! ¡Danos Like!</td>
                      </tr>
                      <tr>
                        <td><a href="​www.cea.uprrp.edu">​cea.uprrp.edu</a></td>
                      </tr>
                       <tr>
                        <td><a href="​​cea.upr@upr.edu">​​cea.upr@upr.edu</a></td>
                      </tr>
                       <tr>
                        <td><a href="​​www.facebook.com/cea.upr">​​Facebook</a></td>
                      </tr>
                       <tr>
                        <td><a href="​​www.twitter.com/CEA_UPR">Twitter</a></td>
                      </tr>
                       <tr>
                        <td><a href="​​www.youtube.com/channel/UCHRnsT1kf4y55WGCfj74mIw?reload=9">Canal de Youtube del CEA</a></td>
                      </tr>
                    </table>
                  </body>
                </html>'; 
    $message->setBody($html, 'text/html'); //assigns body to the message object
    //$message->setTo($to);//se setea el To
    $message->setReplyTo($replyto);

    if (!empty($recipients)) {

      foreach ($recipients as $val) {
        $message->AddBcc($val);  //assigns email adresses to which it will be sent
      }

    } else {

      foreach ($personas as $val) {
        $message->AddBcc($val);  //assigns email adresses to which it will be sent
      }

    }

    $message->AddCc('cea.upr@upr.edu'); // Adds the Center for Academic Excellence email as Copied.

    $result = $swift->send($message); //Sends the message

     if(!$recipients = $swift->send($message, $failures)) //lets us knwo if there were any errors in sending the email
    {
      $objResponseErrEnvio = new xajaxResponse();
      $objResponseErrEnvio->call("Mensaje2","Error(s) found. ".$failures,350);//sino envio el correo se muestra error
      return $objResponseErrEnvio;
    }
    else
    {
      $objResponseNewuser = new xajaxResponse();
      $objResponseNewuser->call("Mensaje","La evaluación ha sido enviada exitosamente.",350);//se muestra confirmacion cuando se matricule
      return $objResponseNewuser;
    }
    $objResponseCreado = new xajaxResponse();
    $objResponseCreado->call("Mensaje", "El correo fue enviado exitosamente!", 350);
    return $objResponseCreado;
  }
?>