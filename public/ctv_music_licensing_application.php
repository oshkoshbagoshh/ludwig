<?php

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CTV Music Licensing Application Form</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bulma CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.0/css/bulma.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <style>
        .is-required::after { content:" *"; color: #f14668; }
        .field.is-grouped .control:not(:last-child) { margin-right: 0.75em; }
    </style>
</head>
<body>
<section class="section">
    <div class="container">
        <div class="box" style="max-width: 800px; margin: auto;">
            <h1 class="title is-3">CTV Music Licensing Application Form</h1>
            <p class="mb-5">
                Please fill out this form to help us match your ad campaign with the most suitable tracks from our music library for sync licensing.
            </p>
            <form id="music-licensing-form" enctype="multipart/form-data">
                <!-- Mood -->
                <div class="field">
                    <label class="label is-required">1. What mood do you want to convey?</label>
                    <div class="control">
                        <label class="checkbox mr-3"><input type="checkbox" name="mood" value="Energetic"> Energetic</label>
                        <label class="checkbox mr-3"><input type="checkbox" name="mood" value="Calm"> Calm</label>
                        <label class="checkbox mr-3"><input type="checkbox" name="mood" value="Happy"> Happy</label>
                        <label class="checkbox mr-3"><input type="checkbox" name="mood" value="Melancholic"> Melancholic</label>
                        <input class="input is-small mt-2" type="text" id="mood-other" placeholder="Other (press Enter to add)">
                        <div id="mood-other-list" class="mt-1"></div>
                    </div>
                </div>
                <!-- Genres -->
                <div class="field">
                    <label class="label is-required">2. Which genres do you prefer?</label>
                    <div class="control">
                        <label class="checkbox mr-3"><input type="checkbox" name="genres" value="Pop"> Pop</label>
                        <label class="checkbox mr-3"><input type="checkbox" name="genres" value="Rock"> Rock</label>
                        <label class="checkbox mr-3"><input type="checkbox" name="genres" value="Electronic"> Electronic</label>
                        <label class="checkbox mr-3"><input type="checkbox" name="genres" value="Classical"> Classical</label>
                        <label class="checkbox mr-3"><input type="checkbox" name="genres" value="Jazz"> Jazz</label>
                        <label class="checkbox mr-3"><input type="checkbox" name="genres" value="Hip-Hop"> Hip-Hop</label>
                        <label class="checkbox mr-3"><input type="checkbox" name="genres" value="Country"> Country</label>
                        <label class="checkbox mr-3"><input type="checkbox" name="genres" value="Indie"> Indie</label>
                        <input class="input is-small mt-2" type="text" id="genres-other" placeholder="Other (press Enter to add)">
                        <div id="genres-other-list" class="mt-1"></div>
                    </div>
                </div>
                <!-- Audience -->
                <div class="field">
                    <label class="label is-required">3. Who is your target audience?</label>
                    <div class="control">
                        <div class="select">
                            <select name="audience" required>
                                <option value="">Select audience</option>
                                <option value="Children">Children</option>
                                <option value="Teenagers">Teenagers</option>
                                <option value="Adults">Adults</option>
                                <option value="Seniors">Seniors</option>
                                <option value="Families">Families</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                </div>
                <!-- Keywords / Themes -->
                <div class="field">
                    <label class="label">4. What keywords or themes are associated with your campaign?</label>
                    <div class="control">
                        <input class="input" type="text" name="keywords" placeholder="e.g., adventure, celebration, nostalgia">
                    </div>
                </div>
                <!-- Duration -->
                <div class="field">
                    <label class="label">5. Preferred duration of the music track (seconds)</label>
                    <div class="control">
                        <input class="input" type="number" name="duration" min="0" placeholder="e.g., 30">
                    </div>
                </div>
                <!-- Instruments -->
                <div class="field">
                    <label class="label">6. Which instruments should be featured?</label>
                    <div class="control">
                        <label class="checkbox mr-3"><input type="checkbox" name="instruments" value="Guitar"> Guitar</label>
                        <label class="checkbox mr-3"><input type="checkbox" name="instruments" value="Piano"> Piano</label>
                        <label class="checkbox mr-3"><input type="checkbox" name="instruments" value="Drums"> Drums</label>
                        <label class="checkbox mr-3"><input type="checkbox" name="instruments" value="Strings"> Strings</label>
                        <label class="checkbox mr-3"><input type="checkbox" name="instruments" value="Brass"> Brass</label>
                        <label class="checkbox mr-3"><input type="checkbox" name="instruments" value="Synth"> Synth</label>
                        <label class="checkbox mr-3"><input type="checkbox" name="instruments" value="Vocals"> Vocals</label>
                        <input class="input is-small mt-2" type="text" id="instruments-other" placeholder="Other (press Enter to add)">
                        <div id="instruments-other-list" class="mt-1"></div>
                    </div>
                </div>
                <!-- Tempo -->
                <div class="field">
                    <label class="label">7. What is the tempo of the music you are looking for?</label>
                    <div class="control">
                        <label class="radio mr-3"><input type="radio" name="tempo" value="Slow"> Slow</label>
                        <label class="radio mr-3"><input type="radio" name="tempo" value="Medium"> Medium</label>
                        <label class="radio mr-3"><input type="radio" name="tempo" value="Fast"> Fast</label>
                        <label class="radio mr-3"><input type="radio" name="tempo" value="No preference"> No preference</label>
                    </div>
                </div>
                <!-- Artist / Band -->
                <div class="field">
                    <label class="label">8. Any specific artist or band in mind?</label>
                    <div class="control">
                        <input class="input" type="text" name="artist" placeholder="Artist or band name">
                    </div>
                </div>
                <!-- Language -->
                <div class="field">
                    <label class="label">9. What is the primary language of your campaign?</label>
                    <div class="control">
                        <div class="select">
                            <select name="language">
                                <option value="">Select language</option>
                                <option value="English">English</option>
                                <option value="Spanish">Spanish</option>
                                <option value="French">French</option>
                                <option value="German">German</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                </div>
                <!-- Reference Tracks -->
                <div class="field">
                    <label class="label">10. Upload any reference tracks or samples</label>
                    <div class="control">
                        <input class="input" type="file" name="files" multiple accept="audio/*">
                    </div>
                </div>
                <!-- Usage Details -->
                <div class="field">
                    <label class="label">11. Where will this ad be shown?</label>
                    <div class="control">
                        <label class="checkbox mr-3"><input type="checkbox" name="usage" value="CTV (Connected TV)"> CTV (Connected TV)</label>
                        <label class="checkbox mr-3"><input type="checkbox" name="usage" value="Online (Web, Social Media)"> Online (Web, Social Media)</label>
                        <label class="checkbox mr-3"><input type="checkbox" name="usage" value="Broadcast TV"> Broadcast TV</label>
                        <label class="checkbox mr-3"><input type="checkbox" name="usage" value="Radio"> Radio</label>
                        <input class="input is-small mt-2" type="text" id="usage-other" placeholder="Other (press Enter to add)">
                        <div id="usage-other-list" class="mt-1"></div>
                    </div>
                </div>
                <!-- Campaign Dates -->
                <div class="field is-grouped">
                    <div class="control">
                        <label class="label">Expected campaign start date</label>
                        <input class="input" type="date" name="startDate">
                    </div>
                    <div class="control">
                        <label class="label">End date</label>
                        <input class="input" type="date" name="endDate">
                    </div>
                </div>
                <!-- Impressions -->
                <div class="field">
                    <label class="label">Estimated number of impressions/views</label>
                    <div class="control">
                        <input class="input" type="number" name="impressions" min="0" placeholder="Optional">
                    </div>
                </div>
                <!-- Territory -->
                <div class="field">
                    <label class="label">In which countries/regions will your ad be broadcast?</label>
                    <div class="control">
                        <input class="input" type="text" name="territory" placeholder="e.g., USA, Canada, Global">
                    </div>
                </div>
                <!-- Notes -->
                <div class="field">
                    <label class="label">Additional notes or requirements</label>
                    <div class="control">
                        <textarea class="textarea" name="notes" rows="2" placeholder="Anything else we should know?"></textarea>
                    </div>
                </div>
                <!-- Rights Confirmation -->
                <div class="field">
                    <div class="control">
                        <label class="checkbox">
                            <input type="checkbox" name="rightsConfirmed" required>
                            <span class="ml-2">
                I confirm that I have the authority to request music licensing for this campaign and agree to comply with all relevant copyright and licensing terms.
              </span>
                        </label>
                    </div>
                </div>
                <!-- Privacy Statement -->
                <div class="notification is-light is-info">
                    <strong>Privacy Statement:</strong><br>
                    Your information is collected solely for the purpose of processing your music licensing request. We will not share your personal data with third parties except as required to fulfill your request or by law. For questions about your data, please contact us using the details below.
                </div>
                <!-- Contact Info -->
                <div class="notification is-light is-primary">
                    <strong>Contact for Follow-Up:</strong><br>
                    Email: <a href="mailto:musiclicensing@yourcompany.com">musiclicensing@yourcompany.com</a><br>
                    Phone: <a href="tel:+1234567890">+1 (234) 567-890</a>
                </div>
                <!-- Submit -->
                <div class="field">
                    <div class="control">
                        <button type="submit" class="button is-link is-medium">Submit Application</button>
                    </div>
                </div>
            </form>
            <div id="form-success" class="notification is-success is-hidden mt-4"></div>
        </div>
    </div>
</section>
<script>
    // Helper for 'Other' fields
    function handleOtherInput(inputId, listId, checkboxName) {
        $(inputId).on('keypress', function(e) {
            if (e.which === 13 && this.value.trim() !== "") {
                e.preventDefault();
                let val = this.value.trim();
                let id = checkboxName + "-other-" + val.replace(/\s+/g, '-').toLowerCase();
                let html = `<label class="checkbox mr-3" id="${id}-label">
          <input type="checkbox" name="${checkboxName}" value="${val}" checked> ${val}
          <a class="delete is-small ml-1" title="Remove"></a>
        </label>`;
                $(listId).append(html);
                this.value = "";
            }
        });
        $(listId).on('click', '.delete', function() {
            $(this).closest('label').remove();
        });
    }
    $(function() {
        handleOtherInput("#mood-other", "#mood-other-list", "mood");
        handleOtherInput("#genres-other", "#genres-other-list", "genres");
        handleOtherInput("#instruments-other", "#instruments-other-list", "instruments");
        handleOtherInput("#usage-other", "#usage-other-list", "usage");

        $("#music-licensing-form").on("submit", function(e) {
            e.preventDefault();
            // For demonstration, just show a success message and reset
            $("#form-success").removeClass("is-hidden").text("Thank you for your submission! We’ll review your request and follow up with you shortly.");
            this.reset();
            $(".checkbox input[type=checkbox]").prop("checked", false);
            $("#mood-other-list, #genres-other-list, #instruments-other-list, #usage-other-list").empty();
            setTimeout(function() {
                $("#form-success").addClass("is-hidden").text("");
            }, 8000);
        });
    });
</script>
</body>
</html>