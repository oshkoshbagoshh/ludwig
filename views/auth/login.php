<section class="section">
    <div class="container">
        <div class="columns is-centered">
            <div class="column is-half">
                <div class="box">
                    <h1 class="title has-text-centered">Login</h1>
                    
                    <?php if (isset($errors['auth'])): ?>
                        <div class="notification is-danger">
                            <?= $errors['auth'] ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" action="/auth/login">
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
                                       placeholder="Your password" 
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
                            <div class="control">
                                <label class="checkbox">
                                    <input type="checkbox" name="remember_me" value="1">
                                    Remember me
                                </label>
                            </div>
                        </div>
                        
                        <div class="field">
                            <div class="control">
                                <button class="button is-primary is-fullwidth" type="submit">Login</button>
                            </div>
                        </div>
                    </form>
                    
                    <div class="has-text-centered mt-4">
                        <p>
                            <a href="/auth/forgot-password">Forgot your password?</a>
                        </p>
                        <p class="mt-2">
                            Don't have an account? <a href="/auth/register">Sign up</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Font Awesome for icons -->
<script defer src="https://use.fontawesome.com/releases/v5.15.4/js/all.js"></script>