<section class="section">
    <div class="container">
        <div class="columns is-centered">
            <div class="column is-half">
                <div class="box">
                    <h1 class="title has-text-centered">Create an Account</h1>
                    
                    <form method="post" action="/auth/register">
                        <div class="field">
                            <label class="label" for="first_name">First Name</label>
                            <div class="control has-icons-left">
                                <input class="input <?= isset($errors['first_name']) ? 'is-danger' : '' ?>" 
                                       type="text" 
                                       id="first_name" 
                                       name="first_name" 
                                       placeholder="Your first name" 
                                       value="<?= $first_name ?? '' ?>" 
                                       required>
                                <span class="icon is-small is-left">
                                    <i class="fas fa-user"></i>
                                </span>
                            </div>
                            <?php if (isset($errors['first_name'])): ?>
                                <p class="help is-danger"><?= $errors['first_name'] ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="field">
                            <label class="label" for="last_name">Last Name</label>
                            <div class="control has-icons-left">
                                <input class="input <?= isset($errors['last_name']) ? 'is-danger' : '' ?>" 
                                       type="text" 
                                       id="last_name" 
                                       name="last_name" 
                                       placeholder="Your last name" 
                                       value="<?= $last_name ?? '' ?>" 
                                       required>
                                <span class="icon is-small is-left">
                                    <i class="fas fa-user"></i>
                                </span>
                            </div>
                            <?php if (isset($errors['last_name'])): ?>
                                <p class="help is-danger"><?= $errors['last_name'] ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="field">
                            <label class="label" for="email">Email</label>
                            <div class="control has-icons-left">
                                <input class="input <?= isset($errors['email']) ? 'is-danger' : '' ?>" 
                                       type="email" 
                                       id="email" 
                                       name="email" 
                                       placeholder="e.g. alex@example.com" 
                                       value="<?= $email ?? '' ?>" 
                                       required>
                                <span class="icon is-small is-left">
                                    <i class="fas fa-envelope"></i>
                                </span>
                            </div>
                            <?php if (isset($errors['email'])): ?>
                                <p class="help is-danger"><?= $errors['email'] ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="field">
                            <label class="label" for="password">Password</label>
                            <div class="control has-icons-left">
                                <input class="input <?= isset($errors['password']) ? 'is-danger' : '' ?>" 
                                       type="password" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Your password (min. 8 characters)" 
                                       required>
                                <span class="icon is-small is-left">
                                    <i class="fas fa-lock"></i>
                                </span>
                            </div>
                            <?php if (isset($errors['password'])): ?>
                                <p class="help is-danger"><?= $errors['password'] ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="field">
                            <label class="label" for="confirm_password">Confirm Password</label>
                            <div class="control has-icons-left">
                                <input class="input <?= isset($errors['confirm_password']) ? 'is-danger' : '' ?>" 
                                       type="password" 
                                       id="confirm_password" 
                                       name="confirm_password" 
                                       placeholder="Confirm your password" 
                                       required>
                                <span class="icon is-small is-left">
                                    <i class="fas fa-lock"></i>
                                </span>
                            </div>
                            <?php if (isset($errors['confirm_password'])): ?>
                                <p class="help is-danger"><?= $errors['confirm_password'] ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="field">
                            <div class="control">
                                <button class="button is-primary is-fullwidth" type="submit">Register</button>
                            </div>
                        </div>
                    </form>
                    
                    <div class="has-text-centered mt-4">
                        <p>
                            Already have an account? <a href="/auth/login">Log in</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Font Awesome for icons -->
<script defer src="https://use.fontawesome.com/releases/v5.15.4/js/all.js"></script>