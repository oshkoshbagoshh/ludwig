<div class="container">
    <h1 class="title">Upload Music</h1>
    
    <form action="/upload" method="POST" enctype="multipart/form-data" class="upload-form">
        <div class="field">
            <label class="label">Select Audio File</label>
            <div class="control">
                <input type="file" name="audio_file" accept="audio/*" required class="input">
            </div>
        </div>
        
        <div class="field">
            <div class="control">
                <button type="submit" class="button is-primary">Upload</button>
            </div>
        </div>
    </form>
</div>