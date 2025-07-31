<section class="section">
    <div class="container">
        <div class="columns is-centered">
            <div class="column is-half">
                <div class="box">
                    <h1 class="title has-text-centered">Reset Password</h1>
                    <p class="subtitle has-text-centered">Enter your new password below.</p>
                    
                    <form method="post" action="/auth/reset-password">
                        <input type="hidden" name="token" value="<?= $token ?>">
                        
                        <div class="field">
                            <label class="label" for="password">New Password</label>
                            <div class="control has-icons-left">
                                <input class="input <?= isset($errors['password']) ? 'is-danger' : '' ?>" 
                                       type="password" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Your new password (min. 8 characters)" 
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
                            <label class="label" for="confirm_password">Confirm New Password</label>
                            <div class="control has-icons-left">
                                <input class="input <?= isset($errors['confirm_password']) ? 'is-danger' : '' ?>" 
                                       type="password" 
                                       id="confirm_password" 
                                       name="confirm_password" 
                                       placeholder="Confirm your new password" 
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
                                <button class="button is-primary is-fullwidth" type="submit">Reset Password</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Font Awesome for icons -->
<script defer src="https://use.fontawesome.com/releases/v5.15.4/js/all.js"></script>