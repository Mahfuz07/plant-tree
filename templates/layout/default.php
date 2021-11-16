
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>

    <link href="https://fonts.googleapis.com/css?family=Raleway:400,700" rel="stylesheet">



    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body>

<!--    <main class="main">-->
<!--        <div class="container">-->
            <?= $this->Flash->render() ?>

<?php
    $allow = false;
    if ($this->request->getSession()->read('Auth')) { ?>
       <?= $this->element('admin_header') ?>
       <?= $this->element('admin_sidebar') ?>
   <?php } ?>

        <?= $this->fetch('content') ?>
        <?= $this->element('admin_footer') ?>

<!--        </div>-->
<!--    </main>-->
    <footer>
    </footer>
<script src="/webroot/plugins/common/common.min.js"></script>
<script src="/webroot/js/custom.min.js"></script>
<script src="/webroot/js/settings.js"></script>
<script src="/webroot/js/gleek.js"></script>
<script src="/webroot/js/styleSwitcher.js"></script>

<!-- Chartjs -->
<script src="/webroot/plugins/chart.js/Chart.bundle.min.js"></script>
<!-- Circle progress -->
<script src="/webroot/plugins/circle-progress/circle-progress.min.js"></script>
<!-- Datamap -->
<script src="/webroot/plugins/d3v3/index.js"></script>
<script src="/webroot/plugins/topojson/topojson.min.js"></script>
<script src="/webroot/plugins/datamaps/datamaps.world.min.js"></script>
<!-- Morrisjs -->
<script src="/webroot/plugins/raphael/raphael.min.js"></script>
<script src="/webroot/plugins/morris/morris.min.js"></script>
<!-- Pignose Calender -->
<script src="/webroot/plugins/moment/moment.min.js"></script>
<script src="/webroot/plugins/pg-calendar/js/pignose.calendar.min.js"></script>
<!-- ChartistJS -->
<script src="/webroot/plugins/chartist/js/chartist.min.js"></script>
<script src="/webroot/plugins/chartist-plugin-tooltips/js/chartist-plugin-tooltip.min.js"></script>
<script src="/webroot/js/dashboard/dashboard-1.js"></script>

<script type="text/javascript">
    $(document).ready(function(){ //newly added
        $('#val-email').blur(function() {
            var email = $('#val-email').val();

            console.log(email);
            $.ajax({
            url: '/admin/customers/check-email',
            type: 'post',
            data: {
                'email' : email
            },
            success: function(response){
                console.log(response)
                var returnData = JSON.parse(response);
                if (returnData.success == true) {
                    $("#Exist").html("");
                }else{
                    $("#Exist").html("Sorry... Username already taken!");
                }
            }
            });
        });
    });


    function checkPasswordMatch() {
        var password = $("#val-password").val();
        var confirmPassword = $("#val-confirm-password").val();
        if (password != confirmPassword)
            $("#CheckPasswordMatch").html("Passwords does not match!");
        else
            $("#CheckPasswordMatch").html("");
    }
    $(document).ready(function () {
        $("#val-confirm-password").keyup(checkPasswordMatch);
    });
</script>
</body>
</html>
