
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

<script src="/webroot/plugins/summernote/dist/summernote.min.js"></script>
<script src="/webroot/plugins/summernote/dist/summernote-init.js"></script>

<link href="/webroot/plugins/summernote/dist/summernote.css" rel="stylesheet">
<link href="/webroot/css/style.css" rel="stylesheet">

<script src="/webroot/plugins/validation/jquery.validate.min.js"></script>
<script src="/webroot/plugins/validation/jquery.validate-init.js"></script>

<script src="/webroot/plugins/tables/js/jquery.dataTables.min.js"></script>
<script src="/webroot/plugins/tables/js/datatable/dataTables.bootstrap4.min.js"></script>
<script src="/webroot/plugins/tables/js/datatable-init/datatable-basic.min.js"></script>


<!--<script src="/webroot/js/jquery.min.js"></script>-->
<!--    <script src="/webroot/js/bootstrap.min.js"></script>-->
<script src="/webroot/js/croppie.js"></script>
<!--    <link rel="stylesheet" href="/webroot/css/bootstrap.min.css" />-->
<link rel="stylesheet" href="/webroot/css/croppie.css" />

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

<script>
    $(document).ready(function(){

        $image_crop = $('#image_demo').croppie({
            enableExif: true,
            viewport: {
                width:400,
                height:400,
                type:'square' //circle
            },
            boundary:{
                width:500,
                height:500
            }
        });

        // $image_crop_big_size = $('#upload_image').croppie({
        //     enableExif: true,
        //     viewport: {
        //         width:800,
        //         height:800,
        //         type:'square' //circle
        //     },
        //     boundary:{
        //         width:800,
        //         height:800
        //     }
        // });

        $('#upload_image').on('change', function(){
            var reader = new FileReader();
            reader.onload = function (event) {
                $image_crop.croppie('bind', {
                    url: event.target.result
                }).then(function(){
                    console.log('jQuery bind complete');
                });
            }
            reader.readAsDataURL(this.files[0]);
            // $('#uploadimageModal').modal('show');
        });

        $('.crop_image').click(function(event){
            $image_crop.croppie('result', {
                type: 'canvas',
                size: 'viewport'
            }).then(function(response){
                console.log(response);

                $('#upload_image').html('');
                $('#uploaded_image').html('<img src="'+response+'" alt="Girl in a jacket" width="200" height="200"/> ' +
                    '<input name="image" value="'+response+'" hidden>');

                // $.ajax({
                //     url:"upload.php",
                //     type: "POST",
                //     data:{"image": response},
                //     success:function(data)
                //     {
                //         $('#uploadimageModal').modal('hide');
                //         $('#uploaded_image').html(data);
                //     }
                // });
            })
        });

    });
</script>

</body>
</html>
