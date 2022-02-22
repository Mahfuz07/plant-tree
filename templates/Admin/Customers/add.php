    <div class="content-body">

        <div class="row page-titles mx-0">
            <div class="col p-md-0">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Customers</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Add Customer</a></li>
                </ol>
            </div>
        </div>
        <!-- row -->

        <div class="container-fluid">
            <?php
            $success = $this->Flash->render('success');
            if ($success) { ?>
            <div class="alert alert-success">
                 <?php echo $success; ?>
            </div>
            <?php } ?>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-validation">
                                <?php echo $this->Form->create(null, ['id' => 'user-password-form']); ?>
                                <div class="form-valide" action="#" method="post">
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label" for="val-username">Display Name <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-lg-6">
<!--                                            <input type="text" class="form-control" id="val-display-name" name="val-display-name" placeholder="Enter a Display Name..">-->
                                            <?php
                                            echo $this->Form->input('display_name', array(
                                                'type' =>'text',
                                                'class' =>'form-control',
                                                'id' =>'val-display-name',
                                                'label' =>false,
                                                'placeholder' =>'Enter a Display Name..',
                                                'required' => true
                                            ));
                                            ?>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label" for="val-email">Email <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-lg-6">
<!--                                            <input type="text" class="form-control" id="val-email" name="val-email" placeholder="Your valid email..">-->
                                            <?php
                                            echo $this->Form->input('email', array(
                                                'type' =>'email',
                                                'class' =>'form-control',
                                                'id' =>'val-email',
                                                'label' =>false,
                                                'placeholder' =>'Your valid email..',
                                                'required' => true
                                            ));
                                            ?>
                                            <span class="registrationFormAlert" style="color:#dc0000;" id="Exist"></span>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label" for="val-password">Password <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-lg-6">
<!--                                            <input type="password" class="form-control" id="val-password" name="val-password" placeholder="Choose a safe one..">-->
                                            <?php
                                            echo $this->Form->input('password', array(
                                                'type' =>'password',
                                                'class' =>'form-control',
                                                'id' =>'val-password',
                                                'label' =>false,
                                                'placeholder' =>'Choose a safe one..',
                                                'required' => true
                                            ));
                                            ?>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-4 col-form-label" for="val-confirm-password">Confirm Password <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-lg-6">
<!--                                            <input type="password" class="form-control" id="val-confirm-password" name="val-confirm-password" placeholder="..and confirm it!">-->
                                            <?php
                                            echo $this->Form->input('confirm_password', array(
                                                'type' =>'password',
                                                'class' =>'form-control',
                                                'id' =>'val-confirm-password',
                                                'label' =>false,
                                                'placeholder' =>'..and confirm it!',
                                                'required' => true,
                                                'equalTo' => '#val-password'
                                            ));
                                            ?>
                                            <span class="registrationFormAlert" style="color:#dc0000;" id="CheckPasswordMatch"></span>
                                        </div>

                                    </div>
<!--                                    <div class="form-group row">-->
<!--                                        <label class="col-lg-4 col-form-label" for="val-suggestions">Suggestions <span class="text-danger">*</span>-->
<!--                                        </label>-->
<!--                                        <div class="col-lg-6">-->
<!--                                            <textarea class="form-control" id="val-suggestions" name="val-suggestions" rows="5" placeholder="What would you like to see?"></textarea>-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                    <div class="form-group row">-->
<!--                                        <label class="col-lg-4 col-form-label" for="val-skill">Best Skill <span class="text-danger">*</span>-->
<!--                                        </label>-->
<!--                                        <div class="col-lg-6">-->
<!--                                            <select class="form-control" id="val-skill" name="val-skill">-->
<!--                                                <option value="">Please select</option>-->
<!--                                                <option value="html">HTML</option>-->
<!--                                                <option value="css">CSS</option>-->
<!--                                                <option value="javascript">JavaScript</option>-->
<!--                                                <option value="angular">Angular</option>-->
<!--                                                <option value="angular">React</option>-->
<!--                                                <option value="vuejs">Vue.js</option>-->
<!--                                                <option value="ruby">Ruby</option>-->
<!--                                                <option value="php">PHP</option>-->
<!--                                                <option value="asp">ASP.NET</option>-->
<!--                                                <option value="python">Python</option>-->
<!--                                                <option value="mysql">MySQL</option>-->
<!--                                            </select>-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                    <div class="form-group row">-->
<!--                                        <label class="col-lg-4 col-form-label" for="val-currency">Currency <span class="text-danger">*</span>-->
<!--                                        </label>-->
<!--                                        <div class="col-lg-6">-->
<!--                                            <input type="text" class="form-control" id="val-currency" name="val-currency" placeholder="$21.60">-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                    <div class="form-group row">-->
<!--                                        <label class="col-lg-4 col-form-label" for="val-website">Website <span class="text-danger">*</span>-->
<!--                                        </label>-->
<!--                                        <div class="col-lg-6">-->
<!--                                            <input type="text" class="form-control" id="val-website" name="val-website" placeholder="http://example.com">-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                    <div class="form-group row">-->
<!--                                        <label class="col-lg-4 col-form-label" for="val-phoneus">Phone (US) <span class="text-danger">*</span>-->
<!--                                        </label>-->
<!--                                        <div class="col-lg-6">-->
<!--                                            <input type="text" class="form-control" id="val-phoneus" name="val-phoneus" placeholder="212-999-0000">-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                    <div class="form-group row">-->
<!--                                        <label class="col-lg-4 col-form-label" for="val-digits">Digits <span class="text-danger">*</span>-->
<!--                                        </label>-->
<!--                                        <div class="col-lg-6">-->
<!--                                            <input type="text" class="form-control" id="val-digits" name="val-digits" placeholder="5">-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                    <div class="form-group row">-->
<!--                                        <label class="col-lg-4 col-form-label" for="val-number">Number <span class="text-danger">*</span>-->
<!--                                        </label>-->
<!--                                        <div class="col-lg-6">-->
<!--                                            <input type="text" class="form-control" id="val-number" name="val-number" placeholder="5.0">-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                    <div class="form-group row">-->
<!--                                        <label class="col-lg-4 col-form-label" for="val-range">Range [1, 5] <span class="text-danger">*</span>-->
<!--                                        </label>-->
<!--                                        <div class="col-lg-6">-->
<!--                                            <input type="text" class="form-control" id="val-range" name="val-range" placeholder="4">-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                    <div class="form-group row">-->
<!--                                        <label class="col-lg-4 col-form-label"><a href="#">Terms &amp; Conditions</a>  <span class="text-danger">*</span>-->
<!--                                        </label>-->
<!--                                        <div class="col-lg-8">-->
<!--                                            <label class="css-control css-control-primary css-checkbox" for="val-terms">-->
<!--                                                <input type="checkbox" class="css-control-input" id="val-terms" name="val-terms" value="1"> <span class="css-control-indicator"></span> I agree to the terms</label>-->
<!--                                        </div>-->
<!--                                    </div>-->
                                    <div class="form-group row">
                                        <div class="col-lg-8 ml-auto">
                                            <button type="submit" id="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- #/ container -->
    </div>
