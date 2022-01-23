<?php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>

    <meta name="author" content="Codeconvey" />
    <!-- Message Box CSS -->
    <link rel="stylesheet" href="/webroot/css/payment/style.css">
    <!--Only for demo purpose - no need to add.-->
    <link rel="stylesheet" href="/webroot/css/payment/demo.css" />

</head>
<body>
<section>
    <div class="rt-container">
        <div class="col-rt-12">
            <div class="Scriptcontent">

                <!-- partial:index.partial.html -->
                <div id='card' class="animated fadeIn">
                    <div id='upper-side-failed'>
                        <xml version="1.0" encoding="utf-8">
                        <img src="/webroot/img/alarm.png" height="100" width="100">
                        <h3 id='status-failed'>
                            Failed
                        </h3>
                    </div>
                    <div id='lower-side-failed'>
                        <p id='message'>
                            Oops, your payment has been failed!
                        </p>
                    </div>
                </div>


            </div>
        </div>
    </div>
</section>

</body>
</html>

<?php
die();
?>


