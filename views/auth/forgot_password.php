<section class="section">
    <div class="container">
        <div class="columns is-centered">
            <div class="column is-half">
                <div class="box">
                    <h1 class="title has-text-centered">Forgot Password</h1>
                    <p class="subtitle has-text-centered">Enter your email address and we'll send you a link to reset your password.</p>
                    
                    <form method="post" action="/auth/forgot-password">
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
                            <div class="control">
                                <button class="button is-primary is-fullwidth" type="submit">Send Reset Link</button>
                            </div>
                        </div>
                    </form>
                    
                    <div class="has-text-centered mt-4">
                        <p>
                            <a href="/auth/login">Back to Login</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Font Awesome for icons -->
<script defer src="https://use.fontawesome.com/releases/v5.15.4/js/all.js"></script>