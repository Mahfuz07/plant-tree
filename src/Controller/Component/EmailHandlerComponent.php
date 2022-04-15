<?php

namespace App\Controller\Component;

use Cake\Event\Event;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class EmailHandlerComponent extends BaseComponent
{

    var $controller;
    var $Session;

    function startup(Event $event)
    {
        $this->controller = $this->_registry->getController();
        $this->Session = $this->controller->getRequest()->getSession();
    }

    function emailSend($toEmail, $name = ''): bool
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'premium163.web-hosting.com';                     //Set the SMTP server to send through             //Enable SMTP authentication
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'no-reply@plantreebd.com';                     //SMTP username
            $mail->Password   = 'plan_tree@83183122';                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
            $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`


            $mail->setFrom('no-reply@plantreebd.com', 'Plan Tree BD');
            $mail->addAddress($toEmail, $name);     //Add a recipient

            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Account Created Successfully';
            $mail->Body    = "Hello " . $name .", <br>Your Account Successfully Created!";
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            if ($mail->send()) {
                return true;
            } else {
                return false;
            }

        } catch (Exception $e) {
            $this->controller->log($e->getMessage());
            return false;
        }
    }

    function emailHandlerService() {

    }

    function cancelOrderEmail($order, $name = ''): bool
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'premium163.web-hosting.com';                     //Set the SMTP server to send through             //Enable SMTP authentication
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'no-reply@plantreebd.com';                     //SMTP username
            $mail->Password   = 'plan_tree@83183122';                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`


            $mail->setFrom('no-reply@plantreebd.com', 'Plan Tree BD');
            $mail->addAddress($order['customer_email'], $order['customer_name']);     //Add a recipient

            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Your Order Has been Cancel';
            $mail->Body    = "Hello " . $order['customer_name'] .", <br>Your Order " .$order['order_id']. " has been Cancel!";
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            if ($mail->send()) {
                return true;
            } else {
                return false;
            }

        } catch (Exception $e) {
            $this->controller->log($e->getMessage());
            return false;
        }
    }

    function completeOrderEmail($order, $name = ''): bool
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'premium163.web-hosting.com';                     //Set the SMTP server to send through             //Enable SMTP authentication
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'no-reply@plantreebd.com';                     //SMTP username
            $mail->Password   = 'plan_tree@83183122';                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`


            $mail->setFrom('no-reply@plantreebd.com', 'Plan Tree BD');
            $mail->addAddress($order['customer_email'], $order['customer_name']);     //Add a recipient

            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Your Order Has been Cancel';
            $mail->Body    = "Hello " . $order['customer_name'] .", <br>Your Order " .$order['order_id']. " has been Completed!";
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            if ($mail->send()) {
                return true;
            } else {
                return false;
            }

        } catch (Exception $e) {
            $this->controller->log($e->getMessage());
            return false;
        }
    }

}
