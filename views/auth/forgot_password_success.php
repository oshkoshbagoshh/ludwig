<section class="section">
    <div class="container">
        <div class="columns is-centered">
            <div class="column is-half">
                <div class="box">
                    <h1 class="title has-text-centered">Password Reset Email Sent</h1>
                    <div class="notification is-success">
                        <p>We've sent a password reset link to your email address. Please check your inbox and follow the instructions to reset your password.</p>
                    </div>
                    
                    <?php if (isset($token)): ?>
                    <div class="notification is-info">
                        <p><strong>Note:</strong> In a production environment, this token would be sent via email. For development purposes, you can use the link below:</p>
                        <p class="mt-2"><a href="/auth/reset-password/<?= $token ?>">Reset Password Link</a></p>
                    </div>
                    <?php endif; ?>
                    
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