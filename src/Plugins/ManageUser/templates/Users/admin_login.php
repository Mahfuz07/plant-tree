<section class="banner-image-new">
    <div class="container">
        <h1>LOGIN</h1>
    </div>
</section>

<section class="event-booking section">
    <div class="container">
        <div class="school-login">

            <?php echo $this->Form->create(null,array('id'=>'UserLoginForm','novalidate'=>'true')); ?>

            <div class="row">
                <div class="intro-header">
                      <span class="heading heading-style">
                        <h3>LOGIN</h3>
                      </span>
                </div>

                <div class="col-md-12">
<!--                    --><?php //echo $this->element('flash_message'); ?>
                </div>

                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="firstname">Email</label>
                        <!--<input type="text" class="form-control" id="Email">-->
                        <?php echo $this->Form->input('username',array('label'=>false,'type'=>'email', 'div'=>false, 'class'=>'form-control', 'id'=>'field-email', 'label'=>false));?>
                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="firstname">Password</label>
                        <!--<input type="text" class="form-control" id="Password">-->
                        <?php echo $this->Form->input('password',array('type'=>'password','div'=>'false','class'=>'form-control','label'=>false, 'required'=>false));?>
                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="form-group">
                        <button type="submit">LOGIN</button>
                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="remeber-box flex-container">
                        <label>
                            <!--<input type="checkbox"><i class="helper"></i>Remember Me -->
                        </label>
                        <div class="forget">
                            <?php echo $this->Html->link('Forgot Password ?', 'users/forgot-password'); ?>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="separate-box text-center">
                        <span>or</span>
                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="new-signup text-center">
                        <?php echo $this->Html->link('Sign up for new account', 'register'); ?>
                    </div>
                </div>
            </div>

        </div>

        <?php echo $this->Form->end(); ?>

    </div>
</section>

<?php echo $this->Html->css('jquery-ui/jquery-ui.css'); ?>

<script type="text/javascript">
    $(document).ready(function(){

        $("#UserLoginForm").validate({
            rules: {
                username: "required",
                password: "required"
            },
            messages: {
                username: "Please enter a valid email address.",
                password: "Please enter your password."
            }
        });

    });
</script>
