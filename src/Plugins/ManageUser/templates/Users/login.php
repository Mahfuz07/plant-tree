<div class="login-area">

    <div class="login-left">
<!--        <img src="/css/admin_styles/images/main_logo.png">-->
    </div>

    <div class="login-right">
        <div class="login-box v-middle">
            <h3>Login</h3>
            <div class="row">
                <div class="col-md-12">
                    <?php echo $this->Flash->render('admin_success'); ?>
                    <?php echo $this->Flash->render('admin_error'); ?>
                </div>
            </div>
            <?php echo $this->Form->create(null, ['id' => 'admin-login-form']); ?>
            <div class="form-area">
                <?php
                echo $this->Form->input('username', array(
                    'type' =>'text',
                    'class' =>'md-input',
                    'id' =>'email',
                    'label' =>false,
                    'placeholder' =>'Username'
                ));

                echo $this->Form->input('password', array(
                    'type' =>'password',
                    'class' =>'md-input',
                    'label' =>false,
                    'id' =>'password',
                    'placeholder' =>'Password'
                ));

                ?>
                <input type="submit" value="Sign in">

            </div>

            <div class="remeber-box flex-container">
                <label for="log-rem">
                    <?php
                    echo $this->Form->checkbox('remember_me', array(
                        'hiddenField' => false,
                        'id' =>'log-rem',
                        //'before' => '<label>',
                        //'after' => 'Remember Me</label>'
                    ));
                    $this->Form->unlockField('remember_me');
                    ?> Remember Me
                    <!--                <input id="log-rem" type="checkbox" name="remember_me"> Remember Me-->
                </label>
                <div class="forget"><a href="<?php echo $this->url->build('/forgot-password'); ?>">Forgot Password ?</a></div>
            </div>
            <?php echo $this->Form->end() ?>
            <div class="separate-box">
                <span>or</span>
            </div>
            <div class="new-signup">
                <a href="">Sign up for new account</a>
            </div>
        </div>
    </div>

</div>
